<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementDefinition extends Model
{
    protected $fillable = [
        'achievement_key',
        'name',
        'description',
        'icon',
        'badge_color',
        'xp_reward',
        'criteria_type',
        'criteria_value',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'xp_reward' => 'integer',
        'criteria_value' => 'integer',
    ];

    public function unlocks(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }
}
