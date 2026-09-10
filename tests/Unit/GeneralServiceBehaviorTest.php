<?php

namespace Tests\Unit;

use App\Models\ModuleLibraryItem;
use App\Services\PlatformContentService;
use App\Services\SuperAdminAnalyticsService;
use App\Services\TableOfSpecificationService;
use App\Support\ModelDevelopmentRoadmap;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GeneralServiceBehaviorTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_platform_content_json_helpers_accept_arrays_and_reject_invalid_json(): void
    {
        $service = new PlatformContentService();

        $this->assertSame([], $service->decodeJsonArray(null, 'content'));
        $this->assertSame([], $service->decodeJsonArray('  ', 'content'));
        $this->assertSame([['title' => 'Intro']], $service->decodeJsonArray('[{"title":"Intro"}]', 'content'));
        $this->assertStringContainsString(
            '"title": "Intro"',
            $service->prettyJson([['title' => 'Intro']])
        );
        $this->assertSame('[]', $service->prettyJson('not-json'));

        try {
            $service->decodeJsonArray('{not-json}', 'content');
            $this->fail('Invalid JSON must raise a validation error.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content', $exception->errors());
        }
    }

    public function test_platform_content_codes_are_normalized_bounded_and_versioned(): void
    {
        $service = new PlatformContentService();

        $this->assertSame('MOD-DATA-ETHICS-V2', $service->makeCode('MOD', 'Data Ethics', 2));
        $this->assertSame('MOD-CONTENT-V1', $service->makeCode('MOD', '---', 1));
        $this->assertLessThanOrEqual(100, strlen($service->makeCode('ASSESSMENT', str_repeat('Long title ', 30), 99)));
    }

    public function test_module_change_detection_compares_normalized_structured_content(): void
    {
        $service = new PlatformContentService();
        $sections = [['heading' => 'Introduction', 'body' => 'Read this section.']];
        $questions = [['question' => 'What is data?', 'answer' => 'Information']];
        $module = new ModuleLibraryItem([
            'content_sections' => $sections,
            'mcq_questions' => $questions,
        ]);

        $this->assertFalse($service->moduleContentChanged($module, $sections, $questions));
        $this->assertTrue($service->moduleContentChanged(
            $module,
            [['heading' => 'Changed', 'body' => 'Read this section.']],
            $questions
        ));
    }

    public function test_tos_status_summaries_cover_incomplete_complete_and_invalid_allocations(): void
    {
        $service = new TableOfSpecificationService();

        $incomplete = $service->statusFromCounts(7, 10);
        $complete = $service->statusFromCounts(10, 10);
        $invalid = $service->statusFromCounts(12, 10);

        $this->assertSame('needs_attention', $incomplete['code']);
        $this->assertSame('3 item(s) remaining.', substr($incomplete['message'], -20));
        $this->assertFalse($incomplete['can_generate']);
        $this->assertSame('complete', $complete['code']);
        $this->assertTrue($complete['can_generate']);
        $this->assertSame('invalid', $invalid['code']);
        $this->assertFalse($invalid['can_generate']);
        $this->assertSame(100, array_sum(TableOfSpecificationService::DEFAULT_COGNITIVE_DISTRIBUTION));
    }

    public function test_model_roadmap_normalizes_invalid_authoring_steps(): void
    {
        $this->assertSame(2, ModelDevelopmentRoadmap::normalizeAuthoringStep('not-a-step'));
        $this->assertSame(2, ModelDevelopmentRoadmap::normalizeAuthoringStep(-5));
        $this->assertSame(5, ModelDevelopmentRoadmap::normalizeAuthoringStep('5'));
        $this->assertSame(7, ModelDevelopmentRoadmap::normalizeAuthoringStep(99));
        $this->assertSame(6, ModelDevelopmentRoadmap::normalizeAuthoringStep(null, 6));
    }

    public function test_analytics_csv_export_uses_stable_columns_and_blocks_formula_injection(): void
    {
        Carbon::setTestNow('2026-09-09 12:34:56');
        $service = new SuperAdminAnalyticsService();
        $rows = $service->exportRows('students', [
            'students' => [
                'topXp' => [[
                    'name' => '=HYPERLINK("https://example.test")',
                    'email' => 'student@example.test',
                    'xp' => 1200,
                    'streak' => 4,
                    'last_activity' => '2026-09-09 12:00:00',
                    'ignored' => 'not exported',
                ]],
            ],
        ]);

        $this->assertSame(['name', 'email', 'xp', 'streak', 'last_activity'], $rows[0]);
        $this->assertSame('\'=HYPERLINK("https://example.test")', $rows[1][0]);
        $this->assertCount(5, $rows[1]);
        $this->assertSame(
            'datasensei_anti-cheat_analytics_20260909_123456.csv',
            $service->csvFilename('Anti Cheat')
        );
    }
}
