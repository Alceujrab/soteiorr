<?php

namespace App\Actions;

use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReserveTicketsAction
{
    /**
     * Reservar números para um usuário em uma Ação Promocional específica.
     *
     * @return Collection<int, Ticket>
     *
     * @throws Exception
     */
    public function execute(User $user, Raffle $raffle, array $numbers): Collection
    {
        if (count($numbers) !== count(array_unique($numbers))) {
            throw new Exception('Existem números duplicados na sua seleção.');
        }

        return DB::transaction(function () use ($user, $raffle, $numbers) {
            $existingTickets = Ticket::where('raffle_id', $raffle->id)
                ->whereIn('number', $numbers)
                ->lockForUpdate()
                ->pluck('number')
                ->toArray();

            if (! empty($existingTickets)) {
                $duplicates = implode(', ', $existingTickets);
                throw new Exception("Os seguintes números já estão ocupados: {$duplicates}");
            }

            foreach ($numbers as $number) {
                if ($number < 1 || $number > $raffle->total_numbers) {
                    throw new Exception("O número {$number} é inválido para esta Ação Promocional.");
                }
            }

            $tickets = new Collection;

            foreach ($numbers as $number) {
                $ticket = Ticket::create([
                    'raffle_id' => $raffle->id,
                    'user_id' => $user->id,
                    'number' => $number,
                    'status' => 'reserved',
                ]);
                $tickets->push($ticket);
            }

            return $tickets;
        });
    }

    /**
     * Seleciona números disponíveis de forma aleatória, sem percorrer 1..N.
     *
     * @return list<int>
     *
     * @throws Exception
     */
    public function pickRandomAvailableNumbers(Raffle $raffle, int $quantity): array
    {
        if ($quantity < 1) {
            throw new Exception('A quantidade de números deve ser maior que zero.');
        }

        $takenCount = Ticket::where('raffle_id', $raffle->id)->count();
        $availableCount = $raffle->total_numbers - $takenCount;

        if ($availableCount < $quantity) {
            throw new Exception('Não há números disponíveis suficientes para o pacote selecionado.');
        }

        $takenSet = array_flip(
            Ticket::where('raffle_id', $raffle->id)->pluck('number')->all()
        );

        $picked = [];
        $attempts = 0;
        $maxAttempts = max($quantity * 80, 2000);

        while (count($picked) < $quantity && $attempts < $maxAttempts) {
            $attempts++;
            $candidate = random_int(1, $raffle->total_numbers);

            if (isset($takenSet[$candidate]) || isset($picked[$candidate])) {
                continue;
            }

            $picked[$candidate] = $candidate;
        }

        if (count($picked) < $quantity) {
            // Fallback para ações quase esgotadas: monta lista residual.
            $remaining = [];
            for ($i = 1; $i <= $raffle->total_numbers; $i++) {
                if (! isset($takenSet[$i]) && ! isset($picked[$i])) {
                    $remaining[] = $i;
                }
            }

            shuffle($remaining);
            foreach ($remaining as $number) {
                $picked[$number] = $number;
                if (count($picked) >= $quantity) {
                    break;
                }
            }
        }

        if (count($picked) < $quantity) {
            throw new Exception('Não foi possível selecionar números disponíveis suficientes.');
        }

        return array_values($picked);
    }
}
