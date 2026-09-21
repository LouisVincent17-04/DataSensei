<?php

namespace Tests\Feature\Regression;

use App\Models\CodingSubmission;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsCodingChallengeWorkflow;
use Tests\Feature\Regression\Concerns\FakePythonSandbox;
use Tests\TestCase;

/**
 * DS-13 — student-controlled output (stderr / traceback / stdout) of a HIDDEN
 * test case must not reach the student: not in the submit JSON, not in the
 * student-visible stored record, not in any page rendered afterwards.
 */
class Ds13HiddenTestDataDoesNotLeakTest extends TestCase
{
    use BuildsCodingChallengeWorkflow;

    private const SENTINEL = 'HIDDEN_TEST_SENTINEL';
    private const HIDDEN_EXPECTED = 'HIDDEN_EXPECTED_OUTPUT_SENTINEL';

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

    private function leakingSandbox(): void
    {
        // Behaves like: raise ValueError(input())
        $this->fakeSandbox(fn (string $code, string $stdin) => FakePythonSandbox::crash(
            'echo:' . $stdin,
            "Traceback (most recent call last):\n  File \"main.py\", line 1, in <module>\nValueError: {$stdin}"
        ));
    }

    public function test_hidden_input_copied_into_an_exception_never_reaches_the_student(): void
    {
        [$q1] = $this->makeCodingChallenge([[
            'tests' => [['visible-input', 'x', false], [self::SENTINEL, self::HIDDEN_EXPECTED, true]],
        ]]);
        $this->leakingSandbox();

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'raise ValueError(input())']);
        $response->assertOk();

        $body = $response->getContent();
        $this->assertStringNotContainsString(self::SENTINEL, $body);
        $this->assertStringNotContainsString(self::HIDDEN_EXPECTED, $body);

        $hidden = collect($response->json('results'))->firstWhere('is_hidden', true);
        $this->assertFalse($hidden['passed']);
        $this->assertSame('runtime_error', $hidden['category']);
        $this->assertNotEmpty($hidden['message']);
        $this->assertNull($hidden['stderr']);
        $this->assertNull($hidden['actual']);
        $this->assertNull($hidden['input']);
        $this->assertNull($hidden['expected']);

        // Visible cases keep their detail.
        $visible = collect($response->json('results'))->firstWhere('is_hidden', false);
        $this->assertStringContainsString('ValueError: visible-input', $visible['stderr']);
        $this->assertSame('visible-input', $visible['input']);

        // Stored student-visible fields are redacted at write time.
        $row = DB::table('coding_submissions')->first();
        $this->assertStringNotContainsString(self::SENTINEL, (string) $row->test_results);
        $this->assertStringNotContainsString(self::SENTINEL, (string) $row->error_message);
        $this->assertStringNotContainsString(self::HIDDEN_EXPECTED, (string) $row->test_results);

        // Restricted diagnostics are retained, but never serialized.
        $this->assertStringContainsString(self::SENTINEL, (string) $row->grader_diagnostics);
        $submission = CodingSubmission::firstOrFail();
        $this->assertStringNotContainsString(self::SENTINEL, $submission->toJson());
        $this->assertArrayNotHasKey('grader_diagnostics', $submission->toArray());

        // The page rendered afterwards (the only student view of saved coding results).
        $page = $this->studentClient()->get($this->codingUrl('quiz'))->assertOk()->getContent();
        $this->assertStringNotContainsString(self::SENTINEL, $page);
        $this->assertStringNotContainsString(self::HIDDEN_EXPECTED, $page);
    }

    public function test_top_level_error_is_not_taken_from_a_hidden_case(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [[self::SENTINEL, 'x', true]]]]);
        $this->leakingSandbox();

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'raise ValueError(input())']);

        $this->assertStringNotContainsString(self::SENTINEL, $response->getContent());
        $this->assertStringNotContainsString('Traceback', $response->getContent());
        $row = DB::table('coding_submissions')->first();
        $this->assertSame('A hidden test case ended with a runtime error.', $row->error_message);
    }

    public function test_passing_hidden_case_does_not_echo_the_expected_output(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['in', self::HIDDEN_EXPECTED, true]]]]);
        $this->fakeSandbox(fn () => FakePythonSandbox::ok(self::HIDDEN_EXPECTED));

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(1)']);

        $this->assertSame('passed', $response->json('status'));
        $this->assertStringNotContainsString(self::HIDDEN_EXPECTED, $response->getContent());
        $this->assertStringNotContainsString(self::HIDDEN_EXPECTED, (string) DB::table('coding_submissions')->value('test_results'));
    }

    public function test_hidden_wrong_answer_and_timeout_return_only_generic_categories(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['one', 'x', true], ['two', 'x', true]]]]);
        $this->fakeSandbox(fn (string $code, string $stdin) => $stdin === 'one'
            ? FakePythonSandbox::ok('LEAK:' . self::SENTINEL)
            : FakePythonSandbox::timeout('LEAK:' . self::SENTINEL));

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(1)']);

        $this->assertSame(['wrong_answer', 'time_limit'], array_column($response->json('results'), 'category'));
        $this->assertStringNotContainsString(self::SENTINEL, $response->getContent());
    }

    public function test_visible_output_is_length_bounded(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', 'x', false]]]]);
        $this->fakeSandbox(fn () => FakePythonSandbox::crash(str_repeat('o', 50000), str_repeat('e', 50000)));

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(1)']);

        $this->assertLessThan(4100, strlen($response->json('results.0.stderr')));
        $this->assertLessThan(4100, strlen($response->json('results.0.actual')));
        $this->assertLessThan(2100, strlen((string) DB::table('coding_submissions')->value('error_message')));
    }
}
