<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Support\CoreCurriculum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * DataSensei Updates 12, part 4: the 24 Core Modules are marked by a
 * database flag and key (not by title), keep their title and identity, are
 * never deleted, can still be edited and published or unpublished. New
 * modules are Custom; a custom module in use is archived instead of deleted.
 */
class Updates12CoreModulesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->roleUser(User::ROLE_ADMIN);
    }

    public function test_the_24_core_modules_are_marked_by_flag_and_key(): void
    {
        $this->curriculum();
        $custom = Module::create(['title' => 'Deep Learning Extras', 'description' => 'x', 'order_index' => 25, 'year_level' => 'Year 4', 'xp_reward' => 100]);

        $first = CoreCurriculum::sync();
        $this->assertSame(24, $first['modules']);
        $this->assertSame(24, Module::core()->count());
        $this->assertSame(CoreCurriculum::keys(), Module::core()->orderBy('order_index')->pluck('module_key')->all());
        $this->assertSame('custom', $custom->fresh()->module_type);
        $this->assertNull($custom->fresh()->module_key);

        // Running it again changes nothing.
        $this->assertSame(['modules' => 0, 'challenges' => 0], CoreCurriculum::sync());
    }

    public function test_a_core_module_renamed_before_this_update_is_still_recognised(): void
    {
        $this->curriculum(['Methods of Proof' => 'Proof Techniques']);
        Module::create(['title' => 'Workshop', 'description' => 'x', 'order_index' => 5, 'year_level' => 'Year 1', 'xp_reward' => 100]);

        CoreCurriculum::sync();

        $this->assertSame('core-05-methods-of-proof', Module::where('title', 'Proof Techniques')->value('module_key'));
        $this->assertSame('custom', Module::where('title', 'Workshop')->value('module_type'));
    }

    public function test_new_modules_are_custom_and_the_type_cannot_be_mass_assigned(): void
    {
        $this->authenticateAs($this->admin)->post(route('admin.modules.store'), [
            'title' => 'My Workshop', 'description' => '', 'year_level' => 'Year 1', 'xp_reward' => 100,
            'module_type' => 'core', 'module_key' => 'core-01-basics-of-python-programming',
        ]);

        $module = Module::where('title', 'My Workshop')->firstOrFail();
        $this->assertSame('custom', $module->module_type);
        $this->assertNull($module->module_key);

        $direct = Module::create(['title' => 'Direct', 'description' => 'x', 'order_index' => 99, 'year_level' => 'Year 1', 'xp_reward' => 1, 'module_type' => 'core']);
        $this->assertSame('custom', $direct->fresh()->module_type);
    }

    public function test_a_core_module_keeps_its_title_but_its_content_can_be_edited(): void
    {
        $core = $this->coreModule();
        // A published module needs an outcome and a section to be saved.
        $core->update(['learning_outcomes' => ['Write a Python program.']]);
        Lesson::create(['module_id' => $core->id, 'title' => 'L1', 'content' => '<p>x</p>', 'order_index' => 1]);

        $this->authenticateAs($this->admin)
            ->put(route('admin.modules.update', $core), $this->form($core, ['title' => 'Python for Everyone']))
            ->assertSessionHasErrors('title');
        $this->assertSame('Basics of Python Programming', $core->fresh()->title);

        $this->put(route('admin.modules.update', $core), $this->form($core, ['description' => 'New description.']))
            ->assertSessionHas('success');
        $this->assertSame('New description.', $core->fresh()->description);
        $this->assertSame('Basics of Python Programming', $core->fresh()->title);

        $edit = $this->get(route('admin.modules.edit', $core))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('#id="module-title"[^>]*readonly#', $edit);
        $this->assertStringContainsString('Core Module', $edit);
        // The delete button is disabled; there is no delete form.
        $this->assertStringNotContainsString("confirm('Delete this module", $edit);
        $this->assertMatchesRegularExpression('#<button[^>]*disabled[^>]*title="Core Modules cannot be deleted"#', $edit);
    }

    public function test_the_model_refuses_identity_changes_and_deletion_of_a_core_module(): void
    {
        $core = $this->coreModule();

        foreach (['title' => 'Renamed', 'module_key' => 'core-99-something', 'module_type' => 'custom'] as $field => $value) {
            $fresh = $core->fresh();
            $fresh->forceFill([$field => $value]);
            try {
                $fresh->save();
                $this->fail($field.' of a core module was changed.');
            } catch (ValidationException) {
                $this->assertSame('core', $core->fresh()->module_type);
            }
        }

        $this->expectException(ValidationException::class);
        $core->fresh()->delete();
    }

    public function test_a_core_module_cannot_be_deleted_but_can_be_unpublished_without_losing_progress(): void
    {
        $core = $this->coreModule();
        $core->update(['learning_outcomes' => ['Write a Python program.']]);
        $lesson = Lesson::create(['module_id' => $core->id, 'title' => 'L1', 'content' => '<p>x</p>', 'order_index' => 1]);
        $student = $this->roleUser();
        DB::table('module_user')->insert(['user_id' => $student->id, 'module_id' => $core->id, 'is_unlocked' => 1, 'is_completed' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lesson_user')->insert(['user_id' => $student->id, 'lesson_id' => $lesson->id, 'is_completed' => 1, 'created_at' => now(), 'updated_at' => now()]);

        $this->authenticateAs($this->admin)
            ->delete(route('admin.modules.destroy', $core))
            ->assertRedirect(route('admin.modules.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('modules', ['id' => $core->id]);

        $list = $this->get(route('admin.modules.index'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('#<button[^>]*disabled[^>]*title="Core Modules cannot be deleted"#', $list);
        $this->assertStringContainsString('Core', $list);

        $this->patch(route('admin.modules.status', $core))->assertSessionHas('success');
        $this->assertFalse((bool) $core->fresh()->is_published);
        $this->patch(route('admin.modules.status', $core))->assertSessionHas('success');
        $this->assertTrue((bool) $core->fresh()->is_published);

        $this->assertDatabaseHas('module_user', ['user_id' => $student->id, 'module_id' => $core->id, 'is_completed' => 1]);
        $this->assertDatabaseHas('lesson_user', ['user_id' => $student->id, 'lesson_id' => $lesson->id]);

        // Core Modules are not archived either.
        $this->patch(route('admin.modules.archive', $core))->assertSessionHas('error');
        $this->assertNull($core->fresh()->archived_at);
    }

    public function test_a_custom_module_in_use_is_archived_instead_of_deleted(): void
    {
        $custom = Module::create(['title' => 'Workshop', 'description' => 'x', 'order_index' => 1, 'year_level' => 'Year 1', 'xp_reward' => 100, 'is_published' => true]);
        $student = $this->roleUser();
        DB::table('module_user')->insert(['user_id' => $student->id, 'module_id' => $custom->id, 'is_unlocked' => 1, 'is_completed' => 0, 'opened_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $this->authenticateAs($this->admin)
            ->delete(route('admin.modules.destroy', $custom))
            ->assertSessionHas('error', fn (string $message) => str_contains($message, 'Archive it instead') && str_contains($message, '1 learner with progress'));
        $this->assertDatabaseHas('modules', ['id' => $custom->id]);

        $this->get(route('admin.modules.edit', $custom))->assertOk()->assertSee('Archive module');

        $this->patch(route('admin.modules.archive', $custom))->assertSessionHas('success');
        $custom->refresh();
        $this->assertNotNull($custom->archived_at);
        $this->assertFalse((bool) $custom->is_published);
        $this->assertDatabaseHas('module_user', ['module_id' => $custom->id, 'user_id' => $student->id]);

        // An archived module is restored before it is published again.
        $this->patch(route('admin.modules.status', $custom))->assertSessionHas('error');
        $this->patch(route('admin.modules.restore', $custom))->assertSessionHas('success');
        $this->assertNull($custom->fresh()->archived_at);

        // A custom module whose challenge was attempted is in use too.
        $other = Module::create(['title' => 'Second', 'description' => 'x', 'order_index' => 2, 'year_level' => 'Year 1', 'xp_reward' => 100]);
        $category = ChallengeCategory::create(['name' => 'Newbie', 'slug' => 'newbie', 'target_audience' => 'Beginners', 'description' => 'Newbie level', 'order_index' => 1]);
        $challenge = Challenge::create(['challenge_category_id' => $category->id, 'module_id' => $other->id, 'title' => 'Second', 'content_code' => 'M'.$other->id.'-NEWBIE-MCQ', 'description' => 'x', 'is_coding_challenge' => false, 'order_index' => 1]);
        DB::table('challenge_attempts')->insert(['user_id' => $student->id, 'challenge_id' => $challenge->id, 'attempt_no' => 1, 'mode' => 'practice', 'status' => 'submitted', 'started_at' => now(), 'submitted_at' => now(), 'score' => 1, 'total_questions' => 2, 'created_at' => now(), 'updated_at' => now()]);
        $this->delete(route('admin.modules.destroy', $other))->assertSessionHas('error', fn (string $message) => str_contains($message, '1 challenge attempt'));
        $this->assertDatabaseHas('modules', ['id' => $other->id]);
    }

    public function test_core_challenges_keep_their_identity_across_versions(): void
    {
        $this->curriculum();
        $newbie = ChallengeCategory::create(['name' => 'Newbie', 'slug' => 'newbie', 'target_audience' => 'Beginners', 'description' => 'Newbie level', 'order_index' => 1]);
        $seeded = Challenge::create(['challenge_category_id' => $newbie->id, 'title' => 'Deep Learning', 'description' => 'x', 'is_coding_challenge' => false, 'order_index' => 17]);
        $adminMade = Challenge::create(['challenge_category_id' => $newbie->id, 'title' => 'Deep Learning Drill', 'description' => 'x', 'is_coding_challenge' => false, 'order_index' => 30]);
        CoreCurriculum::sync();

        $this->assertSame('core-17-deep-learning', $seeded->fresh()->core_module_key);
        $this->assertNull($adminMade->fresh()->core_module_key);

        // A new version (same content code) is still the same core challenge.
        $v2 = Challenge::create(['challenge_category_id' => $newbie->id, 'content_code' => $seeded->content_code, 'title' => 'Deep Learning', 'version_no' => 2, 'version_code' => 'V2', 'description' => 'x', 'is_coding_challenge' => false, 'order_index' => 17]);
        $this->assertSame('core-17-deep-learning', $v2->fresh()->core_module_key);

        // An instructor's challenge is never core, even with the same code.
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        $own = Challenge::create(['challenge_category_id' => $newbie->id, 'content_code' => $seeded->content_code, 'title' => 'Deep Learning', 'version_no' => 3, 'version_code' => 'V3', 'description' => 'x', 'is_coding_challenge' => false, 'order_index' => 17, 'created_by' => $instructor->id, 'visibility' => 'instructor']);
        $this->assertNull($own->fresh()->core_module_key);

        // Its content code and level cannot be changed.
        $this->expectException(ValidationException::class);
        $seeded->fresh()->update(['content_code' => 'SOMETHING-ELSE']);
    }

    public function test_the_last_version_of_a_core_challenge_cannot_be_deleted(): void
    {
        $this->curriculum();
        $newbie = ChallengeCategory::create(['name' => 'Newbie', 'slug' => 'newbie', 'target_audience' => 'Beginners', 'description' => 'Newbie level', 'order_index' => 1]);
        $only = Challenge::create(['challenge_category_id' => $newbie->id, 'title' => 'Data Warehousing', 'description' => 'x', 'is_coding_challenge' => true, 'order_index' => 23]);
        CoreCurriculum::sync();

        try {
            $only->fresh()->delete();
            $this->fail('The only version of a core challenge was deleted.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('challenges', ['id' => $only->id]);
        }

        $v2 = Challenge::create(['challenge_category_id' => $newbie->id, 'content_code' => $only->content_code, 'title' => 'Data Warehousing', 'version_no' => 2, 'version_code' => 'V2', 'description' => 'x', 'is_coding_challenge' => true, 'order_index' => 23]);
        $only->fresh()->delete();
        $this->assertDatabaseMissing('challenges', ['id' => $only->id]);
        $this->assertDatabaseHas('challenges', ['id' => $v2->id, 'core_module_key' => 'core-23-data-warehousing']);
    }

    // ── Fixtures ─────────────────────────────────────────────────────

    /** @param array<string, string> $renamed */
    private function curriculum(array $renamed = []): void
    {
        foreach (CoreCurriculum::MODULES as $index => $entry) {
            Module::create([
                'title' => $renamed[$entry['title']] ?? $entry['title'],
                'description' => 'Core.',
                'order_index' => $index + 1,
                'year_level' => $entry['year'],
                'xp_reward' => 100,
                'is_published' => true,
            ]);
        }
    }

    private function coreModule(): Module
    {
        $this->curriculum();
        CoreCurriculum::sync();

        return Module::where('module_key', 'core-01-basics-of-python-programming')->firstOrFail();
    }

    private function form(Module $module, array $overrides = []): array
    {
        return array_merge([
            'title' => $module->title,
            'description' => $module->description,
            'year_level' => $module->year_level,
            'xp_reward' => $module->xp_reward ?? 100,
            'is_boss' => 0,
            'has_coding_exercises' => 0,
        ], $overrides);
    }
}
