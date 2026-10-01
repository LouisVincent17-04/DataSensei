<?php

namespace Tests\Feature\Regression;

use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\ModuleLibraryItem;
use App\Models\User;
use App\Services\LessonBlockRenderer;
use App\Services\ModuleBlockConverter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 6, task 2: the Google Forms-style module builder.
 *
 * Existing modules open already split into content blocks (headings,
 * paragraphs, lists, Python and SQL code, tables, knowledge checks); what
 * cannot be split is kept whole, so nothing is lost. Admins and instructors
 * see the same organised lesson, never raw HTML or JSON. Only admins edit.
 */
class Updates6ModuleBuilderTest extends TestCase
{
    use RefreshDatabase;

    private const LESSON_HTML = <<<'HTML'
<h2>Python Foundations</h2>
<p>Python is an <strong>interpreted</strong> language. Use <code>*args</code> for extra values, not 2 * 3.</p>

<h3>Your First Script: The <code>print()</code> Function</h3>
<ul><li>Readable</li><li>Popular with <em>data</em> teams</li></ul>
<div class="code-window" style="background:var(--surface2);">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;"><span style="font-size:0.75rem;">PYTHON — Basic Output</span><button onclick="launchIDE(this)">Try in Compiler →</button></div>
  <div style="padding:16px;">
    <div class="code-content" style="white-space:pre;"><span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Hello"</span>)</div>
    <div style="white-space:pre;">
<span style="text-transform:uppercase;">Console Output</span>Hello</div>
  </div>
</div>
<div class="code-window" style="background:var(--surface2);">
  <div style="background:rgba(0,0,0,0.2);"><span>SQL — Top rows</span></div>
  <div style="padding:16px;"><div class="code-content">SELECT * FROM sales LIMIT 5;</div></div>
</div>
<div style="background:var(--surface2);border:1px solid var(--border);">
  <table><thead><tr><th>Chart</th><th>Best for</th></tr></thead><tbody><tr><td>Bar</td><td>Comparing groups</td></tr></tbody></table>
</div>
<div class="step-grid" style="display:grid;"><div>01 Define</div><div>02 Collect</div></div>
<style>
            .quiz-wrapper{display:flex;}
        </style><div class="quiz-wrapper" id="wrap_T1"><div class="quiz-score-bar"><span>Knowledge Check</span><span class="quiz-score-val"><span id="score_T1">0</span> / 1</span></div><div class="quiz-card" id="T1_q1"><div class="quiz-card-header"><span class="quiz-q-num">Q1</span><span class="quiz-q-text">Which prints text?</span></div><div class="quiz-options"><button class="quiz-option" onclick="checkAnswer(this,'T1_q1',false,'T1')"><span class="opt-key">A</span> echo()</button><button class="quiz-option" onclick="checkAnswer(this,'T1_q1',true,'T1')"><span class="opt-key">B</span> print()</button></div><div class="quiz-explanation" id="T1_q1-exp"><strong>Explanation:</strong> print() writes to the console.</div></div></div>
HTML;

    public function test_existing_lesson_html_becomes_content_blocks_without_losing_anything(): void
    {
        $converter = app(ModuleBlockConverter::class);
        $blocks = $converter->fromHtml(self::LESSON_HTML);

        $this->assertSame(['heading', 'paragraph', 'subheading', 'bulleted_list', 'python_code', 'sql_code', 'table', 'preserved', 'quiz'], array_column($blocks, 'type'));
        $this->assertSame('Python Foundations', $blocks[0]['text']);
        $this->assertSame('Python is an **interpreted** language. Use `*args` for extra values, not 2 * 3.', $blocks[1]['text']);
        $this->assertSame('Your First Script: The `print()` Function', $blocks[2]['text']);
        $this->assertSame(['Readable', 'Popular with *data* teams'], $blocks[3]['items']);
        $this->assertSame(['type' => 'python_code', 'title' => 'Basic Output', 'code' => 'print("Hello")', 'explanation' => '', 'output' => 'Hello'], $blocks[4]);
        $this->assertSame('Top rows', $blocks[5]['title']);
        $this->assertSame('SELECT * FROM sales LIMIT 5;', $blocks[5]['code']);
        $this->assertSame(['Chart', 'Best for'], $blocks[6]['columns']);
        $this->assertSame([['Bar', 'Comparing groups']], $blocks[6]['rows']);
        // The custom grid cannot be split: kept whole, together with the
        // non-standard quiz style next to it.
        $this->assertStringContainsString('01 Define', $blocks[7]['html']);
        $this->assertStringContainsString('.quiz-wrapper{display:flex;}', $blocks[7]['html']);
        $this->assertSame([['question' => 'Which prints text?', 'choices' => ['echo()', 'print()'], 'answer' => 1, 'explanation' => 'print() writes to the console.']], $blocks[8]['questions']);
        $this->assertSame('T1', $blocks[8]['prefix']);

        // The same content always converts the same way (the editors rely on it).
        $this->assertSame($blocks, app(LessonBlockRenderer::class)->normalize($blocks));
        $this->assertSame($blocks, $converter->fromHtml(self::LESSON_HTML));

        // Rendered again, every word is still there and the quiz still works.
        $html = app(LessonBlockRenderer::class)->render($blocks);
        foreach (['Python Foundations', 'interpreted', '*args', 'not 2 * 3', 'print()', 'Popular with', 'Hello', 'SELECT * FROM sales LIMIT 5;', 'Comparing groups', '01 Define', '02 Collect', 'Which prints text?', 'print() writes to the console.'] as $text) {
            $this->assertStringContainsString($text, html_entity_decode(strip_tags($html)), $text);
        }
        $this->assertStringContainsString("checkAnswer(this,'T1_q1',true,'T1')", $html);
        $this->assertStringContainsString('window.checkAnswer', $html, 'The quiz brings its script.');
    }

