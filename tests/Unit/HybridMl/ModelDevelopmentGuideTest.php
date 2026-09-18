<?php

namespace Tests\Unit\HybridMl;

use App\Services\HybridMl\AlgorithmCatalogService;
use App\Support\ModelDevelopmentGuide;
use App\Support\ModelDevelopmentRoadmap;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ModelDevelopmentGuideTest extends TestCase
{
    public function test_every_roadmap_step_has_complete_beginner_guidance(): void
    {
        $guides = ModelDevelopmentGuide::stepGuides();

        $this->assertSame(array_keys(ModelDevelopmentRoadmap::steps()), array_keys($guides));
        foreach ($guides as $step => $guide) {
            foreach (['what', 'why', 'example', 'tip', 'mistake'] as $field) {
                $this->assertNotSame('', trim($guide[$field] ?? ''), "Step {$step} is missing '{$field}'.");
            }
        }

        $this->assertSame($guides[1], ModelDevelopmentGuide::stepGuide(99));
        $this->assertCount(3, ModelDevelopmentGuide::phases());
    }

    public function test_verdicts_use_the_main_metric_for_each_problem_type(): void
    {
        $strong = ModelDevelopmentGuide::verdict('classification', ['accuracy' => 60.0, 'f1' => 91.5]);
        $this->assertSame('strong', $strong['level']);
        $this->assertSame('F1 Score', $strong['metric']);
        $this->assertSame('91.50%', $strong['value']);

        $this->assertSame('fair', ModelDevelopmentGuide::verdict('classification', ['accuracy' => 72.0])['level']);
        $this->assertSame('weak', ModelDevelopmentGuide::verdict('regression', ['r2' => 0.2])['level']);
        $this->assertSame('fair', ModelDevelopmentGuide::verdict('clustering', ['silhouette' => 0.3])['level']);
        $this->assertSame('unknown', ModelDevelopmentGuide::verdict('clustering', ['silhouette' => null])['level']);

        $negative = ModelDevelopmentGuide::verdict('regression', ['r2' => -0.4]);
        $this->assertSame('weak', $negative['level']);
        $this->assertStringContainsString('below zero', $negative['summary']);
    }

    public function test_metrics_are_ordered_and_formatted_for_students(): void
    {
        $metrics = [
            'confusion_matrix' => ['labels' => ['a'], 'values' => [[1]]],
            'recall' => 80,
            'accuracy' => 90,
            'roc_auc' => null,
            'f1' => 85.123,
        ];

        $this->assertSame(['accuracy', 'f1', 'recall'], array_keys(ModelDevelopmentGuide::displayMetrics('classification', $metrics)));
        $this->assertSame(['silhouette', 'cluster_count', 'inertia'], array_keys(ModelDevelopmentGuide::displayMetrics('clustering', [
            'inertia' => 12.5, 'silhouette' => 0.41, 'cluster_count' => 3, 'cluster_sizes' => ['Cluster 1' => 4],
        ])));

        $this->assertSame('85.12%', ModelDevelopmentGuide::formatMetric('f1', 85.123));
        $this->assertSame('3', ModelDevelopmentGuide::formatMetric('cluster_count', 3));
        $this->assertSame('0.4100', ModelDevelopmentGuide::formatMetric('silhouette', 0.41));
        $this->assertSame('—', ModelDevelopmentGuide::formatMetric('rmse', null));
        $this->assertSame('lower', ModelDevelopmentGuide::metric('rmse')['better']);
        $this->assertSame('Custom Score', ModelDevelopmentGuide::metric('custom_score')['label']);
    }

    public function test_every_configured_algorithm_has_a_guide_and_beginner_picks_exist_per_problem_type(): void
    {
        $catalog = new AlgorithmCatalogService();

        foreach (['classification', 'regression', 'clustering'] as $problemType) {
            $guides = $catalog->learningGuides($problemType, ['row_count' => 100]);
            $this->assertNotEmpty($guides);
            $this->assertContains(true, array_column($guides, 'beginner_friendly'), "{$problemType} needs a beginner-friendly algorithm.");

            foreach ($guides as $key => $guide) {
                $this->assertNotSame('', $guide['analogy'], "{$key} needs an analogy.");
                foreach (array_keys((array) config("hybrid_ml.algorithms.{$key}.parameters", [])) as $parameter) {
                    $this->assertNotSame(
                        'The safe default works well for a first model.',
                        ModelDevelopmentGuide::parameterHint((string) $parameter),
                        "Parameter {$parameter} needs a specific hint."
                    );
                }
            }
        }
    }

    public function test_classification_targets_with_more_than_one_hundred_classes_are_rejected_before_queueing(): void
    {
        $profile = [
            'headers' => ['size', 'price'],
            'row_count' => 400,
            'columns' => [
                'size' => ['type' => 'decimal', 'unique_count' => 300, 'is_identifier_like' => false],
                'price' => ['type' => 'decimal', 'unique_count' => 350, 'is_identifier_like' => false],
            ],
        ];

        try {
            (new AlgorithmCatalogService())->normalizeTrainingConfiguration([
                'problem_type' => 'classification',
                'algorithm_key' => 'decision_tree',
                'features' => ['size'],
                'target_column' => 'price',
                'test_size' => .20,
                'random_state' => 42,
                'cross_validation' => 0,
            ], $profile);
            $this->fail('A 350-class classification target should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('target_column', $exception->errors());
            $this->assertStringContainsString('more than 100', $exception->errors()['target_column'][0]);
        }
    }

    public function test_beginner_views_keep_the_guidance_hooks(): void
    {
        $wizard = (string) file_get_contents(resource_path('views/student/model-development/wizard.blade.php'));
        foreach (['target-insight', 'data-feature-action="all"', 'split-train-rows', 'data-edit-step="2"', "event.key !== 'Enter'", 'suggestProblem'] as $needle) {
            $this->assertStringContainsString($needle, $wizard);
        }
        $this->assertStringNotContainsString('@json(collect(', $wizard, 'Blade @json splits on commas; build arrays in @php first.');

        foreach (['index', 'dataset', 'wizard', 'training-job', 'model'] as $view) {
            $source = (string) file_get_contents(resource_path("views/student/model-development/{$view}.blade.php"));
            $this->assertStringContainsString('partials.step-guide', $source, "{$view} must show the beginner guide.");
            $this->assertStringNotContainsString('ml-step-kicker', $source, "{$view} should use the numbered step header.");
        }

        $model = (string) file_get_contents(resource_path('views/student/model-development/model.blade.php'));
        $this->assertStringContainsString('ml-verdict', $model);
        $this->assertStringContainsString('fill-typical', $model);
    }
}
