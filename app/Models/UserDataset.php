<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserDataset extends Model
{
    use SoftDeletes;

    protected $hidden = ['storage_path', 'stored_filename'];

    protected $fillable = [
        'user_id', 'class_id', 'uuid', 'name', 'original_filename', 'stored_filename',
        'mime_type', 'storage_path', 'file_size', 'row_count', 'column_count',
        'target_column', 'problem_type', 'status', 'quality_score', 'schema_profile', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'class_id' => 'integer',
            'file_size' => 'integer',
            'row_count' => 'integer',
            'column_count' => 'integer',
            'quality_score' => 'float',
            'schema_profile' => 'array',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DatasetVersion::class, 'user_dataset_id');
    }

    public function models(): HasMany
    {
        return $this->hasMany(MlModel::class, 'user_dataset_id');
    }

    public function qualityReports(): HasMany
    {
        return $this->hasMany(QualityReport::class, 'user_dataset_id')->latest('generated_at');
    }

    public function latestVersion(): ?DatasetVersion
    {
        return $this->versions()->latest('version_number')->first();
    }
}
