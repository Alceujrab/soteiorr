<?php

namespace Tests\Feature;

use App\Mail\RaffleDeletionCodeMail;
use App\Models\Raffle;
use App\Models\RafflePackage;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminRaffleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_raffle_with_free_total_numbers_and_packages(): void
    {
        $admin = $this->makeAdmin();

        $payload = [
            'title' => 'Nova Ação 200k',
            'description' => 'Descrição',
            'total_numbers' => 200000,
            'prize_name' => 'Veículo',
            'prize_description' => 'Prêmio completo',
            'draw_date' => now()->addDays(15)->format('Y-m-d H:i:s'),
            'packages' => RafflePackage::defaultDefinitions(),
        ];

        $response = $this->actingAs($admin)->post(route('admin.raffles.store'), $payload);

        $response->assertRedirect(route('admin.dashboard'));

        $raffle = Raffle::where('title', 'Nova Ação 200k')->firstOrFail();

        $this->assertSame(200000, $raffle->total_numbers);
        $this->assertEquals(9.90, (float) $raffle->price);
        $this->assertCount(4, $raffle->packages);
        $this->assertDatabaseHas('raffle_packages', [
            'raffle_id' => $raffle->id,
            'name' => 'Avançado',
            'numbers_qty' => 120,
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_delete_raffle_after_email_code_confirmation(): void
    {
        Mail::fake();
        Setting::set('admin_security_email', 'contato@rrsorteio.com');

        $admin = $this->makeAdmin();

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação para excluir',
            'description' => 'Teste',
            'price' => 9.90,
            'total_numbers' => 500,
            'status' => 'active',
            'prize_name' => 'Prêmio',
            'draw_date' => now()->addDays(7),
        ]);
        $raffle->seedDefaultPackages();

        Ticket::create([
            'raffle_id' => $raffle->id,
            'user_id' => $admin->id,
            'number' => 10,
            'status' => 'reserved',
        ]);

        $this->actingAs($admin)->post(route('admin.raffles.destroy.request', $raffle));

        $code = null;
        Mail::assertSent(RaffleDeletionCodeMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $response = $this->actingAs($admin)->post(route('admin.raffles.destroy.confirm.submit', $raffle), [
            'code' => $code,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('raffles', ['id' => $raffle->id]);
        $this->assertDatabaseMissing('raffle_packages', ['raffle_id' => $raffle->id]);
        $this->assertDatabaseMissing('tickets', ['raffle_id' => $raffle->id]);
    }

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin Organizador',
            'email' => 'admin-manage@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_organizador',
        ]);
    }
}
