<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RecaptchaProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function enableRecaptcha(string $version = 'v2'): void
    {
        Setting::set('recaptcha_enabled', '1');
        Setting::set('recaptcha_site_key', 'test-site-key');
        Setting::set('recaptcha_secret_key', 'test-secret-key');
        Setting::set('recaptcha_version', $version);
        Setting::set('recaptcha_min_score', '0.5');
    }

    public function test_login_page_shows_recaptcha_widget_when_enabled(): void
    {
        $this->enableRecaptcha();

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('g-recaptcha', false);
        $response->assertSee('test-site-key');
        $response->assertSee('https://www.google.com/recaptcha/api.js', false);
    }

    public function test_login_requires_recaptcha_when_enabled(): void
    {
        $this->enableRecaptcha();

        User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => 'senha123',
            'role' => 'cliente',
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'cliente@example.com',
            'password' => 'senha123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_login_accepts_valid_recaptcha_token(): void
    {
        $this->enableRecaptcha();

        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);

        User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => 'senha123',
            'role' => 'cliente',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'cliente@example.com',
            'password' => 'senha123',
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.google.com/recaptcha/api/siteverify'
                && $request['response'] === 'valid-token'
                && $request['secret'] === 'test-secret-key';
        });
    }

    public function test_login_rejects_invalid_recaptcha_token(): void
    {
        $this->enableRecaptcha();

        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => 'senha123',
            'role' => 'cliente',
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'cliente@example.com',
            'password' => 'senha123',
            'g-recaptcha-response' => 'bad-token',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_forgot_password_requires_recaptcha_when_enabled(): void
    {
        $this->enableRecaptcha();

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'cliente@example.com',
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_forms_work_without_recaptcha_when_disabled(): void
    {
        Setting::set('recaptcha_enabled', '0');

        User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => 'senha123',
            'role' => 'cliente',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'cliente@example.com',
            'password' => 'senha123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }
}
