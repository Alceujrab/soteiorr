<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id', 'action', 'ip_address', 'user_agent', 'payload'
])]
class ActivityLog extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
