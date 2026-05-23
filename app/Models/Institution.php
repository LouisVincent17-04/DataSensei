<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'logo_path',
        'address',
        'contact_number',
        'website',
        'status',
        'notes',
        'institution_code', // ← added
    ];

    // ── Auto-generate slug + institution code on creation ────────────────────
    protected static function booted(): void
    {
        static::creating(function (Institution $institution) {
            if (empty($institution->slug)) {
                $institution->slug = Str::slug($institution->name);
            }

            // ← added
            if (empty($institution->institution_code)) {
                $institution->institution_code = static::generateUniqueCode();
            }
        });
    }

    // ← added
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (static::where('institution_code', $code)->exists());

        return $code;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ← added
    public function instructorApplications(): HasMany
    {
        return $this->hasMany(InstructorApplication::class);
    }

    // ← added
    public function pendingApplications(): HasMany
    {
        return $this->hasMany(InstructorApplication::class)->where('status', 'pending');
    }

    // ← added
    public function approvedApplications(): HasMany
    {
        return $this->hasMany(InstructorApplication::class)->where('status', 'approved');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', 'disabled');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getStudentCountAttribute(): int
    {
        return $this->users()->where('role', User::ROLE_USER)->count();
    }

    public function getAdminCountAttribute(): int
    {
        return $this->users()->where('role', User::ROLE_ADMIN)->count();
    }
}