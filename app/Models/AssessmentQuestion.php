<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    use HasFactory;

    public const TYPES = [
        'multiple_choice' => 'Multiple Choice',
        'true_false' => 'True or False',
        'fill_blank' => 'Fill in the Blank',
        'short_answer' => 'Short Answer',
        'essay' => 'Essay / Paragraph',
    ];

    protected $fillable = [
        'assessment_id',
        'table_of_specification_row_id',
        'ilo_id',
        'item_number',
        'question_type',
        'question_text',
        'image_path',
        'points',
        'is_required',
        'authoring_touched',
        'correct_answer',
        'answer_explanation',
        'rubric_text',
        'topic_title',
        'subtopic_title',
        'learning_objective',
        'bloom_level',
        'difficulty_slug',
    ];

    protected $casts = [
        'item_number' => 'integer',
        'points' => 'integer',
        'is_required' => 'boolean',
        'authoring_touched' => 'boolean',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function tosRow(): BelongsTo
    {
        return $this->belongsTo(TableOfSpecificationRow::class, 'table_of_specification_row_id');
    }

    public function ilo(): BelongsTo
    {
        return $this->belongsTo(IntendedLearningOutcome::class, 'ilo_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssessmentQuestionOption::class)->orderBy('order_index');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->question_type] ?? 'Not Configured';
    }

    /** @return array<int, string> */
    public function authoringErrors(): array
    {
        $errors = [];

        if (! array_key_exists((string) $this->question_type, self::TYPES)) {
            $errors[] = 'Choose a question type.';
        }

        if (trim((string) $this->question_text) === '') {
            $errors[] = 'Enter the question.';
        }

        if ($this->question_type === 'multiple_choice') {
            $options = $this->loadedOptions();
            if ($options->count() < 2) {
                $errors[] = 'Add at least two choices.';
            }
            if ($options->where('is_correct', true)->count() !== 1) {
                $errors[] = 'Select exactly one correct choice.';
            }
        }

        if (in_array($this->question_type, ['fill_blank', 'short_answer'], true)
            && trim((string) $this->correct_answer) === '') {
            $errors[] = 'Enter the accepted answer.';
        }

        if ($this->question_type === 'true_false'
            && ! in_array(strtolower(trim((string) $this->correct_answer)), ['true', 'false'], true)) {
            $errors[] = 'Select True or False as the correct answer.';
        }

        if ($this->question_type === 'essay' && trim((string) $this->rubric_text) === '') {
            $errors[] = 'Enter a scoring rubric.';
        }

        return $errors;
    }

    public function isAuthoringComplete(): bool
    {
        return $this->authoringErrors() === [];
    }

    public function hasDraftContent(): bool
    {
        return (bool) ($this->authoring_touched ?? false)
            || $this->question_type !== 'unconfigured'
            || trim((string) $this->question_text) !== ''
            || trim((string) $this->correct_answer) !== ''
            || trim((string) $this->answer_explanation) !== ''
            || trim((string) $this->rubric_text) !== ''
            || trim((string) $this->image_path) !== ''
            || $this->loadedOptions()->isNotEmpty();
    }

    public function getAuthoringStatusAttribute(): string
    {
        if ($this->isAuthoringComplete()) {
            return 'complete';
        }

        return $this->hasDraftContent() ? 'in_progress' : 'not_started';
    }

    private function loadedOptions()
    {
        return $this->relationLoaded('options')
            ? $this->getRelation('options')
            : $this->options()->get();
    }
}
