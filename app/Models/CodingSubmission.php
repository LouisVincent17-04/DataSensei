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
        'voided',
        'attempt_token',
        'attempt_generation',
        'void_reason',
        'grader_diagnostics',
    ];

    /**
     * DS-13: grader_diagnostics keeps the detailed stderr of hidden test cases
     * for operators. It must never reach a student through toArray()/toJson().
     */
    protected $hidden = [
        'grader_diagnostics',
    ];

    protected $casts = [
        'tests_passed' => 'integer',
        'tests_total' => 'integer',
        'xp_earned' => 'integer',
        'time_taken_seconds' => 'integer',
        'test_results' => 'array',
        'voided' => 'boolean',
        'attempt_generation' => 'integer',
        'grader_diagnostics' => 'array',
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
