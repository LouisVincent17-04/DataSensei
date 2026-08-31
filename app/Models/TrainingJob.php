<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingJob extends Model
{
    protected $fillable = [
        'user_id', 'class_id', 'dataset_id', 'user_dataset_id', 'dataset_version_id',
        'ml_model_id', 'uuid', 'model_name', 'problem_type', 'algorithm_key', 'status',
        'progress', 'stage', 'configuration', 'result', 'error_message', 'duration_ms',
        'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'class_id' => 'integer',
            'dataset_id' => 'integer',
            'user_dataset_id' => 'integer',
            'dataset_version_id' => 'integer',
            'ml_model_id' => 'integer',
            'progress' => 'integer',
            'configuration' => 'array',
            'result' => 'array',
            'duration_ms' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
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

    public function model(): BelongsTo
    {
        return $this->belongsTo(MlModel::class, 'ml_model_id');
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['completed', 'failed', 'cancelled'], true);
    }
}
