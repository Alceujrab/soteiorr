<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleOAuthService
{
    public function isEnabled(): bool
    {
        return Setting::get('google_login_enabled', '0') === '1'
            && filled(Setting::get('google_client_id'))
            && filled(Setting::get('google_client_secret'));
    }

    public function redirectUri(): string
    {
        return route('auth.google.callback');
    }

    public function authorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => Setting::get('google_client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.$query;
    }

    /**
     * @return array{id: string, email: string, name: string, picture: ?string, email_verified: bool}
     */
    public function userFromAuthorizationCode(string $code): array
    {
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => Setting::get('google_client_id'),
            'client_secret' => Setting::get('google_client_secret'),
            'redirect_uri' => $this->redirectUri(),
            'grant_type' => 'authorization_code',
        ]);

        if (! $tokenResponse->successful() || blank($tokenResponse->json('access_token'))) {
            throw new RuntimeException('Não foi possível autenticar com o Google. Tente novamente.');
        }

        $accessToken = (string) $tokenResponse->json('access_token');
        $profileResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if (! $profileResponse->successful() || blank($profileResponse->json('email'))) {
            throw new RuntimeException('Não foi possível obter os dados da conta Google.');
        }

        return [
            'id' => (string) $profileResponse->json('sub'),
            'email' => Str::lower((string) $profileResponse->json('email')),
            'name' => (string) ($profileResponse->json('name') ?: $profileResponse->json('email')),
            'picture' => $profileResponse->json('picture'),
            'email_verified' => (bool) $profileResponse->json('email_verified', false),
        ];
    }
}
