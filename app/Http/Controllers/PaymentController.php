<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Exibir os detalhes do pagamento e o QR Code.
     */
    public function show(Payment $payment)
    {
        // Carregar bilhetes vinculados
        $payment->load('tickets.raffle');
        return view('payments.show', compact('payment'));
    }

    /**
     * Endpoint API para verificar o status do pagamento (usado para auto-update via polling).
     */
    public function checkStatus(Payment $payment)
    {
        return response()->json([
            'status' => $payment->status,
        ]);
    }

    /**
     * Simular a aprovação do pagamento pelo gateway (PIX confirmado).
     */
    public function confirm(Payment $payment, PaymentService $paymentService)
    {
        $paymentService->confirmPayment($payment);

        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Pagamento confirmado com sucesso! Seus bilhetes já são seus.');
    }
}
