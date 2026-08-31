<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessmentDiagnostic extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'assessment_id',
        'table_of_specification_row_id',
        'ilo_id',
        'topic_title',
        'subtopic_title',
        'learning_objective',
        'bloom_level',
        'difficulty_slug',
        'item_count',
        'answered_count',
        'correct_count',
        'earned_points',
        'possible_points',
        'mastery_percent',
        'proficiency_label',
        'manual_review_pending',
        'calculated_at',
    ];

    protected $casts = [
        'earned_points' => 'decimal:2',
        'possible_points' => 'decimal:2',
        'mastery_percent' => 'decimal:2',
        'manual_review_pending' => 'boolean',
        'calculated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function tosRow(): BelongsTo
    {
        return $this->belongsTo(TableOfSpecificationRow::class, 'table_of_specification_row_id');
    }
}
