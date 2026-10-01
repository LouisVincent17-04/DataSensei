<?php

namespace Tests\Feature\Regression;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * DataSensei Updates 5, tasks 4 and 5, DataSensei Modules side: the public
 * modules are written with the same visual editor. Each lesson is a section
 * card; a lesson written before the editor keeps its original HTML unless its
 * card changes; modules have review questions and learning outcomes and are
 * published only with at least one outcome and one section.
 */
class Updates5DataSenseiModulesEditorTest extends TestCase
{
    use RefreshDatabase;

    private const LEGACY_HTML = "<h2>Variables</h2>\r\n<p>A variable <em>names</em> a value.</p>\n\n<div class=\"code-window\"><pre>x = 5</pre></div>\n";

    public function test_the_editor_opens_a_module_with_its_lessons_as_section_cards(): void
    {
        [$module, $legacy] = $this->moduleWithLegacyLesson();

        $response = $this->authenticateAs($this->roleUser(User::ROLE_ADMIN))
            ->get(route('admin.modules.edit', $module))
            ->assertOk()
            ->assertSee('What Students Will Learn')
            ->assertSee('Embedded Review Questions')
            ->assertSee('Store values in variables.')
            ->assertSee('Save Changes')
            ->assertSee('Delete module');

        // Updates 6: the lesson arrives as content blocks, not as HTML.
        $data = $this->editorData($response->getContent());
        $this->assertCount(1, $data['sections']);
        $this->assertSame($legacy->id, $data['sections'][0]['id']);
        $this->assertSame('Variables', $data['sections'][0]['title']);
        $this->assertSame(['type' => 'heading', 'text' => 'Variables'], $data['sections'][0]['blocks'][0]);
        $this->assertSame(['type' => 'paragraph', 'text' => 'A variable *names* a value.'], $data['sections'][0]['blocks'][1]);
        // The code box without the usual parts is kept whole, not dropped.
        $this->assertSame('preserved', $data['sections'][0]['blocks'][2]['type']);
        $this->assertStringContainsString('x = 5', $data['sections'][0]['blocks'][2]['html']);
        $this->assertArrayNotHasKey('html', $data['sections'][0]);

        // The old lesson pages open the editor.
        $this->get(route('admin.modules.lessons.index', $module))->assertRedirect(route('admin.modules.edit', $module).'#content');
        $this->get(route('admin.modules.lessons.edit', [$module, $legacy]))->assertRedirect(route('admin.modules.edit', $module).'#content');
    }

    public function test_saving_syncs_lessons_and_leaves_an_unchanged_legacy_lesson_byte_identical(): void
    {
        [$module, $legacy] = $this->moduleWithLegacyLesson();
        $second = Lesson::create(['module_id' => $module->id, 'title' => 'Loops', 'content' => '<h2>Loops</h2><p>Old.</p>', 'order_index' => 2]);
        $doomed = Lesson::create(['module_id' => $module->id, 'title' => 'Old Extra', 'content' => '<p>Remove me.</p>', 'order_index' => 3]);
        $admin = $this->roleUser(User::ROLE_ADMIN);

        $this->authenticateAs($admin);
        $cards = $this->editorData($this->get(route('admin.modules.edit', $module))->getContent())['sections'];
        $this->assertSame(['Variables', 'Loops', 'Old Extra'], array_column($cards, 'title'));

        // A key point added to "Loops", "Old Extra" removed, a new section first.
        $cards[1]['blocks'][] = ['type' => 'key_points', 'items' => ['for repeats.']];
        $posted = [
            ['id' => null, 'title' => 'Why Python', 'blocks' => [
                ['type' => 'subheading', 'text' => 'A friendly start'],
                ['type' => 'paragraph', 'text' => "Python reads like **English**.\n\nIt is also short."],
                ['type' => 'python_code', 'title' => 'Hello', 'code' => "print('hello')", 'explanation' => 'Prints a greeting.', 'output' => 'hello'],
                ['type' => 'sql_code', 'title' => 'Top rows', 'code' => 'SELECT * FROM t LIMIT 5;', 'explanation' => '', 'result' => 'Five rows.'],
                ['type' => 'walkthrough', 'items' => ['Run it.', '']],
                ['type' => 'activity', 'text' => 'Change the text.'],
                ['type' => 'mistakes', 'items' => ['Missing quotes.']],
                ['type' => 'check', 'items' => ['What does print do?']],
            ]],
            $cards[0],
            $cards[1],
        ];

        $this->put(route('admin.modules.update', $module), $this->formFor($module, [
            'intent' => 'save',
            'lessons_json' => json_encode($posted),
            'review_questions_json' => json_encode([$this->question()]),
            'learning_outcomes' => ['Store values in variables.'],
        ]))
            ->assertRedirect(route('admin.modules.edit', $module))
            ->assertSessionHasNoErrors();

        $lessons = Lesson::where('module_id', $module->id)->orderBy('order_index')->get();
        $this->assertSame(['Why Python', 'Variables', 'Loops'], $lessons->pluck('title')->all());
        $this->assertSame([1, 2, 3], $lessons->pluck('order_index')->map(fn ($i) => (int) $i)->all());
        $this->assertNull(Lesson::find($doomed->id), 'A lesson without a card is deleted.');

        $legacy->refresh();
        $this->assertSame(self::LEGACY_HTML, $legacy->content, 'An unchanged legacy lesson keeps its HTML byte for byte.');
        $this->assertNull($legacy->blocks);

        $new = $lessons->first();
        $this->assertStringStartsWith('<h2>Why Python</h2>', $new->content, 'Without a Heading block the title is the lesson heading.');
        $this->assertStringContainsString('<strong>English</strong>', $new->content);
        $this->assertStringContainsString('PYTHON — Hello', $new->content);
        $this->assertStringContainsString('SQL — Top rows', $new->content);
        $this->assertStringContainsString('Expected Result', $new->content);
        $this->assertStringContainsString('Practice Activity', $new->content);
        $this->assertStringContainsString('Check Your Understanding', $new->content);
        $stored = json_decode($new->blocks, true);
        $this->assertSame(['subheading', 'paragraph', 'python_code', 'sql_code', 'walkthrough', 'activity', 'mistakes', 'check'], array_column($stored, 'type'));
        $this->assertSame(['Run it.'], $stored[4]['items'], 'Empty list items are dropped.');

        $second->refresh();
        $this->assertStringStartsWith('<h2>Loops</h2>', $second->content, 'Its own heading stays first; the title is not added twice.');
        $this->assertStringContainsString('<p>Old.</p>', $second->content);
        $this->assertStringContainsString('for repeats.', $second->content);

        $this->assertSame('Which keyword stores nothing?', $module->fresh()->review_questions[0]['question']);

        // Opening the editor again shows the saved blocks.
        $data = $this->editorData($this->get(route('admin.modules.edit', $module))->getContent());
        $this->assertSame($stored, $data['sections'][0]['blocks']);
        $this->assertSame('Why Python', $data['sections'][0]['title']);
    }

