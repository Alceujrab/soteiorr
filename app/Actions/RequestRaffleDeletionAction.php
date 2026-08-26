<?php

namespace App\Actions;

use App\Mail\RaffleDeletionCodeMail;
use App\Models\Raffle;
use App\Models\RaffleDeletionChallenge;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class RequestRaffleDeletionAction
{
    public const EXPIRES_IN_MINUTES = 15;

    /**
     * @return array{challenge: RaffleDeletionChallenge, email: string}
     */
    public function execute(Raffle $raffle, User $requester): array
    {
        $email = $this->resolutionEmail();

        if (blank($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('E-mail de segurança do administrador não configurado para receber o código de exclusão.');
        }

        RaffleDeletionChallenge::query()
            ->where('raffle_id', $raffle->id)
            ->whereNull('consumed_at')
            ->delete();

        $code = (string) random_int(100000, 999999);

        $challenge = RaffleDeletionChallenge::create([
            'raffle_id' => $raffle->id,
            'requested_by' => $requester->id,
            'email' => $email,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRES_IN_MINUTES),
        ]);

        Mail::to($email)->send(new RaffleDeletionCodeMail(
            $raffle,
            $code,
            self::EXPIRES_IN_MINUTES
        ));

        return [
            'challenge' => $challenge,
            'email' => $email,
        ];
    }

    public function resolutionEmail(): string
    {
        $configured = Setting::get('admin_security_email')
            ?: config('mail.from.address')
            ?: '';

        return trim((string) $configured);
    }
}
