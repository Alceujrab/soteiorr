<?php

namespace App\Actions;

use App\Models\Payment;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireUnpaidReservationsAction
{
    public const DEFAULT_TTL_MINUTES = 30;

    /**
     * Expira pagamentos pendentes e libera os números reservados.
     *
     * @return array{expired_payments: int, released_tickets: int}
     */
    public function execute(?int $ttlMinutes = null): array
    {
        $ttlMinutes = $ttlMinutes ?? $this->ttlMinutes();
        $cutoff = now()->subMinutes($ttlMinutes);

        $expiredPayments = 0;
        $releasedTickets = 0;

        Payment::query()
            ->where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->orderBy('id')
            ->chunkById(100, function ($payments) use (&$expiredPayments, &$releasedTickets): void {
                foreach ($payments as $payment) {
                    $result = $this->expirePayment($payment);
                    $expiredPayments += $result['expired'] ? 1 : 0;
                    $releasedTickets += $result['released_tickets'];
                }
            });

        if ($expiredPayments > 0) {
            Log::info('Reservas não pagas expiradas', [
                'expired_payments' => $expiredPayments,
                'released_tickets' => $releasedTickets,
                'ttl_minutes' => $ttlMinutes,
            ]);
        }

        return [
            'expired_payments' => $expiredPayments,
            'released_tickets' => $releasedTickets,
        ];
    }

    /**
     * @return array{expired: bool, released_tickets: int}
     */
    public function expirePayment(Payment $payment): array
    {
        return DB::transaction(function () use ($payment) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->first();

            if (! $locked || $locked->status !== 'pending') {
                return ['expired' => false, 'released_tickets' => 0];
            }

            $released = Ticket::query()
                ->where('payment_id', $locked->id)
                ->where('status', 'reserved')
                ->delete();

            $locked->update(['status' => 'expired']);

            return [
                'expired' => true,
                'released_tickets' => $released,
            ];
        });
    }

    public function ttlMinutes(): int
    {
        $value = (int) Setting::get('reservation_ttl_minutes', (string) self::DEFAULT_TTL_MINUTES);

        return max(1, $value);
    }
}
