<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityReport extends Model
{
    protected $fillable = [
        'dataset_id', 'user_dataset_id', 'dataset_version_id', 'user_id',
        'quality_score', 'summary', 'column_analysis', 'recommendations', 'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'dataset_id' => 'integer',
            'user_dataset_id' => 'integer',
            'dataset_version_id' => 'integer',
            'user_id' => 'integer',
            'quality_score' => 'float',
            'summary' => 'array',
            'column_analysis' => 'array',
            'recommendations' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function getGradeAttribute(): string
    {
        return match (true) {
            $this->quality_score >= 90 => 'Excellent',
            $this->quality_score >= 80 => 'Good',
            $this->quality_score >= 70 => 'Acceptable',
            $this->quality_score >= 55 => 'Needs cleaning',
            default => 'Poor',
        };
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(MlDataset::class, 'dataset_id');
    }

    public function userDataset(): BelongsTo
    {
        return $this->belongsTo(UserDataset::class, 'user_dataset_id');
    }

    public function datasetVersion(): BelongsTo
    {
        return $this->belongsTo(DatasetVersion::class, 'dataset_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