    public function test_a_section_students_have_started_cannot_be_deleted(): void
    {
        [$module, $legacy] = $this->moduleWithLegacyLesson();
        $student = $this->roleUser();
        $student->lessons()->syncWithoutDetaching([$legacy->id => ['is_completed' => true]]);

        $response = $this->authenticateAs($this->roleUser(User::ROLE_ADMIN))->get(route('admin.modules.edit', $module));
        $this->assertSame(1, $this->editorData($response->getContent())['sections'][0]['progress']);

        $this->put(route('admin.modules.update', $module), $this->formFor($module, [
            'intent' => 'save',
            'lessons_json' => json_encode([['id' => null, 'heading' => 'Replacement', 'body' => 'New.']]),
            'learning_outcomes' => ['Store values in variables.'],
        ]))->assertSessionHasErrors('lessons_json');

        $this->assertNotNull(Lesson::find($legacy->id));
        $this->assertSame(1, Lesson::where('module_id', $module->id)->count(), 'Nothing is saved when the sync is refused.');

        // A card that belongs to another module is refused too.
        [$other] = $this->moduleWithLegacyLesson('Other Module');
        $foreign = Lesson::where('module_id', $other->id)->first();
        $this->put(route('admin.modules.update', $module), $this->formFor($module, [
            'intent' => 'save',
            'lessons_json' => json_encode([['id' => $legacy->id, 'heading' => 'Variables', 'html' => self::LEGACY_HTML], ['id' => $foreign->id, 'heading' => 'Stolen']]),
            'learning_outcomes' => ['Store values in variables.'],
        ]))->assertSessionHasErrors('lessons_json');
        $this->assertSame($other->id, (int) $foreign->fresh()->module_id);
    }

