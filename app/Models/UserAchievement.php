<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_definition_id',
        'unlocked_at',
        'trigger_source',
        'source_id',
        'progress_value',
        'details',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'details' => 'array',
        'progress_value' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(AchievementDefinition::class, 'achievement_definition_id');
    }
}
