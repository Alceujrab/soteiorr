<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Criar um novo pagamento (PIX ou Boleto) associado aos bilhetes reservados.
     */
    public function createPayment(User $user, $tickets, string $gateway = 'asaas', string $paymentMethod = 'pix')
    {
        return DB::transaction(function () use ($user, $tickets, $gateway, $paymentMethod) {
            $totalAmount = 0;
            foreach ($tickets as $ticket) {
                $totalAmount += $ticket->raffle->price;
            }

            // Gerar mock de dados do gateway
            $transactionId = 'tx_' . Str::random(12);
            $pixKey = "00020101021226870014br.gov.bcb.pix2565qr.example.com/pix/" . $transactionId . "5204000053039865405" . number_format($totalAmount, 2, '.', '') . "5802BR5913RR_VEICULOS6009SAO_PAULO62070503***6304" . Str::upper(Str::random(4));

            $payment = Payment::create([
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'gateway' => $gateway,
                'gateway_transaction_id' => $transactionId,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'pix_qr_code' => $pixKey,
                'pix_qr_code_url' => "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($pixKey),
            ]);

            // Atualizar os bilhetes com o ID do pagamento
            foreach ($tickets as $ticket) {
                $ticket->update([
                    'payment_id' => $payment->id,
                ]);
            }

            return $payment;
        });
    }

    /**
     * Confirmar um pagamento e marcar os bilhetes como pagos.
     */
    public function confirmPayment(Payment $payment)
    {
        return DB::transaction(function () use ($payment) {
            if ($payment->status === 'approved') {
                return $payment;
            }

            $payment->update([
                'status' => 'approved',
            ]);

            // Atualizar os bilhetes para status "paid"
            Ticket::where('payment_id', $payment->id)->update([
                'status' => 'paid',
            ]);

            return $payment;
        });
    }
}
