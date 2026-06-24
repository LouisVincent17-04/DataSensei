<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMissionProgress extends Model
{
    protected $table = 'student_mission_progress';

    protected $fillable = [
        'user_id',
        'mission_definition_id',
        'period_start',
        'progress_count',
        'is_completed',
        'completed_at',
        'xp_awarded',
    ];

    protected $casts = [
        'period_start' => 'date',
        'completed_at' => 'datetime',
        'progress_count' => 'integer',
        'is_completed' => 'boolean',
        'xp_awarded' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(MissionDefinition::class, 'mission_definition_id');
    }
}
