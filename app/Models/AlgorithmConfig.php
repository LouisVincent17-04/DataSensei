<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlgorithmConfig extends Model
{
    protected $fillable = [
        'algorithm_key', 'problem_type', 'label', 'description', 'strengths', 'weaknesses',
        'when_not_to_use', 'expected_training_time', 'default_parameters', 'parameter_schema',
        'supports_probability', 'supports_feature_importance', 'is_optional', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'strengths' => 'array',
            'weaknesses' => 'array',
            'when_not_to_use' => 'array',
            'default_parameters' => 'array',
            'parameter_schema' => 'array',
            'supports_probability' => 'boolean',
            'supports_feature_importance' => 'boolean',
            'is_optional' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
