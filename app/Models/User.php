<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
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
