<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_assignment_id',
        'student_id',
        'attempt_no',
        'status',
        'score',
        'total_points',
        'started_at',
        'submitted_at',
        'graded_at',
        'feedback',
        'anti_cheat_session_id',
        'draft_answers',
        'draft_version',
        'draft_saved_at',
        'timed_out_at',
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
        'score' => 'integer',
        'total_points' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'draft_answers' => 'array',
        'draft_version' => 'integer',
        'draft_saved_at' => 'datetime',
        'timed_out_at' => 'datetime',
        'provisional_score' => 'integer',
        'integrity_reviewed_by' => 'integer',
        'integrity_reviewed_at' => 'datetime',
        'rewards_awarded_at' => 'datetime',
    ];

    public function classAssignment(): BelongsTo
    {
        return $this->belongsTo(ClassAssignment::class, 'class_assignment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssignmentSubmissionAnswer::class, 'assignment_submission_id');
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
        if ($this->total_points <= 0) {
            return 0;
        }

        return (int) round(($this->score / $this->total_points) * 100);
    }
}
