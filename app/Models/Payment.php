<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 'raffle_package_id', 'amount', 'gateway', 'gateway_transaction_id',
    'status', 'payment_method', 'pix_qr_code', 'pix_qr_code_url',
])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function package()
    {
        return $this->belongsTo(RafflePackage::class, 'raffle_package_id');
    }
}
