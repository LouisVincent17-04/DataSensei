<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntendedLearningOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_no',
        'ilo_code',
        'title',
        'description',
        'mastery_threshold',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'module_no' => 'integer',
        'mastery_threshold' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function questionMaps(): HasMany
    {
        return $this->hasMany(AssessmentQuestionIlo::class, 'ilo_id');
    }

    public function masteries(): HasMany
    {
        return $this->hasMany(StudentIloMastery::class, 'ilo_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
