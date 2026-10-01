<?php

namespace App\Models;

use App\Support\CoreCurriculum;
use App\Support\SchemaInspector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * A public DataSensei module: open to every user, with or without a class,
 * organised by year level. Class modules are a different library
 * (ModuleLibraryItem, assigned to classes by instructors).
 *
 * DataSensei Updates 12: the 24 Core Modules carry module_type 'core' and a
 * permanent module_key (App\Support\CoreCurriculum); every other module is
 * 'custom', which is also what a new module gets. Neither column can be
 * mass-assigned. For a core module the title, module_key and module_type
 * cannot be changed and the module can never be deleted; its content is still
 * edited and it can still be published or unpublished, which keeps progress
 * and certificates. These rules are enforced here, under every controller.
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
        'archived_at' => 'datetime',
    ];

    /** Fields that make up a core module's identity. */
    public const CORE_IDENTITY_FIELDS = ['title', 'module_key', 'module_type'];

    protected static function booted(): void
    {
        static::creating(function (Module $module): void {
            if (SchemaInspector::hasColumn('modules', 'module_type') && blank($module->getAttribute('module_type'))) {
                $module->setAttribute('module_type', CoreCurriculum::TYPE_CUSTOM);
            }
        });

        static::updating(function (Module $module): void {
            if ($module->getOriginal('module_type') !== CoreCurriculum::TYPE_CORE) {
                // A custom module cannot take a core identity through a save
                // either; only CoreCurriculum::sync() marks core modules.
                if ($module->isDirty('module_type') && $module->getAttribute('module_type') === CoreCurriculum::TYPE_CORE) {
                    throw ValidationException::withMessages(['module_type' => 'Only the 24 Core Modules are core modules.']);
                }

                return;
            }

            foreach (self::CORE_IDENTITY_FIELDS as $field) {
                if ($module->isDirty($field) && (string) $module->getOriginal($field) !== (string) $module->getAttribute($field)) {
                    throw ValidationException::withMessages([
                        $field => $field === 'title'
                            ? 'The title of a Core Module cannot be changed. Edit its content instead.'
                            : 'The identity of a Core Module cannot be changed.',
                    ]);
                }
            }
        });

        static::deleting(function (Module $module): void {
            if ($module->isCore() || $module->getOriginal('module_type') === CoreCurriculum::TYPE_CORE) {
                throw ValidationException::withMessages([
                    'module' => 'Core Modules cannot be deleted. Unpublish it to hide it from learners; their progress and certificates are kept.',
                ]);
            }
        });
    }

    public function isCore(): bool
    {
        return $this->getAttribute('module_type') === CoreCurriculum::TYPE_CORE;
    }

    public function isArchived(): bool
    {
        return $this->getAttribute('archived_at') !== null;
    }

    public function typeLabel(): string
    {
        return $this->isCore() ? 'Core / System' : 'Custom';
    }

    public function scopeCore(Builder $query): Builder
    {
        return $query->where('modules.module_type', CoreCurriculum::TYPE_CORE);
    }

    public function scopeCustom(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q->whereNull('modules.module_type')->orWhere('modules.module_type', '!=', CoreCurriculum::TYPE_CORE));
    }

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