    public function test_library_sections_become_blocks_and_every_role_sees_the_same_organised_lesson(): void
    {
        $library = $this->libraryModule();
        $blocks = app(ModuleBlockConverter::class)->fromLibrarySection($library->content_sections[0]);
        $this->assertSame(['subheading', 'paragraph', 'sql_code', 'walkthrough', 'activity', 'mistakes', 'key_points', 'check'], array_column($blocks, 'type'));
        $this->assertSame('Trace the query.', $blocks[4]['text'], 'The seeded activity key is read too.');

        $institution = Institution::create(['name' => 'U6 Institution', 'email' => 'u6@institution.test', 'status' => 'active']);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
        $student = $this->roleUser();
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Data Science', 'section' => 'IT 4A', 'is_archived' => false]);
        $class->students()->attach($student->id, ['enrolled_at' => now()]);
        ClassModuleAssignment::create(['class_id' => $class->id, 'module_library_item_id' => $library->id, 'status' => 'active', 'assigned_at' => now()]);

        foreach ([[$instructor, route('modules.module-library.show', $library)], [$student, route('student.modules.show', ['module' => $library->id, 'class' => $class->id])]] as [$user, $url]) {
            $page = $this->authenticateAs($user)->get($url)->assertOk();
            $page->assertSee('Joins and filters')
                ->assertSee('Rows are filtered before grouping.')
                ->assertSee('Walkthrough')
                ->assertSee('Practice Activity')
                ->assertSee('Common Mistakes')
                ->assertSee('Key Points')
                ->assertSee('Check Your Understanding')
                ->assertSee('SQL Example')
                ->assertDontSee('"heading"', false)
                ->assertDontSee('lesson_no');
            // The code is coloured like the learning room's code windows.
            $this->assertStringContainsString('<span style="color:', $page->getContent());
        }
    }

    public function test_the_library_builder_keeps_untouched_sections_and_stores_edited_ones_as_blocks(): void
    {
        $library = $this->libraryModule();
        $original = $library->content_sections;
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $this->authenticateAs($admin);

        $data = $this->editorData($this->get(route('admin.module-library.edit', $library))->getContent());
        $this->assertSame(['Joins and filters', 'Grouping'], array_column($data['sections'], 'title'));

        // Nothing changed: stored exactly as before, every key kept.
        $this->put(route('admin.module-library.update', $library), $this->libraryForm($library, ['content_sections_json' => json_encode($data['sections'])]))
            ->assertSessionHasNoErrors();
        $this->assertSame($original, $library->fresh()->content_sections);

        // The second section edited: a Python block added, a list item changed.
        $sections = $data['sections'];
        $sections[1]['blocks'][] = ['type' => 'python_code', 'title' => 'Group in pandas', 'code' => "df.groupby('region').sum()", 'explanation' => 'Totals per region.', 'output' => ''];
        $sections[1]['blocks'][1]['text'] = 'GROUP BY collects rows.';
        $this->put(route('admin.module-library.update', $library), $this->libraryForm($library, ['content_sections_json' => json_encode($sections)]))
            ->assertSessionHasNoErrors();

        $stored = $library->fresh()->content_sections;
        $this->assertSame($original[0], $stored[0], 'The untouched section is not rewritten.');
        $this->assertSame('Grouping', $stored[1]['heading']);
        $this->assertSame(8, $stored[1]['lesson_no'], 'Keys the builder does not show are kept.');
        $this->assertSame(['LO2'], $stored[1]['ilo_codes']);
        $this->assertArrayNotHasKey('body', $stored[1], 'The content now lives in blocks only.');
        $this->assertSame(['subheading', 'paragraph', 'python_code'], array_column($stored[1]['blocks'], 'type'));

        // The student viewer shows the edited blocks.
        $this->get(route('admin.module-library.preview', $library))
            ->assertOk()
            ->assertSee('Group in pandas')
            ->assertSee('Totals per region.')
            ->assertSee('GROUP BY collects rows.');

        // "Preview section" renders one section on its own.
        $this->post(route('admin.module-library.preview-draft'), [
            'section_only' => 1,
            'content_sections_json' => json_encode([$sections[1]]),
        ])->assertOk()->assertSee('Group in pandas')->assertDontSee('Module Contents')->assertSee('is-section-only');
    }

