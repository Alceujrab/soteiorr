<?php

namespace App\Actions;

use App\Models\Payment;
use App\Models\Raffle;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpsellPaymentAction
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Adiciona cotas aleatórias a um pagamento pendente e atualiza o valor/PIX.
     *
     * @return array{payment: Payment, added: int}
     */
    public function execute(Payment $payment, int $extraNumbers, ReserveTicketsAction $reserveAction): array
    {
        if ($payment->status !== 'pending') {
            throw ValidationException::withMessages([
                'payment' => 'Só é possível adicionar cotas em pagamentos pendentes.',
            ]);
        }

        if ($extraNumbers < 1 || $extraNumbers > 500) {
            throw ValidationException::withMessages([
                'extra_numbers' => 'Quantidade de cotas inválida.',
            ]);
        }

        $payment->loadMissing(['tickets.raffle', 'package', 'user']);
        $raffle = $payment->tickets->first()?->raffle
            ?? $payment->package?->raffle
            ?? null;

        if (! $raffle instanceof Raffle) {
            throw ValidationException::withMessages([
                'payment' => 'Não foi possível identificar a ação deste pagamento.',
            ]);
        }

        $unitPrice = $payment->package
            ? $payment->package->effectiveCostPerNumber()
            : (float) $raffle->price;

        if ($unitPrice <= 0) {
            $unitPrice = (float) $raffle->price;
        }

        $extraAmount = round($unitPrice * $extraNumbers, 2);

        return DB::transaction(function () use ($payment, $extraNumbers, $extraAmount, $raffle, $reserveAction) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment' => 'Só é possível adicionar cotas em pagamentos pendentes.',
                ]);
            }

            $numbers = $reserveAction->pickRandomAvailableNumbers($raffle, $extraNumbers);
            $tickets = $reserveAction->execute($locked->user, $raffle, $numbers);

            foreach ($tickets as $ticket) {
                $ticket->update(['payment_id' => $locked->id]);
            }

            $newAmount = round((float) $locked->amount + $extraAmount, 2);
            $locked->update(['amount' => $newAmount]);

            $this->paymentService->refreshPixCharge($locked->fresh());

            return [
                'payment' => $locked->fresh(['tickets.raffle', 'package', 'user']),
                'added' => $extraNumbers,
            ];
        });
    }

    /**
     * Sugestões de upsell a partir do pacote atual.
     *
     * @return list<array{qty: int, label: string, price: float}>
     */
    public function suggestions(Payment $payment): array
    {
        $payment->loadMissing(['package', 'tickets.raffle']);
        $package = $payment->package;
        $unit = $package?->effectiveCostPerNumber() ?: (float) ($payment->tickets->first()?->raffle?->price ?: 0);

        if ($unit <= 0) {
            return [];
        }

        $options = [20, 50, 100];
        $suggestions = [];

        foreach ($options as $qty) {
            $suggestions[] = [
                'qty' => $qty,
                'label' => "+{$qty} números",
                'price' => round($unit * $qty, 2),
            ];
        }

        return $suggestions;
    }
}
