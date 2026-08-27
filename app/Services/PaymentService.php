<?php

namespace App\Services;

use App\Actions\ReserveTicketsAction;
use App\Mail\PurchaseReceiptMail;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(private AsaasService $asaas) {}

    /**
     * Criar um novo pagamento (PIX ou Boleto) associado aos bilhetes reservados.
     *
     * @param  Collection<int, Ticket>|array<int, Ticket>  $tickets
     */
    public function createPayment(
        User $user,
        $tickets,
        string $gateway = 'asaas',
        string $paymentMethod = 'pix',
        ?float $fixedAmount = null,
        ?int $rafflePackageId = null,
        ?int $affiliateUserId = null
    ) {
        return DB::transaction(function () use ($user, $tickets, $gateway, $paymentMethod, $fixedAmount, $rafflePackageId, $affiliateUserId) {
            if ($fixedAmount !== null) {
                $totalAmount = $fixedAmount;
            } else {
                $totalAmount = 0;
                foreach ($tickets as $ticket) {
                    $totalAmount += $ticket->raffle->price;
                }
            }

            $transactionId = 'tx_'.Str::random(12);
            $pixKey = '';
            $pixQrUrl = null;

            if ($gateway === 'asaas' && $this->asaas->isConfigured()) {
                try {
                    $pixData = $this->asaas->createPixCharge(
                        $user,
                        (float) $totalAmount,
                        $transactionId,
                        'Compra de cotas - Ação RR Veículos'
                    );
                    $transactionId = $pixData['id'];
                    $pixKey = $pixData['payload'];
                    if (! empty($pixData['encodedImage'])) {
                        $pixQrUrl = 'data:image/png;base64,'.$pixData['encodedImage'];
                    }
                } catch (\Throwable $e) {
                    Log::error('Asaas PIX falhou, usando simulação', ['message' => $e->getMessage()]);
                }
            } elseif ($gateway === 'itau' && Setting::get('itau_enabled') === '1') {
                $pixData = $this->createItauPix($totalAmount);
                if ($pixData) {
                    $transactionId = $pixData['txid'];
                    $pixKey = $pixData['pixCopiaECola'];
                }
            } elseif ($gateway === 'santander' && Setting::get('santander_enabled') === '1') {
                $pixData = $this->createSantanderPix($totalAmount);
                if ($pixData) {
                    $transactionId = $pixData['txid'];
                    $pixKey = $pixData['pixCopiaECola'];
                }
            }

            if ($pixKey === '') {
                $pixKey = '00020101021226870014br.gov.bcb.pix2565qr.example.com/pix/'.$transactionId.'5204000053039865405'.number_format($totalAmount, 2, '.', '').'5802BR5913RR_VEICULOS6009SAO_PAULO62070503***6304'.Str::upper(Str::random(4));
            }

            if ($pixQrUrl === null) {
                $pixQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($pixKey);
            }

            $payment = Payment::create([
                'user_id' => $user->id,
                'affiliate_user_id' => $affiliateUserId,
                'raffle_package_id' => $rafflePackageId,
                'amount' => $totalAmount,
                'gateway' => $gateway,
                'gateway_transaction_id' => $transactionId,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'pix_qr_code' => $pixKey,
                'pix_qr_code_url' => $pixQrUrl,
            ]);

            foreach ($tickets as $ticket) {
                $ticket->update([
                    'payment_id' => $payment->id,
                ]);
            }

            return $payment;
        });
    }

    /**
     * Regenera o QR PIX após alteração de valor (upsell).
     */
    public function refreshPixCharge(Payment $payment): Payment
    {
        $transactionId = 'tx_'.Str::random(12);
        $pixKey = '';
        $pixQrUrl = null;

        if ($payment->gateway === 'asaas' && $this->asaas->isConfigured() && $payment->user) {
            try {
                $pixData = $this->asaas->createPixCharge(
                    $payment->user,
                    (float) $payment->amount,
                    $transactionId,
                    'Compra de cotas - Ação RR Veículos'
                );
                $transactionId = $pixData['id'];
                $pixKey = $pixData['payload'];
                if (! empty($pixData['encodedImage'])) {
                    $pixQrUrl = 'data:image/png;base64,'.$pixData['encodedImage'];
                }
            } catch (\Throwable $e) {
                Log::error('Asaas PIX (upsell) falhou, usando simulação', ['message' => $e->getMessage()]);
            }
        }

        if ($pixKey === '') {
            $pixKey = '00020101021226870014br.gov.bcb.pix2565qr.example.com/pix/'.$transactionId.'5204000053039865405'.number_format((float) $payment->amount, 2, '.', '').'5802BR5913RR_VEICULOS6009SAO_PAULO62070503***6304'.Str::upper(Str::random(4));
        }

        if ($pixQrUrl === null) {
            $pixQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($pixKey);
        }

        $payment->update([
            'gateway_transaction_id' => $transactionId,
            'pix_qr_code' => $pixKey,
            'pix_qr_code_url' => $pixQrUrl,
        ]);

        return $payment->fresh();
    }

    /**
     * Confirmar um pagamento e marcar os bilhetes como pagos.
     * Se a reserva expirou e os números foram liberados, tenta reemitir as cotas do pacote.
     */
    public function confirmPayment(Payment $payment)
    {
        $shouldNotify = $payment->status !== 'approved';

        $payment = DB::transaction(function () use ($payment) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === 'approved') {
                return $locked->fresh(['user', 'tickets.raffle', 'package']);
            }

            if ($locked->status === 'expired') {
                $this->reissueTicketsForExpiredPayment($locked);
            }

            $locked->update([
                'status' => 'approved',
            ]);

            Ticket::where('payment_id', $locked->id)->update([
                'status' => 'paid',
            ]);

            return $locked->fresh(['user', 'tickets.raffle', 'package']);
        });

        if ($shouldNotify && $payment->user?->email) {
            Mail::to($payment->user->email)->send(new PurchaseReceiptMail($payment));
        }

        return $payment;
    }

    /**
     * Recria cotas quando o PIX chega depois da expiração da reserva.
     */
    private function reissueTicketsForExpiredPayment(Payment $payment): void
    {
        if ($payment->tickets()->exists()) {
            return;
        }

        $package = $payment->package()->with('raffle')->first();
        if (! $package || ! $package->raffle) {
            Log::error('Pagamento expirado sem pacote para reemitir cotas', [
                'payment_id' => $payment->id,
            ]);

            throw new \RuntimeException('Pagamento expirado sem cotas disponíveis para reemitir.');
        }

        $reserveAction = app(ReserveTicketsAction::class);
        $numbers = $reserveAction->pickRandomAvailableNumbers($package->raffle, $package->numbers_qty);
        $tickets = $reserveAction->execute($payment->user, $package->raffle, $numbers);

        foreach ($tickets as $ticket) {
            $ticket->update(['payment_id' => $payment->id]);
        }

        Log::warning('Cotas reemitidas após pagamento tardio de reserva expirada', [
            'payment_id' => $payment->id,
            'package_id' => $package->id,
            'numbers' => $numbers,
        ]);
    }

    /**
     * Integração Direta Itaú API Pix v2
     */
    private function createItauPix($amount)
    {
        $clientId = Setting::get('itau_client_id');
        $clientSecret = Setting::get('itau_client_secret');
        $certPath = Setting::get('itau_cert_path');
        $keyPath = Setting::get('itau_key_path');
        $pixKey = Setting::get('itau_pix_key');

        if (! $clientId || ! $clientSecret || ! $certPath || ! $keyPath) {
            Log::warning('Configurações do Itaú Pix incompletas. Utilizando simulação.');

            return null;
        }

        try {
            // 1. Obter Token OAuth
            $tokenUrl = 'https://sts.itau.com.br/oauth/token';
            $postFields = http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            $authResponse = $this->executeCurlWithCert($tokenUrl, $postFields, [], $certPath, $keyPath);
            $authData = json_decode($authResponse, true);

            if (! isset($authData['access_token'])) {
                Log::error('Falha ao autenticar na API do Itaú: '.$authResponse);

                return null;
            }

            $accessToken = $authData['access_token'];

            // 2. Criar Cobrança Pix Cob v2
            $cobUrl = 'https://api.itau.com.br/pix_recebimentos/v2/cob';
            $body = json_encode([
                'calendario' => ['expiracao' => 3600],
                'valor' => ['original' => number_format($amount, 2, '.', '')],
                'chave' => $pixKey,
                'solicitacaoPagador' => 'Compra de cotas no Acao RR',
            ]);

            $headers = [
                'Authorization: Bearer '.$accessToken,
                'Content-Type: application/json',
            ];

            $cobResponse = $this->executeCurlWithCert($cobUrl, $body, $headers, $certPath, $keyPath);
            $cobData = json_decode($cobResponse, true);

            if (isset($cobData['txid']) && isset($cobData['pixCopiaECola'])) {
                return $cobData;
            }

            Log::error('Erro ao gerar cobrança Pix no Itaú: '.$cobResponse);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao conectar no Itaú Pix: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Integração Direta Santander API Pix v2
     */
    private function createSantanderPix($amount)
    {
        $clientId = Setting::get('santander_client_id');
        $clientSecret = Setting::get('santander_client_secret');
        $certPath = Setting::get('santander_cert_path');
        $keyPath = Setting::get('santander_key_path');
        $pixKey = Setting::get('santander_pix_key');

        if (! $clientId || ! $clientSecret || ! $certPath || ! $keyPath) {
            Log::warning('Configurações do Santander Pix incompletas. Utilizando simulação.');

            return null;
        }

        try {
            // 1. Obter Token OAuth
            $tokenUrl = 'https://sts.santander.com.br/oauth/token';
            $postFields = http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            $authResponse = $this->executeCurlWithCert($tokenUrl, $postFields, [], $certPath, $keyPath);
            $authData = json_decode($authResponse, true);

            if (! isset($authData['access_token'])) {
                Log::error('Falha ao autenticar na API do Santander: '.$authResponse);

                return null;
            }

            $accessToken = $authData['access_token'];

            // 2. Criar Cobrança Pix Cob v2 (txid aleatório)
            $txid = Str::lower(Str::random(32));
            $cobUrl = 'https://api.santander.com.br/pix_recebimentos/v2/cob/'.$txid;

            $body = json_encode([
                'calendario' => ['expiracao' => 3600],
                'valor' => ['original' => number_format($amount, 2, '.', '')],
                'chave' => $pixKey,
                'solicitacaoPagador' => 'Compra de cotas no Acao RR',
            ]);

            $headers = [
                'Authorization: Bearer '.$accessToken,
                'Content-Type: application/json',
            ];

            // Santander exige PUT para TXID pré-determinado ou POST dependendo do fluxo
            $cobResponse = $this->executeCurlWithCert($cobUrl, $body, $headers, $certPath, $keyPath, 'PUT');
            $cobData = json_decode($cobResponse, true);

            if (isset($cobData['txid']) && isset($cobData['pixCopiaECola'])) {
                return $cobData;
            }

            Log::error('Erro ao gerar cobrança Pix no Santander: '.$cobResponse);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao conectar no Santander Pix: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Função auxiliar para executar chamadas cURL com certificados mTLS
     */
    private function executeCurlWithCert(string $url, string $body, array $headers, string $certPath, string $keyPath, string $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        // Certificados mTLS exigidos pelos bancos tradicionais
        curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);

        // Evitar falha de verificação SSL em servidores de teste
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if (! empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('Erro na chamada cURL mTLS: '.$error);
        }

        curl_close($ch);

        return $response;
    }
}
