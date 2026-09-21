<?php

namespace Tests\Feature\Regression;

use App\Models\CodingSubmission;
use Tests\Feature\Regression\Concerns\BuildsCodingChallengeWorkflow;
use Tests\Feature\Regression\Concerns\FakePythonSandbox;
use Tests\TestCase;

/**
 * DS-12 — a program that prints the expected answer and then crashes or times
 * out must not pass, earn XP, or unlock the next question as solved.
 */
class Ds12CodingRuntimeErrorCannotPassTest extends TestCase
{
    use BuildsCodingChallengeWorkflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootCodingWorkflow();
    }

    protected function tearDown(): void
    {
        $this->dropCodingWorkflowTables();
        parent::tearDown();
    }

    public function test_correct_output_followed_by_a_crash_does_not_pass_or_award_xp(): void
    {
        [$q1, $q2] = $this->makeCodingChallenge([
            ['tests' => [['', '42', false], ['', '42', true]]],
            [],
        ]);
        $this->fakeSandbox(fn () => FakePythonSandbox::crash('42', 'ZeroDivisionError: division by zero'));

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => "print('42'); 1/0"]);

        $response->assertOk();
        $this->assertSame('error', $response->json('status'));
        $this->assertSame(0, $response->json('tests_passed'));
        $this->assertSame(0, $response->json('xp_earned'));
        foreach ($response->json('results') as $result) {
            $this->assertFalse($result['passed']);
            $this->assertSame('error', $result['status']);
            $this->assertSame('runtime_error', $result['category']);
        }

        $stored = CodingSubmission::firstOrFail();
        $this->assertSame('error', $stored->status);
        $this->assertSame(0, $stored->tests_passed);
        $this->assertSame(0, $stored->xp_earned);
        $this->assertSame(0, (int) $this->student->fresh()->xp);
        $this->assertFalse(CodingSubmission::where('status', 'passed')->exists());

        // The next question stays locked: Q1 is neither solved nor timed out.
        $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertForbidden();
    }

    public function test_correct_output_followed_by_a_timeout_does_not_pass(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        $this->fakeSandbox(fn () => FakePythonSandbox::timeout('42'));

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => "print(42)\nwhile True: pass"]);

        $response->assertOk();
        $this->assertSame('error', $response->json('status'));
        $this->assertFalse($response->json('results.0.passed'));
        $this->assertSame('time_limit', $response->json('results.0.category'));
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_timeout_flag_without_failed_flag_still_fails_the_test(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        $this->fakeSandbox(fn () => ['stdout' => '42', 'failed' => false, 'timed_out' => true]);

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)']);

        $this->assertNotSame('passed', $response->json('status'));
        $this->assertFalse($response->json('results.0.passed'));
    }

    public function test_partial_credit_is_kept_for_tests_that_really_pass(): void
    {
        [$q1] = $this->makeCodingChallenge([[
            'base_xp' => 100,
            'tests' => [['a', '1', false], ['b', '2', false], ['c', '3', false], ['d', '4', true]],
        ]]);
        // a, b pass; c prints the right answer then crashes; d is a wrong answer.
        $this->fakeSandbox(fn (string $code, string $stdin) => match ($stdin) {
            'a' => FakePythonSandbox::ok('1'),
            'b' => FakePythonSandbox::ok('2'),
            'c' => FakePythonSandbox::crash('3', 'boom'),
            default => FakePythonSandbox::ok('nope'),
        });

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(input())']);

        $response->assertOk();
        $this->assertSame('failed', $response->json('status'));
        $this->assertSame(2, $response->json('tests_passed'));
        $this->assertSame(4, $response->json('tests_total'));
        $this->assertSame(50, $response->json('xp_earned'));
        $this->assertSame(['passed', 'passed', 'error', 'failed'], array_column($response->json('results'), 'status'));
        $this->assertSame([true, true, false, false], array_column($response->json('results'), 'passed'));
        $this->assertSame(50, (int) $this->student->fresh()->xp);
    }

    public function test_a_clean_run_still_passes_and_awards_xp_once(): void
    {
        [$q1, $q2] = $this->makeCodingChallenge([['tests' => [['', '42', false], ['', '42', true]]], []]);
        $this->fakeSandbox(fn () => FakePythonSandbox::ok("42\n"));

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)']);

        $response->assertOk();
        $this->assertSame('passed', $response->json('status'));
        $this->assertSame(120, $response->json('xp_earned')); // fast-solve bonus kept
        $this->assertSame(120, (int) $this->student->fresh()->xp);

        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertStatus(422);
        $this->assertSame(120, (int) $this->student->fresh()->xp);
        $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertOk();
    }

    public function test_real_python_runner_print_then_zero_division_does_not_pass(): void
    {
        $python = trim((string) @shell_exec('command -v python3 2>/dev/null'));
        if ($python === '') {
            $this->markTestSkipped('python3 is not installed.');
        }

        // Real runner, local driver: allowed here only because this is a test machine.
        config()->set('code_execution.python.driver', 'local');
        config()->set('code_execution.python.local.allow_unsafe', true);
        config()->set('code_execution.python.local.binary', $python);

        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => "print(\"42\")\n1/0\n"]);

        $response->assertOk();
        $this->assertSame('42', $response->json('results.0.actual'), 'The runner must really have printed the expected answer.');
        $this->assertStringContainsString('ZeroDivisionError', (string) $response->json('results.0.stderr'));
        $this->assertFalse($response->json('results.0.passed'));
        $this->assertSame('error', $response->json('status'));
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }
}
