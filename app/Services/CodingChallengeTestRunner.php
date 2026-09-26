<?php

namespace App\Services;

/**
 * Runs a candidate program (normally the author's reference solution) against
 * a list of test cases and grades each one exactly the way the learner-facing
 * grader does (CodingQuizController::runSingle):
 *
 *   $actual   = rtrim($result['stdout']);
 *   $expected = rtrim($tc->expected_output);
 *   $passed   = !$result['failed'] && !$result['timed_out'] && $actual === $expected;
 *
 * The service is shared by the admin coding challenge manager and the
 * instructor challenge builder. Execution is delegated to a callable so tests
 * can substitute a canned sandbox; by default it uses PythonSandboxService.
 */
class CodingChallengeTestRunner
{
    /** The most test cases a single check will execute. */
    public const MAX_TEST_CASES = 30;

    /** The longest a single test case may run, in seconds. */
    public const MAX_SECONDS_PER_CASE = 10;

    /** @var callable(string, string, array): array */
    private $execute;

    /**
     * @param  callable|null  $execute  fn (string $code, string $stdin, array $options): array
     *                                  returning the PythonSandboxService::runInline shape
     *                                  (stdout, stderr, exit_code, failed, timed_out).
     */
    public function __construct(?callable $execute = null)
    {
        $this->execute = $execute ?? static function (string $code, string $stdin, array $options): array {
            return app(PythonSandboxService::class)->runInline($code, $stdin, $options);
        };
    }

    /**
     * @param  array<int, array{input?: ?string, expected_output?: ?string, is_hidden?: mixed}>  $testCases
     * @return array{
     *     results: array<int, array{index:int, passed:bool, expected:string, actual:string, stderr:string, timed_out:bool, failed:bool, is_hidden:bool}>,
     *     summary: array{passed:int, total:int, capped:bool, skipped:int}
     * }
     */
    public function check(string $code, array $testCases, int $timeLimitSeconds): array
    {
        $testCases = array_values($testCases);
        $submitted = count($testCases);
        $capped = $submitted > self::MAX_TEST_CASES;

        if ($capped) {
            $testCases = array_slice($testCases, 0, self::MAX_TEST_CASES);
        }

        $timeout = max(1, min(self::MAX_SECONDS_PER_CASE, $timeLimitSeconds));
        $results = [];
        $passedCount = 0;

        foreach ($testCases as $index => $testCase) {
            $testCase = is_array($testCase) ? $testCase : [];
            $result = $this->runCase($code, $testCase, $timeout, $index);

            if ($result['passed']) {
                $passedCount++;
            }

            $results[] = $result;
        }

        return [
            'results' => $results,
            'summary' => [
                'passed' => $passedCount,
                'total' => count($results),
                'capped' => $capped,
                'skipped' => $capped ? $submitted - self::MAX_TEST_CASES : 0,
            ],
        ];
    }

    /**
     * Grades one sandbox result against an expected output with the exact
     * learner-facing comparison. Public so other graders can reuse it.
     */
    public static function grade(array $result, ?string $expectedOutput): bool
    {
        $actual = rtrim((string) ($result['stdout'] ?? ''));
        $expected = rtrim((string) $expectedOutput);

        // Fail closed: a result without an explicit success flag is a failure.
        $failed = (bool) ($result['failed'] ?? true);
        $timedOut = (bool) ($result['timed_out'] ?? false);

        return ! $failed && ! $timedOut && $actual === $expected;
    }

    private function runCase(string $code, array $testCase, int $timeout, int $index): array
    {
        $stdin = (string) ($testCase['input'] ?? '');
        $expected = (string) ($testCase['expected_output'] ?? '');

        try {
            // Same as grading: input() prompts are not printed.
            $result = ($this->execute)($code, $stdin, ['timeout' => $timeout, 'quiet_input_prompts' => true]);
            if (! is_array($result)) {
                $result = ['stdout' => '', 'stderr' => 'The sandbox returned no result.', 'failed' => true, 'timed_out' => false];
            }
        } catch (\Throwable $exception) {
            // The sandbox being unavailable is reported per case, never as a
            // server error: the author sees why the check could not run.
            $result = [
                'stdout' => '',
                'stderr' => 'The code sandbox could not run this test case: ' . $exception->getMessage(),
                'exit_code' => 1,
                'failed' => true,
                'timed_out' => false,
            ];
        }

        $timedOut = (bool) ($result['timed_out'] ?? false);
        $failed = (bool) ($result['failed'] ?? true) || $timedOut;

        return [
            'index' => $index,
            'passed' => self::grade($result, $expected),
            'expected' => rtrim($expected),
            'actual' => rtrim((string) ($result['stdout'] ?? '')),
            'stderr' => (string) ($result['stderr'] ?? ''),
            'timed_out' => $timedOut,
            'failed' => $failed,
            'is_hidden' => filter_var($testCase['is_hidden'] ?? false, FILTER_VALIDATE_BOOL),
        ];
    }
}
