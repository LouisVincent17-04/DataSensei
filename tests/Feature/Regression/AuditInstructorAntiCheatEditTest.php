<?php

namespace Tests\Feature\Regression;

use App\Models\AntiCheatSetting;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * Instructor audit: a saved anti-cheat configuration could not be edited. The
 * page only offered a blank "create" form whose hard-coded defaults silently
 * replaced every stored rule, and the update route had no caller at all.
 */
class AuditInstructorAntiCheatEditTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->seedAssignmentActors();
    }

    private function savedSetting(): AntiCheatSetting
    {
        $this->setPolicy([
            'class_id' => $this->classId,
            'max_tab_switches' => 7,
            'require_fullscreen' => true,
            'show_warnings' => false,
            'block_on_tab_limit' => false,
        ]);

        return AntiCheatSetting::query()
            ->where('instructor_id', $this->instructor->id)
            ->firstOrFail();
    }

    public function test_saved_configuration_offers_an_edit_link(): void
    {
        $setting = $this->savedSetting();

        $this->actAs($this->instructor)
            ->get(route('instructor.anti-cheat.index'))
            ->assertOk()
            ->assertSee(route('instructor.anti-cheat.index', ['setting' => $setting->id], false) . '#anti-cheat-form', false);
    }

    public function test_edit_form_shows_the_stored_rules_and_posts_to_update(): void
    {
        $setting = $this->savedSetting();

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.anti-cheat.index', ['setting' => $setting->id]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('name="max_tab_switches" value="7"', $html);
        $this->assertStringContainsString('name="require_fullscreen" value="1" checked', $html);
        $this->assertStringNotContainsString('name="show_warnings" value="1" checked', $html);
        $this->assertStringNotContainsString('name="block_on_tab_limit" value="1" checked', $html);
        $this->assertStringContainsString(
            route('instructor.anti-cheat.update', $setting, false),
            $html
        );
    }

    public function test_saving_the_edit_keeps_rules_the_instructor_did_not_change(): void
    {
        $setting = $this->savedSetting();

        $this->actAs($this->instructor)
            ->from(route('instructor.anti-cheat.index', ['setting' => $setting->id]))
            ->put(route('instructor.anti-cheat.update', $setting), [
                'class_id' => $this->classId,
                'max_tab_switches' => 5,
                'enabled' => '1',
                'require_fullscreen' => '1',
            ])
            ->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame(5, (int) $fresh->max_tab_switches);
        $this->assertTrue((bool) $fresh->require_fullscreen);
        $this->assertFalse((bool) $fresh->show_warnings);
        $this->assertFalse((bool) $fresh->block_on_tab_limit);
    }

    public function test_another_instructor_cannot_load_or_save_the_configuration(): void
    {
        $setting = $this->savedSetting();

        $html = $this->actAs($this->otherInstructor)
            ->get(route('instructor.anti-cheat.index', ['setting' => $setting->id]))
            ->assertOk()
            ->getContent();

        // Falls back to the blank create form instead of exposing the rules.
        $this->assertStringContainsString('name="max_tab_switches" value="2"', $html);
        $this->assertStringNotContainsString(
            route('instructor.anti-cheat.update', $setting, false),
            $html
        );

        $this->actAs($this->otherInstructor)
            ->put(route('instructor.anti-cheat.update', $setting), [
                'class_id' => '',
                'max_tab_switches' => 0,
            ])
            ->assertStatus(403);

        $this->assertSame(7, (int) $setting->fresh()->max_tab_switches);
    }

    public function test_editing_a_configuration_written_for_an_archived_class_keeps_that_class(): void
    {
        $setting = $this->savedSetting();
        DB::table('classes')->where('id', $this->classId)->update(['is_archived' => true]);

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.anti-cheat.index', ['setting' => $setting->id]))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<option value="' . $this->classId . '"[^>]*selected/',
            $html
        );

        $this->actAs($this->instructor)
            ->from(route('instructor.anti-cheat.index', ['setting' => $setting->id]))
            ->put(route('instructor.anti-cheat.update', $setting), [
                'class_id' => $this->classId,
                'max_tab_switches' => 6,
                'enabled' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame($this->classId, (int) $setting->fresh()->class_id);

        // The blank create form still offers active classes only.
        $createHtml = $this->actAs($this->instructor)
            ->get(route('instructor.anti-cheat.index'))
            ->assertOk()
            ->getContent();

        $this->assertDoesNotMatchRegularExpression(
            '/<option value="' . $this->classId . '"/',
            $createHtml
        );
    }

    public function test_an_unknown_setting_parameter_falls_back_to_the_create_form(): void
    {
        $this->savedSetting();

        foreach (['abc', '999999', ''] as $value) {
            $html = $this->actAs($this->instructor)
                ->get(route('instructor.anti-cheat.index', ['setting' => $value]))
                ->assertOk()
                ->getContent();

            $this->assertStringContainsString('name="max_tab_switches" value="2"', $html);
        }

        $this->assertSame(1, DB::table('anti_cheat_settings')->count());
    }
}
