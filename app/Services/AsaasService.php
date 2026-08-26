<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AsaasService
{
    public function isConfigured(): bool
    {
        return filled(Setting::get('gateway_asaas_key'));
    }

    public function ensureCustomer(User $user): string
    {
        if (filled($user->asaas_customer_id)) {
            return (string) $user->asaas_customer_id;
        }

        $payload = array_filter([
            'name' => $user->name,
            'cpfCnpj' => preg_replace('/\D+/', '', (string) $user->cpf),
            'email' => $user->email,
            'mobilePhone' => preg_replace('/\D+/', '', (string) ($user->whatsapp ?: $user->phone)),
            'phone' => preg_replace('/\D+/', '', (string) ($user->phone_extra ?: $user->whatsapp ?: $user->phone)),
            'postalCode' => preg_replace('/\D+/', '', (string) $user->zip_code),
            'address' => $user->address_street,
            'addressNumber' => $user->address_number,
            'complement' => $user->address_complement,
            'province' => $user->address_neighborhood,
            'city' => $user->address_city,
            'state' => $user->address_state,
            'birthDate' => optional($user->birth_date)?->format('Y-m-d'),
            'externalReference' => (string) $user->id,
            'notificationDisabled' => false,
        ], fn ($value) => $value !== null && $value !== '');

        $response = $this->client()->post('/v3/customers', $payload);

        if (! $response->successful()) {
            Log::error('Asaas create customer failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new RuntimeException('Não foi possível cadastrar o cliente no Asaas. Verifique os dados e a API Key.');
        }

        $customerId = (string) $response->json('id');
        $user->forceFill(['asaas_customer_id' => $customerId])->save();

        return $customerId;
    }

    /**
     * @return array{id: string, payload: string, encodedImage: ?string}
     */
    public function createPixCharge(User $user, float $amount, string $externalReference, string $description): array
    {
        $customerId = $this->ensureCustomer($user);

        $response = $this->client()->post('/v3/payments', [
            'customer' => $customerId,
            'billingType' => 'PIX',
            'value' => round($amount, 2),
            'dueDate' => now()->toDateString(),
            'description' => $description,
            'externalReference' => $externalReference,
        ]);

        if (! $response->successful()) {
            Log::error('Asaas create payment failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new RuntimeException('Não foi possível gerar a cobrança PIX no Asaas.');
        }

        $paymentId = (string) $response->json('id');
        $qr = $this->client()->get('/v3/payments/'.$paymentId.'/pixQrCode');

        if (! $qr->successful()) {
            Log::error('Asaas pixQrCode failed', [
                'status' => $qr->status(),
                'body' => $qr->json(),
            ]);
            throw new RuntimeException('Cobrança criada, mas o QR Code PIX não pôde ser obtido.');
        }

        return [
            'id' => $paymentId,
            'payload' => (string) $qr->json('payload'),
            'encodedImage' => $qr->json('encodedImage'),
        ];
    }

    private function client(): PendingRequest
    {
        $apiKey = (string) Setting::get('gateway_asaas_key');
        if ($apiKey === '') {
            throw new RuntimeException('API Key do Asaas não configurada.');
        }

        $sandbox = Setting::get('asaas_sandbox', '1') === '1';
        $base = $sandbox ? 'https://api-sandbox.asaas.com' : 'https://api.asaas.com';

        return Http::baseUrl($base)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'access_token' => $apiKey,
                'User-Agent' => 'RRSorteio/1.0',
            ])
            ->timeout(30);
    }
}
