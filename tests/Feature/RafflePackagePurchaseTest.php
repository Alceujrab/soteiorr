<?php

namespace Tests\Feature;

use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RafflePackagePurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_lists_packages_without_loading_all_tickets(): void
    {
        [$admin, $cliente, $raffle] = $this->createRaffleWithPackages();

        $this->actingAs($cliente);

        $response = $this->get(route('raffles.show', $raffle));

        $response->assertOk();
        $response->assertSee('Essencial');
        $response->assertSee('Popular');
        $response->assertSee('Avançado');
        $response->assertSee('Premium');
        $response->assertSee('R$ 9,90');
        $response->assertSee('200.000');
        $response->assertDontSee('Comprar Surpresinha');
    }

    public function test_can_buy_package_and_create_payment_with_package_price(): void
    {
        [$admin, $cliente, $raffle] = $this->createRaffleWithPackages();
        $package = $raffle->packages()->where('name', 'Popular')->firstOrFail();

        $response = $this->actingAs($cliente)->post(route('raffles.buy', $raffle), [
            'package_id' => $package->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount('tickets', 50);
        $this->assertDatabaseHas('payments', [
            'user_id' => $cliente->id,
            'raffle_package_id' => $package->id,
            'amount' => 21.90,
            'status' => 'pending',
        ]);

        $this->assertSame(50, Ticket::where('raffle_id', $raffle->id)->where('status', 'reserved')->count());
    }

    public function test_cannot_buy_package_from_another_raffle(): void
    {
        [$admin, $cliente, $raffle] = $this->createRaffleWithPackages();

        $other = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Outra Ação',
            'description' => 'Teste',
            'price' => 9.90,
            'total_numbers' => 1000,
            'status' => 'active',
            'prize_name' => 'Prêmio',
            'draw_date' => now()->addDays(10),
        ]);
        $other->seedDefaultPackages();

        $foreignPackage = $other->packages()->firstOrFail();

        $response = $this->actingAs($cliente)->post(route('raffles.buy', $raffle), [
            'package_id' => $foreignPackage->id,
        ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('tickets', 0);
    }

    /**
     * @return array{0: User, 1: User, 2: Raffle}
     */
    private function createRaffleWithPackages(): array
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-packages@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_organizador',
        ]);

        $cliente = User::create([
            'name' => 'Client Test',
            'email' => 'client-packages@test.com',
            'password' => bcrypt('password'),
            'role' => 'cliente',
        ]);

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação 200k',
            'description' => 'Ação com muitos números',
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
