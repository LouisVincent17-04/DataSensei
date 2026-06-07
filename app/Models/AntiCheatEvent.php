<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntiCheatEvent extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'class_assignment_id',
        'assignment_submission_id',
        'assignment_question_id',
        'assessment_type',
        'event_type',
        'severity',
        'attempt_session_id',
        'details',
        'occurred_at',
    ];

    protected $casts = [
        'details'     => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function classAssignment(): BelongsTo
    {
        return $this->belongsTo(ClassAssignment::class, 'class_assignment_id');
    }

    public function assignmentSubmission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }

    public function assignmentQuestion(): BelongsTo
    {
        return $this->belongsTo(AssignmentQuestion::class, 'assignment_question_id');
    }
}
