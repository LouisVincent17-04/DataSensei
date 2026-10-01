<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Challenge extends Model
{
    protected $fillable = [
        'challenge_category_id',
        'content_code',
        'title',
        'description',
        'time_limit_seconds',
        'base_xp',
        'order_index',
        'is_coding_challenge',
        'version_no',
        'version_name',
        'version_code',
        'is_active',
        'module_id',
        'created_by',
        'visibility',
    ];

    protected $casts = [
        'time_limit_seconds' => 'integer',
        'base_xp' => 'integer',
        'order_index' => 'integer',
        'is_coding_challenge' => 'boolean',
        'version_no' => 'integer',
        'is_active' => 'boolean',
        'module_id' => 'integer',
        'created_by' => 'integer',
    ];

    public const VISIBILITY_PLATFORM = 'platform';
    public const VISIBILITY_INSTRUCTOR = 'instructor';

    protected static function booted(): void
    {
        static::creating(function (Challenge $challenge): void {
            $challenge->version_no = max(1, (int) ($challenge->version_no ?: 1));
            $challenge->version_name = filled($challenge->version_name)
                ? trim((string) $challenge->version_name)
                : 'Version ' . $challenge->version_no;
            $challenge->version_code = filled($challenge->version_code)
                ? strtoupper(trim((string) $challenge->version_code))
                : 'V' . $challenge->version_no;
            $challenge->is_active = $challenge->is_active ?? true;

            if (blank($challenge->content_code)) {
                $type = $challenge->is_coding_challenge ? 'CODE' : 'MCQ';
                $identity = implode('|', [
                    (string) $challenge->challenge_category_id,
                    $type,
                    (string) $challenge->title,
                ]);
                $slug = strtoupper(Str::slug((string) $challenge->title, '-')) ?: 'CHALLENGE';
                $prefix = 'C' . (int) $challenge->challenge_category_id . '-' . $type . '-';
                $suffix = '-' . strtoupper(substr(sha1($identity), 0, 8));
                $available = max(1, 64 - strlen($prefix) - strlen($suffix));

                $challenge->content_code = $prefix . substr($slug, 0, $available) . $suffix;
            } else {
                $challenge->content_code = strtoupper(trim((string) $challenge->content_code));
            }

            // DataSensei Updates 12: core_module_key is never mass-assigned.
            // A new version of a built-in core challenge (same content code,
            // platform content) keeps the core identity; instructor-built
            // challenges and everything else are never core.
            if (\App\Support\SchemaInspector::hasColumn('challenges', 'core_module_key')) {
                $challenge->setAttribute('core_module_key', $challenge->isInstructorOwned() || $challenge->created_by
                    ? null
                    : DB::table('challenges')
                        ->where('content_code', $challenge->content_code)
                        ->whereNotNull('core_module_key')
                        ->value('core_module_key'));
            }
        });

        // The identity of a built-in core challenge (its content code, level
        // and type) stays fixed; its questions are changed through versions.
        static::updating(function (Challenge $challenge): void {
            if (blank($challenge->getOriginal('core_module_key'))) {
                return;
            }

            foreach (['content_code', 'challenge_category_id', 'is_coding_challenge', 'core_module_key', 'visibility', 'created_by'] as $field) {
                if ($challenge->isDirty($field) && (string) $challenge->getOriginal($field) !== (string) $challenge->getAttribute($field)) {
                    throw ValidationException::withMessages([
                        $field => 'This is a built-in Core Module challenge used by the core certificates; its content code, level and type cannot be changed. Create a new version to change its questions.',
                    ]);
                }
            }
        });

        // The last version of a core challenge cannot be removed, or the core
        // certificates would require something that no longer exists.
        static::deleting(function (Challenge $challenge): void {
            if (blank($challenge->getOriginal('core_module_key'))) {
                return;
            }

            $others = DB::table('challenges')
                ->where('content_code', $challenge->content_code)
                ->where('id', '!=', $challenge->id)
                ->exists();

            if (! $others) {
                throw ValidationException::withMessages([
                    'challenge' => 'This is the only version of a built-in Core Module challenge, so it cannot be deleted. Deactivate it instead.',
                ]);
            }
        });
    }

    public function isCoreChallenge(): bool
    {
        return filled($this->getAttribute('core_module_key'));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChallengeCategory::class, 'challenge_category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ChallengeQuestion::class)
            ->orderBy('order_index')
            ->orderBy('id');
    }

    public function codingQuestions(): HasMany
    {
        return $this->hasMany(CodingQuestion::class)->orderBy('order_index');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeMcq(Builder $query): Builder
    {
        return $query->where('is_coding_challenge', false);
    }

    public function scopeCoding(Builder $query): Builder
    {
        return $query->where('is_coding_challenge', true);
    }
    /** The public module this challenge was fanned out from, if any. */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    /** The instructor who built it; NULL for platform content. */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function classAssignments(): HasMany
    {
        return $this->hasMany(ClassChallengeAssignment::class, 'challenge_id');
    }

    public function isInstructorOwned(): bool
    {
        return $this->visibility === self::VISIBILITY_INSTRUCTOR;
    }

    /**
     * Whether finishing this challenge can award XP. Class work (an
     * instructor-built challenge or the University Student level) never does.
     */
    public function awardsXp(): bool
    {
        return \App\Services\XpPolicy::challengeAwardsXp($this);
    }

    /** Platform content only: what every learner on a level can see. */
    public function scopePlatform(Builder $query): Builder
    {
        return $query->where(function (Builder $builder): void {
            $builder->whereNull('visibility')->orWhere('visibility', self::VISIBILITY_PLATFORM);
        });
    }
}
