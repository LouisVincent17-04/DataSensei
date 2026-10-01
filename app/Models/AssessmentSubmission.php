<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'attempt_no',
        'status',
        'score',
        'total_points',
        'started_at',
        'submitted_at',
        'graded_at',
        'feedback',
        'draft_answers',
        'draft_version',
        'draft_saved_at',
        'timed_out_at',
        'anti_cheat_session_id',
        'integrity_status',
        'integrity_reason',
        'provisional_score',
        'integrity_reviewed_by',
        'integrity_reviewed_at',
    ];

    public const INTEGRITY_CLEAR = 'clear';
    public const INTEGRITY_BLOCKED = 'blocked';
    public const INTEGRITY_REVIEW_REQUIRED = 'review_required';

    protected $casts = [
        'attempt_no' => 'integer',
        'score' => 'decimal:2',
        'total_points' => 'decimal:2',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'draft_answers' => 'array',
        'draft_version' => 'integer',
        'draft_saved_at' => 'datetime',
        'timed_out_at' => 'datetime',
        'provisional_score' => 'decimal:2',
        'integrity_reviewed_by' => 'integer',
        'integrity_reviewed_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    /**
     * True while an anti-cheat decision withholds credit for this attempt and
     * the instructor has not reviewed it yet. The saved work stays stored.
     */
    public function isHeldForIntegrityReview(): bool
    {
        return $this->status === 'submitted'
            && in_array($this->integrity_status, [
                self::INTEGRITY_BLOCKED,
                self::INTEGRITY_REVIEW_REQUIRED,
            ], true);
    }

    public function getPercentageAttribute(): int
    {
        return (float) $this->total_points > 0
            ? (int) round(((float) $this->score / (float) $this->total_points) * 100)
            : 0;
    }
}
