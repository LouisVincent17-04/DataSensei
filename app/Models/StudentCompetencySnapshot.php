<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCompetencySnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'competency_id',
        'percentage',
        'evidence_count',
        'level',
        'source_breakdown',
        'last_evidence_at',
        'calculated_at',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'class_id' => 'integer',
        'competency_id' => 'integer',
        'percentage' => 'float',
        'evidence_count' => 'integer',
        'source_breakdown' => 'array',
        'last_evidence_at' => 'datetime',
        'calculated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }
}
