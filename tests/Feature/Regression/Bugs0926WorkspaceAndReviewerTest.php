<?php

namespace Tests\Feature\Regression;

use App\Models\ChallengeOption;
use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\IdeNode;
use App\Models\IdeWorkspace;
use App\Models\Institution;
use App\Models\ModuleLibraryItem;
use App\Models\User;
use App\Support\ChoiceText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsMcqChallengeWorkflow;
use Tests\TestCase;

/**
 * Reported 2026-09-26 (Bugs.pdf, bugs2.pdf, Error.docx): Module Library
 * "Assign Selected" failed, an MCQ showed two identical "hi" choices, the AI
 * reviewer cut follow-up answers short, students were signed out quickly,
 * the SQL Sandbox ran everything instead of the highlighted query, and CSV
 * uploads in the Python IDE failed at 50,000 characters.
 */
class Bugs0926WorkspaceAndReviewerTest extends TestCase
{
    use BuildsMcqChallengeWorkflow;
    use RefreshDatabase;

    // ── Module Library ────────────────────────────────────────────────

    public function test_assign_selected_accepts_modules_left_on_do_not_include(): void
    {
        $institution = Institution::create(['name' => 'Bugs0926 School', 'email' => 'school-'.Str::random(6).'@example.test', 'status' => 'active']);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'BSIT 3-A', 'section' => 'A', 'is_archived' => false]);

        $python = $this->moduleVersion(1, 'Basics of Python Programming');
        $stats = $this->moduleVersion(2, 'Basics of Statistics');
        $this->moduleVersion(3, 'Introduction to Data Science');

        // One radio group per module title; module 3 stays on "Do not include".
        $this->authenticateAs($instructor)
            ->from(route('modules.module-library.index'))
            ->post(route('modules.module-library.assign'), [
                'class_id' => $class->id,
                'selected_modules' => [1 => $python->id, 2 => $stats->id, 3 => ''],
            ])
            ->assertRedirect(route('modules.module-library.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertEqualsCanonicalizing(
            [$python->id, $stats->id],
            ClassModuleAssignment::where('class_id', $class->id)->where('status', 'active')->pluck('module_library_item_id')->map(fn ($id) => (int) $id)->all()
        );

        // Nothing picked at all is still refused, and a bogus id still fails.
        $this->authenticateAs($instructor)
            ->from(route('modules.module-library.index'))
            ->post(route('modules.module-library.assign'), [
                'class_id' => $class->id,
                'selected_modules' => [1 => '', 2 => '', 3 => ''],
            ])
            ->assertSessionHasErrors('selected_modules');

        $this->authenticateAs($instructor)
            ->from(route('modules.module-library.index'))
            ->post(route('modules.module-library.assign'), [
                'class_id' => $class->id,
                'selected_modules' => [1 => 999999, 2 => ''],
            ])
            ->assertSessionHasErrors('selected_modules.1');
    }

    // ── Answer choices whose spaces matter ────────────────────────────

    public function test_choices_that_differ_only_in_spaces_look_different(): void
    {
        $this->assertTrue(ChoiceText::hasSignificantWhitespace('  hi  '));
        $this->assertTrue(ChoiceText::hasSignificantWhitespace("def f():\n    return 1"));
        $this->assertFalse(ChoiceText::hasSignificantWhitespace('h i'));
        $this->assertFalse(ChoiceText::hasSignificantWhitespace('hi'));

        $padded = (string) ChoiceText::html('  hi  ');
        $plain = (string) ChoiceText::html('hi', true);
        $this->assertNotSame($padded, $plain);
        $this->assertSame(4, substr_count($padded, '␣'));
        $this->assertStringContainsString('ds-choice-literal', $plain);
        $this->assertSame('&lt;b&gt;', (string) ChoiceText::html('<b>'), 'Text stays escaped.');
        $this->assertStringContainsString('&lt;script&gt;', (string) ChoiceText::html('  <script>  '));
    }

    public function test_quiz_shows_the_padded_choice_with_visible_spaces(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge([], 1);
        $question = $questions[0]['question'];
        $question->update(['question_text' => 'What is the output? print("  hi  ".strip())']);
        $questions[0]['correct']->update(['option_text' => 'hi']);
        $questions[0]['wrong']->update(['option_text' => '  hi  ']);
        ChallengeOption::create(['challenge_question_id' => $question->id, 'option_text' => 'h i', 'is_correct' => false, 'order_index' => 3]);

        $html = $this->actAsStudent($student)->get($this->quizUrl($challenge))->assertOk()->getContent();

        $marker = '<span class="ds-choice-space" title="space">␣</span>';
        $this->assertStringContainsString('<code class="ds-choice-literal">'.$marker.$marker.'hi'.$marker.$marker.'</code>', $html);
        $this->assertStringContainsString('<code class="ds-choice-literal">hi</code>', $html);
        $this->assertStringContainsString('<code class="ds-choice-literal">h i</code>', $html);
    }

    // ── Sessions ──────────────────────────────────────────────────────

    public function test_signing_out_elsewhere_or_being_enrolled_does_not_end_this_session(): void
    {
        $student = $this->roleUser();
        $this->authenticateAs($student)->get(route('studentDashboard'))->assertOk();

        // Another device signs out (Laravel rotates the remember token) and an
        // instructor enrolls the student (fills in the institution).
        $institution = Institution::create(['name' => 'Bugs0926 School', 'email' => 'school-'.Str::random(6).'@example.test', 'status' => 'active']);
        $student->forceFill([
            'remember_token' => Str::random(60),
            'institution_id' => $institution->id,
        ])->save();

        $this->get(route('studentDashboard'))->assertOk();

        // A password change still ends the session.
        $student->forceFill(['password' => bcrypt('A-new-password-2026')])->save();
        $this->get(route('studentDashboard'))->assertRedirect(route('login'));
    }

    // ── Python IDE data files ─────────────────────────────────────────

    public function test_csv_files_larger_than_the_source_limit_can_be_uploaded_and_saved(): void
    {
        [$student, $workspace] = $this->workspace();
        $csv = $this->csv(120_000);

        $response = $this->authenticateAs($student)->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id,
            'parent_id' => null,
            'type' => 'file',
            'name' => 'sales.csv',
            'content' => $csv,
            'language' => 'python',
        ])->assertCreated();

        $node = IdeNode::findOrFail($response->json('node.id'));
        $this->assertSame(strlen($csv), strlen((string) $node->content));

        $this->patchJson(route('ide.nodes.save', $node), ['content' => $csv."2026-09-26,99\n"])->assertOk();

        // Python source keeps its 50,000 character limit.
        $this->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id,
            'parent_id' => null,
            'type' => 'file',
            'name' => 'huge.py',
            'content' => str_repeat('x = 1'."\n", 12_000),
        ])->assertStatus(422)->assertJsonPath('errors.content.0', '“huge.py” has more than 50,000 characters. Split the program into smaller files.');
    }

    public function test_data_files_over_the_limit_get_a_clear_message(): void
    {
        config([
            'code_execution.ide.max_data_file_bytes' => 100_000,
            'code_execution.ide.max_workspace_bytes' => 250_000,
        ]);
        [$student, $workspace] = $this->workspace();

        $this->authenticateAs($student)->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id, 'parent_id' => null, 'type' => 'file',
            'name' => 'big.csv', 'content' => $this->csv(150_000),
        ])->assertStatus(422)->assertJsonPath('errors.content.0', '“big.csv” is 146 KB. Data files can be up to 98 KB here.');

        foreach (['a.csv', 'b.csv'] as $name) {
            $this->postJson(route('ide.nodes.store'), [
                'workspace_id' => $workspace->id, 'parent_id' => null, 'type' => 'file',
                'name' => $name, 'content' => $this->csv(95_000),
            ])->assertCreated();
        }

        $this->postJson(route('ide.nodes.store'), [
            'workspace_id' => $workspace->id, 'parent_id' => null, 'type' => 'file',
            'name' => 'c.csv', 'content' => $this->csv(95_000),
        ])->assertStatus(422)->assertJsonPath('errors.content.0', fn ($message) => str_contains($message, 'Delete files you no longer need'));

        $this->assertSame(2, IdeNode::where('workspace_id', $workspace->id)->count());
    }

    public function test_ide_page_passes_the_limits_and_the_reviewer_can_expand(): void
    {
        [$student] = $this->workspace();

        $this->authenticateAs($student)
            ->get(route('ide.index'))
            ->assertOk()
            ->assertSee('const IDE_LIMITS = {"source_chars":50000', false)
            ->assertSee('id="rb-expand"', false)
            ->assertSee('toggleWide', false);

        $this->get(route('sql-sandbox.index'))
            ->assertOk()
            ->assertSee('id="run-btn-label"', false)
            ->assertSee('const q = selectedSql() || $query.value.trim();', false)
            ->assertSee('id="rb-expand"', false);
    }

    // ── AI reviewer follow-up answers ─────────────────────────────────

    public function test_follow_up_answer_keeps_prose_and_program_output(): void
    {
        $this->reviewerConfig();
        $answer = "This will output:\n```\nhi\n```\nThe strip() method removes the spaces at both ends, so print() shows hi.\n"
            ."```python\nname = input()\nprint(name.strip())\n```\nCall strip() on the value before you print it.";
        Http::fake(['*' => Http::response($this->ollamaBody($answer), 200)]);

        $message = $this->chat('What will this print?')->assertOk()->json('message');

        $this->assertStringContainsString('This will output:', $message);
        $this->assertStringContainsString('hi', $message);
        $this->assertStringContainsString('The strip() method removes the spaces at both ends, so print() shows hi.', $message);
        $this->assertStringContainsString('Call strip() on the value before you print it.', $message);
        $this->assertStringNotContainsString('name = input()', $message, 'Code the model wrote is still removed.');
        $this->assertStringNotContainsString('print(name.strip())', $message);
    }

    public function test_answer_cut_by_the_token_limit_ends_at_a_full_sentence(): void
    {
        $this->reviewerConfig();
        $answer = 'The loop stops one step early because range ends before its second value. '
            .'Change the end value so the last number is included, and then the total will match what';
        Http::fake(['*' => Http::response($this->ollamaBody($answer, 'length'), 200)]);

        $message = $this->chat('Why is the total wrong?')->assertOk()->json('message');

        $this->assertSame('The loop stops one step early because range ends before its second value.', $message);
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function moduleVersion(int $moduleNo, string $title): ModuleLibraryItem
    {
        return ModuleLibraryItem::create([
            'module_no' => $moduleNo,
            'module_code' => 'MOD'.$moduleNo.'-'.Str::upper(Str::random(5)),
            'title' => $title,
            'year_level' => '1',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1-'.Str::upper(Str::random(5)),
            'is_active' => true,
            'content_sections' => [],
            'mcq_questions' => [],
        ]);
    }

    /** @return array{0: User, 1: IdeWorkspace} */
    private function workspace(): array
    {
        $student = $this->roleUser();

        return [$student, IdeWorkspace::create(['user_id' => $student->id, 'name' => 'Workspace'])];
    }

    private function csv(int $bytes): string
    {
        $content = "date,amount\n";
        $row = 0;
        while (strlen($content) < $bytes) {
            $content .= sprintf("2026-09-%02d,%d\n", ($row % 28) + 1, $row);
            $row++;
        }

        return substr($content, 0, $bytes);
    }

    private function reviewerConfig(): void
    {
        Cache::flush();
        config([
            'cache.default' => 'array',
            'code_execution.ollama.url' => 'http://127.0.0.1:11434/api/generate',
            'code_execution.ollama.background_continuation' => false,
            'code_execution.review.fast_path' => false,
        ]);
    }

    private function ollamaBody(string $response, string $doneReason = 'stop'): array
    {
        return ['model' => 'qwen2.5-coder:1.5b-instruct', 'response' => $response, 'done' => true,
            'done_reason' => $doneReason, 'total_duration' => 1_000_000_000, 'prompt_eval_count' => 80, 'eval_count' => 24];
    }

    private function chat(string $question)
    {
        $student = $this->roleUser();

        return $this->authenticateAs($student)->postJson(route('api.code-review'), [
            'mode' => 'chat',
            'language' => 'python',
            'code' => "name = input()\nprint(name.strip())",
            'run_output' => "STDOUT:\nhi\nExit code: 0",
            'question' => $question,
        ]);
    }
}
