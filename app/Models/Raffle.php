<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 'title', 'description', 'price', 'total_numbers',
    'status', 'prize_name', 'prize_description', 'image_url',
    'youtube_url', 'draw_date', 'live_url',
])]
class Raffle extends Model
{
    protected function casts(): array
    {
        return [
            'draw_date' => 'datetime',
            'price' => 'decimal:2',
            'total_numbers' => 'integer',
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

    public function draw()
    {
        return $this->hasOne(Draw::class);
    }
}
