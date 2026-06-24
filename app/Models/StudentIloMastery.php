<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentIloMastery extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'ilo_id',
        'mastery_percent',
        'evidence_count',
        'status',
        'last_evaluated_at',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'class_id' => 'integer',
        'ilo_id' => 'integer',
        'mastery_percent' => 'float',
        'evidence_count' => 'integer',
        'last_evaluated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function ilo(): BelongsTo
    {
        return $this->belongsTo(IntendedLearningOutcome::class, 'ilo_id');
    }
}
