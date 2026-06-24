<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentQuestionIlo extends Model
{
    use HasFactory;

    protected $fillable = [
        'ilo_id',
        'assessment_source',
        'question_id',
        'weight',
    ];

    protected $casts = [
        'ilo_id' => 'integer',
        'question_id' => 'integer',
        'weight' => 'integer',
    ];

    public function ilo(): BelongsTo
    {
        return $this->belongsTo(IntendedLearningOutcome::class, 'ilo_id');
    }
}
