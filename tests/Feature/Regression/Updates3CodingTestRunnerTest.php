<?php

namespace Tests\Feature\Regression;

use App\Services\CodingChallengeTestRunner;
use Tests\TestCase;

/**
 * Updates 3, C2: the shared test-case runner grades with exactly the learner
 * grader's comparison (rtrim both sides, exact match, failed or timed-out runs
 * never pass) and never lets a broken sandbox turn into a server error.
 */
class Updates3CodingTestRunnerTest extends TestCase
{
    /**
     * A canned sandbox: echoes stdin back as stdout by default, with per-stdin
     * overrides for the result shape.
     *
     * @param  array<string, array>  $overrides  stdin => partial result
     */
    private function fakeSandbox(array $overrides = [], ?array &$calls = null): callable
    {
        return function (string $code, string $stdin, array $options) use ($overrides, &$calls): array {
            if (is_array($calls)) {
                $calls[] = ['code' => $code, 'stdin' => $stdin, 'options' => $options];
            }

            $base = ['stdout' => $stdin, 'stderr' => '', 'exit_code' => 0, 'failed' => false, 'timed_out' => false];

            return array_merge($base, $overrides[$stdin] ?? []);
        };
    }

    public function test_exact_match_passes_and_trailing_newlines_are_ignored(): void
    {
        $runner = new CodingChallengeTestRunner($this->fakeSandbox([
            'a' => ['stdout' => "hello\n"],
            'b' => ['stdout' => "hello"],
            'c' => ['stdout' => "hello\n\n"],
        ]));

        $report = $runner->check('print("hello")', [
            ['input' => 'a', 'expected_output' => 'hello'],
            ['input' => 'b', 'expected_output' => "hello\n"],
            ['input' => 'c', 'expected_output' => "hello\r\n"],
        ], 600);

        $this->assertSame(3, $report['summary']['passed']);
        $this->assertSame(3, $report['summary']['total']);
        $this->assertFalse($report['summary']['capped']);

        foreach ($report['results'] as $index => $result) {
            $this->assertSame($index, $result['index']);
            $this->assertTrue($result['passed'], "Case {$index} should pass.");
            $this->assertSame('hello', $result['actual']);
            $this->assertSame('hello', $result['expected']);
            $this->assertFalse($result['timed_out']);
            $this->assertSame('', $result['stderr']);
        }
    }

    public function test_leading_whitespace_and_content_differences_fail(): void
    {
        $runner = new CodingChallengeTestRunner($this->fakeSandbox([
            'lead' => ['stdout' => ' hello'],
            'case' => ['stdout' => 'Hello'],
            'inner' => ['stdout' => "a\n\nb"],
        ]));

        $report = $runner->check('code', [
            ['input' => 'lead', 'expected_output' => 'hello'],
            ['input' => 'case', 'expected_output' => 'hello'],
            ['input' => 'inner', 'expected_output' => "a\nb"],
        ], 600);

        $this->assertSame(0, $report['summary']['passed']);
        $this->assertSame(3, $report['summary']['total']);
        $this->assertSame(' hello', $report['results'][0]['actual'], 'Leading whitespace is part of the output.');
        $this->assertFalse($report['results'][0]['passed']);
        $this->assertFalse($report['results'][1]['passed']);
        $this->assertFalse($report['results'][2]['passed']);
    }

    public function test_failed_run_does_not_pass_even_when_stdout_matches(): void
    {
        $runner = new CodingChallengeTestRunner($this->fakeSandbox([
            'x' => ['stdout' => '42', 'stderr' => 'Traceback: boom', 'exit_code' => 1, 'failed' => true],
        ]));

        $report = $runner->check('code', [['input' => 'x', 'expected_output' => '42']], 600);

        $result = $report['results'][0];
        $this->assertFalse($result['passed']);
        $this->assertTrue($result['failed']);
        $this->assertFalse($result['timed_out']);
        $this->assertSame('42', $result['actual']);
        $this->assertSame('Traceback: boom', $result['stderr']);
        $this->assertSame(0, $report['summary']['passed']);
    }

    public function test_timed_out_run_does_not_pass_even_when_stdout_matches(): void
    {
        $runner = new CodingChallengeTestRunner($this->fakeSandbox([
            'slow' => ['stdout' => 'done', 'failed' => false, 'timed_out' => true],
        ]));

        $report = $runner->check('code', [['input' => 'slow', 'expected_output' => 'done']], 600);

        $this->assertFalse($report['results'][0]['passed']);
        $this->assertTrue($report['results'][0]['timed_out']);
        $this->assertTrue($report['results'][0]['failed']);
    }

