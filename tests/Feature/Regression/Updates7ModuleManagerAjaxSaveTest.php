<?php

namespace Tests\Feature\Regression;

use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\ModuleLibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 7, task 1: the module managers save in the background.
 *
 * The admin module editors (DataSensei Modules and the Instructor Module
 * Library) and the instructor's Module Library assignments answer an AJAX
 * save with JSON instead of a redirect, report validation errors as 422 JSON
 * keyed by field, and keep the same validation and authorization. A normal
 * form post still redirects.
 */
class Updates7ModuleManagerAjaxSaveTest extends TestCase
{
    use RefreshDatabase;

    private const AJAX = ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'];

    public function test_a_datasensei_module_saves_in_the_background_without_a_redirect(): void
    {
        [$module, $lesson] = $this->publicModule();
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $cards = [
            ['id' => $lesson->id, 'title' => 'Variables', 'blocks' => [['type' => 'paragraph', 'text' => 'Edited in the background.']]],
            ['id' => null, 'title' => 'Loops', 'blocks' => [['type' => 'python_code', 'title' => 'Loop', 'code' => "for i in range(3):\n    print(i)", 'explanation' => '', 'output' => "0\n1\n2"]]],
        ];

        $response = $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, [
            'intent' => 'save',
            'title' => '  Python Basics  ',
            'lessons_json' => json_encode($cards),
            'review_questions_json' => json_encode([$this->question()]),
            'learning_outcomes' => ['Write a loop.'],
        ]));

        $response->assertOk()
            ->assertHeaderMissing('Location')
            ->assertJson(['message' => 'Module saved.', 'created' => false, 'is_published' => true, 'question_count' => 1])
            ->assertJsonPath('fields.title', 'Python Basics')
            ->assertJsonPath('sections.0.id', $lesson->id);

        $newId = $response->json('sections.1.id');
        $this->assertIsInt($newId);
        $this->assertSame(['Variables', 'Loops'], Lesson::where('module_id', $module->id)->orderBy('order_index')->pluck('title')->all());
        $this->assertStringContainsString('Edited in the background.', Lesson::find($lesson->id)->content);
        $this->assertSame(['Write a loop.'], $module->fresh()->learning_outcomes);

        // The next save sends the new section with its id: no duplicate lesson.
        $cards[1]['id'] = $newId;
        $cards[1]['title'] = 'Loops and ranges';
        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, [
            'intent' => 'save',
            'lessons_json' => json_encode($cards),
            'review_questions_json' => json_encode([$this->question()]),
            'learning_outcomes' => ['Write a loop.'],
        ]))->assertOk()->assertJsonPath('sections.1.id', $newId);
        $this->assertSame(2, Lesson::where('module_id', $module->id)->count());
        $this->assertSame('Loops and ranges', Lesson::find($newId)->title);

        // Unpublishing reports the new status so the editor can switch its buttons.
        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, [
            'intent' => 'unpublish',
            'lessons_json' => json_encode($cards),
            'review_questions_json' => json_encode([$this->question()]),
            'learning_outcomes' => ['Write a loop.'],
        ]))->assertOk()->assertJson(['is_published' => false]);
        $this->assertFalse((bool) $module->fresh()->is_published);
    }

    public function test_background_validation_errors_come_back_by_field_and_nothing_is_saved(): void
    {
        [$module, $lesson] = $this->publicModule();
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));
        $before = Lesson::find($lesson->id)->content;

        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, [
            'intent' => 'save',
            'title' => '',
            'xp_reward' => -1,
        ]))->assertStatus(422)->assertJsonValidationErrors(['title', 'xp_reward']);

        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, [
            'intent' => 'save',
            'lessons_json' => json_encode([['id' => $lesson->id, 'title' => '', 'blocks' => [['type' => 'paragraph', 'text' => 'Changed.']]]]),
            'learning_outcomes' => ['Write a loop.'],
        ]))->assertStatus(422)->assertJsonPath('errors.lessons_json.0', 'Section 1 needs a section title.');

        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, [
            'intent' => 'publish',
            'learning_outcomes' => ['', ''],
        ]))->assertStatus(422)->assertJsonValidationErrors(['learning_outcomes']);

        $question = $this->question();
        $question['answer'] = 'Nope';
        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, [
            'intent' => 'save',
            'review_questions_json' => json_encode([$question]),
            'learning_outcomes' => ['Write a loop.'],
        ]))->assertStatus(422)->assertJsonPath('errors.review_questions_json.0', 'Review question 1: choose its correct answer.');

        $this->assertSame('Python Programming', $module->fresh()->title);
        $this->assertSame($before, Lesson::find($lesson->id)->content);
        $this->assertSame(['Store values in variables.'], $module->fresh()->learning_outcomes);
    }

    public function test_creating_a_module_in_the_background_says_where_later_saves_go(): void
    {
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $response = $this->withHeaders(self::AJAX)->post(route('admin.modules.store'), [
            'title' => 'Statistics',
            'description' => 'Numbers.',
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'is_boss' => 0,
            'has_coding_exercises' => 0,
            'intent' => 'draft',
            'lessons_json' => json_encode([['id' => null, 'title' => 'Mean', 'blocks' => [['type' => 'paragraph', 'text' => 'The average.']]]]),
            'review_questions_json' => '[]',
            'learning_outcomes' => ['Compute a mean.'],
        ]);

        $module = Module::where('title', 'Statistics')->firstOrFail();
        $response->assertCreated()
            ->assertHeaderMissing('Location')
            ->assertJson([
                'created' => true,
                'is_published' => false,
                'module_id' => $module->id,
                'edit_url' => route('admin.modules.edit', $module),
                'update_url' => route('admin.modules.update', $module),
                'page_title' => 'Edit DataSensei Module',
            ])
            ->assertJsonPath('sections.0.id', Lesson::where('module_id', $module->id)->value('id'));
        $this->assertStringContainsString('saved as a draft', $response->json('message'));
        $this->assertSame(1, Module::where('title', 'Statistics')->count());
    }

    public function test_a_library_module_saves_in_the_background(): void
    {
        $module = $this->libraryModule();
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $sections = [
            ['source' => 0, 'title' => 'How Python Runs Code', 'blocks' => [['type' => 'paragraph', 'text' => 'Edited.']]],
            ['title' => 'Indentation', 'blocks' => [['type' => 'paragraph', 'text' => 'Spaces matter.']]],
        ];

        $this->withHeaders(self::AJAX)->put(route('admin.module-library.update', $module), $this->libraryForm($module, [
            'intent' => 'save',
            'module_code' => 'mod-lower',
            'content_sections_json' => json_encode($sections),
            'mcq_questions_json' => json_encode($module->mcq_questions),
            'learning_outcomes' => ['Explain how Python runs code.'],
        ]))->assertOk()
            ->assertHeaderMissing('Location')
            ->assertJson(['message' => 'Changes saved.', 'created' => false, 'is_published' => true])
            ->assertJsonPath('sections', [['source' => 0], ['source' => 1]])
            ->assertJsonPath('fields.module_code', 'MOD-LOWER');

        $fresh = $module->fresh();
        $this->assertSame('MOD-LOWER', $fresh->module_code);
        $this->assertSame(['How Python Runs Code', 'Indentation'], array_column($fresh->content_sections, 'heading'));
        $this->assertSame(7, $fresh->content_sections[0]['lesson_no'], 'Keys the editor does not show are kept.');

        $this->withHeaders(self::AJAX)->put(route('admin.module-library.update', $module), $this->libraryForm($module, [
            'intent' => 'publish',
            'module_code' => 'MOD-LOWER',
            'learning_outcomes' => [''],
        ]))->assertStatus(422)->assertJsonValidationErrors(['learning_outcomes']);

        // The internal module code is no longer typed (DataSensei Updates 9):
        // an empty one keeps the current code instead of failing validation.
        $this->withHeaders(self::AJAX)->put(route('admin.module-library.update', $module), $this->libraryForm($module, [
            'intent' => 'save',
            'module_code' => '',
            'estimated_minutes' => 0,
        ]))->assertStatus(422)->assertJsonValidationErrors(['estimated_minutes'])->assertJsonMissingValidationErrors(['module_code']);
        $this->assertSame('MOD-LOWER', $module->fresh()->module_code);
    }

    public function test_an_assigned_library_version_still_refuses_content_changes_in_the_background(): void
    {
        $module = $this->libraryModule();
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Data Science', 'section' => 'IT 4A', 'is_archived' => false]);
        ClassModuleAssignment::create(['class_id' => $class->id, 'module_library_item_id' => $module->id, 'status' => 'active', 'assigned_at' => now()]);
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $this->withHeaders(self::AJAX)->put(route('admin.module-library.update', $module), $this->libraryForm($module, [
            'intent' => 'save',
            'content_sections_json' => json_encode([['source' => 0, 'title' => 'Changed', 'blocks' => [['type' => 'paragraph', 'text' => 'Changed.']]]]),
            'learning_outcomes' => ['Explain how Python runs code.'],
        ]))->assertStatus(422)->assertJsonValidationErrors(['content_sections_json']);

        $this->assertSame('How Python Runs Code', $module->fresh()->content_sections[0]['heading']);
    }

    public function test_background_saves_keep_authorization(): void
    {
        [$module] = $this->publicModule();
        $library = $this->libraryModule();
        $institution = Institution::create(['name' => 'Updates7 Save Institution', 'email' => 'u7-save-'.Str::lower(Str::random(6)).'@institution.test', 'status' => 'active']);

        foreach ([$this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]), $this->roleUser(User::ROLE_USER)] as $user) {
            $this->authenticateAs($user);
            $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, ['title' => 'Hacked']))->assertForbidden();
            $this->withHeaders(self::AJAX)->post(route('admin.modules.store'), $this->publicForm($module, ['title' => 'Hacked']))->assertForbidden();
            $this->withHeaders(self::AJAX)->put(route('admin.module-library.update', $library), $this->libraryForm($library, ['title' => 'Hacked']))->assertForbidden();
        }

        auth()->logout();
        $this->flushSession();
        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $this->publicForm($module, ['title' => 'Hacked']))->assertUnauthorized();

        $this->assertSame('Python Programming', $module->fresh()->title);
        $this->assertSame('Basics of Python Programming', $library->fresh()->title);
        $this->assertSame(0, Module::where('title', 'Hacked')->count());
    }

    public function test_a_normal_form_post_still_redirects(): void
    {
        [$module] = $this->publicModule();
        $library = $this->libraryModule();
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $this->put(route('admin.modules.update', $module), $this->publicForm($module, ['intent' => 'save', 'learning_outcomes' => ['Write a loop.']]))
            ->assertRedirect(route('admin.modules.edit', $module))
            ->assertSessionHas('success', 'Module saved.');

        $this->put(route('admin.module-library.update', $library), $this->libraryForm($library, ['intent' => 'save', 'learning_outcomes' => ['Explain.']]))
            ->assertRedirect(route('admin.module-library.edit', $library))
            ->assertSessionHas('success', 'Changes saved.');
    }

    public function test_the_editor_page_has_what_the_background_save_needs(): void
    {
        [$module] = $this->publicModule();
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $html = $this->get(route('admin.modules.edit', $module))->assertOk()->getContent();
        // Both button sets are on the page; the draft ones are hidden while published.
        $this->assertMatchesRegularExpression('#<button class="btn" type="submit" name="intent" value="save" data-when="published"\s*>Save Changes</button>#', $html);
        $this->assertMatchesRegularExpression('#name="intent" value="publish" data-when="draft"\s+hidden disabled\s*>Publish</button>#', $html);
        $this->assertStringContainsString('data-editor-state role="status" aria-live="polite"', $html);
        $this->assertStringContainsString('data-editor-notice', $html);
        $this->assertStringContainsString('data-status-text', $html);
        $this->assertStringContainsString('data-editor-cancel', $html);

        $script = file_get_contents(public_path('js/admin-module-editor.js'));
        foreach (["'Saving...'", "'Failed to save'", "'Accept': 'application/json'", "'X-CSRF-TOKEN': csrf", 'function saveInBackground', 'function showServerErrors', 'event.preventDefault();'] as $needle) {
            $this->assertStringContainsString($needle, $script);
        }
    }

    public function test_instructor_module_library_assigns_and_removes_in_the_background(): void
    {
        $instructor = $this->activeInstructor();
        $module = $this->libraryModule();
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Data Science', 'section' => 'IT 4A', 'is_archived' => false]);
        $this->authenticateAs($instructor);

        $this->get(route('modules.module-library.index'))
            ->assertOk()
            ->assertSee('data-library-form', false)
            ->assertSee('data-save-state', false)
            ->assertSee('data-error-for="class_id"', false);

        $this->withHeaders(self::AJAX)->post(route('modules.module-library.assign'), [
            'class_id' => $class->id,
            'selected_modules' => [1 => $module->id],
        ])->assertOk()
            ->assertHeaderMissing('Location')
            ->assertJson(['class_id' => $class->id, 'assigned_module_ids' => [$module->id]])
            ->assertJsonPath('message', '1 module version(s) successfully assigned to Data Science.');

        $this->withHeaders(self::AJAX)->post(route('modules.module-library.assign'), [
            'class_id' => $class->id,
            'selected_modules' => [1 => ''],
        ])->assertStatus(422)->assertJsonPath('errors.selected_modules.0', 'Please select at least one module version.');

        $this->withHeaders(self::AJAX)->post(route('modules.module-library.unassign'), [
            'class_id' => $class->id,
            'remove_module_id' => $module->id,
        ])->assertOk()->assertJson(['class_id' => $class->id, 'assigned_module_ids' => []]);
        $this->assertSame('archived', ClassModuleAssignment::where('class_id', $class->id)->value('status'));

        $this->withHeaders(self::AJAX)->post(route('modules.module-library.unassign'), [
            'class_id' => $class->id,
            'remove_module_id' => $module->id,
        ])->assertStatus(422)->assertJsonPath('errors.selected_modules.0', 'That module is not assigned to Data Science.');

        // Another instructor's class stays out of reach.
        $otherClass = ClassRoom::create(['instructor_id' => $this->activeInstructor()->id, 'name' => 'Other', 'section' => 'IT 4B', 'is_archived' => false]);
        $this->withHeaders(self::AJAX)->post(route('modules.module-library.assign'), [
            'class_id' => $otherClass->id,
            'selected_modules' => [1 => $module->id],
        ])->assertNotFound();
        $this->assertFalse(ClassModuleAssignment::where('class_id', $otherClass->id)->exists());

        // A normal form post still goes back to the page.
        $this->from(route('modules.module-library.index'))->withHeaders(['Accept' => 'text/html', 'X-Requested-With' => ''])->post(route('modules.module-library.assign'), [
            'class_id' => $class->id,
            'selected_modules' => [1 => $module->id],
        ])->assertRedirect(route('modules.module-library.index'))->assertSessionHas('success');
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    /** @return array{0: Module, 1: Lesson} */
    private function publicModule(): array
    {
        $module = Module::create([
            'title' => 'Python Programming',
            'description' => 'Python basics.',
            'order_index' => (int) Module::max('order_index') + 1,
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'learning_outcomes' => ['Store values in variables.'],
        ]);
        $lesson = Lesson::create(['module_id' => $module->id, 'title' => 'Variables', 'order_index' => 1, 'content' => '<h2>Variables</h2><p>Names for values.</p>']);

        return [$module->fresh(), $lesson];
    }

    private function publicForm(Module $module, array $overrides = []): array
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

    private function libraryModule(): ModuleLibraryItem
    {
        return ModuleLibraryItem::create([
            'module_no' => 1,
            'module_code' => 'MOD-001-'.Str::upper(Str::random(5)),
            'title' => 'Basics of Python Programming',
            'year_level' => 'Year 1',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'description' => 'Python basics.',
            'estimated_minutes' => 45,
            'content_sections' => [[
                'heading' => 'How Python Runs Code',
                'body' => 'Python reads a script from top to bottom.',
                'key_points' => ['Order matters.'],
                'lesson_no' => 7,
            ]],
            'mcq_questions' => [$this->question()],
            'learning_outcomes' => ['Explain how Python runs code.'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    private function libraryForm(ModuleLibraryItem $module, array $overrides = []): array
    {
        return array_merge([
            'module_no' => $module->module_no,
            'module_code' => $module->module_code,
            'title' => $module->title,
            'year_level' => $module->year_level,
            'version_no' => $module->version_no,
            'version_name' => $module->version_name,
            'version_code' => $module->version_code,
            'description' => $module->description,
            'estimated_minutes' => $module->estimated_minutes,
            'sort_order' => $module->sort_order,
        ], $overrides);
    }

    private function question(): array
    {
        return [
            'question' => 'Which keyword stores nothing?',
            'scenario' => '',
            'choices' => ['None', 'null', 'nil', 'void'],
            'answer' => 'None',
            'explanation' => 'Python uses None.',
            'why_other_choices_are_wrong' => [],
            'learning_tip' => '',
            'difficulty_level' => 'Easy',
            'topic' => 'Values',
        ];
    }

    private function activeInstructor(): User
    {
        $institution = Institution::create([
            'name' => 'Updates7 Institution '.Str::random(4),
            'email' => 'updates7-'.Str::lower(Str::random(6)).'@institution.test',
            'status' => 'active',
        ]);

        return $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
    }
}
