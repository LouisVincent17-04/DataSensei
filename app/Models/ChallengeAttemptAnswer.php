<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeAttemptAnswer extends Model
{
    protected $fillable = [
        'challenge_attempt_id',
        'challenge_question_id',
        'selected_option_id',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ChallengeAttempt::class, 'challenge_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ChallengeQuestion::class, 'challenge_question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(ChallengeOption::class, 'selected_option_id');
    }
}
