<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A public DataSensei module: open to every user, with or without a class,
 * organised by year level. Class modules are a different library
 * (ModuleLibraryItem, assigned to classes by instructors).
 */
class Module extends Model
{
    public const YEAR_LEVELS = ['Year 1', 'Year 2', 'Year 3', 'Year 4'];

    protected $fillable = [
        'title',
        'description',
        'order_index',
        'year_level',
        'xp_reward',
        'is_boss',
        'has_coding_exercises',
        'learning_outcomes',
        'review_questions',
        'is_published',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'xp_reward' => 'integer',
        'is_boss' => 'boolean',
        'has_coding_exercises' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order_index', 'asc');
    }

    /** The challenges fanned out from this module, one per level. */
    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class, 'module_id');
    }

    /** Modules learners can see. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('modules.is_published', true);
    }

    /**
     * Intended learning outcomes (DataSensei Updates 5): plain sentences shown
     * to learners as "What You Will Learn". Purely descriptive.
     *
     * @return list<string>
     */
    public function getLearningOutcomesAttribute(mixed $value): array
    {
        return ModuleLibraryItem::cleanOutcomes(self::decodeList($value));
    }

    public function setLearningOutcomesAttribute(mixed $value): void
    {
        $outcomes = ModuleLibraryItem::cleanOutcomes(is_array($value) ? $value : self::decodeList($value));
        $this->attributes['learning_outcomes'] = json_encode($outcomes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }

    /**
     * Embedded review questions: the same shape as a library module's
     * mcq_questions (question, scenario, choices, answer, explanation, ...).
     * Instructional only, never scored.
     *
     * @return list<array<string, mixed>>
     */
    public function getReviewQuestionsAttribute(mixed $value): array
    {
        return array_values(array_filter(self::decodeList($value), 'is_array'));
    }

    public function setReviewQuestionsAttribute(mixed $value): void
    {
        $questions = array_values(array_filter(is_array($value) ? $value : self::decodeList($value), 'is_array'));
        $this->attributes['review_questions'] = json_encode($questions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }

    private static function decodeList(mixed $value): array
    {
        $decoded = $value;

        for ($attempt = 0; $attempt < 3 && is_string($decoded); $attempt++) {
            $trimmed = trim($decoded);
            if ($trimmed === '') {
                return [];
            }
            $decoded = json_decode($trimmed, true);
        }

        return is_array($decoded) ? $decoded : [];
    }
}
