<?php

namespace Tests\Unit\HybridMl;

use App\Support\ModelDevelopmentOutcome;
use PHPUnit\Framework\TestCase;

class ModelDevelopmentOutcomeTest extends TestCase
{
    public function test_breast_cancer_predictions_are_answered_in_plain_words(): void
    {
        $wording = ModelDevelopmentOutcome::wording('breast-cancer-wisconsin', 'classification', 'diagnosis', ['benign', 'malignant']);

        $this->assertSame('Is this tumour breast cancer?', $wording['question']);
        $this->assertSame('malignant', $wording['positive']);

        $cancer = ModelDevelopmentOutcome::describePrediction($wording, 'classification', [
            'predicted_value' => 'malignant',
            'confidence' => 97.31,
            'probabilities' => ['benign' => 2.69, 'malignant' => 97.31],
        ]);
        $this->assertSame('Likely breast cancer', $cancer['headline']);
        $this->assertSame('alert', $cancer['tone']);
        $this->assertSame('Very sure', $cancer['confidence_label']);
        $this->assertSame('About 97 out of 100', $cancer['confidence_text']);
        $this->assertSame('Cancer (malignant)', $cancer['chances'][0]['label']);
        $this->assertTrue($cancer['chances'][0]['chosen']);
        $this->assertStringContainsString('not a medical test', (string) $cancer['caution']);

        $clear = ModelDevelopmentOutcome::describePrediction($wording, 'classification', [
            'predicted_value' => 'benign',
            'confidence' => 99.9,
            'probabilities' => ['benign' => 99.9, 'malignant' => 0.1],
        ]);
        $this->assertSame('Likely not breast cancer', $clear['headline']);
        $this->assertSame('calm', $clear['tone']);
        $this->assertSame('More than 99 out of 100', $clear['confidence_text']);
    }

    public function test_a_close_call_is_not_presented_as_a_confident_answer(): void
    {
        $wording = ModelDevelopmentOutcome::wording('breast-cancer-wisconsin', 'classification', 'diagnosis', ['benign', 'malignant']);
        $result = ModelDevelopmentOutcome::describePrediction($wording, 'classification', [
            'predicted_value' => 'malignant',
            'confidence' => 53.0,
            'probabilities' => ['benign' => 47.0, 'malignant' => 53.0],
        ]);

        $this->assertSame('Hard to say, leaning towards: breast cancer', $result['headline']);
        $this->assertSame('Not sure, it is a close call', $result['confidence_label']);
    }

    public function test_preset_wording_is_dropped_when_the_student_predicts_a_different_column(): void
    {
        $wording = ModelDevelopmentOutcome::wording('titanic', 'classification', 'sex', ['female', 'male']);

        $this->assertSame('What is the sex?', $wording['question']);
        $this->assertNull($wording['positive']);
        $this->assertSame('Likely female', $wording['labels']['female']['headline']);
        $this->assertSame('neutral', $wording['labels']['female']['tone']);
    }

    public function test_uploaded_yes_no_answers_read_as_yes_and_no(): void
    {
        $wording = ModelDevelopmentOutcome::wording(null, 'classification', 'loan_default', ['0', '1']);

        $this->assertSame('1', $wording['positive']);
        $this->assertSame('Likely yes: loan default', $wording['labels']['1']['headline']);
        $this->assertSame('Likely no: not loan default', $wording['labels']['0']['headline']);
        $this->assertSame('alert', $wording['labels']['1']['tone']);
        $this->assertSame('calm', $wording['labels']['0']['tone']);
        $this->assertSame(['label' => 'Monthly income', 'help' => ''], ModelDevelopmentOutcome::field($wording, 'monthly_income'));
    }

    public function test_number_predictions_come_with_a_realistic_range(): void
    {
        $wording = ModelDevelopmentOutcome::wording('housing-prices', 'regression', 'price');
        $result = ModelDevelopmentOutcome::describePrediction($wording, 'regression', ['predicted_value' => 349462.4], ['mae' => 23962.0]);

        $this->assertSame('About 349,462', $result['headline']);
        $this->assertStringContainsString('325,500', $result['confidence_text']);
        $this->assertStringContainsString('373,424', $result['confidence_text']);
        $this->assertNull($result['confidence']);
    }

    public function test_results_are_summarised_in_sentences_with_both_kinds_of_mistake(): void
    {
        $wording = ModelDevelopmentOutcome::wording('breast-cancer-wisconsin', 'classification', 'diagnosis', ['benign', 'malignant']);
        $summary = ModelDevelopmentOutcome::summarizeResults($wording, 'classification', [
            'accuracy' => 95.61,
            'baseline_accuracy' => 63.16,
            'baseline_label' => 'benign',
            // rows = real, columns = predicted
            'confusion_matrix' => ['labels' => ['benign', 'malignant'], 'values' => [[70, 2], [3, 39]]],
        ]);

        $this->assertSame('Right about 96 times out of 100', $summary['headline']);
        $this->assertStringContainsString('109 right and 5 wrong', $summary['lines'][0]);
        $this->assertStringContainsString('really learned something', $summary['lines'][1]);
        $this->assertSame(3, $summary['mistakes']['missed']);
        $this->assertSame(2, $summary['mistakes']['false_alarms']);
        $this->assertFalse($summary['suspicious']);
    }

    public function test_a_model_that_only_matches_the_baseline_or_looks_too_perfect_is_called_out(): void
    {
        $wording = ModelDevelopmentOutcome::wording(null, 'classification', 'fraud', ['0', '1']);

        $lazy = ModelDevelopmentOutcome::summarizeResults($wording, 'classification', [
            'accuracy' => 95.0, 'baseline_accuracy' => 94.5, 'baseline_label' => '0',
            'confusion_matrix' => ['labels' => ['0', '1'], 'values' => [[95, 0], [5, 0]]],
        ]);
        $this->assertStringContainsString('adds little', $lazy['lines'][1]);
        $this->assertSame(5, $lazy['mistakes']['missed']);

        $perfect = ModelDevelopmentOutcome::summarizeResults($wording, 'classification', [
            'accuracy' => 100.0,
            'confusion_matrix' => ['labels' => ['0', '1'], 'values' => [[60, 0], [0, 40]]],
        ]);
        $this->assertTrue($perfect['suspicious']);
    }

    public function test_the_wine_dataset_hides_the_column_that_gives_the_answer_away(): void
    {
        $wording = ModelDevelopmentOutcome::wording('wine-quality', 'classification', 'quality_label', ['high', 'low', 'medium']);

        $this->assertSame(['quality_score'], $wording['leak_columns']);
    }

    public function test_every_built_in_dataset_has_a_question_and_described_fields(): void
    {
        $manifest = json_decode((string) file_get_contents(dirname(__DIR__, 3).'/storage/app/ml/system/manifest.json'), true);
        $presets = ModelDevelopmentOutcome::presets();

        foreach ($manifest['datasets'] as $dataset) {
            $slug = $dataset['slug'];
            $this->assertArrayHasKey($slug, $presets, "{$slug} needs plain-language wording.");
            $this->assertStringEndsWith('?', $presets[$slug]['question']);
            foreach ((array) $dataset['recommended_setup']['features'] as $feature) {
                $this->assertArrayHasKey($feature, $presets[$slug]['fields'], "{$slug}.{$feature} needs a friendly label.");
            }
        }
    }
}
