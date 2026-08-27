<?php

namespace Tests\Feature;

use App\Actions\CaptureAffiliateReferralAction;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\RafflePackage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GrowthFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_affiliate_ref_is_captured_and_attached_to_payment(): void
    {
        Mail::fake();

        $affiliate = User::factory()->create([
            'role' => 'vendedor',
            'affiliate_code' => 'VENDE001',
        ]);

        [$cliente, $raffle, $package] = $this->readyBuyerAndPopularPackage();

        $this->get('/?ref=VENDE001')->assertOk();
        $this->assertEquals($affiliate->id, session(CaptureAffiliateReferralAction::SESSION_KEY));

        $this->actingAs($cliente)
            ->post(route('checkout.start', $raffle), ['package_id' => $package->id])
            ->assertRedirect(route('checkout.continue'));

        $this->actingAs($cliente)
            ->get(route('checkout.continue'))
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'user_id' => $cliente->id,
            'affiliate_user_id' => $affiliate->id,
            'status' => 'pending',
        ]);
    }

    public function test_premium_package_allows_manual_number_selection(): void
    {
        Mail::fake();

        [$cliente, $raffle] = $this->readyBuyerAndRaffle();
        $premium = $raffle->packages()->where('name', 'Premium')->firstOrFail();
        $this->assertTrue((bool) $premium->allows_selection);

        $numbers = range(1, $premium->numbers_qty);

        $this->actingAs($cliente)
            ->post(route('checkout.start', $raffle), ['package_id' => $premium->id])
            ->assertRedirect(route('checkout.select'));

        $this->actingAs($cliente)
            ->post(route('checkout.select.store'), [
                'mode' => 'manual',
                'numbers' => implode(',', $numbers),
            ])
            ->assertRedirect(route('checkout.continue'));

        $this->actingAs($cliente)
            ->get(route('checkout.continue'))
            ->assertRedirect();

        $payment = Payment::where('user_id', $cliente->id)->latest('id')->first();
        $this->assertNotNull($payment);
        $this->assertSame($premium->numbers_qty, Ticket::where('payment_id', $payment->id)->count());
        $this->assertEqualsCanonicalizing($numbers, Ticket::where('payment_id', $payment->id)->pluck('number')->all());
    }

    public function test_upsell_adds_numbers_and_increases_pending_amount(): void
    {
        Mail::fake();

        [$cliente, $raffle, $package] = $this->readyBuyerAndPopularPackage();

        $this->actingAs($cliente)
            ->post(route('checkout.start', $raffle), ['package_id' => $package->id])
            ->assertRedirect(route('checkout.continue'));
        $this->actingAs($cliente)->get(route('checkout.continue'));

        $payment = Payment::where('user_id', $cliente->id)->latest('id')->firstOrFail();
        $beforeAmount = (float) $payment->amount;
        $beforeTickets = $payment->tickets()->count();

        $this->actingAs($cliente)
            ->post(route('payments.upsell', $payment), ['extra_numbers' => 20])
            ->assertRedirect(route('payments.show', $payment));

        $payment->refresh();
        $this->assertSame($beforeTickets + 20, $payment->tickets()->count());
        $this->assertGreaterThan($beforeAmount, (float) $payment->amount);
        $this->assertSame('pending', $payment->status);
    }

    public function test_admin_affiliates_page_lists_codes(): void
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        User::factory()->create(['role' => 'vendedor', 'affiliate_code' => 'ABC12345', 'name' => 'Vendedor Teste']);

        $this->actingAs($admin)
            ->get(route('admin.affiliates'))
            ->assertOk()
            ->assertSee('ABC12345', false)
            ->assertSee('Vendedor Teste', false);
    }

    /**
     * @return array{0: User, 1: Raffle}
     */
    private function readyBuyerAndRaffle(): array
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $cliente = User::factory()->create([
            'role' => 'cliente',
            'cpf' => '390.533.447-05',
            'birth_date' => now()->subYears(25)->toDateString(),
            'whatsapp' => '66999999999',
            'zip_code' => '78680000',
            'address_street' => 'Rua A',
            'address_number' => '10',
            'address_neighborhood' => 'Centro',
            'address_city' => 'Água Boa',
            'address_state' => 'MT',
            'accepted_regulation_at' => now(),
        ]);

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação Growth',
            'description' => 'Teste',
            'price' => 9.90,
            'total_numbers' => 1000,
            'status' => 'active',
            'prize_name' => 'Prêmio',
            'draw_date' => now()->addDays(10),
        ]);
        $raffle->seedDefaultPackages();

        return [$cliente, $raffle->fresh('packages')];
    }

    /**
     * @return array{0: User, 1: Raffle, 2: RafflePackage}
     */
    private function readyBuyerAndPopularPackage(): array
    {
        [$cliente, $raffle] = $this->readyBuyerAndRaffle();
        $package = $raffle->packages()->where('name', 'Popular')->firstOrFail();

        return [$cliente, $raffle, $package];
    }
}
