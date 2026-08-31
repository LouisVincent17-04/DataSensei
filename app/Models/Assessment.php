<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_of_specification_id',
        'class_id',
        'created_by',
        'title',
        'description',
        'instructions',
        'status',
        'draft_last_item',
        'draft_saved_at',
        'total_items',
        'total_points',
        'time_limit_minutes',
        'max_attempts',
        'available_at',
        'due_at',
        'published_at',
    ];

    protected $casts = [
        'total_items' => 'integer',
        'total_points' => 'integer',
        'draft_last_item' => 'integer',
        'draft_saved_at' => 'datetime',
        'time_limit_minutes' => 'integer',
        'max_attempts' => 'integer',
        'available_at' => 'datetime',
        'due_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function tos(): BelongsTo
    {
        return $this->belongsTo(TableOfSpecification::class, 'table_of_specification_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('item_number');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssessmentSubmission::class);
    }

    public function diagnostics(): HasMany
    {
        return $this->hasMany(StudentAssessmentDiagnostic::class);
    }

    public function scopeVisibleToStudents($query)
    {
        return $query->whereIn('status', ['published', 'closed'])
            ->where(function ($q) {
                $q->whereNull('available_at')->orWhere('available_at', '<=', now());
            });
    }
}
