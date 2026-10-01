<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

/**
 * Checks, on the server, that every coding problem's reference solution
 * produces the expected output for every test case before a coding challenge
 * is saved or published (DataSensei Updates 5, task 2).
 *
 *   reference solution -> test case input -> actual output -> expected output
 *
 * The reference solution runs through CodingChallengeTestRunner, which uses
 * the same Python sandbox, the same options and the same comparison as the
 * grader that marks student submissions (CodingQuizController::runSingle).
 * If one test case of one problem fails, nothing is saved: the author gets a
 * validation error and, for each failed case, the input, the expected output,
 * the actual output and the error message.
 *
 * Admins and instructors both go through this check, so a changed request
 * or a skipped browser check cannot save an unverified challenge.
 */
class ReferenceSolutionVerifier
{
    /** Session key holding the failed cases, for the builder forms. */
    public const SESSION_KEY = 'reference_check_failures';

    public function __construct(private readonly CodingChallengeTestRunner $runner)
    {
    }

    /**
     * @param  array<int, array{title?: ?string, reference_solution?: ?string, test_cases?: array<int, array{input?: ?string, expected_output?: ?string, is_hidden?: mixed}>}>  $questions
     * @return array<int, array{problem: int, problem_title: string, case: int, is_hidden: bool, input: string, expected: string, actual: string, error: string}>
     */
    public function failures(array $questions): array
    {
        $caseCount = array_sum(array_map(fn ($question): int => count((array) ($question['test_cases'] ?? [])), $questions));
        // Every case runs in the sandbox one after another; keep a long check
        // from hitting PHP's execution time limit.
        if (function_exists('set_time_limit')) {
            @set_time_limit(max(120, 30 * max(1, $caseCount)));
        }

        $failures = [];

        foreach (array_values($questions) as $questionIndex => $question) {
            $problem = $questionIndex + 1;
            $title = trim((string) ($question['title'] ?? '')) ?: 'Problem '.$problem;
            $code = (string) ($question['reference_solution'] ?? '');
            $testCases = array_values((array) ($question['test_cases'] ?? []));

            if (trim($code) === '') {
                $failures[] = [
                    'problem' => $problem,
                    'problem_title' => $title,
                    'case' => 0,
                    'is_hidden' => false,
                    'input' => '',
                    'expected' => '',
                    'actual' => '',
                    'error' => 'The reference solution is required.',
                ];
                continue;
            }

            $report = $this->runner->check($code, $testCases, 0);

            foreach ($report['results'] as $result) {
                if ($result['passed']) {
                    continue;
                }

                $case = $testCases[$result['index']] ?? [];
                $failures[] = [
                    'problem' => $problem,
                    'problem_title' => $title,
                    'case' => $result['index'] + 1,
                    'is_hidden' => (bool) $result['is_hidden'],
                    'input' => (string) ($case['input'] ?? ''),
                    'expected' => (string) $result['expected'],
                    'actual' => (string) $result['actual'],
                    'error' => $this->errorText($result),
                ];
            }
        }

        return $failures;
    }

    /**
     * Throws a validation error (and keeps the failed cases for the form)
     * unless every reference solution passes every test case.
     */
    public function assertPasses(array $questions): void
    {
        $failures = $this->failures($questions);

        if ($failures === []) {
            return;
        }

        session()->flash(self::SESSION_KEY, $failures);

        $messages = [];
        foreach ($failures as $failure) {
            $messages[] = $failure['case'] === 0
                ? "{$failure['problem_title']}: {$failure['error']}"
                : "{$failure['problem_title']}, test case {$failure['case']}: the reference solution's output does not match the expected output."
                    .($failure['error'] !== '' ? ' '.$failure['error'] : '');
        }

        throw ValidationException::withMessages([
            'reference_solution' => array_merge(
                ['The coding challenge was not saved: the reference solution must produce the expected output for every test case.'],
                $messages
            ),
        ]);
    }

    private function errorText(array $result): string
    {
        if ($result['timed_out']) {
            return 'The reference solution ran out of time.'.($result['stderr'] !== '' ? ' '.trim($result['stderr']) : '');
        }

        if ($result['failed']) {
            return trim($result['stderr']) !== '' ? trim($result['stderr']) : 'The reference solution ended with an error.';
        }

        return '';
    }
}
