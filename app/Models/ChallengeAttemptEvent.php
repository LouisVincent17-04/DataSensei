<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeAttemptEvent extends Model
{
    protected $fillable = [
        'challenge_attempt_id',
        'event_type',
        'severity',
        'details',
        'occurred_at',
    ];

    protected $casts = [
        'details' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ChallengeAttempt::class, 'challenge_attempt_id');
    }
}
