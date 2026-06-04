<?php

namespace App\Actions;

use App\Models\Raffle;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Exception;

class ReserveTicketsAction
{
    /**
     * Reservar números para um usuário em uma rifa específica.
     *
     * @param User $user
     * @param Raffle $raffle
     * @param array $numbers
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */
    public function execute(User $user, Raffle $raffle, array $numbers)
    {
        // Validar se há números repetidos na requisição
        if (count($numbers) !== count(array_unique($numbers))) {
            throw new Exception("Existem números duplicados na sua seleção.");
        }

        return DB::transaction(function () use ($user, $raffle, $numbers) {
            // Verificar se os números já estão reservados ou pagos
            $existingTickets = Ticket::where('raffle_id', $raffle->id)
                ->whereIn('number', $numbers)
                ->lockForUpdate()
                ->pluck('number')
                ->toArray();

            if (!empty($existingTickets)) {
                $duplicates = implode(', ', $existingTickets);
                throw new Exception("Os seguintes números já estão ocupados: {$duplicates}");
            }

            // Validar limites dos números
            foreach ($numbers as $number) {
                if ($number < 1 || $number > $raffle->total_numbers) {
                    throw new Exception("O número {$number} é inválido para esta rifa.");
                }
            }

            // Criar os bilhetes
            $tickets = collect();
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
}
