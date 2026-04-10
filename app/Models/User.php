<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    const ROLE_STUDENT = 1;
    const ROLE_INSTRUCTOR = 2;
    const ROLE_ADMIN = 3;
    const ROLE_SUPERADMIN = 4;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'xp',
        'streak',
        'last_activity',
        'bio',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isStudent()
    {
        return $this->role == self::ROLE_STUDENT;
    }

    public function isInstructor()
    {
        return $this->role == self::ROLE_INSTRUCTOR;
    }

    public function isAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->wherePivot('is_completed', true);
    }
    

    public function isSuperAdmin()
    {
        return $this->role == self::ROLE_SUPERADMIN;
    }
}
