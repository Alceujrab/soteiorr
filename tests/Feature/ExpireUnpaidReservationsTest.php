<?php

namespace Tests\Feature;

use App\Actions\ExpireUnpaidReservationsAction;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExpireUnpaidReservationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_expires_pending_reservations_and_releases_numbers(): void
    {
        [$admin, $owner, $raffle, $payment] = $this->makePendingReservation();

        $payment->forceFill(['created_at' => now()->subMinutes(31)])->save();

        Artisan::call('reservations:expire');

        $this->assertSame('expired', $payment->fresh()->status);
        $this->assertDatabaseMissing('tickets', [
            'payment_id' => $payment->id,
        ]);
        $this->assertSame(0, Ticket::where('raffle_id', $raffle->id)->where('status', 'reserved')->count());
    }

    public function test_recent_pending_reservations_are_kept(): void
    {
        [, , , $payment] = $this->makePendingReservation();

        $result = app(ExpireUnpaidReservationsAction::class)->execute();

        $this->assertSame(0, $result['expired_payments']);
        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertDatabaseHas('tickets', [
            'payment_id' => $payment->id,
            'status' => 'reserved',
        ]);
    }

    public function test_payment_page_expires_stale_reservation_on_view(): void
    {
        [, $owner, , $payment] = $this->makePendingReservation();
        $payment->forceFill(['created_at' => now()->subMinutes(45)])->save();

        $this->actingAs($owner)
            ->get(route('payments.show', $payment))
            ->assertOk()
            ->assertSee('Reserva expirada', false);

        $this->assertSame('expired', $payment->fresh()->status);
    }

    public function test_late_webhook_reissues_tickets_for_expired_payment(): void
    {
        [, $owner, $raffle, $payment] = $this->makePendingReservation();
        $payment->forceFill(['created_at' => now()->subMinutes(40)])->save();

        app(ExpireUnpaidReservationsAction::class)->expirePayment($payment);
        $this->assertSame('expired', $payment->fresh()->status);
        $this->assertSame(0, Ticket::where('payment_id', $payment->id)->count());

        app(PaymentService::class)->confirmPayment($payment->fresh());

        $payment->refresh();
        $this->assertSame('approved', $payment->status);
        $this->assertSame(50, Ticket::where('payment_id', $payment->id)->where('status', 'paid')->count());
        $this->assertSame(50, Ticket::where('raffle_id', $raffle->id)->where('user_id', $owner->id)->where('status', 'paid')->count());
    }

    /**
     * @return array{0: User, 1: User, 2: Raffle, 3: Payment}
     */
    private function makePendingReservation(): array
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $owner = User::factory()->create(['role' => 'cliente']);

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação Expiração',
            'description' => 'Teste',
            'price' => 9.90,
            'total_numbers' => 1000,
            'status' => 'active',
            'prize_name' => 'Prêmio',
            'draw_date' => now()->addDays(10),
        ]);
        $raffle->seedDefaultPackages();
        $package = $raffle->packages()->where('name', 'Popular')->firstOrFail();

        $payment = Payment::create([
            'user_id' => $owner->id,
            'raffle_package_id' => $package->id,
            'amount' => $package->price,
            'gateway' => 'asaas',
            'gateway_transaction_id' => 'tx_expire_1',
            'status' => 'pending',
            'payment_method' => 'pix',
            'pix_qr_code' => 'pix-code',
            'pix_qr_code_url' => 'https://example.com/qr.png',
        ]);

        for ($i = 1; $i <= 50; $i++) {
            Ticket::create([
                'raffle_id' => $raffle->id,
                'user_id' => $owner->id,
                'payment_id' => $payment->id,
                'number' => $i,
                'status' => 'reserved',
            ]);
        }

        return [$admin, $owner, $raffle, $payment];
    }
}