    public function test_a_module_is_published_only_with_an_outcome_and_a_section_and_drafts_are_hidden(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $student = $this->roleUser();
        $first = Module::create(['title' => 'First Module', 'description' => 'Open', 'order_index' => 1, 'year_level' => 'Year 1', 'learning_outcomes' => ['Start.']]);
        Lesson::create(['module_id' => $first->id, 'title' => 'Start', 'content' => '<p>Start</p>', 'order_index' => 1]);

        $this->authenticateAs($admin);
        $payload = ['title' => 'Draft Module', 'description' => 'Soon', 'year_level' => 'Year 2', 'xp_reward' => 100, 'is_boss' => 0, 'has_coding_exercises' => 0];

        $this->from(route('admin.modules.create'))
            ->post(route('admin.modules.store'), $payload + ['intent' => 'publish', 'lessons_json' => json_encode([['heading' => 'Only section', 'body' => 'Text.']])])
            ->assertSessionHasErrors('learning_outcomes');
        $this->assertSame(1, Module::count(), 'Nothing is created when publishing is refused.');

        $this->post(route('admin.modules.store'), $payload + ['intent' => 'publish', 'learning_outcomes' => ['Plan a study.'], 'lessons_json' => '[]'])
            ->assertSessionHasErrors('lessons_json');
        $this->assertSame(1, Module::count());

        $this->post(route('admin.modules.store'), $payload + ['intent' => 'draft', 'lessons_json' => json_encode([['heading' => 'Only section', 'body' => 'Text.']])])
            ->assertSessionHasNoErrors();
        $draft = Module::where('title', 'Draft Module')->sole();
        $this->assertFalse($draft->is_published);
        $this->assertSame(2, (int) $draft->order_index);

        // Students do not see a draft, and cannot open it.
        $this->authenticateAs($student)->get(route('modules.index'))->assertOk()->assertSee('First Module')->assertDontSee('Draft Module');
        $this->get(route('lesson.show', $draft))->assertNotFound();

        // Publishing from the list needs an outcome.
        $this->authenticateAs($admin)->patch(route('admin.modules.status', $draft))->assertSessionHas('error');
        $this->assertFalse($draft->fresh()->is_published);

        $this->put(route('admin.modules.update', $draft), $this->formFor($draft, ['intent' => 'publish', 'learning_outcomes' => ['Plan a study.']]))
            ->assertSessionHasNoErrors();
        $this->assertTrue($draft->fresh()->is_published);
        $this->authenticateAs($student)->get(route('modules.index'))->assertSee('Draft Module');

        $this->authenticateAs($admin)->patch(route('admin.modules.status', $draft))->assertSessionHas('success');
        $this->assertFalse($draft->fresh()->is_published);
    }

    public function test_review_questions_reach_the_learning_room_and_previews_render(): void
    {
        [$module, $legacy] = $this->moduleWithLegacyLesson();
        $module->update(['review_questions' => [$this->question()]]);
        $admin = $this->roleUser(User::ROLE_ADMIN);

        $this->authenticateAs($admin)
            ->get(route('admin.modules.preview', $module))
            ->assertOk()
            ->assertSee('Store values in variables.')
            ->assertSee('A variable <em>names</em> a value.', false)
            ->assertSee('Which keyword stores nothing?')
            ->assertSee('(correct answer)');

        $this->post(route('admin.modules.preview-draft'), [
            'title' => 'Unsaved',
            'lessons_json' => json_encode([['id' => null, 'heading' => 'Draft Section', 'body' => 'Draft **bold**.']]),
            'review_questions_json' => '[]',
            'learning_outcomes' => ['Draft outcome.'],
        ])->assertOk()->assertSee('Draft Section')->assertSee('<strong>bold</strong>', false)->assertSee('Draft outcome.');

        // One section on its own, as the editor's "Preview section" shows it.
        $this->post(route('admin.lessons.preview'), ['blocks_json' => json_encode([['type' => 'section', 'heading' => 'Alone', 'key_points' => ['Point.']]])])
            ->assertOk()->assertSee('<h2>Alone</h2>', false)->assertSee('Key Points');

        $student = $this->roleUser();
        $this->authenticateAs($student)
            ->get(route('lesson.show', $module))
            ->assertOk()
            ->assertSee('What you will learn')
            ->assertSee('Store values in variables.')
            ->assertSee(route('lesson.review', $module), false);
        $this->get(route('lesson.review', $module))
            ->assertOk()
            ->assertSee('Which keyword stores nothing?')
            ->assertSee('Explanation:');
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    /** @return array{0: Module, 1: Lesson} */
    private function moduleWithLegacyLesson(string $title = 'Basics of Python Programming'): array
    {
        $module = Module::create([
            'title' => $title,
            'description' => 'Python basics.',
            'order_index' => (int) Module::max('order_index') + 1,
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'learning_outcomes' => ['Store values in variables.'],
        ]);

        // Written before the editor: HTML only, no blocks.
        $lesson = new Lesson(['module_id' => $module->id, 'title' => 'Variables', 'order_index' => 1]);
        $lesson->content = self::LEGACY_HTML;
        $lesson->save();

        return [$module->fresh(), $lesson->fresh()];
    }

    private function formFor(Module $module, array $overrides = []): array
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

    private function question(): array
    {
        return [
            'question' => 'Which keyword stores nothing?',
            'scenario' => '',
            'choices' => ['None', 'null', 'nil', 'void'],
            'answer' => 'None',
            'explanation' => 'Python uses None.',
            'why_other_choices_are_wrong' => [],
            'learning_tip' => 'Print None.',
            'difficulty_level' => 'Easy',
            'topic' => 'Values',
        ];
    }

    private function editorData(string $html): array
    {
        $this->assertSame(1, preg_match('#<script type="application/json" id="module-editor-data">(.*?)</script>#s', $html, $match));

        return json_decode($match[1], true);
    }
}
