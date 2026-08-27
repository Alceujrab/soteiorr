<?php

namespace App\Actions;

use App\Mail\PaymentReminderMail;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPendingPaymentRemindersAction
{
    /**
     * Envia lembrete único quando a reserva já passou da metade do prazo.
     *
     * @return array{reminders_sent: int}
     */
    public function execute(): array
    {
        $ttl = app(ExpireUnpaidReservationsAction::class)->ttlMinutes();
        $halfLifeMinutes = max(1, (int) floor($ttl / 2));
        $cutoff = now()->subMinutes($halfLifeMinutes);

        $sent = 0;

        Payment::query()
            ->where('status', 'pending')
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', $cutoff)
            ->where('created_at', '>', now()->subMinutes($ttl))
            ->with(['user', 'tickets.raffle', 'package.raffle'])
            ->orderBy('id')
            ->chunkById(50, function ($payments) use (&$sent): void {
                foreach ($payments as $payment) {
                    if (! $payment->user?->email) {
                        $payment->forceFill(['reminder_sent_at' => now()])->save();

                        continue;
                    }

                    Mail::to($payment->user->email)->send(new PaymentReminderMail($payment));
                    $payment->forceFill(['reminder_sent_at' => now()])->save();
                    $sent++;
                }
            });

        if ($sent > 0) {
            Log::info('Lembretes de PIX enviados', ['count' => $sent]);
        }

        return ['reminders_sent' => $sent];
    }
}
