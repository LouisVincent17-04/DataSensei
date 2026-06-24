<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPerformanceSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'average_score_percent',
        'average_time_ratio',
        'completed_activities',
        'missing_assignments',
        'late_submissions',
        'anti_cheat_warnings',
        'engagement_score',
        'cluster_label',
        'risk_level',
        'generated_at',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'class_id' => 'integer',
        'average_score_percent' => 'float',
        'average_time_ratio' => 'float',
        'completed_activities' => 'integer',
        'missing_assignments' => 'integer',
        'late_submissions' => 'integer',
        'anti_cheat_warnings' => 'integer',
        'engagement_score' => 'float',
        'generated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
