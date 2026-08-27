<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Raffle;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_cliente_cannot_access_admin_dashboard(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($cliente)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_simulate_login_is_available_in_testing_environment(): void
    {
        User::factory()->create(['role' => 'cliente']);

        $this->get(route('simulate-login', 'cliente'))
            ->assertRedirect();

        $this->assertAuthenticated();
    }

    public function test_simulate_login_is_blocked_outside_local_and_testing(): void
    {
        User::factory()->create(['role' => 'cliente']);

        $this->app['env'] = 'production';

        $this->get(route('simulate-login', 'cliente'))
            ->assertNotFound();
    }

    public function test_payment_show_requires_owner_or_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $owner = User::factory()->create(['role' => 'cliente']);
        $other = User::factory()->create(['role' => 'cliente']);
        $payment = $this->makePaymentWithTicket($admin, $owner);

        $this->actingAs($other)
            ->get(route('payments.show', $payment))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('payments.show', $payment))
            ->assertOk();
    }

    public function test_payment_confirm_works_for_owner_in_testing(): void
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $owner = User::factory()->create(['role' => 'cliente']);
        $payment = $this->makePaymentWithTicket($admin, $owner);

        $this->actingAs($owner)
            ->get(route('payments.confirm', $payment))
            ->assertRedirect(route('payments.show', $payment));

        $this->assertSame('approved', $payment->fresh()->status);
    }

    public function test_payment_confirm_is_blocked_in_production(): void
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $owner = User::factory()->create(['role' => 'cliente']);
        $payment = $this->makePaymentWithTicket($admin, $owner);

        $this->app['env'] = 'production';

        $this->actingAs($owner)
            ->get(route('payments.confirm', $payment))
            ->assertNotFound();

        $this->assertSame('pending', $payment->fresh()->status);
    }

    public function test_asaas_webhook_rejects_invalid_token_when_configured(): void
    {
        Setting::set('asaas_webhook_token', 'segredo-webhook');
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $owner = User::factory()->create(['role' => 'cliente']);
        $payment = $this->makePaymentWithTicket($admin, $owner, 'pay_asaas_1');

        $this->postJson(route('api.webhook.asaas'), [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_asaas_1'],
        ])->assertUnauthorized();

        $this->withHeader('asaas-access-token', 'segredo-webhook')
            ->postJson(route('api.webhook.asaas'), [
                'event' => 'PAYMENT_CONFIRMED',
                'payment' => ['id' => 'pay_asaas_1'],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame('approved', $payment->fresh()->status);
    }

    public function test_asaas_webhook_requires_token_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->postJson(route('api.webhook.asaas'), [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_missing'],
        ])->assertUnauthorized();
    }

    public function test_guest_cannot_open_support_page(): void
    {
        $this->get(route('support.index'))
            ->assertRedirect(route('login'));
    }

    private function makePaymentWithTicket(User $admin, User $owner, string $transactionId = 'tx_test'): Payment
    {
        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação Teste',
            'description' => 'Descrição',
            'price' => 10.00,
            'total_numbers' => 1000,
            'status' => 'active',
            'prize_name' => 'Prêmio',
            'draw_date' => now()->addDays(10),
        ]);

        $payment = Payment::create([
            'user_id' => $owner->id,
            'amount' => 10.00,
            'gateway' => 'asaas',
            'gateway_transaction_id' => $transactionId,
            'status' => 'pending',
            'payment_method' => 'pix',
            'pix_qr_code' => 'pix-code',
            'pix_qr_code_url' => 'https://example.com/qr.png',
        ]);

        Ticket::create([
            'raffle_id' => $raffle->id,
            'user_id' => $owner->id,
            'payment_id' => $payment->id,
            'number' => 1,
            'status' => 'reserved',
        ]);

        return $payment;
    }
}
