<?php

namespace App\Console\Commands;

use App\Actions\SendPendingPaymentRemindersAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('payments:send-reminders')]
#[Description('Envia lembrete único de PIX pendente')]
class SendPendingPaymentRemindersCommand extends Command
{
    public function handle(SendPendingPaymentRemindersAction $action): int
    {
        $result = $action->execute();

        $this->info(sprintf('Lembretes enviados: %d', $result['reminders_sent']));

        return self::SUCCESS;
    }
}
