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
    ];

    protected $casts = [
        'attempt_no' => 'integer',
        'score' => 'integer',
        'total_points' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
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

    public function getPercentageAttribute(): int
    {
        if ($this->total_points <= 0) {
            return 0;
        }

        return (int) round(($this->score / $this->total_points) * 100);
    }
}
