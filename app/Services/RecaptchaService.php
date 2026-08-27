<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RecaptchaService
{
    public function isEnabled(): bool
    {
        return Setting::get('recaptcha_enabled', '0') === '1'
            && filled(Setting::get('recaptcha_site_key'))
            && filled(Setting::get('recaptcha_secret_key'));
    }

    public function siteKey(): string
    {
        return (string) Setting::get('recaptcha_site_key', '');
    }

    public function version(): string
    {
        $version = strtolower((string) Setting::get('recaptcha_version', 'v3'));

        return in_array($version, ['v2', 'v3'], true) ? $version : 'v3';
    }

    public function minScore(): float
    {
        $score = (float) Setting::get('recaptcha_min_score', '0.5');

        if ($score < 0.0) {
            return 0.0;
        }

        if ($score > 1.0) {
            return 1.0;
        }

        return $score;
    }

    /**
     * @throws ValidationException
     */
    public function validateOrFail(?string $token, ?string $remoteIp = null, ?string $expectedAction = null): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        if (! filled($token)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Confirme que você não é um robô.',
            ]);
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => Setting::get('recaptcha_secret_key'),
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]);
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Não foi possível validar o reCAPTCHA. Tente novamente.',
            ]);
        }

        $payload = $response->json();

        if (! is_array($payload) || empty($payload['success'])) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Falha na verificação do reCAPTCHA. Tente novamente.',
            ]);
        }

        if ($this->version() === 'v3') {
            $score = (float) ($payload['score'] ?? 0);
            if ($score < $this->minScore()) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Verificação de segurança reprovada. Tente novamente.',
                ]);
            }

            if ($expectedAction !== null) {
                $action = (string) ($payload['action'] ?? '');
                if ($action !== '' && $action !== $expectedAction) {
                    throw ValidationException::withMessages([
                        'g-recaptcha-response' => 'Falha na verificação do reCAPTCHA. Tente novamente.',
                    ]);
                }
            }
        }
    }
}
