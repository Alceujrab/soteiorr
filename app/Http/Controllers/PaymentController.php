<?php

namespace App\Http\Controllers;

use App\Actions\ExpireUnpaidReservationsAction;
use App\Actions\LogActivityAction;
use App\Actions\UpsellPaymentAction;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Exibir os detalhes do pagamento e o QR Code.
     */
    public function show(Payment $payment, ExpireUnpaidReservationsAction $expireReservations, UpsellPaymentAction $upsell)
    {
        $this->authorizePaymentAccess($payment);
        $payment = $this->refreshExpiredReservation($payment, $expireReservations);
        $payment->load(['tickets.raffle', 'package.raffle']);

        $upsellSuggestions = $payment->status === 'pending'
            ? $upsell->suggestions($payment)
            : [];

        return view('payments.show', compact('payment', 'upsellSuggestions'));
    }

    /**
     * Endpoint API para verificar o status do pagamento (usado para auto-update via polling).
     */
    public function checkStatus(Payment $payment, ExpireUnpaidReservationsAction $expireReservations)
    {
        $this->authorizePaymentAccess($payment);
        $payment = $this->refreshExpiredReservation($payment, $expireReservations);

        return response()->json([
            'status' => $payment->status,
            'seconds_remaining' => $payment->reservationSecondsRemaining(),
        ]);
    }

    /**
     * Simular a aprovação do pagamento pelo gateway (somente local/testing).
     */
    public function confirm(Payment $payment, PaymentService $paymentService, LogActivityAction $logActivity)
    {
        $this->authorizePaymentAccess($payment);

        $paymentService->confirmPayment($payment);

        $logActivity->execute("Pagamento aprovado. ID Transação: {$payment->gateway_transaction_id}. Valor: R$ {$payment->amount}", json_encode($payment->toArray()));

        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Pagamento confirmado com sucesso! Seus bilhetes já são seus.');
    }

    private function authorizePaymentAccess(Payment $payment): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if (in_array($user->role, ['admin_organizador', 'super_admin'], true)) {
            return;
        }

        if ((int) $payment->user_id !== (int) $user->id) {
            abort(403);
        }
    }

    private function refreshExpiredReservation(Payment $payment, ExpireUnpaidReservationsAction $expireReservations): Payment
    {
        if ($payment->status === 'pending' && $payment->reservationExpiresAt()->isPast()) {
            $expireReservations->expirePayment($payment);

            return $payment->fresh() ?? $payment;
        }

        return $payment;
    }
}
