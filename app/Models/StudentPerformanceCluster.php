<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPerformanceCluster extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'cluster_label',
        'cluster_description',
        'average_score_percent',
        'engagement_score',
        'risk_level',
        'assigned_at',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'class_id' => 'integer',
        'average_score_percent' => 'float',
        'engagement_score' => 'float',
        'assigned_at' => 'datetime',
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
