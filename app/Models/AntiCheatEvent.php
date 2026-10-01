<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntiCheatEvent extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'assessment_id',
        'assessment_submission_id',
        'assessment_question_id',
        'assessment_type',
        'event_type',
        'severity',
        'attempt_session_id',
        'event_uuid',
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

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function assessmentSubmission(): BelongsTo
    {
        return $this->belongsTo(AssessmentSubmission::class, 'assessment_submission_id');
    }

    public function assessmentQuestion(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
}
