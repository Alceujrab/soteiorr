<?php

namespace Tests\Feature;

use App\Models\Raffle;
use App\Models\User;
use App\Models\Ticket;
use App\Actions\ReserveTicketsAction;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaffleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_reserve_and_pay_tickets_successfully()
    {
        // 1. Criar dados de teste
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_organizador',
        ]);

        $cliente = User::create([
            'name' => 'Client Test',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'role' => 'cliente',
        ]);

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Carro Teste 1.0',
            'description' => 'Um carro de teste para a suite de testes.',
            'price' => 10.00,
            'total_numbers' => 100,
            'status' => 'active',
            'prize_name' => 'Carro Modelo X',
            'draw_date' => now()->addDays(5),
        ]);

        // 2. Executar a ação de reserva
        $reserveAction = new ReserveTicketsAction();
        $tickets = $reserveAction->execute($cliente, $raffle, [5, 12, 88]);

        $this->assertCount(3, $tickets);
        $this->assertEquals('reserved', $tickets->first()->status);

        // 3. Criar pagamento
        $paymentService = new PaymentService();
        $payment = $paymentService->createPayment($cliente, $tickets);

        $this->assertEquals('pending', $payment->status);
        $this->assertEquals(30.00, $payment->amount);

        // 4. Confirmar pagamento
        $paymentService->confirmPayment($payment);

        $this->assertEquals('approved', $payment->fresh()->status);
        
        // Verificar se os bilhetes mudaram para "paid"
        foreach ($tickets as $ticket) {
            $this->assertEquals('paid', $ticket->fresh()->status);
        }
    }
}
