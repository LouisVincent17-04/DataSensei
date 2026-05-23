<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodingChallengeRetake extends Model
{
    protected $fillable = [
        'user_id',
        'challenge_id',
        'retake_count',
    ];

    public const MAX_RETAKES = 3;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function hasRetakesRemaining(): bool
    {
        return $this->retake_count < self::MAX_RETAKES;
    }

    public function retakesRemaining(): int
    {
        return max(0, self::MAX_RETAKES - $this->retake_count);
    }
}