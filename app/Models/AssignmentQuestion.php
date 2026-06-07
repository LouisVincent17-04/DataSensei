<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_library_item_id',
        'question_type',
        'question_text',
        'points',
        'order_index',
        'explanation',
    ];

    protected $casts = [
        'points' => 'integer',
        'order_index' => 'integer',
    ];

    public function assignmentLibraryItem(): BelongsTo
    {
        return $this->belongsTo(AssignmentLibraryItem::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssignmentQuestionOption::class)
            ->orderBy('order_index');
    }

    public function blankAnswers(): HasMany
    {
        return $this->hasMany(AssignmentBlankAnswer::class);
    }

    public function submissionAnswers(): HasMany
    {
        return $this->hasMany(AssignmentSubmissionAnswer::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->question_type === 'mcq' ? 'MCQ' : 'Fill Blank';
    }
}
