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

    public function test_a_beginner_only_has_to_choose_the_answer_and_the_clues(): void
    {
        $catalog = new AlgorithmCatalogService();
        $input = $catalog->applyBeginnerDefaults([
            'model_name' => 'My first model',
            'features' => ['age', 'income', 'plan'],
            'target_column' => 'churn',
        ], $this->profile);
        $config = $catalog->normalizeTrainingConfiguration($input, $this->profile);

        $this->assertSame('classification', $config['problem_type']);
        $this->assertSame('auto_classification', $config['algorithm_key']);
        $this->assertTrue($config['tune']);
        $this->assertSame(0.2, $config['test_size']);
        $this->assertSame(42, $config['random_state']);
        $this->assertSame(5, $config['cross_validation']);
        $this->assertSame('auto', $config['preprocessing']['scale_mode']);
        $this->assertGreaterThanOrEqual(10, $config['search_budget_seconds']);
    }

    public function test_the_kind_of_problem_is_worked_out_from_the_answer_column(): void
    {
        $catalog = new AlgorithmCatalogService();
        $profile = [
            'row_count' => 500,
            'columns' => [
                'label' => ['type' => 'categorical', 'unique_count' => 3],
                'flag' => ['type' => 'integer', 'unique_count' => 2],
                'rating' => ['type' => 'integer', 'unique_count' => 5],
                'price' => ['type' => 'decimal', 'unique_count' => 480],
            ],
        ];

        $this->assertSame('classification', $catalog->inferProblemType('label', $profile));
        $this->assertSame('classification', $catalog->inferProblemType('flag', $profile));
        $this->assertSame('classification', $catalog->inferProblemType('rating', $profile));
        $this->assertSame('regression', $catalog->inferProblemType('price', $profile));
        $this->assertSame('clustering', $catalog->inferProblemType('', $profile));
        $this->assertSame('clustering', $catalog->inferProblemType('missing', $profile));
    }

    public function test_a_named_algorithm_is_fine_tuned_unless_the_student_opts_out(): void
    {
        $catalog = new AlgorithmCatalogService();
        $base = [
            'problem_type' => 'classification',
            'algorithm_key' => 'random_forest',
            'features' => ['age', 'income'],
            'target_column' => 'churn',
        ];

        $tuned = $catalog->normalizeTrainingConfiguration($catalog->applyBeginnerDefaults($base, $this->profile), $this->profile);
        $manual = $catalog->normalizeTrainingConfiguration($catalog->applyBeginnerDefaults($base + ['tune' => '0'], $this->profile), $this->profile);
        $automatic = $catalog->normalizeTrainingConfiguration(
            $catalog->applyBeginnerDefaults(['algorithm_key' => 'auto_classification', 'tune' => '0'] + $base, $this->profile),
            $this->profile
        );

        $this->assertTrue($tuned['tune']);
        $this->assertFalse($manual['tune']);
        $this->assertTrue($automatic['tune'], 'Automatic always compares candidates.');
    }

    public function test_automatic_entries_exist_for_answers_but_not_for_grouping(): void
    {
        $catalog = new AlgorithmCatalogService();

        $this->assertSame('auto_classification', $catalog->automaticKey('classification'));
        $this->assertSame('auto_regression', $catalog->automaticKey('regression'));
        $this->assertNull($catalog->automaticKey('clustering'));
        $this->assertSame('Automatic (best of several)', $catalog->label('auto_regression'));

        $this->expectException(ValidationException::class);
        $catalog->normalizeTrainingConfiguration([
            'problem_type' => 'regression',
            'algorithm_key' => 'auto_classification',
            'features' => ['age'],
            'target_column' => 'income',
            'test_size' => .20,
            'random_state' => 42,
            'cross_validation' => 5,
        ], $this->profile);
    }
}
