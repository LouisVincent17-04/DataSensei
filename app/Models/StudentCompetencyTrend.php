<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCompetencyTrend extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'competency_id',
        'percentage',
        'evidence_count',
        'recorded_on',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'class_id' => 'integer',
        'competency_id' => 'integer',
        'percentage' => 'float',
        'evidence_count' => 'integer',
        'recorded_on' => 'date',
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
