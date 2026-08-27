<?php

namespace App\Http\Controllers;

use App\Actions\LogActivityAction;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\Setting;
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
     * Webhook do Asaas para confirmação automática.
     */
    public function webhookAsaas(Request $request, PaymentService $paymentService, LogActivityAction $logActivity)
    {
        $this->assertWebhookToken($request, Setting::get('asaas_webhook_token', ''));

        $event = (string) $request->input('event');
        $asaasPaymentId = $request->input('payment.id');
        $externalReference = $request->input('payment.externalReference');

        $confirmedEvents = ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED'];

        if (! in_array($event, $confirmedEvents, true)) {
            return response()->json(['success' => true, 'message' => 'Event ignored']);
        }

        $payment = null;
        if (filled($asaasPaymentId)) {
            $payment = Payment::where('gateway_transaction_id', $asaasPaymentId)->first();
        }
        if (! $payment && filled($externalReference)) {
            $payment = Payment::where('gateway_transaction_id', $externalReference)->first();
        }

        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $paymentService->confirmPayment($payment);
        $logActivity->execute("Webhook Asaas: Pagamento aprovado ID {$payment->id}", json_encode($request->all()));

        return response()->json(['success' => true, 'message' => 'Payment approved']);
    }

    /**
     * Webhook do Mercado Pago para confirmação automática.
     */
    public function webhookMercadoPago(Request $request, PaymentService $paymentService, LogActivityAction $logActivity)
    {
        $this->assertWebhookToken($request, Setting::get('mercadopago_webhook_token', ''));

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

    private function assertWebhookToken(Request $request, ?string $expectedToken): void
    {
        $expectedToken = trim((string) $expectedToken);
        $provided = (string) (
            $request->header('asaas-access-token')
            ?? $request->header('X-Webhook-Token')
            ?? $request->bearerToken()
            ?? ''
        );

        if (app()->isProduction()) {
            if ($expectedToken === '' || ! hash_equals($expectedToken, $provided)) {
                abort(401, 'Webhook não autorizado.');
            }

            return;
        }

        if ($expectedToken !== '' && ! hash_equals($expectedToken, $provided)) {
            abort(401, 'Webhook não autorizado.');
        }
    }
}
