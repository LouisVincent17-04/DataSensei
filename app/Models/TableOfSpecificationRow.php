<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableOfSpecificationRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_of_specification_id',
        'ilo_id',
        'topic_title',
        'difficulty_slug',
        'item_count',
        'cognitive_level',
    ];

    protected $casts = [
        'table_of_specification_id' => 'integer',
        'ilo_id' => 'integer',
        'item_count' => 'integer',
    ];

    public function tableOfSpecification(): BelongsTo
    {
        return $this->belongsTo(TableOfSpecification::class, 'table_of_specification_id');
    }

    public function ilo(): BelongsTo
    {
        return $this->belongsTo(IntendedLearningOutcome::class, 'ilo_id');
    }
}
