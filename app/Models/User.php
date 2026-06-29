<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const ROLE_USER = 1;
    public const ROLE_ADMIN = 2;
    public const ROLE_SUPERADMIN = 3;
    public const ROLE_INSTRUCTOR = 4;
    public const ROLE_INSTITUTION_ADMIN = 5;

    public const ROLE_LABELS = [
        self::ROLE_USER => 'Learner / Common User',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SUPERADMIN => 'Superadmin',
        self::ROLE_INSTRUCTOR => 'Instructor',
        self::ROLE_INSTITUTION_ADMIN => 'Institution Admin',
    ];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'institution_id',
        'xp',
        'streak',
        'last_activity',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
            'institution_id' => 'integer',
            'xp' => 'integer',
            'streak' => 'integer',
            'last_activity' => 'datetime',
        ];
    }

    public function isUser(): bool
    {
        return (int) $this->role === self::ROLE_USER;
    }

    public function isLearner(): bool
    {
        return $this->isUser();
    }

    public function isAdmin(): bool
    {
        return (int) $this->role === self::ROLE_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return (int) $this->role === self::ROLE_SUPERADMIN;
    }

    public function isInstructor(): bool
    {
        return (int) $this->role === self::ROLE_INSTRUCTOR;
    }

    public function isInstitutionAdmin(): bool
    {
        return (int) $this->role === self::ROLE_INSTITUTION_ADMIN;
    }

    public function isPlatformStaff(): bool
    {
        return in_array((int) $this->role, [self::ROLE_ADMIN, self::ROLE_SUPERADMIN], true);
    }

    public function roleName(): string
    {
        return self::ROLE_LABELS[(int) $this->role] ?? 'Unknown Role';
    }

    public function getRoleNameAttribute(): string
    {
        return $this->roleName();
    }

    public function getIsActiveAttribute(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function getLearnerStateAttribute(): string
    {
        if (! $this->isLearner()) {
            return $this->roleName();
        }

        return $this->classesAsStudent()->exists() ? 'Student' : 'Common User';
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'module_user')
            ->withPivot('is_unlocked', 'is_completed')
            ->withTimestamps();
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withPivot('is_completed')
            ->withTimestamps();
    }

    public function completedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withPivot('is_completed')
            ->wherePivot('is_completed', 1)
            ->withTimestamps();
    }

    public function instructorApplications(): HasMany
    {
        return $this->hasMany(InstructorApplication::class);
    }

    public function passwordResetOtps(): HasMany
    {
        return $this->hasMany(PasswordResetOtp::class);
    }

    public function classesAsStudent(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_id')
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class, 'user_id');
    }

    public function achievedBadges(): BelongsToMany
    {
        return $this->belongsToMany(AchievementDefinition::class, 'user_achievements', 'user_id', 'achievement_definition_id')
            ->withPivot('unlocked_at', 'trigger_source', 'source_id', 'progress_value')
            ->withTimestamps();
    }

    public function missionProgress(): HasMany
    {
        return $this->hasMany(StudentMissionProgress::class, 'user_id');
    }

    public function dataToolkitActivities(): HasMany
    {
        return $this->hasMany(StudentDataToolkitActivity::class, 'user_id');
    }

    public function currentRank(): ?Rank
    {
        return Rank::currentForXp((int) ($this->xp ?? 0));
    }

    public function nextRank(): ?Rank
    {
        return Rank::nextForXp((int) ($this->xp ?? 0));
    }

    public function rankProgressPercent(): float
    {
        $currentRank = $this->currentRank();
        $nextRank = $this->nextRank();

        if (! $currentRank) {
            return 0.0;
        }

        if (! $nextRank) {
            return 100.0;
        }

        $currentFloor = (int) $currentRank->exp_required;
        $nextFloor = (int) $nextRank->exp_required;
        $range = max(1, $nextFloor - $currentFloor);
        $earnedInRange = max(0, (int) ($this->xp ?? 0) - $currentFloor);

        return round(min(100, ($earnedInRange / $range) * 100), 2);
    }
}