    public function test_knowledge_checks_need_a_correct_answer_when_their_section_is_saved(): void
    {
        [$module] = $this->publicModule();
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $this->authenticateAs($admin);

        $card = ['id' => null, 'title' => 'Quiz time', 'blocks' => [
            ['type' => 'quiz', 'title' => '', 'prefix' => 'kcnew', 'questions' => [['question' => 'Pick one', 'choices' => ['A', 'B'], 'answer' => -1, 'explanation' => '']]],
        ]];

        $this->put(route('admin.modules.update', $module), $this->moduleForm($module, ['intent' => 'save', 'lessons_json' => json_encode([$card])]))
            ->assertSessionHasErrors('lessons_json');

        $card['blocks'][0]['questions'][0]['answer'] = 1;
        $this->put(route('admin.modules.update', $module), $this->moduleForm($module, ['intent' => 'save', 'lessons_json' => json_encode([$card])]))
            ->assertSessionHasNoErrors();

        $content = Lesson::where('module_id', $module->id)->where('title', 'Quiz time')->value('content');
        $this->assertStringContainsString("checkAnswer(this,'kcnew_q1',true,'kcnew')", $content);
        $this->assertStringContainsString('<h2>Quiz time</h2>', $content);
    }

    public function test_every_builder_block_reaches_the_learning_room(): void
    {
        [$module] = $this->publicModule();
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $blocks = [
            ['type' => 'heading', 'text' => 'Reading Data'],
            ['type' => 'subheading', 'text' => 'Why it matters'],
            ['type' => 'paragraph', 'text' => "Data comes in **many** shapes.\n\nCSV is common."],
            ['type' => 'bulleted_list', 'items' => ['CSV', 'JSON']],
            ['type' => 'numbered_list', 'items' => ['Open', 'Read']],
            ['type' => 'note', 'title' => '', 'text' => 'Always check the encoding.'],
            ['type' => 'example', 'title' => 'Sales file', 'text' => 'A file with one row per sale.'],
            ['type' => 'image', 'src' => '/uploads/lessons/1/chart.png', 'alt' => 'A chart', 'caption' => 'Monthly sales', 'width' => 50],
            ['type' => 'python_code', 'title' => 'Read a CSV', 'code' => "import pandas as pd\ndf = pd.read_csv('sales.csv')", 'explanation' => 'Loads the file.', 'output' => '   region  total'],
            ['type' => 'sql_code', 'title' => 'Count rows', 'code' => 'SELECT COUNT(*) FROM sales;', 'explanation' => 'Counts sales.', 'result' => 'One number.'],
            ['type' => 'code_snippet', 'title' => 'Install', 'code' => 'pip install pandas', 'explanation' => '', 'output' => ''],
            ['type' => 'walkthrough', 'items' => ['Import pandas.', 'Read the file.']],
            ['type' => 'activity', 'text' => 'Read your own CSV.'],
            ['type' => 'mistakes', 'items' => ['Wrong path.']],
            ['type' => 'key_points', 'items' => ['read_csv returns a DataFrame.']],
            ['type' => 'check', 'items' => ['What does read_csv return?']],
            ['type' => 'table', 'label' => 'Formats', 'columns' => ['Format', 'Reader'], 'rows' => [['CSV', 'read_csv']], 'note' => ''],
        ];

        $this->authenticateAs($admin)
            ->put(route('admin.modules.update', $module), $this->moduleForm($module, ['intent' => 'save', 'lessons_json' => json_encode([['id' => null, 'title' => 'Reading Data', 'blocks' => $blocks]])]))
            ->assertSessionHasNoErrors();

        $lesson = Lesson::where('module_id', $module->id)->sole();
        $this->assertSame($blocks, json_decode($lesson->blocks, true));

        $page = $this->authenticateAs($this->roleUser())->get(route('lesson.show', $module))->assertOk();
        foreach (['Reading Data', 'Why it matters', '<strong>many</strong>', 'CSV is common.', '<ol><li>Open</li>', 'Important Note', 'Always check the encoding.', 'Sales file', 'Monthly sales', 'PYTHON — Read a CSV', 'Try in Compiler', 'Console Output', 'Loads the file.', 'SQL — Count rows', 'Expected Result', 'CODE — Install', 'Walkthrough', 'Practice Activity', 'Common Mistakes', 'Key Points', 'Check Your Understanding', 'read_csv'] as $expected) {
            $page->assertSee($expected, false);
        }
        $this->assertSame(1, substr_count($page->getContent(), '<h2>Reading Data</h2>'), 'The heading block is the lesson heading; the title is not added twice.');
    }

