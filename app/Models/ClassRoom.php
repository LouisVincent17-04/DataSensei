<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'instructor_id',
        'institution_id',
        'name',
        'section',
        'term',
        'academic_year',
        'description',
        'class_code',
        'subject_code',
        'max_students',
        'is_archived',
        'allow_self_enroll',
    ];

    protected $casts = [
        'is_archived'       => 'boolean',
        'allow_self_enroll' => 'boolean',
    ];

    // ─── Boot ────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (ClassRoom $class) {
            if (empty($class->class_code)) {
                $class->class_code = static::generateUniqueCode();
            }
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(7));
        } while (static::where('class_code', $code)->exists());

        return $code;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('is_archived', false)->orWhereNull('is_archived');
        });
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeForInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id')
                    ->withPivot('enrolled_at')
                    ->withTimestamps();
    }

    // Added for ModuleLibrary — links to class_module_assignments
    public function assignedModules(): HasMany
    {
        return $this->hasMany(ClassModuleAssignment::class, 'class_id');
    }

    public function assignmentPosts(): HasMany
    {
        return $this->hasMany(ClassAssignment::class, 'class_id');
    }

    public function competencySnapshots(): HasMany
    {
        return $this->hasMany(StudentCompetencySnapshot::class, 'class_id');
    }

    public function competencyTrends(): HasMany
    {
        return $this->hasMany(StudentCompetencyTrend::class, 'class_id');
    }

    public function modelDevelopmentRuns(): HasMany
    {
        return $this->hasMany(ModelDevelopmentRun::class, 'class_id');
    }

    public function mlDatasets(): HasMany
    {
        return $this->hasMany(UserDataset::class, 'class_id');
    }

    public function mlModels(): HasMany
    {
        return $this->hasMany(MlModel::class, 'class_id');
    }

    public function mlTrainingJobs(): HasMany
    {
        return $this->hasMany(TrainingJob::class, 'class_id');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getStudentCountAttribute(): int
    {
        if (array_key_exists('students_count', $this->attributes)) {
            return (int) $this->attributes['students_count'];
        }

        return $this->students()->count();
    }

    public function getIsFullAttribute(): bool
    {
        if (is_null($this->max_students)) {
            return false;
        }

        return $this->students()->count() >= $this->max_students;
    }
}
