<?php

namespace Tests\Unit\HybridMl;

use App\Support\ModelDevelopmentRoadmap;
use Tests\TestCase;

class ModelDevelopmentRoadmapTest extends TestCase
{
    public function test_it_defines_the_required_ten_stage_learning_sequence(): void
    {
        $steps = ModelDevelopmentRoadmap::steps();

        $this->assertSame(10, ModelDevelopmentRoadmap::totalSteps());
        $this->assertSame([
            'Dataset',
            'Target',
            'Features',
            'Problem Type',
            'Train/Test Split',
            'Algorithm',
            'Train Model',
            'Evaluate',
            'Predict',
            'Save Model',
        ], array_column($steps, 'label'));

        foreach ($steps as $step) {
            $this->assertNotSame('', trim($step['description']));
        }
    }

    public function test_progress_is_derived_from_completed_stages_and_safely_clamped(): void
    {
        $this->assertSame(['completed' => 4, 'total' => 10, 'percent' => 40], ModelDevelopmentRoadmap::progress(4));
        $this->assertSame(['completed' => 0, 'total' => 10, 'percent' => 0], ModelDevelopmentRoadmap::progress(-10));
        $this->assertSame(['completed' => 10, 'total' => 10, 'percent' => 100], ModelDevelopmentRoadmap::progress(99));
    }

    public function test_validation_errors_return_students_to_the_affected_stage(): void
    {
        $this->assertSame(2, ModelDevelopmentRoadmap::stepForValidationFields(['target_column']));
        $this->assertSame(3, ModelDevelopmentRoadmap::stepForValidationFields(['features.0']));
        $this->assertSame(5, ModelDevelopmentRoadmap::stepForValidationFields(['cross_validation']));
        $this->assertSame(6, ModelDevelopmentRoadmap::stepForValidationFields(['parameters.max_depth']));
        $this->assertSame(7, ModelDevelopmentRoadmap::stepForValidationFields(['model_name']));
        $this->assertSame(3, ModelDevelopmentRoadmap::stepForValidationFields(['model_name', 'features.0']));
        $this->assertNull(ModelDevelopmentRoadmap::stepForValidationFields(['unrelated']));
    }

    public function test_save_stage_remains_locked_until_a_prediction_exists(): void
    {
        $this->assertSame(8, ModelDevelopmentRoadmap::resultStep('evaluate', false));
        $this->assertSame(9, ModelDevelopmentRoadmap::resultStep('predict', false));
        $this->assertSame(9, ModelDevelopmentRoadmap::resultStep('save', false));
        $this->assertSame(10, ModelDevelopmentRoadmap::resultStep('save', true));
    }

    public function test_every_model_development_screen_uses_the_shared_roadmap_contract(): void
    {
        foreach (['index', 'dataset', 'wizard', 'training-job', 'model'] as $view) {
            $source = (string) file_get_contents(resource_path("views/student/model-development/{$view}.blade.php"));
            $this->assertStringContainsString("partials.roadmap", $source, "{$view} must render the shared roadmap.");
        }

        $roadmap = (string) file_get_contents(resource_path('views/student/model-development/partials/roadmap.blade.php'));
        $this->assertStringContainsString('✓ Completed', $roadmap);
        $this->assertStringContainsString('CURRENT STEP', $roadmap);
        $this->assertStringContainsString('🔒 Not available yet', $roadmap);
        $this->assertStringContainsString('⚠ Needs attention', $roadmap);
        $this->assertStringContainsString('window.DataSenseiModelRoadmap = {update}', $roadmap);
    }

    public function test_authoring_training_and_result_views_follow_the_pdf_interaction_rules(): void
    {
        $wizard = (string) file_get_contents(resource_path('views/student/model-development/wizard.blade.php'));
        foreach (range(2, 7) as $step) {
            $this->assertSame(1, substr_count($wizard, "data-workflow-step=\"{$step}\""));
        }
        $this->assertGreaterThanOrEqual(2, substr_count($wizard, '<summary>Advanced Options</summary>'));
        $this->assertStringContainsString('data-profile-unavailable', $wizard);
        $this->assertStringContainsString('completedThrough = Math.max(1, step - 1)', $wizard);

        $training = (string) file_get_contents(resource_path('views/student/model-development/training-job.blade.php'));
        foreach ([0, 15, 35, 58, 90] as $threshold) {
            $this->assertStringContainsString("'start' => {$threshold}", $training);
        }
        $this->assertStringContainsString('not a simulated timer', $training);
        $this->assertStringContainsString('evaluation_url', $training);
        $this->assertStringContainsString('✓ MODEL TRAINED SUCCESSFULLY', $training);

        $model = (string) file_get_contents(resource_path('views/student/model-development/model.blade.php'));
        $this->assertStringContainsString('data-result-step="evaluate"', $model);
        $this->assertStringContainsString('data-result-step="predict"', $model);
        $this->assertStringContainsString('data-result-step="save"', $model);
        $this->assertStringContainsString('Make a prediction to unlock Save Model.', $model);
        $this->assertStringContainsString('Version History', $model);
    }

    public function test_session_expiration_notification_has_a_page_flow_host_on_login(): void
    {
        $login = (string) file_get_contents(resource_path('views/auth/login.blade.php'));
        $notifications = (string) file_get_contents(resource_path('views/partials/ui-polish.blade.php'));

        $this->assertStringContainsString('right auth-page-notification-host', $login);
        $this->assertStringContainsString('grid-template-rows: auto minmax(0, 1fr)', $login);
        $this->assertStringContainsString('.right #ds-global-notification-stack', $login);
        $this->assertStringContainsString('Your session expired because there was no activity. Please sign in again.', $login);
        $this->assertStringContainsString("'.auth-page-notification-host'", $notifications);
    }
}
