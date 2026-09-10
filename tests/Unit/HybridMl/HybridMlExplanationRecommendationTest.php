<?php

namespace Tests\Unit\HybridMl;

use App\Models\MlDataset;
use App\Services\HybridMl\AlgorithmCatalogService;
use App\Services\HybridMl\DatasetProfiler;
use App\Services\HybridMl\EducationalExplanationService;
use App\Services\HybridMl\PredefinedDatasetRecommendationService;
use Tests\TestCase;

class HybridMlExplanationRecommendationTest extends TestCase
{
    public function test_dataset_profiler_detects_types_target_problem_and_identifier_columns(): void
    {
        $rows = [];
        for ($index = 1; $index <= 30; $index++) {
            $rows[] = [
                'customer_id' => 'C-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'age' => 20 + $index,
                'income' => (string) (15000.5 + ($index * 100.25)),
                'joined_on' => '2026-08-'.str_pad((string) (($index - 1) % 28 + 1), 2, '0', STR_PAD_LEFT),
                'plan' => $index % 2 === 0 ? 'premium' : 'basic',
                'churn' => $index % 3 === 0 ? 'yes' : 'no',
            ];
        }

        $profile = (new DatasetProfiler())->profile(array_keys($rows[0]), $rows);

        $this->assertSame(30, $profile['row_count']);
        $this->assertSame(6, $profile['column_count']);
        $this->assertSame('integer', $profile['columns']['age']['type']);
        $this->assertSame('decimal', $profile['columns']['income']['type']);
        $this->assertSame('date', $profile['columns']['joined_on']['type']);
        $this->assertSame('categorical', $profile['columns']['plan']['type']);
        $this->assertSame('boolean', $profile['columns']['churn']['type']);
        $this->assertTrue($profile['columns']['customer_id']['is_identifier_like']);
        $this->assertSame('churn', $profile['suggested_target']);
        $this->assertSame('classification', $profile['detected_problem_type']);
        $this->assertContains('age', $profile['numeric_columns']);
        $this->assertContains('joined_on', $profile['date_columns']);
    }

    public function test_educational_explanations_cover_data_metrics_and_model_tradeoffs(): void
    {
        $service = new EducationalExplanationService();
        $items = $service->explain(
            'classification',
            'random_forest',
            ['accuracy' => 92, 'f1' => 84, 'roc_auc' => 0.95],
            [
                'rows' => 80,
                'missing_percent' => 8.5,
                'duplicate_rows' => 7,
                'class_balance' => ['imbalance_ratio' => 0.30],
            ],
            ['rows_used' => 80, 'train_rows' => 60, 'test_rows' => 20]
        );
        $explanation = implode(' ', $items);

        $this->assertStringContainsString('small dataset', $explanation);
        $this->assertStringContainsString('held-out test rows', $explanation);
        $this->assertStringContainsString('classes are imbalanced', $explanation);
        $this->assertStringContainsString('ROC AUC is high', $explanation);
        $this->assertStringContainsString('Random Forest', $explanation);
        $this->assertStringContainsString('missing values', $explanation);
        $this->assertStringContainsString('duplicate rows', $explanation);

        $poorRegression = implode(' ', $service->explain(
            'regression',
            'linear_regression',
            ['r2' => -0.2, 'rmse' => 12.5],
            ['rows' => 500],
            ['rows_used' => 500, 'train_rows' => 400, 'test_rows' => 100]
        ));
        $this->assertStringContainsString('R² is below zero', $poorRegression);
    }

    public function test_prediction_explanations_distinguish_each_problem_type(): void
    {
        $service = new EducationalExplanationService();

        $classification = $service->predictionExplanation(
            'classification',
            ['confidence' => 87.456],
            [['feature' => 'tenure'], ['feature' => 'monthly_charges']]
        );
        $this->assertStringContainsString('87.46%', $classification[0]);
        $this->assertStringContainsString('tenure, monthly_charges', $classification[1]);

        $this->assertStringContainsString(
            'estimate',
            $service->predictionExplanation('regression', ['prediction' => 42])[0]
        );
        $this->assertStringContainsString(
            'cluster center',
            $service->predictionExplanation('clustering', ['cluster' => 2])[0]
        );
    }

    public function test_predefined_recommendation_validates_and_normalizes_the_saved_setup(): void
    {
        config()->set('hybrid_ml.algorithms', [
            'logistic_regression' => [
                'label' => 'Logistic Regression',
                'problem_type' => 'classification',
            ],
            'random_forest' => [
                'label' => 'Random Forest Classifier',
                'problem_type' => 'classification',
            ],
        ]);

        $dataset = new MlDataset([
            'name' => 'Customer Churn',
            'description' => 'Customer account records.',
            'target_column' => 'churn',
            'problem_type' => 'classification',
            'metadata' => [
                'recommended_setup' => [
                    'objective' => 'Predict whether a customer will leave.',
                    'target' => 'churn',
                    'problem_type' => 'classification',
                    'algorithm_key' => 'random_forest',
                    'features' => ['tenure', 'monthly_charges', 'churn', 'unknown'],
                    'reason' => 'Handles nonlinear customer patterns.',
                ],
            ],
        ]);

        $service = new PredefinedDatasetRecommendationService(new AlgorithmCatalogService());
        $recommendation = $service->forDataset($dataset, [
            'headers' => ['tenure', 'monthly_charges', 'churn'],
        ]);

        $this->assertNotNull($recommendation);
        $this->assertSame('churn', $recommendation['target']);
        $this->assertSame('Random Forest Classifier', $recommendation['model_label']);
        $this->assertSame(['tenure', 'monthly_charges'], $recommendation['features']);

        $comparison = $service->compareToConfiguration($recommendation, [
            'problem_type' => 'classification',
            'algorithm_key' => 'random_forest',
            'target_column' => 'churn',
            'features' => ['monthly_charges', 'tenure'],
        ]);
        $this->assertTrue($comparison['is_recommended_model']);
        $this->assertTrue($comparison['is_full_recommended_setup']);

        $this->assertNull($service->forDataset($dataset, [
            'headers' => ['tenure', 'monthly_charges', 'different_target'],
        ]));
        $this->assertNull($service->forDataset(new MlDataset(['metadata' => []])));
    }
}
