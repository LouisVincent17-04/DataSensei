<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelDevelopmentRun extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'dataset_key',
        'task_type',
        'algorithm',
        'feature_columns',
        'target_column',
        'test_size',
        'random_seed',
        'preprocessing',
        'parameters',
        'status',
        'metrics',
        'visualization_data',
        'training_summary',
        'error_message',
        'duration_ms',
        'trained_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'class_id' => 'integer',
            'feature_columns' => 'array',
            'test_size' => 'float',
            'random_seed' => 'integer',
            'preprocessing' => 'array',
            'parameters' => 'array',
            'metrics' => 'array',
            'visualization_data' => 'array',
            'training_summary' => 'array',
            'duration_ms' => 'integer',
            'trained_at' => 'datetime',
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

    public function predictions(): HasMany
    {
        return $this->hasMany(ModelPrediction::class)->latest();
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
