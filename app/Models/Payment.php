<?php

namespace App\Models;

use App\Actions\ExpireUnpaidReservationsAction;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable([
    'user_id', 'affiliate_user_id', 'raffle_package_id', 'amount', 'gateway', 'gateway_transaction_id',
    'status', 'reminder_sent_at', 'payment_method', 'pix_qr_code', 'pix_qr_code_url',
])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function package()
    {
        return $this->belongsTo(RafflePackage::class, 'raffle_package_id');
    }

    public function reservationExpiresAt(): Carbon
    {
        $ttl = app(ExpireUnpaidReservationsAction::class)->ttlMinutes();

        return $this->created_at->copy()->addMinutes($ttl);
    }

    public function reservationSecondsRemaining(): int
    {
        if ($this->status !== 'pending') {
            return 0;
        }

        return max(0, $this->reservationExpiresAt()->getTimestamp() - now()->getTimestamp());
    }

    public function isReservationExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->status === 'pending' && $this->reservationExpiresAt()->isPast());
    }
}
