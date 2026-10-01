<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\Institution;
use App\Models\TestCase as CodingTestCase;
use App\Models\User;
use App\Services\CodingChallengeTestRunner;
use App\Services\ReferenceSolutionVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 5, task 2: the reference solution is required, and
 * before a coding challenge is saved or published the server runs it
 * against every test case with the student grader:
 *
 *   reference solution -> test case input -> actual output -> expected output
 *
 * One failing case and nothing is saved; the author sees the input, the
 * expected output, the actual output and the error.
 */
class Updates5ReferenceSolutionCheckTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, array{code: string, stdin: string, options: array}> */
    private array $runs = [];

    protected function setUp(): void
    {
        parent::setUp();

        // A scripted sandbox: "sum" programs print the sum of the numbers on
        // stdin, programs containing "raise" crash, "loop" times out.
        $this->app->instance(CodingChallengeTestRunner::class, new CodingChallengeTestRunner(
            function (string $code, string $stdin, array $options): array {
                $this->runs[] = ['code' => $code, 'stdin' => $stdin, 'options' => $options];

                if (str_contains($code, 'raise')) {
                    return ['stdout' => '', 'stderr' => "Traceback (most recent call last):\nValueError: bad input", 'exit_code' => 1, 'failed' => true, 'timed_out' => false];
                }
                if (str_contains($code, 'loop')) {
                    return ['stdout' => '', 'stderr' => 'Execution stopped after 20 seconds.', 'exit_code' => 124, 'failed' => true, 'timed_out' => true];
                }

                $numbers = preg_split('/\s+/', trim($stdin), -1, PREG_SPLIT_NO_EMPTY);

                return ['stdout' => array_sum(array_map('intval', $numbers))."\n", 'stderr' => '', 'exit_code' => 0, 'failed' => false, 'timed_out' => false];
            }
        ));
    }

    public function test_admin_cannot_save_without_a_reference_solution(): void
    {
        $category = $this->category();

        $this->actingAsUser($this->user(User::ROLE_ADMIN))
            ->postJson(route('admin.coding-challenges.store'), $this->adminPayload($category, reference: ''))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions.0.reference_solution']);

        $this->assertSame(0, Challenge::count());
        $this->assertSame([], $this->runs);
    }

    public function test_a_wrong_reference_solution_blocks_saving_and_shows_every_detail(): void
    {
        $category = $this->category();
        $admin = $this->user(User::ROLE_ADMIN);

        // Case 2 expects 7, but 3 + 5 = 8.
        $payload = $this->adminPayload($category, cases: [
            ['input' => '1 2', 'expected_output' => '3', 'is_hidden' => 0],
            ['input' => '3 5', 'expected_output' => '7', 'is_hidden' => 1],
        ]);

        $this->actingAsUser($admin)
            ->from(route('admin.coding-challenges.create'))
            ->post(route('admin.coding-challenges.store'), $payload)
            ->assertRedirect(route('admin.coding-challenges.create'))
            ->assertSessionHasErrors('reference_solution')
            ->assertSessionHas(ReferenceSolutionVerifier::SESSION_KEY, function (array $failures): bool {
                return count($failures) === 1
                    && $failures[0]['problem'] === 1
                    && $failures[0]['case'] === 2
                    && $failures[0]['is_hidden'] === true
                    && $failures[0]['input'] === '3 5'
                    && $failures[0]['expected'] === '7'
                    && $failures[0]['actual'] === '8';
            });

        $this->assertSame(0, Challenge::count(), 'Nothing is saved.');
        $this->assertSame(['1 2', '3 5'], array_column($this->runs, 'stdin'), 'Every test case input reached the program.');
        $this->assertSame(['quiet_input_prompts' => true], $this->runs[0]['options'], 'The student grading options.');

        // The form lists the failed case.
        $this->actingAsUser($admin)
            ->withSession([ReferenceSolutionVerifier::SESSION_KEY => session(ReferenceSolutionVerifier::SESSION_KEY)])
            ->get(route('admin.coding-challenges.create'))
            ->assertOk()
            ->assertSee('The reference solution did not pass every test case')
            ->assertSee('test case 2 (hidden)')
            ->assertSee('Expected output')
            ->assertSee('Actual output');
    }

    public function test_errors_and_time_outs_are_reported(): void
    {
        $category = $this->category();

        $this->actingAsUser($this->user(User::ROLE_ADMIN))
            ->post(route('admin.coding-challenges.store'), $this->adminPayload($category, reference: "raise ValueError('bad input')"))
            ->assertSessionHasErrors('reference_solution')
            ->assertSessionHas(ReferenceSolutionVerifier::SESSION_KEY, fn (array $failures): bool => str_contains($failures[0]['error'], 'ValueError: bad input'));

        $this->actingAsUser($this->user(User::ROLE_ADMIN))
            ->post(route('admin.coding-challenges.store'), $this->adminPayload($category, reference: 'while True: loop()'))
            ->assertSessionHasErrors('reference_solution')
            ->assertSessionHas(ReferenceSolutionVerifier::SESSION_KEY, fn (array $failures): bool => str_contains($failures[0]['error'], 'ran out of time'));

        $this->assertSame(0, Challenge::count());
    }

    public function test_a_correct_reference_solution_is_saved(): void
    {
        $category = $this->category();

        $this->actingAsUser($this->user(User::ROLE_ADMIN))
            ->post(route('admin.coding-challenges.store'), $this->adminPayload($category, active: true))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $challenge = Challenge::firstOrFail();
        $this->assertTrue((bool) $challenge->is_active);
        $this->assertSame('sum(map(int, input().split()))', CodingQuestion::firstOrFail()->reference_solution);
        $this->assertCount(2, $this->runs);
    }

    public function test_publishing_checks_the_saved_reference_solution(): void
    {
        $category = $this->category();
        $admin = $this->user(User::ROLE_ADMIN);

        $missing = $this->storedChallenge($category, reference: null, expected: '3');
        $wrong = $this->storedChallenge($category, reference: 'sum', expected: '4', code: 'WRONG');
        $good = $this->storedChallenge($category, reference: 'sum', expected: '3', code: 'GOOD');

        $this->actingAsUser($admin)->patch(route('admin.coding-challenges.status', $missing))
            ->assertSessionHas('error', fn (string $message): bool => str_contains($message, 'The reference solution is required'));
        $this->actingAsUser($admin)->patch(route('admin.coding-challenges.status', $wrong))
            ->assertSessionHas('error', fn (string $message): bool => str_contains($message, 'expected "4", got "3"'));
        $this->actingAsUser($admin)->patch(route('admin.coding-challenges.status', $good))->assertSessionHas('success');

        $this->assertFalse((bool) $missing->fresh()->is_active);
        $this->assertFalse((bool) $wrong->fresh()->is_active);
        $this->assertTrue((bool) $good->fresh()->is_active);

        // Taking a challenge down needs no check.
        $this->runs = [];
        $this->actingAsUser($admin)->patch(route('admin.coding-challenges.status', $good))->assertSessionHas('success');
        $this->assertFalse((bool) $good->fresh()->is_active);
        $this->assertSame([], $this->runs);
    }

    public function test_after_students_start_a_new_reference_solution_is_still_checked(): void
    {
        $category = $this->category();
        $admin = $this->user(User::ROLE_ADMIN);
        $challenge = $this->storedChallenge($category, reference: 'sum', expected: '3', active: true);
        $question = $challenge->codingQuestions()->firstOrFail();
        CodingQuestionAttempt::create([
            'user_id' => $this->user(User::ROLE_USER)->id, 'coding_question_id' => $question->id,
            'started_at' => now()->subHour(), 'expired' => true, 'attempt_token' => Str::random(40), 'generation' => 1,
        ]);

        $payload = $this->adminPayload($category, code: 'STORED', reference: "raise RuntimeError('oops')", cases: [
            ['input' => '1 2', 'expected_output' => '3', 'is_hidden' => 0],
        ]);
        $payload['questions'][0] = array_merge($payload['questions'][0], [
            'problem_description' => $question->problem_description,
            'starter_code' => $question->starter_code,
            'time_limit_seconds' => $question->time_limit_seconds,
            'base_xp' => $question->base_xp,
        ]);
        $payload['version_no'] = $challenge->version_no;
        $payload['version_code'] = $challenge->version_code;

        $this->actingAsUser($admin)->put(route('admin.coding-challenges.update', $challenge), $payload)
            ->assertSessionHasErrors('reference_solution');
        $this->assertSame('sum', $question->fresh()->reference_solution);

        $payload['questions'][0]['reference_solution'] = 'print(sum(map(int, input().split())))';
        $this->actingAsUser($admin)->put(route('admin.coding-challenges.update', $challenge), $payload)
            ->assertSessionHasNoErrors();
        $this->assertSame('print(sum(map(int, input().split())))', $question->fresh()->reference_solution);
    }

    public function test_instructors_go_through_the_same_check(): void
    {
        $this->category('University Student', 'university-student');
        $instructor = $this->instructor();

        $payload = [
            'type' => 'coding',
            'title' => 'Sum Two Numbers',
            'description' => 'Read two integers and print their sum.',
            'time_limit_seconds' => 1800,
            'is_active' => 1,
            'questions' => [[
                'title' => 'Sum',
                'problem_description' => 'Print the sum.',
                'language' => 'python',
                'starter_code' => '',
                'reference_solution' => 'sum',
                'time_limit_seconds' => 600,
                'test_cases' => [['input' => '10 -4', 'expected_output' => '5', 'is_hidden' => 0]],
            ]],
        ];

        $this->actingAsUser($instructor)
            ->post(route('instructor.challenge-builder.store', ['type' => 'coding']), $payload)
            ->assertSessionHasErrors('reference_solution')
            ->assertSessionHas(ReferenceSolutionVerifier::SESSION_KEY, fn (array $failures): bool => $failures[0]['actual'] === '6' && $failures[0]['expected'] === '5');

        // A request that leaves the reference solution out is refused too.
        $withoutReference = $payload;
        unset($withoutReference['questions'][0]['reference_solution']);
        $this->actingAsUser($instructor)
            ->postJson(route('instructor.challenge-builder.store', ['type' => 'coding']), $withoutReference)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions.0.reference_solution']);

        $this->assertSame(0, Challenge::count());

        $payload['questions'][0]['test_cases'][0]['expected_output'] = '6';
        $this->actingAsUser($instructor)
            ->post(route('instructor.challenge-builder.store', ['type' => 'coding']), $payload)
            ->assertSessionHasNoErrors();
        $this->assertSame(1, Challenge::count());
    }

    public function test_the_real_python_checker_compares_actual_and_expected_output(): void
    {
        $python = trim((string) @shell_exec('command -v python3 2>/dev/null'));
        if ($python === '') {
            $this->markTestSkipped('python3 is not installed.');
        }

        config()->set('code_execution.python.driver', 'local');
        config()->set('code_execution.python.local.allow_unsafe', true);
        config()->set('code_execution.python.local.binary', $python);
        // The real runner, not the scripted one.
        $this->app->forgetInstance(CodingChallengeTestRunner::class);
        $this->app->forgetInstance(ReferenceSolutionVerifier::class);

        $verifier = app(ReferenceSolutionVerifier::class);
        $reference = "a, b = map(int, input().split())\nprint(a * b)";

        $failures = $verifier->failures([[
            'title' => 'Product',
            'reference_solution' => $reference,
            'test_cases' => [
                ['input' => '3 4', 'expected_output' => '12', 'is_hidden' => false],
                ['input' => '5 6', 'expected_output' => '31', 'is_hidden' => false],
                ['input' => 'x y', 'expected_output' => '0', 'is_hidden' => false],
            ],
        ]]);

        $this->assertCount(2, $failures);
        $this->assertSame(2, $failures[0]['case']);
        $this->assertSame('30', $failures[0]['actual'], 'Python really ran the program on the input.');
        $this->assertSame('31', $failures[0]['expected']);
        $this->assertSame(3, $failures[1]['case']);
        $this->assertStringContainsString('ValueError', $failures[1]['error']);

        $this->assertSame([], $verifier->failures([[
            'title' => 'Product',
            'reference_solution' => $reference,
            'test_cases' => [['input' => '3 4', 'expected_output' => '12', 'is_hidden' => false]],
        ]]));
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function category(string $name = 'Newbie', string $slug = 'newbie'): ChallengeCategory
    {
        return ChallengeCategory::create([
            'name' => $name, 'slug' => $slug, 'target_audience' => 'Students',
            'description' => $name, 'order_index' => ChallengeCategory::count() + 1,
        ]);
    }

    private function adminPayload(ChallengeCategory $category, string $reference = 'sum(map(int, input().split()))', ?array $cases = null, bool $active = false, string $code = 'CODE-SUM'): array
    {
        return [
            'challenge_category_id' => $category->id,
            'content_code' => $code,
            'title' => 'Sum Two Numbers',
            'description' => 'Warm-up.',
            'time_limit_seconds' => 1800,
            'base_xp' => 100,
            'order_index' => 1,
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'is_active' => $active ? 1 : 0,
            'questions' => [[
                'title' => 'Sum',
                'problem_description' => 'Read two numbers and print their sum.',
                'language' => 'python',
                'starter_code' => '# start',
                'reference_solution' => $reference,
                'time_limit_seconds' => 300,
                'base_xp' => 50,
                'test_cases' => $cases ?? [
                    ['input' => '1 2', 'expected_output' => '3', 'is_hidden' => 0],
                    ['input' => '10 -4', 'expected_output' => '6', 'is_hidden' => 1],
                ],
            ]],
        ];
    }

    private function storedChallenge(ChallengeCategory $category, ?string $reference, string $expected, string $code = 'STORED', bool $active = false): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id, 'content_code' => $code, 'title' => 'Stored '.$code,
            'description' => '', 'time_limit_seconds' => 1800, 'base_xp' => 100, 'order_index' => 1,
            'is_coding_challenge' => true, 'version_no' => 1, 'version_name' => 'Version 1', 'version_code' => 'V1',
            'is_active' => $active,
        ]);
        $question = CodingQuestion::create([
            'challenge_id' => $challenge->id, 'title' => 'Sum', 'problem_description' => 'Print the sum.',
            'language' => 'python', 'starter_code' => '# start', 'reference_solution' => $reference,
            'order_index' => 1, 'time_limit_seconds' => 300, 'base_xp' => 50,
        ]);
        CodingTestCase::create([
            'coding_question_id' => $question->id, 'input' => '1 2', 'expected_output' => $expected,
            'is_hidden' => false, 'order_index' => 1,
        ]);

        return $challenge;
    }

    private function user(int $role): User
    {
        return User::create([
            'name' => 'Updates5 User',
            'email' => 'updates5-'.Str::lower(Str::random(10)).'@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function instructor(): User
    {
        $institution = Institution::create([
            'name' => 'Updates5 Institution',
            'email' => 'updates5-'.Str::lower(Str::random(6)).'@institution.test',
            'status' => 'active',
        ]);

        return $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
    }

    private function actingAsUser(User $user): static
    {
        return $this->authenticateAs($user);
    }
}
