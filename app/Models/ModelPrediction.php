<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelPrediction extends Model
{
    protected $fillable = [
        'model_development_run_id',
        'user_id',
        'input_values',
        'predicted_value',
        'probabilities',
        'explanation',
    ];

    protected function casts(): array
    {
        return [
            'model_development_run_id' => 'integer',
            'user_id' => 'integer',
            'input_values' => 'array',
            'probabilities' => 'array',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(ModelDevelopmentRun::class, 'model_development_run_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