    public function test_what_you_will_learn_starts_closed_with_a_two_second_arrow_hint(): void
    {
        [$module] = $this->publicModule();
        Lesson::create(['module_id' => $module->id, 'title' => 'Start', 'content' => '<p>Start</p>', 'order_index' => 1]);

        $page = $this->authenticateAs($this->roleUser())->get(route('lesson.show', $module))->assertOk();
        $html = $page->getContent();

        $this->assertMatchesRegularExpression('#<details class="ds-outcomes page-learning-outcomes"\s*>#', $html, 'Closed: no "open" attribute.');
        $this->assertStringContainsString('class="ds-outcomes-arrow"', $html);
        $this->assertStringContainsString('animation:ds-outcomes-nudge .5s ease-in-out 4', $html, 'Four half-second nudges: two seconds.');
        $this->assertStringContainsString('prefers-reduced-motion', $html);

        $this->get(route('modules.index'))->assertOk()->assertSee('class="ds-outcomes page-modules-outcomes"', false);
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    /** @return array{0: Module} */
    private function publicModule(): array
    {
        return [Module::create([
            'title' => 'Data Files',
            'description' => 'Reading files.',
            'order_index' => 1,
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'learning_outcomes' => ['Read a CSV file.'],
        ])];
    }

    private function libraryModule(): ModuleLibraryItem
    {
        $legacyActivity = base64_decode('bmV0YWNhZF9zdHlsZV9hY3Rpdml0eQ==');

        return ModuleLibraryItem::create([
            'module_no' => 10,
            'module_code' => 'MOD-010-'.Str::upper(Str::random(5)),
            'title' => 'Database Management',
            'year_level' => 'Year 2',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'description' => 'SQL.',
            'estimated_minutes' => 45,
            'content_sections' => [
                [
                    'heading' => 'Joins and filters',
                    'subheading' => 'WHERE before GROUP BY',
                    'body' => 'Rows are filtered before grouping.',
                    'code' => "SELECT region, SUM(total)\nFROM sales\nWHERE total > 0\nGROUP BY region;",
                    'walkthrough' => ['Read FROM first.'],
                    $legacyActivity => 'Trace the query.',
                    'common_mistakes' => ['Filtering after grouping.'],
                    'key_points' => ['WHERE runs first.'],
                    'check_your_understanding' => ['When does HAVING run?'],
                    'lesson_no' => 7,
                    'ilo_codes' => ['LO1'],
                ],
                [
                    'heading' => 'Grouping',
                    'subheading' => 'Totals per group',
                    'body' => 'GROUP BY collects rows with the same value.',
                    'lesson_no' => 8,
                    'ilo_codes' => ['LO2'],
                ],
            ],
            'mcq_questions' => [],
            'learning_outcomes' => ['Write a grouped query.'],
            'sort_order' => 10,
            'is_active' => true,
        ]);
    }

    private function libraryForm(ModuleLibraryItem $module, array $overrides = []): array
    {
        return array_merge([
            'intent' => 'save',
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
            'learning_outcomes' => $module->learning_outcomes,
        ], $overrides);
    }

    private function moduleForm(Module $module, array $overrides = []): array
    {
        return array_merge([
            'title' => $module->title,
            'description' => $module->description,
            'year_level' => $module->year_level,
            'xp_reward' => $module->xp_reward ?? 100,
            'is_boss' => 0,
            'has_coding_exercises' => 0,
            'learning_outcomes' => $module->learning_outcomes,
        ], $overrides);
    }

    private function editorData(string $html): array
    {
        $this->assertSame(1, preg_match('#<script type="application/json" id="module-editor-data">(.*?)</script>#s', $html, $match));

        return json_decode($match[1], true);
    }
}
