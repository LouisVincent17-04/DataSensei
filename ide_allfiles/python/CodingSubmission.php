<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodingSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'coding_question_id',
        'code',
        'language',
        'status',
        'tests_passed',
        'tests_total',
        'xp_earned',
        'time_taken_seconds',
        'test_results',
        'error_message',
    ];

    protected $casts = [
        'test_results' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function codingQuestion(): BelongsTo
    {
        return $this->belongsTo(CodingQuestion::class);
    }

    public function getScorePercentAttribute(): float
    {
        if ($this->tests_total === 0) return 0;
        return round(($this->tests_passed / $this->tests_total) * 100, 1);
    }

    public function isPerfect(): bool
    {
        return $this->tests_passed === $this->tests_total && $this->tests_total > 0;
    }
}