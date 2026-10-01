<?php

namespace Tests\Feature\Regression;

use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\ModuleLibraryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 5, tasks 4 and 5, Instructor Module Library side:
 * the raw JSON fields are replaced by the visual module editor; the editor
 * keeps every key of the seeded content it does not show; a version is
 * published only with at least one learning outcome; only admins can edit
 * modules and their outcomes.
 */
class Updates5ModuleLibraryEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_editor_replaces_the_raw_json_fields(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $module = $this->libraryModule();

        $this->authenticateAs($admin)
            ->get(route('admin.module-library.create'))
            ->assertOk()
            ->assertSee('Basic Information')
            ->assertSee('Intended Learning Outcomes')
            ->assertSee('What Students Will Learn')
            ->assertSee('Learning Content')
            ->assertSee('Embedded Review Questions')
            ->assertSee('Preview &amp; Publish', false)
            ->assertSee('Save Draft')
            ->assertSee('Preview Module')
            ->assertSee('>Publish<', false)
            ->assertSee('js/admin-module-editor.js', false)
            ->assertDontSee('Content Sections JSON')
            ->assertDontSee('Review Questions JSON');

        $response = $this->get(route('admin.module-library.edit', $module))
            ->assertOk()
            ->assertSee('Save Changes')
            ->assertSee('Unpublish')
            ->assertSee('name="learning_outcomes[]"', false)
            ->assertSee('Explain how Python runs code.')
            ->assertDontSee('code-editor');

        // The sections reach the editor as content blocks (Updates 6); keys
        // the editor does not show stay on the server and are kept on save.
        $data = $this->editorData($response->getContent());
        $this->assertSame('How Python Runs Code', $data['sections'][0]['title']);
        $this->assertSame(0, $data['sections'][0]['source']);
        $this->assertSame(
            ['subheading', 'paragraph', 'python_code', 'walkthrough', 'activity', 'mistakes', 'key_points', 'check'],
            array_column($data['sections'][0]['blocks'], 'type')
        );
        $this->assertArrayNotHasKey('ilo_codes', $data['sections'][0]);
        $this->assertSame('Which keyword defines a function?', $data['questions'][0]['question']);

        // The old show page no longer dumps JSON either.
        $this->get(route('admin.module-library.show', $module))
            ->assertOk()
            ->assertDontSee('Learning Sections JSON')
            ->assertSee('How Python Runs Code')
            ->assertSee(route('admin.module-library.preview', $module), false);
    }

    public function test_saving_from_the_editor_keeps_every_key_it_does_not_show(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $module = $this->libraryModule();
        $legacyActivity = base64_decode('bmV0YWNhZF9zdHlsZV9hY3Rpdml0eQ==');

        $sections = $module->content_sections;
        $sections[0]['body'] = 'Edited body.';
        $sections[0]['key_points'][] = 'A new key point.';
        $sections[] = ['heading' => 'New Section', 'subheading' => '', 'body' => 'Fresh.', 'code' => '', 'walkthrough' => ['Step one'], 'learning_activity' => 'Try it.', 'common_mistakes' => [], 'key_points' => [], 'check_your_understanding' => []];
        $questions = $module->mcq_questions;
        $questions[0]['answer'] = 'def';
        $questions[] = ['question' => 'Which symbol starts a comment?', 'scenario' => '', 'choices' => ['#', '//', '--', '/*'], 'answer' => '#', 'explanation' => 'Python comments start with #.', 'why_other_choices_are_wrong' => [], 'learning_tip' => '', 'difficulty_level' => 'Easy', 'topic' => 'Syntax'];

        $this->authenticateAs($admin)
            ->put(route('admin.module-library.update', $module), $this->formFor($module, [
                'intent' => 'save',
                'content_sections_json' => json_encode($sections),
                'mcq_questions_json' => json_encode($questions),
                'learning_outcomes' => ['Explain how Python runs code.', '  ', 'Write a small function.'],
            ]))
            ->assertRedirect(route('admin.module-library.edit', $module))
            ->assertSessionHasNoErrors();

        $module->refresh();
        $this->assertSame($sections, $module->content_sections);
        $this->assertSame(7, $module->content_sections[0]['lesson_no']);
        $this->assertSame(['LO1'], $module->content_sections[0]['ilo_codes']);
        $this->assertSame('Read each line.', $module->content_sections[0][$legacyActivity]);
        $this->assertSame($questions, $module->mcq_questions);
        $this->assertSame(12, $module->mcq_questions[0]['question_no']);
        $this->assertSame(['Explain how Python runs code.', 'Write a small function.'], $module->learning_outcomes);
        $this->assertTrue($module->is_active, 'Save Changes keeps a published version published.');
    }

    public function test_a_version_is_published_only_with_a_learning_outcome(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $this->authenticateAs($admin);

        $payload = [
            'module_no' => 40,
            'module_code' => 'MOD-040-NEW',
            'title' => 'Brand New Module',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'estimated_minutes' => 30,
            'sort_order' => 40,
            'content_sections_json' => json_encode([['heading' => 'Intro', 'body' => 'Hello.']]),
            'mcq_questions_json' => '[]',
        ];

        $this->from(route('admin.module-library.create'))
            ->post(route('admin.module-library.store'), $payload + ['intent' => 'publish'])
            ->assertRedirect(route('admin.module-library.create'))
            ->assertSessionHasErrors('learning_outcomes');
        $this->assertSame(0, ModuleLibraryItem::count());

        // A draft does not need one yet; the year level is optional.
        $this->post(route('admin.module-library.store'), $payload + ['intent' => 'draft'])->assertSessionHasNoErrors();
        $draft = ModuleLibraryItem::sole();
        $this->assertFalse($draft->is_active);
        $this->assertSame('', $draft->year_level);

        // Publishing it from the list is refused while it has no outcome.
        $this->patch(route('admin.module-library.status', $draft))->assertSessionHas('error');
        $this->assertFalse($draft->fresh()->is_active);

        // With an outcome it publishes, and unpublishes again.
        $this->put(route('admin.module-library.update', $draft), $this->formFor($draft, [
            'intent' => 'publish',
            'learning_outcomes' => ['Describe the module.'],
        ]))->assertSessionHasNoErrors();
        $this->assertTrue($draft->fresh()->is_active);

        $this->put(route('admin.module-library.update', $draft), $this->formFor($draft->fresh(), [
            'intent' => 'unpublish',
            'learning_outcomes' => ['Describe the module.'],
        ]))->assertSessionHasNoErrors();
        $this->assertFalse($draft->fresh()->is_active);

        $this->patch(route('admin.module-library.status', $draft))->assertSessionHas('success');
        $this->assertTrue($draft->fresh()->is_active);

        // Removing every outcome of a published version is refused.
        $this->put(route('admin.module-library.update', $draft), $this->formFor($draft->fresh(), ['intent' => 'save']))
            ->assertSessionHasErrors('learning_outcomes');
        $this->assertSame(['Describe the module.'], $draft->fresh()->learning_outcomes);
    }

    public function test_the_server_checks_sections_and_review_questions(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $module = $this->libraryModule();
        $this->authenticateAs($admin);

        $cases = [
            ['content_sections_json', json_encode([['heading' => '  ', 'body' => 'No title.']]), 'needs a section title'],
            ['content_sections_json', '{"heading":"not a list"}', 'could not be read'],
            ['mcq_questions_json', json_encode([['question' => 'Q?', 'choices' => ['A', 'B', 'C', 'D'], 'answer' => 'E']]), 'choose its correct answer'],
            ['mcq_questions_json', json_encode([['question' => 'Q?', 'choices' => ['A', '', 'C', 'D'], 'answer' => 'A']]), 'fill in every choice'],
            ['mcq_questions_json', json_encode([['question' => 'Q?', 'choices' => ['A', 'A', 'C', 'D'], 'answer' => 'A']]), 'two choices with the same text'],
            ['mcq_questions_json', json_encode([['question' => '', 'choices' => ['A', 'B', 'C', 'D'], 'answer' => 'A']]), 'needs the question text'],
        ];

        foreach ($cases as [$field, $json, $message]) {
            $this->put(route('admin.module-library.update', $module), $this->formFor($module, ['intent' => 'save', $field => $json]))
                ->assertSessionHasErrors($field);
            $this->assertStringContainsString($message, implode(' ', session('errors')->get($field)), $field.': '.$message);
        }

        $this->assertSame('How Python Runs Code', $module->fresh()->content_sections[0]['heading']);
    }

    public function test_an_assigned_version_keeps_its_content_but_outcomes_stay_editable(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $module = $this->libraryModule();
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Data Science', 'section' => 'IT 4A', 'is_archived' => false]);
        ClassModuleAssignment::create(['class_id' => $class->id, 'module_library_item_id' => $module->id, 'status' => 'active', 'assigned_at' => now()]);

        $this->authenticateAs($admin)
            ->get(route('admin.module-library.edit', $module))
            ->assertOk()
            ->assertSee('is assigned to at least one class');

        $changed = $module->content_sections;
        $changed[0]['body'] = 'Changed.';
        $this->put(route('admin.module-library.update', $module), $this->formFor($module, [
            'intent' => 'save',
            'content_sections_json' => json_encode($changed),
            'learning_outcomes' => ['Explain how Python runs code.'],
        ]))->assertSessionHasErrors('content_sections_json');

        $this->put(route('admin.module-library.update', $module), $this->formFor($module, [
            'intent' => 'save',
            'content_sections_json' => json_encode($module->content_sections),
            'mcq_questions_json' => json_encode($module->mcq_questions),
            'learning_outcomes' => ['Explain how Python runs code.', 'Trace a loop.'],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(['Explain how Python runs code.', 'Trace a loop.'], $module->fresh()->learning_outcomes);
        $this->assertSame('How Python Runs Code', $module->fresh()->content_sections[0]['heading']);
    }

    public function test_preview_module_shows_the_editor_content_as_students_see_it(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $module = $this->libraryModule();
        $this->authenticateAs($admin);

        $this->get(route('admin.module-library.preview', $module))
            ->assertOk()
            ->assertSee('What You Will Learn')
            ->assertSee('Explain how Python runs code.')
            ->assertSee('How Python Runs Code')
            ->assertSee('Which keyword defines a function?');

        $this->post(route('admin.module-library.preview-draft'), [
            'title' => 'Unsaved Title',
            'content_sections_json' => json_encode([['heading' => 'Unsaved Section', 'body' => 'Draft text.', 'key_points' => ['Draft point']]]),
            'mcq_questions_json' => '[]',
            'learning_outcomes' => ['Unsaved outcome.'],
        ])
            ->assertOk()
            ->assertSee('Unsaved Title')
            ->assertSee('Unsaved Section')
            ->assertSee('Draft point')
            ->assertSee('Unsaved outcome.');

        $this->assertSame('Basics of Python Programming', $module->fresh()->title, 'Preview stores nothing.');
    }

    public function test_only_admins_reach_the_module_editors(): void
    {
        $module = $this->libraryModule();

        $institution = Institution::create(['name' => 'Updates5 Editor Institution', 'email' => 'u5-editor@institution.test', 'status' => 'active']);

        foreach ([User::ROLE_INSTRUCTOR, User::ROLE_USER] as $role) {
            $this->authenticateAs($this->roleUser($role, $role === User::ROLE_INSTRUCTOR ? ['institution_id' => $institution->id] : []));

            $this->get(route('admin.module-library.edit', $module))->assertForbidden();
            $this->put(route('admin.module-library.update', $module), $this->formFor($module, ['learning_outcomes' => ['Hacked.']]))->assertForbidden();
            $this->patch(route('admin.module-library.status', $module))->assertForbidden();
            $this->post(route('admin.module-library.preview-draft'), [])->assertForbidden();
            $this->get(route('admin.modules.create'))->assertForbidden();
        }

        $this->assertSame(['Explain how Python runs code.'], $module->fresh()->learning_outcomes);
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function libraryModule(): ModuleLibraryItem
    {
        $legacyActivity = base64_decode('bmV0YWNhZF9zdHlsZV9hY3Rpdml0eQ==');

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
                'subheading' => 'Interpreters and scripts',
                'body' => 'Python reads a script from top to bottom.',
                'code' => "print('hi')",
                'walkthrough' => ['Step 1: read the line.'],
                $legacyActivity => 'Read each line.',
                'common_mistakes' => ['Skipping colons.'],
                'key_points' => ['Order matters.'],
                'check_your_understanding' => ['What runs first?'],
                'lesson_no' => 7,
                'difficulty_level' => 'Easy',
                'ilo_codes' => ['LO1'],
            ]],
            'mcq_questions' => [[
                'question' => 'Which keyword defines a function?',
                'scenario' => 'A learner writes a helper.',
                'choices' => ['def', 'function', 'fn', 'lambda'],
                'answer' => 'def',
                'explanation' => 'Python uses def.',
                'why_other_choices_are_wrong' => ['JavaScript.', 'Rust.', 'Anonymous only.'],
                'learning_tip' => 'Write one.',
                'topic' => 'Functions',
                'question_no' => 12,
                'difficulty_level' => 'Easy',
                'ilo_code' => 'LO1',
            ]],
            'learning_outcomes' => ['Explain how Python runs code.'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    private function formFor(ModuleLibraryItem $module, array $overrides = []): array
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

    private function editorData(string $html): array
    {
        $this->assertSame(1, preg_match('#<script type="application/json" id="module-editor-data">(.*?)</script>#s', $html, $match));

        return json_decode($match[1], true);
    }
}
