<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MlModel extends Model
{
    use SoftDeletes;

    protected $table = 'ml_models';

    protected $fillable = [
        'user_id', 'class_id', 'dataset_id', 'user_dataset_id', 'current_version_id',
        'identity_key', 'uuid', 'name', 'pipeline_type', 'problem_type', 'algorithm_key', 'status',
        'is_read_only', 'metadata',
    ];

    protected $hidden = ['identity_key'];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'class_id' => 'integer',
            'dataset_id' => 'integer',
            'user_dataset_id' => 'integer',
            'current_version_id' => 'integer',
            'is_read_only' => 'boolean',
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

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(MlDataset::class, 'dataset_id');
    }

    public function userDataset(): BelongsTo
    {
        return $this->belongsTo(UserDataset::class, 'user_dataset_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ModelVersion::class, 'ml_model_id')->latest('version_number');
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(ModelVersion::class, 'current_version_id');
    }

    public function trainingJobs(): HasMany
    {
        return $this->hasMany(TrainingJob::class, 'ml_model_id')->latest();
    }

    public function isSystemModel(): bool
    {
        return $this->pipeline_type === 'system';
    }
}
