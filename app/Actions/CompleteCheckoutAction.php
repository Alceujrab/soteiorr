<?php

namespace App\Actions;

use App\Mail\PendingPaymentMail;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\RafflePackage;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CompleteCheckoutAction
{
    public function __construct(
        private PaymentService $paymentService,
        private CaptureAffiliateReferralAction $affiliates,
    ) {}

    /**
     * Reserva números, cria o pagamento PIX e dispara o e-mail pendente.
     *
     * @param  list<int>|null  $chosenNumbers
     */
    public function execute(
        User $user,
        Raffle $raffle,
        RafflePackage $package,
        ReserveTicketsAction $reserveAction,
        ?array $chosenNumbers = null
    ): Payment {
        if ($chosenNumbers !== null && $chosenNumbers !== []) {
            if (! $package->allows_selection) {
                throw ValidationException::withMessages([
                    'numbers' => 'Este pacote não permite escolha manual de números.',
                ]);
            }

            if (count($chosenNumbers) !== (int) $package->numbers_qty) {
                throw ValidationException::withMessages([
                    'numbers' => "Selecione exatamente {$package->numbers_qty} números.",
                ]);
            }

            $numbers = array_values(array_unique(array_map('intval', $chosenNumbers)));

            if (count($numbers) !== (int) $package->numbers_qty) {
                throw ValidationException::withMessages([
                    'numbers' => 'Há números duplicados na seleção.',
                ]);
            }
        } else {
            $numbers = $reserveAction->pickRandomAvailableNumbers($raffle, $package->numbers_qty);
        }

        $tickets = $reserveAction->execute($user, $raffle, $numbers);

        $payment = $this->paymentService->createPayment(
            $user,
            $tickets,
            'asaas',
            'pix',
            (float) $package->price,
            $package->id,
            $this->affiliates->currentAffiliateId($user->id)
        );

        $payment->load(['user', 'tickets.raffle', 'package']);

        if ($payment->user?->email) {
            Mail::to($payment->user->email)->send(new PendingPaymentMail($payment));
        }

        return $payment;
    }
}
