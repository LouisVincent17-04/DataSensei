<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MissionDefinition extends Model
{
    protected $fillable = [
        'mission_key',
        'title',
        'description',
        'period_type',
        'target_type',
        'target_count',
        'xp_reward',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'target_count' => 'integer',
        'xp_reward' => 'integer',
        'is_active' => 'boolean',
    ];

    public function progress(): HasMany
    {
        return $this->hasMany(StudentMissionProgress::class);
    }
}
