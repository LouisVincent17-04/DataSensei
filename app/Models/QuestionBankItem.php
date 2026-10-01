<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One reusable question in an instructor's Question Bank (DataSensei
 * Updates 11). A row with a NULL instructor belongs to the shared pool that
 * every instructor can copy from but nobody edits. Adding a bank question to
 * an assessment copies it as a snapshot, so later edits or archiving here
 * never change a published assessment, an attempt, or a grade.
 */
class QuestionBankItem extends Model
{
    /** The question types the assessment system can display and grade. */
    public const TYPES = AssessmentQuestion::TYPES;

    /**
     * Bloom's six thinking levels. A Table of Specifications plans with the
     * first four under the same labels, so a tagged question can match a
     * planned item; Evaluate and Create are for assessments without a TOS.
     */
    public const THINKING_LEVELS = ['Remember', 'Understand', 'Apply', 'Analyze', 'Evaluate', 'Create'];

    public const DIFFICULTIES = [
        'newbie' => 'Newbie',
        'university-student' => 'University student',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'professional' => 'Professional',
    ];

    protected $fillable = [
        'instructor_id',
        'module_no',
        'topic_title',
        'question_type',
        'question_text',
        'correct_answer',
        'answer_explanation',
        'rubric_text',
        'points',
        'difficulty_slug',
        'ilo_id',
        'bloom_level',
        'is_archived',
    ];

    protected $casts = [
        'module_no' => 'integer',
        'points' => 'integer',
        'ilo_id' => 'integer',
        'is_archived' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(QuestionBankOption::class)->orderBy('order_index');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function ilo(): BelongsTo
    {
        return $this->belongsTo(IntendedLearningOutcome::class, 'ilo_id');
    }

    /** The instructor's own questions plus the shared pool. */
    public function scopeVisibleTo(Builder $query, int $instructorId): Builder
    {
        return $query->where(function (Builder $q) use ($instructorId): void {
            $q->where('instructor_id', $instructorId)->orWhereNull('instructor_id');
        });
    }

    public function isShared(): bool
    {
        return $this->instructor_id === null;
    }

    public function isEditableBy(int $instructorId): bool
    {
        return (int) $this->instructor_id === $instructorId;
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->question_type] ?? ucfirst(str_replace('_', ' ', (string) $this->question_type));
    }
}
