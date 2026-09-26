<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\Module;
use App\Models\TestCase as CodingTestCase;
use App\Models\User;
use App\Services\CodingChallengeTestRunner;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, C1 to C4: the admin coding challenge manager. A coding challenge
 * holds ordered problems, each graded by its own test cases; the manager
 * creates, edits, publishes and deletes them with the same history rules as
 * the MCQ manager, checks reference solutions against test cases without
 * persisting anything, and its output is exactly what the learner map shows.
 */
class Updates3CodingChallengeManagerTest extends TestCase
{
    use RefreshDatabase;

    // ── Pages ─────────────────────────────────────────────────────────

    public function test_index_lists_coding_challenges_with_filters_and_plain_status_text(): void
    {
        $category = $this->makeCategory();
        $other = $this->makeCategory('Rookie', 'rookie', 2);
        $module = Module::create(['title' => 'Loops and Lists', 'description' => 'x', 'order_index' => 1, 'year_level' => 1]);

        $active = $this->makeChallenge($category, 'Sum Two Numbers', true, 'CODE-SUM');
        $active->update(['module_id' => $module->id]);
        $inactive = $this->makeChallenge($category, 'Reverse a String', false, 'CODE-REV');
        $mcq = Challenge::create([
            'challenge_category_id' => $category->id, 'title' => 'An MCQ, not coding', 'description' => '',
            'time_limit_seconds' => 600, 'base_xp' => 10, 'order_index' => 1, 'is_coding_challenge' => false, 'version_no' => 1, 'is_active' => true,
        ]);
        $otherLevel = $this->makeChallenge($other, 'Rookie Problem', true, 'CODE-ROOKIE');

        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.index'))
            ->assertOk()
            ->assertSee('Sum Two Numbers')
            ->assertSee('Reverse a String')
            ->assertSee('Rookie Problem')
            ->assertDontSee('An MCQ, not coding')
            ->assertSee('Loops and Lists')
            ->assertSee('Available')
            ->assertSee('Unavailable')
            ->assertDontSee('class="badge', false)
            ->assertSee(route('admin.coding-challenges.edit', $active), false)
            ->assertSee(route('admin.coding-challenges.status', $active), false);

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.index', ['search' => 'Reverse']))
            ->assertOk()
            ->assertSee('Reverse a String')
            ->assertDontSee('Sum Two Numbers');

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee('Sum Two Numbers')
            ->assertDontSee('Reverse a String');

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.index', ['category_id' => $other->id]))
            ->assertOk()
            ->assertSee('Rookie Problem')
            ->assertDontSee('Sum Two Numbers');
    }

