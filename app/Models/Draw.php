<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'raffle_id',
    'status',
    'is_test',
    'digit_length',
    'revealed_digits',
    'winning_number',
    'winning_number_padded',
    'winning_ticket_id',
    'winning_user_id',
    'live_url',
    'winner_snapshot',
    'draw_seed',
    'eligible_hash',
    'eligible_count',
    'selection_index',
    'eligible_numbers',
    'ops_checklist',
    'started_at',
    'completed_at',
    'last_reveal_at',
    'auto_reveal_started_at',
    'public_slug',
    'drawn_at',
])]
class Draw extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_LIVE = 'live';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function checklistKeys(): array
    {
        return [
            'contact_winner',
            'publish_minutes',
            'deliver_prize',
            'archive_recording',
        ];
    }

    protected function casts(): array
    {
        return [
            'winning_number' => 'integer',
            'digit_length' => 'integer',
            'revealed_digits' => 'integer',
            'is_test' => 'boolean',
            'winner_snapshot' => 'array',
            'eligible_numbers' => 'array',
            'ops_checklist' => 'array',
            'eligible_count' => 'integer',
            'selection_index' => 'integer',
            'drawn_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_reveal_at' => 'datetime',
            'auto_reveal_started_at' => 'datetime',
        ];
    }

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }

    public function winningTicket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'winning_ticket_id');
    }

    public function winningUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winning_user_id');
    }

    public function isLive(): bool
    {
        return $this->status === self::STATUS_LIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function allDigitsRevealed(): bool
    {
        return $this->revealed_digits >= $this->digit_length;
    }

    public function revealedPrefix(): string
    {
        $padded = (string) ($this->winning_number_padded ?? '');

        if ($padded === '' || $this->revealed_digits <= 0) {
            return '';
        }

        return substr($padded, 0, min($this->revealed_digits, strlen($padded)));
    }
}
