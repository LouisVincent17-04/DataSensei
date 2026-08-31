<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'dedupe_key',
        'title',
        'notification_text',
        'action_url',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayTitleAttribute(): string
    {
        if (filled($this->title)) {
            return (string) $this->title;
        }

        return match (true) {
            Str::startsWith($this->type, 'achievement_') => 'Achievement unlocked',
            Str::startsWith($this->type, 'mission_') => 'Mission completed',
            Str::contains($this->type, 'assignment') => 'Assignment update',
            Str::contains($this->type, 'assessment') => 'Assessment update',
            Str::contains($this->type, 'class_') => 'Class update',
            Str::contains($this->type, 'module_') => 'Module update',
            default => 'Notification',
        };
    }

    public function getCategoryAttribute(): string
    {
        return match (true) {
            Str::contains($this->type, 'assignment') => 'assignment',
            Str::contains($this->type, 'assessment') => 'assessment',
            Str::contains($this->type, ['achievement', 'mission', 'rank']) => 'achievement',
            Str::contains($this->type, ['challenge', 'exceptional_unlock']) => 'challenge',
            Str::contains($this->type, ['class_', 'enrollment']) => 'class',
            Str::contains($this->type, 'module') => 'module',
            default => 'general',
        };
    }

    public function markRead(): void
    {
        if ($this->is_read) {
            return;
        }

        $this->forceFill(['is_read' => true, 'read_at' => now()])->save();
    }

    public function markUnread(): void
    {
        if (! $this->is_read) {
            return;
        }

        $this->forceFill(['is_read' => false, 'read_at' => null])->save();
    }
}
