<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenchmarkModel extends Model
{
    protected $fillable = [
        'dataset_id', 'ml_model_id', 'model_version_id', 'algorithm_key',
        'benchmark_rank', 'is_primary', 'metrics',
    ];

    protected function casts(): array
    {
        return [
            'dataset_id' => 'integer',
            'ml_model_id' => 'integer',
            'model_version_id' => 'integer',
            'benchmark_rank' => 'integer',
            'is_primary' => 'boolean',
            'metrics' => 'array',
        ];
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(MlDataset::class, 'dataset_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(MlModel::class, 'ml_model_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(ModelVersion::class, 'model_version_id');
    }
}