    public function test_create_page_renders_the_editor_preview_and_check_endpoint(): void
    {
        $this->makeCategory();

        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))
            ->get(route('admin.coding-challenges.create'))
            ->assertOk()
            ->assertSee('data-coding-form', false)
            ->assertSee('data-coding-preview', false)
            ->assertSee('Live Preview')
            ->assertSee('data-check-tests', false)
            ->assertSee(route('admin.coding-challenges.check-tests'), false)
            ->assertSee('js/admin-coding-challenge-editor.js', false)
            ->assertSee('name="questions[0][problem_description]"', false)
            ->assertSee('name="questions[0][test_cases][0][expected_output]"', false)
            ->assertSee('<option value="python"', false);
    }

    // ── Store ─────────────────────────────────────────────────────────

    public function test_store_creates_the_challenge_its_problems_and_test_cases_in_order(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $payload = $this->payload($category, [
            'questions' => [
                $this->question('Add', "Read two numbers and print their sum.", [
                    ['input' => "1\n2", 'expected_output' => '3', 'is_hidden' => 0],
                    ['input' => "10\r\n-4", 'expected_output' => "6\r\n7", 'is_hidden' => 1],
                ]),
                $this->question('Echo', "Print the input.", [
                    ['input' => 'hello', 'expected_output' => 'hello'],
                ]),
            ],
        ]);

        $response = $this->actingAsUser($admin)
            ->post(route('admin.coding-challenges.store'), $payload)
            ->assertSessionHasNoErrors();

        $challenge = Challenge::query()->where('title', 'Coding Basics')->firstOrFail();
        $response->assertRedirect(route('admin.coding-challenges.show', $challenge));

        $this->assertTrue((bool) $challenge->is_coding_challenge);
        $this->assertFalse((bool) $challenge->is_active);
        $this->assertSame('CODE-BASICS', $challenge->content_code);
        $this->assertSame('V1', $challenge->version_code);

        $questions = $challenge->codingQuestions()->with('testCases')->get();
        $this->assertCount(2, $questions);
        $this->assertSame([1, 2], $questions->pluck('order_index')->all());
        $this->assertSame(['Add', 'Echo'], $questions->pluck('title')->all());
        $this->assertSame('python', $questions[0]->language);
        $this->assertSame('print(int(input()) + int(input()))', $questions[0]->reference_solution);
        $this->assertSame('# starter', $questions[0]->starter_code);
        $this->assertSame(300, (int) $questions[0]->time_limit_seconds);
        $this->assertSame(50, (int) $questions[0]->base_xp);

        $cases = $questions[0]->testCases;
        $this->assertCount(2, $cases);
        $this->assertSame([1, 2], $cases->pluck('order_index')->all());
        $this->assertSame("1\n2", $cases[0]->input);
        $this->assertSame('3', $cases[0]->expected_output);
        $this->assertFalse((bool) $cases[0]->is_hidden);
        $this->assertSame("6\n7", $cases[1]->expected_output, 'CRLF from a browser textarea is stored as LF.');
        $this->assertSame("10\n-4", $cases[1]->input);
        $this->assertTrue((bool) $cases[1]->is_hidden);
        $this->assertCount(1, $questions[1]->testCases);

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.show', $challenge))
            ->assertOk()
            ->assertSee('Read two numbers and print their sum.')
            ->assertSee('hidden')
            ->assertSee('visible to learners')
            ->assertSee('Unavailable');
    }

    public function test_store_with_is_active_publishes_and_deactivates_other_versions(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $v1 = $this->makeChallenge($category, 'Coding Basics', true, 'CODE-BASICS');

        $this->actingAsUser($admin)
            ->post(route('admin.coding-challenges.store'), $this->payload($category, [
                'version_no' => 2, 'version_code' => 'V2', 'version_name' => 'Version 2', 'is_active' => 1,
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $v2 = Challenge::query()->where('content_code', 'CODE-BASICS')->where('version_no', 2)->firstOrFail();
        $this->assertTrue((bool) $v2->is_active);
        $this->assertFalse((bool) $v1->fresh()->is_active, 'Publishing a version deactivates its siblings.');
    }

    public function test_store_validation_requires_problems_test_cases_and_expected_output(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.store'), $this->payload($category, ['questions' => []]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions']);

        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.store'), $this->payload($category, [
                'questions' => [$this->question('No cases', 'Desc', [])],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions.0.test_cases']);

        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.store'), $this->payload($category, [
                'questions' => [$this->question('Missing output', 'Desc', [['input' => '1', 'expected_output' => '']])],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions.0.test_cases.0.expected_output']);

        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.store'), $this->payload($category, [
                'questions' => [$this->question('No description', '', [['input' => '', 'expected_output' => '1']])],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions.0.problem_description']);

        $bad = $this->question('Limits', 'Desc', [['input' => '', 'expected_output' => '1']]);
        $bad['time_limit_seconds'] = 30;
        $bad['base_xp'] = 20000;
        $bad['language'] = 'javascript';
        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.store'), $this->payload($category, ['questions' => [$bad]]))
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'questions.0.time_limit_seconds',
                'questions.0.base_xp',
                'questions.0.language',
            ]);

        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.store'), $this->payload($category, ['time_limit_seconds' => 9000, 'base_xp' => -1]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['time_limit_seconds', 'base_xp']);

        $this->assertSame(0, Challenge::count());
        $this->assertSame(0, CodingQuestion::count());
        $this->assertSame(0, CodingTestCase::count());
    }

    // ── Update ────────────────────────────────────────────────────────

    public function test_update_without_history_recreates_problems_and_test_cases(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $challenge = $this->makeChallenge($category, 'Coding Basics', false, 'CODE-BASICS');
        $oldQuestionIds = $challenge->codingQuestions()->pluck('id')->all();

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.edit', $challenge))
            ->assertOk()
            ->assertSee('Print the number.')
            ->assertSee('name="questions[0][test_cases][0][expected_output]"', false)
            ->assertDontSee('already has submission history')
            ->assertDontSee('readonly', false);

        $this->actingAsUser($admin)
            ->put(route('admin.coding-challenges.update', $challenge), $this->payload($category, [
                'title' => 'Coding Basics Renamed',
                'questions' => [
                    $this->question('Second first', 'Now the first problem.', [
                        ['input' => 'a', 'expected_output' => 'A'],
                        ['input' => 'b', 'expected_output' => 'B', 'is_hidden' => 1],
                        ['input' => 'c', 'expected_output' => 'C'],
                    ]),
                    $this->question('Brand new', 'A second problem.', [['input' => '', 'expected_output' => 'ok']]),
                ],
            ]))
            ->assertRedirect(route('admin.coding-challenges.show', $challenge))
            ->assertSessionHasNoErrors();

        $challenge->refresh();
        $this->assertSame('Coding Basics Renamed', $challenge->title);

        $questions = $challenge->codingQuestions()->with('testCases')->get();
        $this->assertCount(2, $questions);
        $this->assertSame([1, 2], $questions->pluck('order_index')->all());
        $this->assertSame(['Second first', 'Brand new'], $questions->pluck('title')->all());
        $this->assertEmpty(array_intersect($oldQuestionIds, $questions->pluck('id')->all()), 'Rows are recreated.');
        $this->assertSame(['A', 'B', 'C'], $questions[0]->testCases->pluck('expected_output')->all());
        $this->assertSame([1, 2, 3], $questions[0]->testCases->pluck('order_index')->all());
        $this->assertSame([false, true, false], $questions[0]->testCases->map(fn ($c) => (bool) $c->is_hidden)->all());
        $this->assertSame(4, CodingTestCase::count(), 'Old test cases were removed.');
    }

    public function test_update_with_history_refuses_graded_changes_but_allows_title_and_reference_solution(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $challenge = $this->makeChallenge($category, 'Coding Basics', true, 'CODE-BASICS');
        $question = $challenge->codingQuestions()->firstOrFail();
        $caseIds = $question->testCases()->pluck('id')->all();

        CodingSubmission::create([
            'user_id' => $this->makeUser(User::ROLE_USER)->id,
            'coding_question_id' => $question->id,
            'code' => 'print(1)',
            'language' => 'python',
            'status' => 'failed',
            'tests_passed' => 0,
            'tests_total' => 1,
            'xp_earned' => 0,
            'time_taken_seconds' => 5,
            'test_results' => [],
            'voided' => false,
        ]);

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.edit', $challenge))
            ->assertOk()
            ->assertSee('already has submission history')
            ->assertSee('readonly', false);

        $base = $this->payload($category, [
            'is_active' => 1,
            'questions' => [$this->existingQuestionPayload($question)],
        ]);

        // Graded content is frozen: the problem text.
        $changed = $base;
        $changed['questions'][0]['problem_description'] = 'A different task.';
        $this->actingAsUser($admin)
            ->from(route('admin.coding-challenges.edit', $challenge))
            ->put(route('admin.coding-challenges.update', $challenge), $changed)
            ->assertRedirect(route('admin.coding-challenges.edit', $challenge))
            ->assertSessionHasErrors(['questions']);

        // Graded content is frozen: a test case.
        $changed = $base;
        $changed['questions'][0]['test_cases'][0]['expected_output'] = '99';
        $this->actingAsUser($admin)
            ->putJson(route('admin.coding-challenges.update', $challenge), $changed)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions']);

        // Graded content is frozen: an added test case.
        $changed = $base;
        $changed['questions'][0]['test_cases'][] = ['input' => '', 'expected_output' => 'extra'];
        $this->actingAsUser($admin)
            ->putJson(route('admin.coding-challenges.update', $challenge), $changed)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions']);

        // Graded content is frozen: an added problem.
        $changed = $base;
        $changed['questions'][] = $this->question('More', 'More work.', [['input' => '', 'expected_output' => 'x']]);
        $this->actingAsUser($admin)
            ->putJson(route('admin.coding-challenges.update', $challenge), $changed)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions']);

        // Identity is frozen.
        $this->actingAsUser($admin)
            ->putJson(route('admin.coding-challenges.update', $challenge), array_merge($base, ['content_code' => 'OTHER-CODE']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['content_code']);

        $this->assertSame('Print the number.', $question->fresh()->problem_description);
        $this->assertSame($caseIds, $question->testCases()->pluck('id')->all(), 'Nothing was recreated.');

        // Allowed: challenge settings, problem title and reference solution.
        $allowed = $base;
        $allowed['title'] = 'Coding Basics, corrected';
        $allowed['description'] = 'New description.';
        $allowed['order_index'] = 7;
        $allowed['questions'][0]['title'] = 'Print It';
        $allowed['questions'][0]['reference_solution'] = "n = int(input())\nprint(n)";
        $this->actingAsUser($admin)
            ->put(route('admin.coding-challenges.update', $challenge), $allowed)
            ->assertRedirect(route('admin.coding-challenges.show', $challenge))
            ->assertSessionHasNoErrors();

        $challenge->refresh();
        $question->refresh();
        $this->assertSame('Coding Basics, corrected', $challenge->title);
        $this->assertSame('New description.', $challenge->description);
        $this->assertSame(7, (int) $challenge->order_index);
        $this->assertTrue((bool) $challenge->is_active);
        $this->assertSame('Print It', $question->title);
        $this->assertSame("n = int(input())\nprint(n)", $question->reference_solution);
        $this->assertSame('Print the number.', $question->problem_description);
        $this->assertSame($caseIds, $question->testCases()->pluck('id')->all(), 'Frozen test cases keep their rows.');
    }

    // ── Status ────────────────────────────────────────────────────────

    public function test_toggle_status_publishes_deactivates_siblings_and_then_deactivates(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $v1 = $this->makeChallenge($category, 'Coding Basics', true, 'CODE-BASICS', 1);
        $v2 = $this->makeChallenge($category, 'Coding Basics', false, 'CODE-BASICS', 2);

        $this->actingAsUser($admin)
            ->from(route('admin.coding-challenges.index'))
            ->patch(route('admin.coding-challenges.status', $v2))
            ->assertRedirect(route('admin.coding-challenges.index'))
            ->assertSessionHas('success');

        $this->assertTrue((bool) $v2->fresh()->is_active);
        $this->assertFalse((bool) $v1->fresh()->is_active);

        $this->actingAsUser($admin)
            ->patch(route('admin.coding-challenges.status', $v2))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertFalse((bool) $v2->fresh()->is_active);
    }

    public function test_toggle_status_refuses_to_deactivate_while_an_attempt_is_in_progress(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $challenge = $this->makeChallenge($category, 'Coding Basics', true, 'CODE-BASICS');
        $question = $challenge->codingQuestions()->firstOrFail();
        $student = $this->makeUser(User::ROLE_USER);

        $attempt = CodingQuestionAttempt::create([
            'user_id' => $student->id,
            'coding_question_id' => $question->id,
            'started_at' => now()->subSeconds(30),
            'expired' => false,
            'attempt_token' => Str::random(40),
            'generation' => 1,
        ]);

        $this->actingAsUser($admin)
            ->patch(route('admin.coding-challenges.status', $challenge))
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertTrue((bool) $challenge->fresh()->is_active, 'Still available while a learner is mid-attempt.');

        // Once the clock has run out the attempt no longer blocks deactivation.
        $attempt->update(['started_at' => now()->subSeconds($question->time_limit_seconds + 5)]);

        $this->actingAsUser($admin)
            ->patch(route('admin.coding-challenges.status', $challenge))
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertFalse((bool) $challenge->fresh()->is_active);
    }

    // ── Destroy ───────────────────────────────────────────────────────

    public function test_destroy_removes_a_challenge_without_history_and_refuses_one_with_history(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $fresh = $this->makeChallenge($category, 'Deletable', false, 'CODE-DEL');
        $this->actingAsUser($admin)
            ->delete(route('admin.coding-challenges.destroy', $fresh))
            ->assertRedirect(route('admin.coding-challenges.index'))
            ->assertSessionHas('success');
        $this->assertNull(Challenge::find($fresh->id));
        $this->assertSame(0, CodingQuestion::count());
        $this->assertSame(0, CodingTestCase::count());

        $used = $this->makeChallenge($category, 'Used', true, 'CODE-USED');
        $question = $used->codingQuestions()->firstOrFail();
        CodingQuestionAttempt::create([
            'user_id' => $this->makeUser(User::ROLE_USER)->id,
            'coding_question_id' => $question->id,
            'started_at' => now()->subHours(3),
            'expired' => true,
            'attempt_token' => Str::random(40),
            'generation' => 1,
        ]);

        $this->actingAsUser($admin)
            ->from(route('admin.coding-challenges.show', $used))
            ->delete(route('admin.coding-challenges.destroy', $used))
            ->assertRedirect(route('admin.coding-challenges.show', $used))
            ->assertSessionHas('error');
        $this->assertNotNull(Challenge::find($used->id));
        $this->assertSame(1, CodingQuestion::count());

        $this->actingAsUser($admin)
            ->get(route('admin.coding-challenges.show', $used))
            ->assertOk()
            ->assertSee('cannot be deleted')
            ->assertDontSee('Delete Challenge Version');
    }

    // ── Check tests ───────────────────────────────────────────────────

    public function test_check_tests_grades_with_the_fake_sandbox_and_persists_nothing(): void
    {
        $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $calls = [];

        $this->app->instance(CodingChallengeTestRunner::class, new CodingChallengeTestRunner(
            function (string $code, string $stdin, array $options) use (&$calls): array {
                $calls[] = [$code, $stdin, $options];

                return match ($stdin) {
                    'boom' => ['stdout' => '', 'stderr' => 'Sandbox policy: os is blocked', 'exit_code' => 126, 'failed' => true, 'timed_out' => false],
                    'slow' => ['stdout' => '', 'stderr' => '', 'exit_code' => 124, 'failed' => true, 'timed_out' => true],
                    default => ['stdout' => strtoupper($stdin) . "\n", 'stderr' => '', 'exit_code' => 0, 'failed' => false, 'timed_out' => false],
                };
            }
        ));

        $response = $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.check-tests'), [
                'reference_solution' => "import sys\r\nprint(input().upper())",
                'time_limit_seconds' => 600,
                'test_cases' => [
                    ['input' => 'abc', 'expected_output' => "ABC\r\n", 'is_hidden' => 0],
                    ['input' => 'xyz', 'expected_output' => 'wrong', 'is_hidden' => 1],
                    ['input' => 'boom', 'expected_output' => ''],
                    ['input' => 'slow', 'expected_output' => ''],
                ],
            ])
            ->assertOk()
            ->assertJsonStructure([
                'results' => [['index', 'passed', 'expected', 'actual', 'stderr', 'timed_out', 'failed', 'is_hidden']],
                'summary' => ['passed', 'total', 'capped', 'skipped'],
            ])
            ->assertJsonPath('summary.passed', 1)
            ->assertJsonPath('summary.total', 4)
            ->assertJsonPath('summary.capped', false)
            ->assertJsonPath('results.0.passed', true)
            ->assertJsonPath('results.0.expected', 'ABC')
            ->assertJsonPath('results.0.actual', 'ABC')
            ->assertJsonPath('results.1.passed', false)
            ->assertJsonPath('results.1.is_hidden', true)
            ->assertJsonPath('results.1.actual', 'XYZ')
            ->assertJsonPath('results.2.passed', false)
            ->assertJsonPath('results.2.failed', true)
            ->assertJsonPath('results.2.stderr', 'Sandbox policy: os is blocked')
            ->assertJsonPath('results.3.passed', false)
            ->assertJsonPath('results.3.timed_out', true);

        $this->assertCount(4, $calls);
        $this->assertSame("import sys\nprint(input().upper())", $calls[0][0], 'Line endings are normalised before running.');
        $this->assertSame(['timeout' => 10, 'quiet_input_prompts' => true], $calls[0][2], 'Checked the way students are graded: input() prompts are not printed.');

        $this->assertSame(0, Challenge::count());
        $this->assertSame(0, CodingQuestion::count());
        $this->assertSame(0, CodingTestCase::count());
        $this->assertSame(0, CodingSubmission::count());
        $this->assertSame(0, CodingQuestionAttempt::count());
        $this->assertIsArray($response->json('results'));
    }

    public function test_check_tests_validates_its_input_and_runs_nothing_on_bad_input(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $calls = 0;
        $this->app->instance(CodingChallengeTestRunner::class, new CodingChallengeTestRunner(
            function () use (&$calls): array {
                $calls++;

                return ['stdout' => '', 'stderr' => '', 'exit_code' => 0, 'failed' => false, 'timed_out' => false];
            }
        ));

        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.check-tests'), ['reference_solution' => '', 'test_cases' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['reference_solution', 'test_cases']);

        $this->actingAsUser($admin)
            ->postJson(route('admin.coding-challenges.check-tests'), [
                'reference_solution' => 'print(1)',
                'test_cases' => [['input' => str_repeat('x', 10001), 'expected_output' => '1']],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['test_cases.0.input']);

        $this->assertSame(0, $calls);
    }

    // ── Authorization ─────────────────────────────────────────────────

    public function test_students_and_guests_cannot_reach_the_manager(): void
    {
        $category = $this->makeCategory();
        $challenge = $this->makeChallenge($category, 'Coding Basics', true, 'CODE-BASICS');

        $this->get(route('admin.coding-challenges.index'))->assertRedirect(route('login'));

        $student = $this->makeUser(User::ROLE_USER);
        $payload = $this->payload($category);

        $this->actingAsUser($student)->get(route('admin.coding-challenges.index'))->assertForbidden();
        $this->actingAsUser($student)->get(route('admin.coding-challenges.create'))->assertForbidden();
        $this->actingAsUser($student)->post(route('admin.coding-challenges.store'), $payload)->assertForbidden();
        $this->actingAsUser($student)->postJson(route('admin.coding-challenges.check-tests'), [
            'reference_solution' => 'print(1)', 'test_cases' => [['input' => '', 'expected_output' => '1']],
        ])->assertForbidden();
        $this->actingAsUser($student)->get(route('admin.coding-challenges.show', $challenge))->assertForbidden();
        $this->actingAsUser($student)->get(route('admin.coding-challenges.edit', $challenge))->assertForbidden();
        $this->actingAsUser($student)->put(route('admin.coding-challenges.update', $challenge), $payload)->assertForbidden();
        $this->actingAsUser($student)->patch(route('admin.coding-challenges.status', $challenge))->assertForbidden();
        $this->actingAsUser($student)->delete(route('admin.coding-challenges.destroy', $challenge))->assertForbidden();

        $this->assertSame(1, Challenge::count());
        $this->assertSame('Coding Basics', $challenge->fresh()->title);
        $this->assertTrue((bool) $challenge->fresh()->is_active);
    }

    public function test_mcq_challenges_are_not_reachable_through_the_coding_manager(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $mcq = Challenge::create([
            'challenge_category_id' => $category->id, 'title' => 'MCQ', 'description' => '',
            'time_limit_seconds' => 600, 'base_xp' => 10, 'order_index' => 1, 'is_coding_challenge' => false, 'version_no' => 1, 'is_active' => true,
        ]);

        $this->actingAsUser($admin)->get(route('admin.coding-challenges.show', $mcq))->assertNotFound();
        $this->actingAsUser($admin)->get(route('admin.coding-challenges.edit', $mcq))->assertNotFound();
        $this->actingAsUser($admin)->patch(route('admin.coding-challenges.status', $mcq))->assertNotFound();
        $this->actingAsUser($admin)->delete(route('admin.coding-challenges.destroy', $mcq))->assertNotFound();
        $this->assertTrue((bool) $mcq->fresh()->is_active);
    }

    // ── Learner side ──────────────────────────────────────────────────

    public function test_the_learner_coding_map_lists_only_the_available_challenge_created_by_the_manager(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->post(route('admin.coding-challenges.store'), $this->payload($category, [
                'title' => 'Published Through the Manager', 'content_code' => 'CODE-PUB', 'is_active' => 1,
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->actingAsUser($admin)
            ->post(route('admin.coding-challenges.store'), $this->payload($category, [
                'title' => 'Still a Draft', 'content_code' => 'CODE-DRAFT', 'is_active' => 0,
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $published = Challenge::query()->where('content_code', 'CODE-PUB')->firstOrFail();
        $this->assertTrue((bool) $published->is_active);

        $student = $this->makeUser(User::ROLE_USER);

        $this->actingAsUser($student)
            ->get(route('challenges.coding.map', 'newbie'))
            ->assertOk()
            ->assertSee('Published Through the Manager')
            ->assertDontSee('Still a Draft');

        // Making it unavailable through the manager removes it from the map.
        $this->actingAsUser($admin)
            ->patch(route('admin.coding-challenges.status', $published))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->actingAsUser($student)
            ->get(route('challenges.coding.map', 'newbie'))
            ->assertOk()
            ->assertDontSee('Published Through the Manager');
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function makeCategory(string $name = 'Newbie', string $slug = 'newbie', int $order = 1): ChallengeCategory
    {
        return ChallengeCategory::create([
            'name' => $name,
            'slug' => $slug,
            'target_audience' => 'Beginners',
            'description' => $name . ' challenges',
            'order_index' => $order,
        ]);
    }

    /** One problem ("Print the number.") with one visible test case. */
    private function makeChallenge(ChallengeCategory $category, string $title, bool $active, string $contentCode, int $versionNo = 1): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'content_code' => $contentCode,
            'title' => $title,
            'description' => $title . ' description',
            'time_limit_seconds' => 1800,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => true,
            'version_no' => $versionNo,
            'version_name' => 'Version ' . $versionNo,
            'version_code' => 'V' . $versionNo,
            'is_active' => $active,
        ]);

        $question = CodingQuestion::create([
            'challenge_id' => $challenge->id,
            'title' => 'Print',
            'problem_description' => 'Print the number.',
            'language' => 'python',
            'starter_code' => '# start',
            'reference_solution' => 'print(input())',
            'order_index' => 1,
            'time_limit_seconds' => 600,
            'base_xp' => 100,
        ]);

        CodingTestCase::create([
            'coding_question_id' => $question->id,
            'input' => '7',
            'expected_output' => '7',
            'is_hidden' => false,
            'order_index' => 1,
        ]);

        return $challenge;
    }

    private function existingQuestionPayload(CodingQuestion $question): array
    {
        return [
            'title' => $question->title,
            'problem_description' => $question->problem_description,
            'language' => $question->language,
            'starter_code' => $question->starter_code,
            'reference_solution' => $question->reference_solution,
            'time_limit_seconds' => $question->time_limit_seconds,
            'base_xp' => $question->base_xp,
            'test_cases' => $question->testCases()->get()->map(fn (CodingTestCase $case): array => [
                'input' => $case->input,
                'expected_output' => $case->expected_output,
                'is_hidden' => $case->is_hidden ? 1 : 0,
            ])->all(),
        ];
    }

    private function payload(ChallengeCategory $category, array $overrides = []): array
    {
        return array_merge([
            'challenge_category_id' => $category->id,
            'content_code' => 'CODE-BASICS',
            'title' => 'Coding Basics',
            'description' => 'Warm-up problems.',
            'time_limit_seconds' => 1800,
            'base_xp' => 100,
            'order_index' => 1,
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'is_active' => 0,
            'questions' => [
                $this->question('Print', 'Print the number.', [['input' => '7', 'expected_output' => '7']]),
            ],
        ], $overrides);
    }

    private function question(string $title, string $description, array $testCases): array
    {
        return [
            'title' => $title,
            'problem_description' => $description,
            'language' => 'python',
            'starter_code' => '# starter',
            'reference_solution' => 'print(int(input()) + int(input()))',
            'time_limit_seconds' => 300,
            'base_xp' => 50,
            'test_cases' => $testCases,
        ];
    }

    private function makeUser(int $role): User
    {
        return User::create([
            'name' => 'Updates3 User',
            'email' => 'updates3-' . Str::lower(Str::random(10)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function actingAsUser(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }
}
