<?php

namespace Tests\Feature;

use App\Mail\RaffleDeletionCodeMail;
use App\Models\Raffle;
use App\Models\RaffleDeletionChallenge;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminRaffleDeletionConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_requesting_deletion_sends_code_email_and_does_not_delete_yet(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $raffle = $this->makeRaffle($admin);

        $response = $this->actingAs($admin)
            ->post(route('admin.raffles.destroy.request', $raffle));

        $response->assertRedirect(route('admin.raffles.destroy.confirm', $raffle));
        $this->assertDatabaseHas('raffles', ['id' => $raffle->id]);
        $this->assertDatabaseCount('raffle_deletion_challenges', 1);

        Mail::assertSent(RaffleDeletionCodeMail::class, function (RaffleDeletionCodeMail $mail) use ($admin, $raffle) {
            return $mail->hasTo($admin->email)
                && $mail->raffle->is($raffle)
                && preg_match('/^\d{6}$/', $mail->code) === 1;
        });
    }

    public function test_confirming_with_valid_code_deletes_raffle(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $raffle = $this->makeRaffle($admin);

        Ticket::create([
            'raffle_id' => $raffle->id,
            'user_id' => $admin->id,
            'number' => 10,
            'status' => 'reserved',
        ]);

        $this->actingAs($admin)->post(route('admin.raffles.destroy.request', $raffle));

        $code = null;
        Mail::assertSent(RaffleDeletionCodeMail::class, function (RaffleDeletionCodeMail $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $response = $this->actingAs($admin)->post(route('admin.raffles.destroy.confirm.submit', $raffle), [
            'code' => $code,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('raffles', ['id' => $raffle->id]);
        $this->assertDatabaseMissing('tickets', ['raffle_id' => $raffle->id]);
        $this->assertDatabaseMissing('raffle_packages', ['raffle_id' => $raffle->id]);
    }

    public function test_confirming_with_invalid_code_keeps_raffle(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $raffle = $this->makeRaffle($admin);

        $this->actingAs($admin)->post(route('admin.raffles.destroy.request', $raffle));

        $response = $this->actingAs($admin)->from(route('admin.raffles.destroy.confirm', $raffle))
            ->post(route('admin.raffles.destroy.confirm.submit', $raffle), [
                'code' => '000000',
            ]);

        $response->assertRedirect(route('admin.raffles.destroy.confirm', $raffle));
        $response->assertSessionHasErrors('code');
        $this->assertDatabaseHas('raffles', ['id' => $raffle->id]);
        $this->assertSame(1, RaffleDeletionChallenge::where('raffle_id', $raffle->id)->value('attempts'));
    }

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin Organizador',
            'email' => 'admin-delete@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_organizador',
        ]);
    }

    private function makeRaffle(User $admin): Raffle
    {
        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação para exclusão segura',
            'description' => 'Teste',
            'price' => 9.90,
            'total_numbers' => 500,
            'status' => 'active',
            'prize_name' => 'Prêmio',
            'draw_date' => now()->addDays(7),
        ]);
        $raffle->seedDefaultPackages();

        return $raffle;
    }
}
