<?php

namespace Tests\Feature;

use App\Models\Draw;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use App\Services\DrawCeremonyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DrawCeremonyTest extends TestCase
{
    use RefreshDatabase;

    private function createPaidSetup(): array
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $buyer = User::factory()->create([
            'role' => 'cliente',
            'name' => 'Maria Oliveira Santos',
            'whatsapp' => '66991234567',
            'phone' => '66991234567',
            'address_street' => 'Rua das Palmeiras',
            'address_number' => '120',
            'address_neighborhood' => 'Centro',
            'address_city' => 'Água Boa',
            'address_state' => 'MT',
            'zip_code' => '78635000',
            'cpf' => '39053344705',
        ]);

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Santana 1997',
            'description' => 'Teste',
            'price' => 9.9,
            'total_numbers' => 200000,
            'status' => 'active',
            'prize_name' => 'Santana 1997',
            'draw_date' => now()->addDays(10),
        ]);

        $ticket = Ticket::create([
            'raffle_id' => $raffle->id,
            'user_id' => $buyer->id,
            'number' => 54321,
            'status' => 'paid',
        ]);

        return compact('admin', 'buyer', 'raffle', 'ticket');
    }

    public function test_admin_can_start_official_draw_and_reveal_digits(): void
    {
        ['admin' => $admin, 'raffle' => $raffle, 'ticket' => $ticket] = $this->createPaidSetup();

        $this->actingAs($admin)
            ->post(route('admin.draws.start', $raffle), [
                'live_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertRedirect(route('admin.draws.room', $raffle));

        $draw = Draw::query()->where('raffle_id', $raffle->id)->where('is_test', false)->first();
        $this->assertNotNull($draw);
        $this->assertSame('live', $draw->status);
        $this->assertSame(0, $draw->revealed_digits);
        $this->assertSame($ticket->number, $draw->winning_number);
        $this->assertSame('054321', $draw->winning_number_padded);

        $ceremony = app(DrawCeremonyService::class);

        for ($i = 1; $i <= 5; $i++) {
            $draw->forceFill(['last_reveal_at' => now()->subSeconds(5)])->save();
            $draw = $ceremony->revealNextDigit($draw->fresh());
            $this->assertSame($i, $draw->revealed_digits);
            $this->assertSame('live', $draw->status);
        }

        $draw->forceFill(['last_reveal_at' => now()->subSeconds(5)])->save();
        $draw = $ceremony->revealNextDigit($draw->fresh());
        $this->assertSame(6, $draw->revealed_digits);
        $this->assertSame('completed', $draw->status);
        $this->assertSame('completed', $raffle->fresh()->status);

        $public = $ceremony->publicState($draw);
        $this->assertSame('Maria Oliveira Santos', $public['winner']['name']);
        $this->assertStringContainsString('*', $public['winner']['phone']);
        $this->assertStringContainsString('*', $public['winner']['address']);
        $this->assertSame('054321', $public['winner']['number_padded']);

        $adminState = $ceremony->adminState($draw);
        $this->assertSame('66991234567', $adminState['winner_full']['phone']);
        $this->assertStringContainsString('Palmeiras', $adminState['winner_full']['address']);
    }

    public function test_public_watch_page_masks_winner_and_hides_number_until_complete(): void
    {
        ['admin' => $admin, 'raffle' => $raffle] = $this->createPaidSetup();
        $ceremony = app(DrawCeremonyService::class);
        $draw = $ceremony->startOfficial($raffle, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->get(route('draws.watch', $draw->public_slug))->assertOk()->assertSee('Revelação ao vivo', false);

        $state = $this->getJson(route('draws.state', $draw->public_slug))->assertOk()->json();
        $this->assertNull($state['winning_number_padded']);
        $this->assertNull($state['winner']);
        $this->assertContains(null, $state['target_digits']);

        for ($i = 0; $i < 6; $i++) {
            $draw = $draw->fresh();
            $draw->forceFill(['last_reveal_at' => now()->subSeconds(5)])->save();
            $draw = $ceremony->revealNextDigit($draw->fresh());
        }

        $state = $this->getJson(route('draws.state', $draw->public_slug))->assertOk()->json();
        $this->assertSame('054321', $state['winning_number_padded']);
        $this->assertNotNull($state['winner']);
        $this->assertStringContainsString('*', $state['winner']['phone']);
        $this->assertSame('Maria Oliveira Santos', $state['winner']['name']);
    }

    public function test_test_draw_does_not_complete_raffle(): void
    {
        ['admin' => $admin, 'raffle' => $raffle] = $this->createPaidSetup();

        $this->actingAs($admin)
            ->post(route('admin.draws.test.start'), [
                'forced_number' => 123456,
            ])
            ->assertRedirect(route('admin.draws.test'));

        $draw = Draw::query()->where('is_test', true)->latest('id')->first();
        $this->assertNotNull($draw);
        $this->assertSame('123456', $draw->winning_number_padded);

        $ceremony = app(DrawCeremonyService::class);
        for ($i = 0; $i < 6; $i++) {
            $draw->forceFill(['last_reveal_at' => now()->subSeconds(5)])->save();
            $draw = $ceremony->revealNextDigit($draw->fresh());
        }

        $this->assertSame('completed', $draw->status);
        $this->assertSame('active', $raffle->fresh()->status);
        $this->assertStringContainsString('Demonstração', $draw->winner_snapshot['name']);
    }

    public function test_test_draw_rejects_number_above_raffle_total(): void
    {
        ['admin' => $admin, 'raffle' => $raffle] = $this->createPaidSetup();

        $this->assertSame(200000, $raffle->total_numbers);

        $this->actingAs($admin)
            ->from(route('admin.draws.test'))
            ->post(route('admin.draws.test.start'), [
                'forced_number' => 200001,
            ])
            ->assertRedirect(route('admin.draws.test'))
            ->assertSessionHasErrors('forced_number');
    }

    public function test_public_draw_page_is_always_available_before_start(): void
    {
        ['raffle' => $raffle] = $this->createPaidSetup();

        $this->get(route('draws.raffle', $raffle))->assertRedirect();

        $draw = Draw::query()->where('raffle_id', $raffle->id)->where('is_test', false)->first();
        $this->assertNotNull($draw);
        $this->assertSame('pending', $draw->status);
        $this->assertNotEmpty($draw->public_slug);

        $this->get(route('draws.watch', $draw->public_slug))
            ->assertOk()
            ->assertSee('ainda não começou', false);

        $this->get(route('draws.index'))
            ->assertRedirect(route('draws.watch', $draw->public_slug));

        $ceremony = app(DrawCeremonyService::class);
        $started = $ceremony->startOfficial($raffle, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertSame($draw->id, $started->id);
        $this->assertSame($draw->public_slug, $started->public_slug);
        $this->assertSame('live', $started->status);
    }

    public function test_auto_reveal_marks_ceremony_as_running(): void
    {
        ['admin' => $admin, 'raffle' => $raffle] = $this->createPaidSetup();
        $ceremony = app(DrawCeremonyService::class);
        $draw = $ceremony->startOfficial($raffle);

        $this->actingAs($admin)
            ->postJson(route('admin.draws.auto', $draw))
            ->assertOk()
            ->assertJsonPath('auto_running', true);

        $this->assertNotNull($draw->fresh()->auto_reveal_started_at);

        $public = $this->getJson(route('draws.state', $draw->public_slug))->assertOk()->json();
        $this->assertTrue($public['auto_running']);
        $this->assertSame(5, $public['reveal_interval_seconds']);
    }

    public function test_cancel_resets_official_draw_to_pending(): void
    {
        ['admin' => $admin, 'raffle' => $raffle] = $this->createPaidSetup();
        $ceremony = app(DrawCeremonyService::class);
        $draw = $ceremony->startOfficial($raffle);
        $slug = $draw->public_slug;

        $this->actingAs($admin)
            ->post(route('admin.draws.cancel', $draw))
            ->assertRedirect(route('admin.draws.room', $raffle));

        $draw = $draw->fresh();
        $this->assertSame('pending', $draw->status);
        $this->assertSame($slug, $draw->public_slug);
        $this->assertNull($draw->winning_number);
        $this->assertSame(0, $draw->revealed_digits);
        $this->assertSame('active', $raffle->fresh()->status);
    }

    public function test_cannot_start_without_paid_tickets(): void
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Sem pagos',
            'description' => 'x',
            'price' => 1,
            'total_numbers' => 100,
            'status' => 'active',
            'prize_name' => 'X',
            'draw_date' => now()->addDay(),
        ]);

        $this->actingAs($admin)
            ->from(route('admin.draws.room', $raffle))
            ->post(route('admin.draws.start', $raffle))
            ->assertRedirect(route('admin.draws.room', $raffle))
            ->assertSessionHasErrors('raffle');
    }
}
