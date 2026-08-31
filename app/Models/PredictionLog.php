<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionLog extends Model
{
    protected $fillable = [
        'model_version_id', 'user_id', 'input_values', 'predicted_value',
        'probabilities', 'explanation', 'latency_ms',
    ];

    protected function casts(): array
    {
        return [
            'model_version_id' => 'integer',
            'user_id' => 'integer',
            'input_values' => 'array',
            'probabilities' => 'array',
            'latency_ms' => 'integer',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(ModelVersion::class, 'model_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
