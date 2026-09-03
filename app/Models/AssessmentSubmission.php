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
    ];

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

    public function getPercentageAttribute(): int
    {
        return (float) $this->total_points > 0
            ? (int) round(((float) $this->score / (float) $this->total_points) * 100)
            : 0;
    }
}
