<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_requires_enabled_settings(): void
    {
        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_google_callback_creates_user_and_asks_profile_completion(): void
    {
        Mail::fake();

        Setting::set('google_login_enabled', '1');
        Setting::set('google_client_id', 'client-id');
        Setting::set('google_client_secret', 'client-secret');

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'token-123',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'sub' => 'google-uid-1',
                'email' => 'google.user@example.com',
                'name' => 'Google User',
                'picture' => 'https://example.com/avatar.jpg',
                'email_verified' => true,
            ], 200),
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-abc'])
            ->get(route('auth.google.callback', [
                'state' => 'state-abc',
                'code' => 'auth-code',
            ]));

        $response->assertRedirect(route('profile.complete'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'google.user@example.com',
            'google_id' => 'google-uid-1',
            'role' => 'cliente',
        ]);
    }

    public function test_complete_profile_blocks_underage_users(): void
    {
        $user = User::factory()->create([
            'role' => 'cliente',
            'google_id' => 'google-uid-2',
            'email' => 'parcial@example.com',
        ]);

        $response = $this->actingAs($user)->from(route('profile.complete'))->post(route('profile.complete.submit'), [
            'name' => 'Parcial',
            'cpf' => '390.533.447-05',
            'birth_date' => now()->subYears(16)->toDateString(),
            'whatsapp' => '66999999999',
            'zip_code' => '78680000',
            'address_street' => 'Rua A',
            'address_number' => '10',
            'address_neighborhood' => 'Centro',
            'address_city' => 'Agua Boa',
            'address_state' => 'MT',
            'accepted_regulation' => '1',
        ]);

        $response->assertRedirect(route('profile.complete'));
        $response->assertSessionHasErrors('birth_date');
    }
}
