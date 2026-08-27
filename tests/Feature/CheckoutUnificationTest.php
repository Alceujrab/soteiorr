<?php

namespace Tests\Feature;

use App\Mail\PaymentReminderMail;
use App\Mail\PendingPaymentMail;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutUnificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_start_and_continue_create_payment_and_send_pending_email(): void
    {
        Mail::fake();
        Setting::set('contact_whatsapp', '66999999999');

        [$admin, $cliente, $raffle] = $this->createReadyClienteAndRaffle();
        $package = $raffle->packages()->where('name', 'Popular')->firstOrFail();

        $this->actingAs($cliente)
            ->post(route('checkout.start', $raffle), ['package_id' => $package->id])
            ->assertRedirect(route('checkout.continue'));

        $this->actingAs($cliente)
            ->get(route('checkout.continue'))
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'user_id' => $cliente->id,
            'raffle_package_id' => $package->id,
            'status' => 'pending',
            'amount' => 21.90,
        ]);

        $this->assertSame(50, Ticket::where('user_id', $cliente->id)->where('status', 'reserved')->count());

        Mail::assertSent(PendingPaymentMail::class, function (PendingPaymentMail $mail) use ($cliente) {
            return $mail->payment->user_id === $cliente->id;
        });
    }

    public function test_legacy_buy_route_uses_unified_checkout(): void
    {
        Mail::fake();

        [$admin, $cliente, $raffle] = $this->createReadyClienteAndRaffle();
        $package = $raffle->packages()->where('name', 'Popular')->firstOrFail();

        $this->actingAs($cliente)
            ->post(route('raffles.buy', $raffle), ['package_id' => $package->id])
            ->assertRedirect(route('checkout.continue'));

        $this->actingAs($cliente)
            ->followingRedirects()
            ->get(route('checkout.continue'))
            ->assertOk()
            ->assertSee('Pagamento Seguro', false);
    }

    public function test_reminder_command_sends_once_after_half_ttl(): void
    {
        Mail::fake();

        [$admin, $cliente, $raffle] = $this->createReadyClienteAndRaffle();
        $package = $raffle->packages()->where('name', 'Popular')->firstOrFail();

        $payment = Payment::create([
            'user_id' => $cliente->id,
            'raffle_package_id' => $package->id,
            'amount' => $package->price,
            'gateway' => 'asaas',
            'gateway_transaction_id' => 'tx_reminder_1',
            'status' => 'pending',
            'payment_method' => 'pix',
            'pix_qr_code' => 'pix',
            'pix_qr_code_url' => 'https://example.com/qr.png',
        ]);
        $payment->forceFill([
            'created_at' => now()->subMinutes(16),
            'updated_at' => now()->subMinutes(16),
        ])->save();

        Ticket::create([
            'raffle_id' => $raffle->id,
            'user_id' => $cliente->id,
            'payment_id' => $payment->id,
            'number' => 7,
            'status' => 'reserved',
        ]);

        Artisan::call('payments:send-reminders');
        Artisan::call('payments:send-reminders');

        Mail::assertSent(PaymentReminderMail::class, 1);
        $this->assertNotNull($payment->fresh()->reminder_sent_at);
    }

    /**
     * @return array{0: User, 1: User, 2: Raffle}
     */
    private function createReadyClienteAndRaffle(): array
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $cliente = User::factory()->create([
            'role' => 'cliente',
            'email' => 'checkout-uni@example.com',
            'cpf' => '390.533.447-05',
            'birth_date' => now()->subYears(25)->toDateString(),
            'whatsapp' => '66999999999',
            'zip_code' => '78680000',
            'address_street' => 'Rua das Flores',
            'address_number' => '100',
            'address_neighborhood' => 'Centro',
            'address_city' => 'Água Boa',
            'address_state' => 'MT',
            'accepted_regulation_at' => now(),
        ]);

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação Checkout Unificado',
            'description' => 'Teste',
            'price' => 9.90,
            'total_numbers' => 200000,
            'status' => 'active',
            'prize_name' => 'Carro',
            'draw_date' => now()->addDays(20),
        ]);
        $raffle->seedDefaultPackages();

        return [$admin, $cliente, $raffle->fresh('packages')];
    }
}
