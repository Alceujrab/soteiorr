<?php

namespace App\Console\Commands;

use App\Actions\ExpireUnpaidReservationsAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reservations:expire {--minutes= : TTL em minutos (padrão: configuração/30)}')]
#[Description('Expira reservas PIX não pagas e libera os números')]
class ExpireUnpaidReservationsCommand extends Command
{
    public function handle(ExpireUnpaidReservationsAction $action): int
    {
        $minutes = $this->option('minutes');
        $ttl = filled($minutes) ? (int) $minutes : null;

        $result = $action->execute($ttl);

        $this->info(sprintf(
            'Expirados: %d pagamento(s). Números liberados: %d.',
            $result['expired_payments'],
            $result['released_tickets']
        ));

        return self::SUCCESS;
    }
}
