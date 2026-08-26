<?php

namespace App\Actions;

use App\Models\Payment;
use App\Models\Raffle;
use App\Models\RaffleDeletionChallenge;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class ConfirmRaffleDeletionAction
{
    public const MAX_ATTEMPTS = 5;

    public function execute(Raffle $raffle, User $requester, string $code, LogActivityAction $logActivity): void
    {
        $challenge = RaffleDeletionChallenge::query()
            ->where('raffle_id', $raffle->id)
            ->where('requested_by', $requester->id)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $challenge) {
            throw new RuntimeException('Nenhuma solicitação de exclusão pendente. Peça um novo código.');
        }

        if ($challenge->isExpired()) {
            $challenge->delete();
            throw new RuntimeException('O código expirou. Solicite um novo código de exclusão.');
        }

        if ($challenge->hasExceededAttempts(self::MAX_ATTEMPTS)) {
            $challenge->delete();
            throw new RuntimeException('Limite de tentativas excedido. Solicite um novo código.');
        }

        if (! Hash::check($code, $challenge->code_hash)) {
            $challenge->increment('attempts');
            $fresh = $challenge->fresh();
            $remaining = self::MAX_ATTEMPTS - $fresh->attempts;

            if ($remaining <= 0) {
                $fresh->delete();
                throw new RuntimeException('Código inválido. Limite de tentativas excedido. Solicite um novo código.');
            }

            throw new RuntimeException("Código inválido. Você ainda tem {$remaining} tentativa(s).");
        }

        $title = $raffle->title;
        $id = $raffle->id;

        DB::transaction(function () use ($raffle, $challenge) {
            $paymentIds = Ticket::where('raffle_id', $raffle->id)
                ->whereNotNull('payment_id')
                ->pluck('payment_id')
                ->unique()
                ->filter()
                ->values();

            Ticket::where('raffle_id', $raffle->id)->delete();
            $raffle->packages()->delete();
            $raffle->draw()?->delete();

            $challenge->update(['consumed_at' => now()]);
            $raffle->delete();

            if ($paymentIds->isNotEmpty()) {
                Payment::whereIn('id', $paymentIds)
                    ->whereDoesntHave('tickets')
                    ->delete();
            }
        });

        $logActivity->execute("Excluiu a Ação Promocional ID: {$id} - {$title} (confirmado por e-mail)");
    }
}
