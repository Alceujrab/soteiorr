<?php

namespace App\Http\Controllers;

use App\Actions\LogActivityAction;
use App\Models\Payment;
use App\Models\Raffle;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Obter lista de todas as Ações Promocionais ativas via API.
     */
    public function getRaffles()
    {
        $raffles = Raffle::where('status', 'active')
            ->select('id', 'title', 'description', 'price', 'total_numbers', 'prize_name', 'draw_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $raffles,
        ]);
    }

    /**
     * Obter detalhes de uma Ação Promocional via API.
     */
    public function getRaffleDetails(Raffle $raffle)
    {
        $raffle->load(['tickets' => function ($q) {
            $q->select('id', 'raffle_id', 'number', 'status');
        }]);

        return response()->json([
            'success' => true,
            'data' => $raffle,
        ]);
    }

    /**
     * Webhook de simulação do Asaas para confirmação automática.
     */
    public function webhookAsaas(Request $request, PaymentService $paymentService, LogActivityAction $logActivity)
    {
        // No Asaas real, o payload contém o ID do pagamento e o evento 'PAYMENT_RECEIVED'
        // Simulação do payload do Asaas: {"event": "PAYMENT_RECEIVED", "payment": {"id": "pay_xxxxx", "externalReference": "tx_xxxx"}}
        $event = $request->input('event');
        $transactionId = $request->input('payment.externalReference');

        if ($event === 'PAYMENT_RECEIVED' && $transactionId) {
            $payment = Payment::where('gateway_transaction_id', $transactionId)->first();
            if ($payment) {
                $paymentService->confirmPayment($payment);
                $logActivity->execute("Webhook Asaas: Pagamento aprovado ID {$payment->id}", json_encode($request->all()));

                return response()->json(['success' => true, 'message' => 'Payment approved']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid payload or payment not found'], 400);
    }

    /**
     * Webhook de simulação do Mercado Pago para confirmação automática.
     */
    public function webhookMercadoPago(Request $request, PaymentService $paymentService, LogActivityAction $logActivity)
    {
        // Simulação do payload do MP: {"action": "payment.created", "data": {"id": "mp_id"}, "external_reference": "tx_xxxx"}
        $action = $request->input('action');
        $transactionId = $request->input('external_reference');

        if ($action === 'payment.created' && $transactionId) {
            $payment = Payment::where('gateway_transaction_id', $transactionId)->first();
            if ($payment) {
                $paymentService->confirmPayment($payment);
                $logActivity->execute("Webhook Mercado Pago: Pagamento aprovado ID {$payment->id}", json_encode($request->all()));

                return response()->json(['success' => true, 'message' => 'Payment approved']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid payload or payment not found'], 400);
    }
}
