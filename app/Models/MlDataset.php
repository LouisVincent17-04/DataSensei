<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MlDataset extends Model
{
    protected $table = 'datasets';

    protected $hidden = ['storage_path'];

    protected $fillable = [
        'slug', 'name', 'description', 'source_name', 'source_url', 'license_name',
        'storage_path', 'row_count', 'column_count', 'target_column', 'problem_type',
        'feature_list', 'metadata', 'version_label', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'row_count' => 'integer',
            'column_count' => 'integer',
            'feature_list' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DatasetVersion::class, 'dataset_id');
    }

    public function models(): HasMany
    {
        return $this->hasMany(MlModel::class, 'dataset_id');
    }

    public function benchmarks(): HasMany
    {
        return $this->hasMany(BenchmarkModel::class, 'dataset_id');
    }

    public function qualityReports(): HasMany
    {
        return $this->hasMany(QualityReport::class, 'dataset_id');
    }
}
