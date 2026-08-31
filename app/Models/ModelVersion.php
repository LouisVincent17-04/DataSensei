<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelVersion extends Model
{
    protected $hidden = ['artifact_path', 'metadata_path'];

    protected $fillable = [
        'ml_model_id', 'training_job_id', 'dataset_version_id', 'created_by',
        'version_number', 'version_label', 'artifact_path', 'metadata_path', 'metrics',
        'hyperparameters', 'feature_names', 'target_column', 'visualizations', 'explanations',
        'training_time_ms', 'python_version', 'sklearn_version', 'status', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'ml_model_id' => 'integer',
            'training_job_id' => 'integer',
            'dataset_version_id' => 'integer',
            'created_by' => 'integer',
            'version_number' => 'integer',
            'metrics' => 'array',
            'hyperparameters' => 'array',
            'feature_names' => 'array',
            'visualizations' => 'array',
            'explanations' => 'array',
            'training_time_ms' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(MlModel::class, 'ml_model_id');
    }

    public function trainingJob(): BelongsTo
    {
        return $this->belongsTo(TrainingJob::class, 'training_job_id');
    }

    public function datasetVersion(): BelongsTo
    {
        return $this->belongsTo(DatasetVersion::class, 'dataset_version_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(PredictionLog::class, 'model_version_id')->latest();
    }
}
