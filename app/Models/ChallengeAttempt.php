<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'challenge_id',
        'attempt_no',
        'mode',
        'status',
        'started_at',
        'expires_at',
        'submitted_at',
        'last_seen_at',
        'time_limit_seconds',
        'time_taken_seconds',
        'score',
        'total_questions',
        'xp_awarded',
        'is_ranked',
        'is_leaderboard_eligible',
        'suspicious_event_count',
        'question_order',
        'option_order',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'time_limit_seconds' => 'integer',
        'time_taken_seconds' => 'integer',
        'score' => 'integer',
        'total_questions' => 'integer',
        'xp_awarded' => 'integer',
        'attempt_no' => 'integer',
        'is_ranked' => 'boolean',
        'is_leaderboard_eligible' => 'boolean',
        'suspicious_event_count' => 'integer',
        'question_order' => 'array',
        'option_order' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ChallengeAttemptAnswer::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ChallengeAttemptEvent::class);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['submitted', 'expired', 'voided', 'disqualified'], true);
    }
}
