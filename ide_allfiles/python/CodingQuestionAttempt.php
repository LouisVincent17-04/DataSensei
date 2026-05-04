<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodingQuestionAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'coding_question_id',
        'started_at',
        'expired',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired'    => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function codingQuestion(): BelongsTo
    {
        return $this->belongsTo(CodingQuestion::class);
    }

    /**
     * Seconds remaining based on wall-clock time since started_at.
     *
     * FIX: Use raw Unix timestamps (->timestamp) instead of Carbon's
     * diffInSeconds(). Carbon::diffInSeconds() always returns an UNSIGNED
     * (absolute) value — so when there is any timezone mismatch between
     * now() and the stored started_at, the sign is lost and elapsed becomes
     * e.g. -28 800 (8 hrs behind), making:
     *   remaining = 600 - (-28 800) = 29 400 s ≈ 490 minutes  ← the bug
     *
     * Raw Unix timestamp subtraction is always correctly signed and
     * completely timezone-safe regardless of APP_TIMEZONE or DB settings.
     */
    public function remainingSeconds(int $timeLimitSeconds = 0): int
    {
        $limit   = $timeLimitSeconds ?: $this->codingQuestion->time_limit_seconds;
        $elapsed = max(0, now()->timestamp - $this->started_at->timestamp);
        return max(0, $limit - $elapsed);
    }

    public function isExpired(): bool
    {
        return $this->remainingSeconds() <= 0;
    }
}