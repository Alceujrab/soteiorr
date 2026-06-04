<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'raffle_id', 'winning_number', 'winning_ticket_id', 
    'winning_user_id', 'live_url', 'drawn_at'
])]
class Draw extends Model
{
    protected function casts(): array
    {
        return [
            'winning_number' => 'integer',
            'drawn_at' => 'datetime',
        ];
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function winningTicket()
    {
        return $this->belongsTo(Ticket::class, 'winning_ticket_id');
    }

    public function winningUser()
    {
        return $this->belongsTo(User::class, 'winning_user_id');
    }
}
