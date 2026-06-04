<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id', 'subject', 'category', 'priority', 'message', 'status'
])]
class SupportTicket extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
