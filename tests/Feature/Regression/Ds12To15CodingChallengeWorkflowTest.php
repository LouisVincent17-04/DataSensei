<?php

namespace Tests\Feature\Regression;

use App\Models\CodingChallengeRetake;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsCodingChallengeWorkflow;
use Tests\Feature\Regression\Concerns\FakePythonSandbox;
use Tests\TestCase;

/**
 * End-to-end coding challenge workflow across DS-12 .. DS-15.
 */
class Ds12To15CodingChallengeWorkflowTest extends TestCase
{
    use BuildsCodingChallengeWorkflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootCodingWorkflow();
        Carbon::setTestNow(Carbon::parse('2026-09-20 10:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        $this->dropCodingWorkflowTables();
        parent::tearDown();
    }

    public function test_full_student_workflow(): void
    {
        [$q1, $q2, $q3] = $this->makeCodingChallenge([
            ['problem_description' => 'WF_Q1_TEXT', 'tests' => [['', '42', false], ['', '42', true]]],
            ['problem_description' => 'WF_Q2_TEXT', 'time_limit_seconds' => 300],
            ['problem_description' => 'WF_Q3_TEXT'],
        ]);
        $answer = 'wrong';
        $this->fakeSandbox(function () use (&$answer) {
            return FakePythonSandbox::ok($answer);
        });

        // Open: nothing timed is shipped.
        $html = $this->studentClient()->get($this->codingUrl('quiz'))->assertOk()->getContent();
        $this->assertStringNotContainsString('WF_Q1_TEXT', $html);

        // Start Q1 → run → failing submit → passing submit (XP once).
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk()->assertJsonPath('question.problem_description', 'WF_Q1_TEXT');
        $this->studentClient()->postJson($this->codingUrl('run', $q1), ['code' => 'print(1)'])->assertOk()->assertJsonPath('status', 'ok');
        $this->assertSame(0, CodingSubmission::count(), 'Run never records a submission.');

        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(0)'])->assertOk()->assertJsonPath('status', 'failed');
        $this->assertSame(0, (int) $this->student->fresh()->xp);
        $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertForbidden();

        Carbon::setTestNow(Carbon::parse('2026-09-20 10:06:00')); // past the fast-solve bonus window
        $answer = '42';
        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertOk()->assertJsonPath('status', 'passed');
        $this->assertSame(100, (int) $this->student->fresh()->xp);

        // Duplicate submit: refused, no second XP.
        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertStatus(422);
        $this->assertSame(100, (int) $this->student->fresh()->xp);

        // Q2 unlocks; refresh resumes the same attempt.
        $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertOk()->assertJsonPath('remaining_seconds', 300);
        Carbon::setTestNow(Carbon::parse('2026-09-20 10:08:00'));
        $html = $this->studentClient()->get($this->codingUrl('quiz'))->assertOk()->getContent();
        $this->assertStringContainsString('WF_Q1_TEXT', $html);
        $this->assertStringContainsString('WF_Q2_TEXT', $html);
        $this->assertStringNotContainsString('WF_Q3_TEXT', $html);
        $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertOk()->assertJsonPath('remaining_seconds', 180);
        $this->studentClient()->getJson($this->codingUrl('ping', $q2))->assertOk()->assertJsonPath('remaining_seconds', 180);

        // Expiry path on Q2: no XP, run refused, Q3 becomes available.
        Carbon::setTestNow(Carbon::parse('2026-09-20 10:12:00'));
        $this->studentClient()->postJson($this->codingUrl('run', $q2), ['code' => 'print(1)'])->assertForbidden()->assertJsonPath('expired', true);
        $this->studentClient()->postJson($this->codingUrl('submit', $q2), ['code' => 'print(42)'])->assertOk()->assertJsonPath('status', 'expired');
        $this->assertSame(100, (int) $this->student->fresh()->xp);
        $this->studentClient()->postJson($this->codingUrl('start', $q3))->assertOk()->assertJsonPath('question.problem_description', 'WF_Q3_TEXT');

        // Retake: history preserved, new run starts clean, earned XP is kept.
        $submissionsBefore = CodingSubmission::count();
        $this->studentClient()->post($this->codingUrl('retake'))->assertRedirect($this->codingUrl('quiz'));

        $this->assertSame($submissionsBefore, CodingSubmission::count());
        $this->assertSame(0, CodingSubmission::where('voided', false)->count());
        $this->assertSame(0, CodingQuestionAttempt::count());
        $this->assertSame(3, DB::table('coding_question_attempt_archives')->count());
        $this->assertSame(1, (int) CodingChallengeRetake::firstOrFail()->retake_count);
        $this->assertSame(100, (int) $this->student->fresh()->xp);

        $html = $this->studentClient()->get($this->codingUrl('quiz'))->assertOk()->getContent();
        $this->assertStringNotContainsString('WF_Q1_TEXT', $html, 'After a retake the questions are unstarted again.');
        $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertForbidden();
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $this->assertSame(2, CodingQuestionAttempt::firstOrFail()->generation);
    }

    public function test_submit_while_another_submit_holds_the_lock_is_409_without_side_effects(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        $sandbox = $this->fakeSandbox(fn () => FakePythonSandbox::ok('42'));
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();

        $lock = Cache::lock('datasensei:coding-submit:' . $this->student->id . ':' . $q1->id, 600);
        $this->assertTrue($lock->get());
        try {
            $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertStatus(409);
        } finally {
            $lock->release();
        }

        $this->assertSame([], $sandbox->calls);
        $this->assertSame(0, CodingSubmission::count());
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_other_students_cannot_use_or_affect_an_attempt(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        $this->fakeSandbox(fn () => FakePythonSandbox::ok('42'));
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();

        // The other student has no attempt of their own: cannot run or submit.
        $this->clientFor($this->otherStudent)->postJson($this->codingUrl('run', $q1), ['code' => 'print(42)'])->assertForbidden();
        $this->clientFor($this->otherStudent)->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertForbidden();

        // Their retake does not touch this student's run.
        $this->clientFor($this->otherStudent)->post($this->codingUrl('retake'));
        $this->assertSame(1, CodingQuestionAttempt::where('user_id', $this->student->id)->count());
        $this->assertSame(0, CodingSubmission::count());
    }

    public function test_wrong_slug_challenge_and_question_combinations_are_404(): void
    {
        [$q1] = $this->makeCodingChallenge([[]]);
        $first = $this->challenge;
        [$foreign] = $this->makeCodingChallenge([[]]);

        foreach (['start', 'run', 'submit'] as $action) {
            $this->studentClient()->postJson(route('challenges.coding.' . $action, [
                'slug' => $this->slug, 'challenge' => $first->id, 'question' => $foreign->id,
            ]), ['code' => 'print(1)'])->assertNotFound();
            $this->studentClient()->postJson(route('challenges.coding.' . $action, [
                'slug' => 'other-slug', 'challenge' => $first->id, 'question' => $q1->id,
            ]), ['code' => 'print(1)'])->assertNotFound();
        }
        $this->studentClient()->get(route('challenges.coding.quiz', ['slug' => 'other-slug', 'challenge' => $first->id]))->assertNotFound();
        $this->studentClient()->get(route('challenges.coding.quiz', ['slug' => $this->slug, 'challenge' => 999999]))->assertNotFound();

        $first->update(['is_active' => false]);
        $this->studentClient()->postJson(route('challenges.coding.start', [
            'slug' => $this->slug, 'challenge' => $first->id, 'question' => $q1->id,
        ]))->assertNotFound();
    }
}
