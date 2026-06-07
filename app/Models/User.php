<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    const ROLE_USER       = 1;
    const ROLE_ADMIN      = 2;
    const ROLE_SUPERADMIN = 3;
    const ROLE_INSTRUCTOR = 4; 
    const ROLE_INSTITUTION_ADMIN = 5;

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
            'password'          => 'hashed',
        ];
    }

    // ─── ROLE CHECKERS ─────────────────────────────────────────

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isInstructor(): bool // ← added
    {
        return $this->role === self::ROLE_INSTRUCTOR;
    }

    public function isInstitutionAdmin(): bool // ← added
    {
        return $this->role === self::ROLE_INSTITUTION_ADMIN;
    }

    // ─── RELATIONSHIPS ──────────────────────────────────────────

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_user')
                    ->withPivot('is_unlocked', 'is_completed')
                    ->withTimestamps();
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
                    ->withPivot('is_completed')
                    ->withTimestamps();
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
                    ->withPivot('is_completed')
                    ->wherePivot('is_completed', 1)
                    ->withTimestamps();
    }

    public function instructorApplications() // ← added
    {
        return $this->hasMany(InstructorApplication::class);
    }


    public function classesAsStudent()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_id')
                    ->withPivot('enrolled_at')
                    ->withTimestamps();
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }
}