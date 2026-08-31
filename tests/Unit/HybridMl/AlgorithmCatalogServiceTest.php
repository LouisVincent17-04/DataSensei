<?php

namespace Tests\Unit\HybridMl;

use App\Services\HybridMl\AlgorithmCatalogService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AlgorithmCatalogServiceTest extends TestCase
{
    private array $profile = [
        'headers' => ['age', 'income', 'plan', 'churn'],
        'row_count' => 500,
        'numeric_columns' => ['age', 'income'],
        'categorical_columns' => ['plan', 'churn'],
        'columns' => [
            'age' => ['type' => 'integer', 'unique_count' => 55, 'is_identifier_like' => false],
            'income' => ['type' => 'decimal', 'unique_count' => 450, 'is_identifier_like' => false],
            'plan' => ['type' => 'categorical', 'unique_count' => 3, 'is_identifier_like' => false],
            'churn' => ['type' => 'boolean', 'unique_count' => 2, 'is_identifier_like' => false],
        ],
    ];

    public function test_it_normalizes_an_allowlisted_configuration(): void
    {
        $config = (new AlgorithmCatalogService())->normalizeTrainingConfiguration([
            'problem_type' => 'classification',
            'algorithm_key' => 'random_forest',
            'features' => ['age', 'income', 'plan'],
            'target_column' => 'churn',
            'test_size' => .20,
            'random_state' => 42,
            'cross_validation' => 5,
            'scale_mode' => 'auto',
            'numeric_imputation' => 'median',
            'remove_duplicates' => true,
            'parameters' => ['n_estimators' => 5000, 'max_depth' => 12],
        ], $this->profile);

        $this->assertSame('random_forest', $config['algorithm_key']);
        $this->assertSame(500, $config['parameters']['n_estimators']);
        $this->assertFalse($config['preprocessing']['apply_scaling']);
        $this->assertSame(5, $config['cross_validation']);
    }

    public function test_it_rejects_target_as_a_feature(): void
    {
        $this->expectException(ValidationException::class);
        (new AlgorithmCatalogService())->normalizeTrainingConfiguration([
            'problem_type' => 'classification',
            'algorithm_key' => 'logistic_regression',
            'features' => ['age', 'churn'],
            'target_column' => 'churn',
            'test_size' => .20,
            'random_state' => 42,
            'cross_validation' => 3,
        ], $this->profile);
    }
}
