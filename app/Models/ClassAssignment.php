<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'assignment_library_item_id',
        'assigned_by',
        'title',
        'instructions',
        'available_at',
        'due_at',
        'max_attempts',
        'status',
        'assigned_at',
    ];

    protected $casts = [
        'available_at' => 'datetime',
        'due_at' => 'datetime',
        'assigned_at' => 'datetime',
        'max_attempts' => 'integer',
    ];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function libraryItem(): BelongsTo
    {
        return $this->belongsTo(AssignmentLibraryItem::class, 'assignment_library_item_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'class_assignment_id');
    }

    public function scopeVisibleToStudents($query)
    {
        return $query->whereIn('status', ['published', 'closed'])
            ->where(function ($q) {
                $q->whereNull('available_at')
                  ->orWhere('available_at', '<=', now());
            });
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getIsDueAttribute(): bool
    {
        return $this->due_at && now()->greaterThan($this->due_at);
    }
}
