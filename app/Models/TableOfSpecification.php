<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableOfSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'module_no',
        'custom_coverage',
        'total_items',
        'title',
        'status',
        'cognitive_distribution',
        'created_by',
    ];

    protected $casts = [
        'class_id' => 'integer',
        'module_no' => 'integer',
        'total_items' => 'integer',
        'cognitive_distribution' => 'array',
        'created_by' => 'integer',
    ];

    public function getCoverageLabelAttribute(): string
    {
        $customCoverage = trim((string) $this->custom_coverage);

        return $customCoverage !== ''
            ? $customCoverage
            : 'Module ' . $this->module_no;
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(TableOfSpecificationRow::class, 'table_of_specification_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'table_of_specification_id');
    }
}
