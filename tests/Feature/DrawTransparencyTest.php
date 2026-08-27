<?php

namespace Tests\Feature;

use App\Models\Draw;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use App\Services\DrawCeremonyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DrawTransparencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_official_draw_stores_deterministic_proof_and_public_minutes(): void
    {
        $admin = User::factory()->create(['role' => 'admin_organizador']);
        $buyer = User::factory()->create(['role' => 'cliente', 'name' => 'Ana Transparent']);
        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação Transparência',
            'description' => 'Teste',
            'price' => 9.9,
            'total_numbers' => 1000,
            'status' => 'active',
            'prize_name' => 'Prêmio',
            'draw_date' => now()->addDays(5),
        ]);

        foreach ([10, 20, 30, 40, 50] as $number) {
            Ticket::create([
                'raffle_id' => $raffle->id,
                'user_id' => $buyer->id,
                'number' => $number,
                'status' => 'paid',
            ]);
        }

        $ceremony = app(DrawCeremonyService::class);
        $expected = $ceremony->selectWinnerFromEligible([10, 20, 30, 40, 50], str_repeat('a', 64));

        $this->actingAs($admin)
            ->post(route('admin.draws.start', $raffle))
            ->assertRedirect(route('admin.draws.room', $raffle));

        $draw = Draw::query()->where('raffle_id', $raffle->id)->where('is_test', false)->first();
        $this->assertNotNull($draw);
        $this->assertNotEmpty($draw->draw_seed);
        $this->assertNotEmpty($draw->eligible_hash);
        $this->assertSame(5, $draw->eligible_count);
        $this->assertContains($draw->winning_number, [10, 20, 30, 40, 50]);
        $this->assertTrue($ceremony->verifyDraw($draw));

        $public = $ceremony->publicState($draw);
        $this->assertSame(5, $public['proof']['eligible_count']);
        $this->assertNull($public['proof']['draw_seed']);

        // Complete reveal
        for ($i = 0; $i < 6; $i++) {
            $draw->forceFill(['last_reveal_at' => now()->subSeconds(5)])->save();
            $draw = $ceremony->revealNextDigit($draw->fresh());
        }

        $this->assertTrue($draw->isCompleted());
        $completedProof = $ceremony->publicState($draw)['proof'];
        $this->assertNotNull($completedProof['draw_seed']);

        $this->get(route('draws.minutes', $draw->public_slug))
            ->assertOk()
            ->assertSee('Ata pública do sorteio', false)
            ->assertSee($draw->eligible_hash, false);

        $this->get(route('draws.minutes.pdf', $draw->public_slug))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->getJson(route('draws.eligible', $draw->public_slug))
            ->assertOk()
            ->assertJsonPath('eligible_count', 5)
            ->assertJsonCount(5, 'numbers');

        $this->actingAs($admin)
            ->post(route('admin.draws.checklist', $draw), [
                'checklist' => [
                    'contact_winner' => '1',
                    'publish_minutes' => '1',
                    'deliver_prize' => '0',
                    'archive_recording' => '0',
                ],
            ])
            ->assertRedirect(route('admin.draws.room', $raffle));

        $this->assertTrue($draw->fresh()->ops_checklist['contact_winner']);
        $this->assertFalse($draw->fresh()->ops_checklist['deliver_prize']);

        // Determinism of helper
        $again = $ceremony->selectWinnerFromEligible([10, 20, 30, 40, 50], str_repeat('a', 64));
        $this->assertSame($expected, $again);
    }

    public function test_select_winner_is_stable_for_known_seed(): void
    {
        $ceremony = app(DrawCeremonyService::class);
        $result = $ceremony->selectWinnerFromEligible([1, 2, 3, 4, 5], 'seed-demo');

        $this->assertSame(5, $result['eligible_count']);
        $this->assertSame(hash('sha256', '1,2,3,4,5'), $result['eligible_hash']);
        $this->assertSame(
            $result['winning_number'],
            $ceremony->selectWinnerFromEligible([5, 4, 3, 2, 1], 'seed-demo')['winning_number']
        );
    }
}
