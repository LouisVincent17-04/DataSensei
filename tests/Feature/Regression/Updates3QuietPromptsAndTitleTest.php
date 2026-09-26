<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\CodingQuestion;
use App\Models\TestCase as CodingTestCase;
use App\Models\User;
use App\Services\CodingChallengeTestRunner;
use App\Services\PythonSandboxService;
use App\Services\PythonWarmSandbox;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Two follow-ups to the coding challenge builder.
 *
 * 1. Graded runs ask the runner not to print input() prompts. The lessons teach
 *    input("Enter a: "), and a correct program otherwise failed every stdin
 *    test with "Enter a: Enter b: 5" against an expected "5". The flag has to
 *    reach the runner on every path: the environment for the classic and local
 *    paths, the job payload for warm standby containers. The IDE never sets it.
 *    (The runner's own behaviour is covered by tests/Python/test_quiet_input_prompts.py.)
 *
 * 2. The problem title an author fills in is shown to students.
 */
class Updates3QuietPromptsAndTitleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_builders_check_asks_for_quiet_prompts(): void
    {
        $seen = null;
        $runner = new CodingChallengeTestRunner(function (string $code, string $stdin, array $options) use (&$seen): array {
            $seen = $options;

            return ['stdout' => '5', 'stderr' => '', 'exit_code' => 0, 'failed' => false, 'timed_out' => false];
        });

        $runner->check('print(5)', [['input' => '', 'expected_output' => '5']], 10);

        $this->assertTrue($seen['quiet_input_prompts'] ?? false, '"Check test cases" must grade the way students are graded.');
    }

    public function test_the_student_grader_asks_for_quiet_prompts(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/CodingQuizController.php'));

        $this->assertStringContainsString(
            "runInline(\$code, \$stdin ?? '', ['quiet_input_prompts' => true])",
            $source,
            'Grading and the learner\'s Run in the coding quiz must both use quiet prompts.'
        );
    }

    public function test_the_classic_and_local_paths_receive_the_flag_in_their_environment(): void
    {
        $service = app(PythonSandboxService::class);
        $method = (new \ReflectionClass($service))->getMethod('runnerEnvironment');

        $graded = $method->invoke($service, '/workspace', '/input', 10, false, true);
        $ide = $method->invoke($service, '/workspace', '/input', 10, true, false);

        $this->assertSame('1', $graded['DS_QUIET_INPUT_PROMPTS'] ?? null);
        $this->assertArrayNotHasKey('DS_QUIET_INPUT_PROMPTS', $ide, 'The IDE keeps its prompts.');
    }

    public function test_standby_containers_do_not_carry_the_flag_in_their_shared_environment(): void
    {
        // It must travel with each job instead, or every run in that container
        // would be quiet, including the IDE's.
        $this->assertArrayNotHasKey('DS_QUIET_INPUT_PROMPTS', app(PythonSandboxService::class)->containerEnvironment());
    }

    public function test_the_warm_path_carries_the_flag_in_the_job(): void
    {
        $workspace = storage_path('framework/testing/quiet-'.Str::random(8));
        @mkdir($workspace, 0777, true);
        file_put_contents($workspace.'/main.py', "print(input('Enter: '))\n");

        try {
            $warm = app(PythonWarmSandbox::class);
            $method = (new \ReflectionClass($warm))->getMethod('buildJob');

            $graded = $method->invoke($warm, $workspace, 'main.py', "7\n", 10, false, true);
            $ide = $method->invoke($warm, $workspace, 'main.py', '', 10, true, false);

            $this->assertTrue($graded['quiet_prompts']);
            $this->assertFalse($ide['quiet_prompts']);
        } finally {
            @unlink($workspace.'/main.py');
            @rmdir($workspace);
        }
    }

    public function test_students_receive_the_problem_title_when_the_timer_starts(): void
    {
        // The title travels with the rest of the timed content (DS-14): the
        // start POST returns it and the page script fills the heading.
        [$student, $challenge, $question] = $this->studentWithChallenge('Sum of Two Numbers');

        $this->actingAsUser($student)
            ->postJson(route('challenges.coding.start', ['slug' => 'newbie', 'challenge' => $challenge->id, 'question' => $question->id]))
            ->assertOk()
            ->assertJsonPath('question.title', 'Sum of Two Numbers');
    }

    public function test_a_started_problem_renders_its_title_on_the_page(): void
    {
        [$student, $challenge, $question] = $this->studentWithChallenge('Sum of Two Numbers');

        $this->actingAsUser($student)
            ->postJson(route('challenges.coding.start', ['slug' => 'newbie', 'challenge' => $challenge->id, 'question' => $question->id]))
            ->assertOk();

        $this->actingAsUser($student)
            ->get(route('challenges.coding.quiz', ['slug' => 'newbie', 'challenge' => $challenge->id]))
            ->assertOk()
            ->assertSee('<h2 class="problem-name" id="problem-name-0" >Sum of Two Numbers</h2>', false);
    }

    public function test_the_title_is_not_on_the_page_before_the_timer_starts(): void
    {
        [$student, $challenge] = $this->studentWithChallenge('Sum of Two Numbers');

        $this->actingAsUser($student)
            ->get(route('challenges.coding.quiz', ['slug' => 'newbie', 'challenge' => $challenge->id]))
            ->assertOk()
            ->assertDontSee('Sum of Two Numbers');
    }

    public function test_an_untitled_problem_renders_no_empty_heading(): void
    {
        [$student, $challenge] = $this->studentWithChallenge(null);

        $html = $this->actingAsUser($student)
            ->get(route('challenges.coding.quiz', ['slug' => 'newbie', 'challenge' => $challenge->id]))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression('/<h2 class="problem-name" id="problem-name-0"\s+hidden\s*>/', $html);
    }

    public function test_the_reference_solution_never_reaches_the_student_page(): void
    {
        [$student, $challenge] = $this->studentWithChallenge('Secret Check');

        $this->actingAsUser($student)
            ->get(route('challenges.coding.quiz', ['slug' => 'newbie', 'challenge' => $challenge->id]))
            ->assertOk()
            ->assertDontSee('REFERENCE_SOLUTION_MARKER');
    }

    /** @return array{0: User, 1: Challenge, 2: CodingQuestion} */
    private function studentWithChallenge(?string $title): array
    {
        $category = ChallengeCategory::create([
            'name' => 'Newbie', 'slug' => 'newbie', 'target_audience' => 'Beginners',
            'description' => 'Newbie challenges', 'order_index' => 1,
        ]);

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'content_code' => 'TITLE-'.Str::upper(Str::random(6)),
            'title' => 'Title check',
            'description' => 'Title check',
            'time_limit_seconds' => 1800,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => true,
            'is_active' => true,
        ]);

        $question = CodingQuestion::create([
            'challenge_id' => $challenge->id,
            'title' => $title,
            'problem_description' => 'Read two integers and print their sum.',
            'language' => 'python',
            'starter_code' => '# start',
            'reference_solution' => "# REFERENCE_SOLUTION_MARKER\nprint(sum(map(int, open(0).read().split())))",
            'order_index' => 1,
            'time_limit_seconds' => 600,
            'base_xp' => 100,
        ]);

        CodingTestCase::create([
            'coding_question_id' => $question->id,
            'input' => "2\n3",
            'expected_output' => '5',
            'is_hidden' => false,
            'order_index' => 1,
        ]);

        $student = User::create([
            'name' => 'Title Student',
            'email' => 'title-'.Str::lower(Str::random(8)).'@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        return [$student, $challenge, $question];
    }

    private function actingAsUser(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }
}
