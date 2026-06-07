<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentLibraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_no',
        'assignment_code',
        'title',
        'topic_title',
        'year_level',
        'assignment_type',
        'version_no',
        'version_name',
        'version_code',
        'description',
        'instructions',
        'time_limit_minutes',
        'total_points',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'module_no' => 'integer',
        'version_no' => 'integer',
        'time_limit_minutes' => 'integer',
        'total_points' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(AssignmentQuestion::class)
            ->orderBy('order_index');
    }

    public function classAssignments(): HasMany
    {
        return $this->hasMany(ClassAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->assignment_type) {
            'mcq' => 'MCQ',
            'fill_blank' => 'Fill in the Blanks',
            'mixed' => 'Mixed',
            default => ucfirst(str_replace('_', ' ', (string) $this->assignment_type)),
        };
    }
}
