<?php

namespace Tests\Feature\Regression;

use App\Models\CodingQuestionAttempt;
use Carbon\Carbon;
use Tests\Feature\Regression\Concerns\BuildsCodingChallengeWorkflow;
use Tests\Feature\Regression\Concerns\FakePythonSandbox;
use Tests\TestCase;

/**
 * DS-14 — timed question content must not leave the server before the server
 * attempt (started_at) exists, in HTML, inline JavaScript or JSON.
 */
class Ds14CodingContentRequiresStartedAttemptTest extends TestCase
{
    use BuildsCodingChallengeWorkflow;

    /** @var array<int, \App\Models\CodingQuestion> */
    private array $questions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootCodingWorkflow();
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));

        $this->questions = $this->makeCodingChallenge([
            [
                'problem_description' => 'Q1_DESCRIPTION_SENTINEL',
                'starter_code' => '# Q1_STARTER_SENTINEL',
                'tests' => [['Q1_VISIBLE_INPUT_SENTINEL', 'Q1_VISIBLE_EXPECTED_SENTINEL', false], ['h', 'Q1_HIDDEN_SENTINEL', true]],
            ],
            [
                'problem_description' => 'Q2_DESCRIPTION_SENTINEL',
                'starter_code' => '# Q2_STARTER_SENTINEL',
                'tests' => [['Q2_VISIBLE_INPUT_SENTINEL', 'Q2_VISIBLE_EXPECTED_SENTINEL', false]],
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        $this->dropCodingWorkflowTables();
        parent::tearDown();
    }

    public function test_page_before_start_contains_no_timed_content_and_creates_no_attempt(): void
    {
        $html = $this->studentClient()->get($this->codingUrl('quiz'))->assertOk()->getContent();

        foreach (['Q1_', 'Q2_'] as $prefix) {
            foreach (['DESCRIPTION', 'STARTER', 'VISIBLE_INPUT', 'VISIBLE_EXPECTED'] as $part) {
                $this->assertStringNotContainsString($prefix . $part . '_SENTINEL', $html);
            }
        }
        $this->assertStringNotContainsString('Q1_HIDDEN_SENTINEL', $html);
        $this->assertSame(0, CodingQuestionAttempt::count(), 'GET must stay read-only.');

        // Only structural metadata is present.
        $this->assertStringContainsString('Question 1 of 2', $html);
        $this->assertStringContainsString('10 min limit', $html);
    }

    public function test_start_records_the_attempt_before_returning_content(): void
    {
        [$q1] = $this->questions;

        $response = $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();

        $attempt = CodingQuestionAttempt::where('user_id', $this->student->id)->firstOrFail();
        $this->assertSame('2026-09-20 09:00:00', $attempt->started_at->toDateTimeString());
        $this->assertNotEmpty($attempt->attempt_token);
        $this->assertTrue($attempt->started_at->lessThanOrEqualTo(now()));

        $this->assertSame('Q1_DESCRIPTION_SENTINEL', $response->json('question.problem_description'));
        $this->assertSame('# Q1_STARTER_SENTINEL', $response->json('question.starter_code'));
        $this->assertSame('Q1_VISIBLE_INPUT_SENTINEL', $response->json('question.test_cases.0.input'));
        $this->assertCount(1, $response->json('question.test_cases'));
        $this->assertStringNotContainsString('Q1_HIDDEN_SENTINEL', $response->getContent());
        $this->assertStringNotContainsString('Q2_', $response->getContent());
        $this->assertSame(600, $response->json('remaining_seconds'));
    }

    public function test_refresh_and_repeated_start_resume_the_same_attempt_without_resetting_the_timer(): void
    {
        [$q1] = $this->questions;
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $original = CodingQuestionAttempt::firstOrFail();

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:04:00'));

        $again = $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $this->assertSame(360, $again->json('remaining_seconds'));

        $html = $this->studentClient()->get($this->codingUrl('quiz'))->assertOk()->getContent();
        $this->assertStringContainsString('Q1_DESCRIPTION_SENTINEL', $html);
        $this->assertStringContainsString('Q1_STARTER_SENTINEL', $html);
        $this->assertStringContainsString('Q1_VISIBLE_EXPECTED_SENTINEL', $html);
        $this->assertStringNotContainsString('Q1_HIDDEN_SENTINEL', $html);
        $this->assertStringNotContainsString('Q2_', $html, 'The locked question stays withheld.');
        $this->assertMatchesRegularExpression('/0:\s*360,/', $html, 'Server-rendered remaining seconds resume the running clock.');

        $this->assertSame(1, CodingQuestionAttempt::count());
        $resumed = CodingQuestionAttempt::firstOrFail();
        $this->assertSame($original->id, $resumed->id);
        $this->assertSame($original->attempt_token, $resumed->attempt_token);
        $this->assertSame('2026-09-20 09:00:00', $resumed->started_at->toDateTimeString());
    }

    public function test_locked_question_cannot_be_started_run_pinged_or_submitted(): void
    {
        [$q1, $q2] = $this->questions;
        $this->fakeSandbox(fn () => FakePythonSandbox::ok('x'));
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();

        $start = $this->studentClient()->postJson($this->codingUrl('start', $q2));
        $start->assertForbidden();
        $this->assertStringNotContainsString('Q2_', $start->getContent());
        $this->studentClient()->postJson($this->codingUrl('run', $q2), ['code' => 'print(1)'])->assertForbidden();
        $this->studentClient()->postJson($this->codingUrl('submit', $q2), ['code' => 'print(1)'])->assertForbidden();
        $this->studentClient()->getJson($this->codingUrl('ping', $q2))->assertForbidden();
        $this->assertSame(0, CodingQuestionAttempt::where('coding_question_id', $q2->id)->count());
    }

    public function test_run_and_submit_are_refused_for_an_available_but_unstarted_question(): void
    {
        [$q1] = $this->questions;
        $sandbox = $this->fakeSandbox(fn () => FakePythonSandbox::ok('x'));

        $this->studentClient()->postJson($this->codingUrl('run', $q1), ['code' => 'print(1)'])->assertForbidden();
        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(1)'])->assertForbidden();
        $this->assertSame([], $sandbox->calls, 'No code may execute before the attempt exists.');
    }

    public function test_later_question_unlocks_only_after_prerequisite_is_passed_or_timed_out(): void
    {
        [$q1, $q2] = $this->questions;
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:11:00')); // Q1 (600 s) has timed out
        $html = $this->studentClient()->get($this->codingUrl('quiz'))->assertOk()->getContent();
        $this->assertStringNotContainsString('Q2_DESCRIPTION_SENTINEL', $html, 'Q2 is available but still unstarted.');

        $response = $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertOk();
        $this->assertSame('Q2_DESCRIPTION_SENTINEL', $response->json('question.problem_description'));
        $this->assertSame(
            '2026-09-20 09:11:00',
            CodingQuestionAttempt::where('coding_question_id', $q2->id)->firstOrFail()->started_at->toDateTimeString()
        );
    }

    public function test_another_students_attempt_is_untouched_and_grants_nothing(): void
    {
        [$q1] = $this->questions;
        $this->clientFor($this->otherStudent)->postJson($this->codingUrl('start', $q1))->assertOk();
        $other = CodingQuestionAttempt::where('user_id', $this->otherStudent->id)->firstOrFail();

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:03:00'));
        $html = $this->clientFor($this->student)->get($this->codingUrl('quiz'))->assertOk()->getContent();
        $this->assertStringNotContainsString('Q1_DESCRIPTION_SENTINEL', $html);

        $this->clientFor($this->student)->postJson($this->codingUrl('start', $q1))->assertOk();
        $this->assertSame(2, CodingQuestionAttempt::count());
        $this->assertSame($other->started_at->toDateTimeString(), $other->fresh()->started_at->toDateTimeString());
        $this->assertSame($other->attempt_token, $other->fresh()->attempt_token);
    }

    public function test_guest_cannot_start_and_wrong_combinations_are_404(): void
    {
        [$q1] = $this->questions;
        $this->postJson($this->codingUrl('start', $q1))->assertUnauthorized();

        $foreign = $this->makeCodingChallenge([['problem_description' => 'FOREIGN_SENTINEL']]);
        // $this->challenge now points at the second challenge; $q1 belongs to the first.
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertNotFound();
        $this->studentClient()->postJson(route('challenges.coding.start', [
            'slug' => 'not-the-slug', 'challenge' => $this->challenge->id, 'question' => $foreign[0]->id,
        ]))->assertNotFound(); // the challenge does not belong to that slug
    }

    public function test_start_without_a_csrf_token_is_rejected_by_the_real_middleware(): void
    {
        [$q1] = $this->questions;

        // The CSRF middleware short-circuits while the app reports "testing".
        $this->app->detectEnvironment(fn () => 'production');

        $this->studentClient()
            ->post($this->codingUrl('start', $q1), [], ['Accept' => 'application/json'])
            ->assertStatus(419);
        $this->assertSame(0, CodingQuestionAttempt::count());

        $this->studentClient()
            ->withSession(['_token' => 'known-token'])
            ->post($this->codingUrl('start', $q1), [], ['Accept' => 'application/json', 'X-CSRF-TOKEN' => 'known-token'])
            ->assertOk();
        $this->assertSame(1, CodingQuestionAttempt::count());
    }
}
