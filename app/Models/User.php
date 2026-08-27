<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'email',
    'google_id',
    'avatar',
    'password',
    'role',
    'affiliate_code',
    'cpf',
    'phone',
    'birth_date',
    'whatsapp',
    'phone_extra',
    'zip_code',
    'address_street',
    'address_number',
    'address_complement',
    'address_neighborhood',
    'address_city',
    'address_state',
    'asaas_customer_id',
    'accepted_regulation_at',
    'email_verified_at',
    'two_factor_secret',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function raffles()
    {
        return $this->hasMany(Raffle::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function referredPayments()
    {
        return $this->hasMany(Payment::class, 'affiliate_user_id');
    }

    public function ensureAffiliateCode(): string
    {
        if (filled($this->affiliate_code)) {
            return (string) $this->affiliate_code;
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (self::query()->where('affiliate_code', $code)->exists());

        $this->forceFill(['affiliate_code' => $code])->save();

        return $code;
    }

    public function hasCompleteCheckoutProfile(): bool
    {
        return filled($this->cpf)
            && filled($this->birth_date)
            && filled($this->whatsapp)
            && filled($this->zip_code)
            && filled($this->address_street)
            && filled($this->address_number)
            && filled($this->address_neighborhood)
            && filled($this->address_city)
            && filled($this->address_state)
            && filled($this->accepted_regulation_at);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'accepted_regulation_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