    public function test_a_result_without_a_success_flag_fails_closed(): void
    {
        $runner = new CodingChallengeTestRunner(fn () => ['stdout' => 'ok']);

        $report = $runner->check('code', [['input' => '', 'expected_output' => 'ok']], 600);

        $this->assertFalse($report['results'][0]['passed']);
        $this->assertTrue($report['results'][0]['failed']);
    }

    public function test_an_unavailable_sandbox_is_reported_per_case_not_thrown(): void
    {
        $runner = new CodingChallengeTestRunner(function (): array {
            throw new \RuntimeException('Docker is not responding');
        });

        $report = $runner->check('code', [
            ['input' => '', 'expected_output' => 'a'],
            ['input' => '', 'expected_output' => 'b'],
        ], 600);

        $this->assertSame(0, $report['summary']['passed']);
        $this->assertSame(2, $report['summary']['total']);
        foreach ($report['results'] as $result) {
            $this->assertFalse($result['passed']);
            $this->assertTrue($result['failed']);
            $this->assertStringContainsString('Docker is not responding', $result['stderr']);
        }
    }

    public function test_at_most_thirty_test_cases_run_and_the_summary_says_so(): void
    {
        $calls = [];
        $runner = new CodingChallengeTestRunner($this->fakeSandbox([], $calls));

        $cases = [];
        for ($i = 0; $i < 45; $i++) {
            $cases[] = ['input' => "in{$i}", 'expected_output' => "in{$i}"];
        }

        $report = $runner->check('code', $cases, 600);

        $this->assertCount(CodingChallengeTestRunner::MAX_TEST_CASES, $calls);
        $this->assertCount(30, $report['results']);
        $this->assertSame(30, $report['summary']['total']);
        $this->assertSame(30, $report['summary']['passed']);
        $this->assertTrue($report['summary']['capped']);
        $this->assertSame(15, $report['summary']['skipped']);
        $this->assertSame(29, $report['results'][29]['index']);
    }

    public function test_the_sandbox_receives_the_code_the_stdin_and_a_capped_timeout(): void
    {
        $calls = [];
        $runner = new CodingChallengeTestRunner($this->fakeSandbox([], $calls));

        $runner->check('print(input())', [
            ['input' => "first\n", 'expected_output' => 'first'],
            ['input' => null, 'expected_output' => ''],
            ['input' => 'hidden', 'expected_output' => 'hidden', 'is_hidden' => 1],
        ], 3600);

        $this->assertCount(3, $calls);
        $this->assertSame('print(input())', $calls[0]['code']);
        $this->assertSame("first\n", $calls[0]['stdin']);
        $this->assertSame('', $calls[1]['stdin'], 'A NULL input becomes empty stdin.');
        $this->assertSame(CodingChallengeTestRunner::MAX_SECONDS_PER_CASE, $calls[0]['options']['timeout']);

        $report = $runner->check('x', [['input' => 'a', 'expected_output' => 'a']], 3);
        $this->assertTrue($report['results'][0]['passed']);

        $calls = [];
        $runner = new CodingChallengeTestRunner($this->fakeSandbox([], $calls));
        $runner->check('x', [['input' => 'a', 'expected_output' => 'a', 'is_hidden' => '1']], 3);
        $this->assertSame(3, $calls[0]['options']['timeout'], 'A shorter question limit is honoured.');
    }

    public function test_hidden_flag_is_echoed_so_the_form_can_label_cases(): void
    {
        $runner = new CodingChallengeTestRunner($this->fakeSandbox());

        $report = $runner->check('x', [
            ['input' => 'a', 'expected_output' => 'a', 'is_hidden' => '1'],
            ['input' => 'b', 'expected_output' => 'b'],
        ], 60);

        $this->assertTrue($report['results'][0]['is_hidden']);
        $this->assertFalse($report['results'][1]['is_hidden']);
    }

    public function test_the_container_builds_the_runner_without_a_callable(): void
    {
        $runner = app(CodingChallengeTestRunner::class);

        $this->assertInstanceOf(CodingChallengeTestRunner::class, $runner);
    }

    public function test_static_grade_mirrors_the_learner_comparison(): void
    {
        $this->assertTrue(CodingChallengeTestRunner::grade(['stdout' => "5\n", 'failed' => false, 'timed_out' => false], '5'));
        $this->assertFalse(CodingChallengeTestRunner::grade(['stdout' => "5\n", 'failed' => true, 'timed_out' => false], '5'));
        $this->assertFalse(CodingChallengeTestRunner::grade(['stdout' => "5\n", 'failed' => false, 'timed_out' => true], '5'));
        $this->assertFalse(CodingChallengeTestRunner::grade(['stdout' => " 5", 'failed' => false, 'timed_out' => false], '5'));
        $this->assertFalse(CodingChallengeTestRunner::grade(['stdout' => '5'], '5'), 'Missing failed flag fails closed.');
    }
}
