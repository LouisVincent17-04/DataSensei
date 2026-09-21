<?php

namespace Tests\Unit\HybridMl;

use App\Support\ModelDevelopmentRoadmap;
use Tests\TestCase;

/**
 * Model Development was reduced from ten stages to four. These tests pin the
 * new journey and the old links that must keep working.
 */
class ModelDevelopmentRoadmapTest extends TestCase
{
    public function test_it_defines_the_four_step_journey(): void
    {
        $steps = ModelDevelopmentRoadmap::steps();

        $this->assertSame(4, ModelDevelopmentRoadmap::totalSteps());
        $this->assertSame(['Choose data', 'Set up', 'Results', 'Predict'], array_column($steps, 'label'));
        $this->assertSame(['data', 'setup', 'results', 'predict'], array_column($steps, 'key'));

        foreach ($steps as $step) {
            $this->assertNotSame('', trim($step['description']));
        }
    }

    public function test_progress_is_derived_from_completed_stages_and_safely_clamped(): void
    {
        $this->assertSame(['completed' => 2, 'total' => 4, 'percent' => 50], ModelDevelopmentRoadmap::progress(2));
        $this->assertSame(['completed' => 0, 'total' => 4, 'percent' => 0], ModelDevelopmentRoadmap::progress(-10));
        $this->assertSame(['completed' => 4, 'total' => 4, 'percent' => 100], ModelDevelopmentRoadmap::progress(99));
    }

    public function test_validation_errors_open_the_affected_part_of_the_set_up_page(): void
    {
        $this->assertSame('target', ModelDevelopmentRoadmap::sectionForValidationFields(['target_column']));
        $this->assertSame('columns', ModelDevelopmentRoadmap::sectionForValidationFields(['features.0']));
        $this->assertSame('method', ModelDevelopmentRoadmap::sectionForValidationFields(['cross_validation']));
        $this->assertSame('method', ModelDevelopmentRoadmap::sectionForValidationFields(['parameters.max_depth']));
        $this->assertSame('name', ModelDevelopmentRoadmap::sectionForValidationFields(['model_name']));
        $this->assertSame('columns', ModelDevelopmentRoadmap::sectionForValidationFields(['model_name', 'features.0']));
        $this->assertNull(ModelDevelopmentRoadmap::sectionForValidationFields(['unrelated']));

        $this->assertSame(2, ModelDevelopmentRoadmap::stepForValidationFields(['model_name']));
        $this->assertNull(ModelDevelopmentRoadmap::stepForValidationFields(['unrelated']));
    }

    public function test_links_from_the_ten_stage_version_still_resolve(): void
    {
        $this->assertSame(3, ModelDevelopmentRoadmap::resultStep('results', false));
        $this->assertSame(3, ModelDevelopmentRoadmap::resultStep('evaluate', false));
        $this->assertSame(3, ModelDevelopmentRoadmap::resultStep(null, false));
        $this->assertSame(4, ModelDevelopmentRoadmap::resultStep('predict', false));

        // Models are saved when training ends, so "save" no longer needs a prediction first.
        $this->assertSame(4, ModelDevelopmentRoadmap::resultStep('save', false));
        $this->assertSame(4, ModelDevelopmentRoadmap::resultStep('save', true));
    }

    public function test_every_model_development_screen_uses_the_shared_step_strip(): void
    {
        foreach (['index', 'dataset', 'wizard', 'training-job', 'model'] as $view) {
            $source = (string) file_get_contents(resource_path("views/student/model-development/{$view}.blade.php"));
            $this->assertStringContainsString('partials.roadmap', $source, "{$view} must render the shared step strip.");
            $this->assertStringNotContainsString('ml-roadmap-layout', $source, "{$view} should use the single-column frame.");
        }

        $roadmap = (string) file_get_contents(resource_path('views/student/model-development/partials/roadmap.blade.php'));
        $this->assertStringContainsString('aria-current="step"', $roadmap);
        $this->assertStringContainsString('window.DataSenseiModelRoadmap = {update}', $roadmap);
        foreach (['🔒', '⚠', '✓'] as $symbol) {
            $this->assertStringNotContainsString($symbol, $roadmap, 'Step states are written in words.');
        }
    }

    public function test_set_up_training_and_result_views_keep_their_contracts(): void
    {
        $wizard = (string) file_get_contents(resource_path('views/student/model-development/wizard.blade.php'));
        foreach (['target', 'columns', 'method', 'name'] as $section) {
            $this->assertSame(1, substr_count($wizard, "data-setup-section=\"{$section}\""));
        }
        $this->assertStringContainsString('data-profile-unavailable', $wizard);
        $this->assertStringContainsString('name="problem_type"', $wizard);
        $this->assertStringContainsString('name="algorithm_key"', $wizard);
        $this->assertStringNotContainsString('data-workflow-step', $wizard, 'The ten-stage pager is gone.');

        $training = (string) file_get_contents(resource_path('views/student/model-development/training-job.blade.php'));
        // The thresholds mirror the progress values written by the trusted runner.
        foreach ([0, 15, 35, 58, 90] as $threshold) {
            $this->assertStringContainsString("'start' => {$threshold}", $training);
        }
        $this->assertStringContainsString('not a simulated timer', $training);
        $this->assertStringContainsString('evaluation_url', $training);
        $this->assertStringContainsString('watchedTraining', $training, 'Only a run the student watched may redirect by itself.');

        $model = (string) file_get_contents(resource_path('views/student/model-development/model.blade.php'));
        $this->assertStringContainsString('data-result-step="evaluate"', $model);
        $this->assertStringContainsString('data-result-step="predict"', $model);
        $this->assertStringContainsString('Version History', $model);
        $this->assertStringContainsString('id="answer"', $model);
        // A student must be able to delete a model they trained, from the list
        // and from the model's own page; a shared reference model must not offer it.
        $this->assertStringContainsString('models.destroy', $model);
        $this->assertStringContainsString("! \$model->isSystemModel() && (int) \$model->user_id === (int) auth()->id()", $model);
        $index = (string) file_get_contents(resource_path('views/student/model-development/index.blade.php'));
        $this->assertStringContainsString('models.destroy', $index);
        $this->assertStringContainsString("! \$model->isSystemModel() && (int) \$model->user_id === (int) auth()->id()", $index);
        $this->assertStringNotContainsString('ml-badge', $model, 'Plain text labels, no capsules.');
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
