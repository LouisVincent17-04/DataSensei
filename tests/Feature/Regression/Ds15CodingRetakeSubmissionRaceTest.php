<?php

namespace Tests\Feature\Regression;

use App\Http\Controllers\CodingQuizController;
use App\Models\CodingChallengeRetake;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsCodingChallengeWorkflow;
use Tests\Feature\Regression\Concerns\FakePythonSandbox;
use Tests\TestCase;

/**
 * DS-15 — deterministic single-process version of the retake/submit race:
 * the fake sandbox performs the retake WHILE the old submission is being
 * graded (after its timer check, before its result is committed).
 * The two-process MariaDB reproduction lives in Ds15CodingRetakeRaceMysqlTest.
 */
class Ds15CodingRetakeSubmissionRaceTest extends TestCase
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

    public function test_submission_graded_across_a_retake_is_kept_as_voided_history_of_the_old_attempt(): void
    {
        [$q1, $q2] = $this->makeCodingChallenge([['tests' => [['', '42', false]]], []]);

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $oldToken = CodingQuestionAttempt::firstOrFail()->attempt_token;

        $retakes = 0;
        $this->fakeSandbox(function () use (&$retakes) {
            if ($retakes++ === 0) {
                // Retake completes while "Python" is still running, then the
                // learner immediately starts the new run.
                app(CodingQuizController::class)->retake($this->slug, $this->challenge);
            }

            return FakePythonSandbox::ok('42');
        });

        $response = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)']);

        $response->assertStatus(409);
        $this->assertTrue($response->json('stale_attempt'));

        // History preserved, bound to the OLD attempt, never active.
        $stale = CodingSubmission::firstOrFail();
        $this->assertTrue($stale->voided);
        $this->assertSame('stale_attempt', $stale->void_reason);
        $this->assertSame($oldToken, $stale->attempt_token);
        $this->assertSame(1, $stale->attempt_generation);
        $this->assertSame(0, $stale->xp_earned);
        $this->assertSame('print(42)', $stale->code);

        // No XP, no solved progress in the new run.
        $this->assertSame(0, (int) $this->student->fresh()->xp);
        $this->assertFalse(CodingSubmission::where('voided', false)->exists());
        $this->studentClient()->postJson($this->codingUrl('start', $q2))->assertForbidden();

        // Old attempt archived rather than lost.
        $this->assertDatabaseHas('coding_question_attempt_archives', [
            'user_id' => $this->student->id,
            'coding_question_id' => $q1->id,
            'attempt_token' => $oldToken,
        ]);

        // The new run works normally and gets a new identity.
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $newAttempt = CodingQuestionAttempt::firstOrFail();
        $this->assertNotSame($oldToken, $newAttempt->attempt_token);
        $this->assertSame(2, $newAttempt->generation);

        $fresh = $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertOk();
        $this->assertSame('passed', $fresh->json('status'));
        $active = CodingSubmission::where('voided', false)->firstOrFail();
        $this->assertSame($newAttempt->attempt_token, $active->attempt_token);
        $this->assertSame(2, $active->attempt_generation);
        $this->assertSame(120, (int) $this->student->fresh()->xp);
    }

    public function test_retake_followed_by_a_restart_of_the_same_question_still_voids_the_old_submission(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();

        $done = false;
        $this->fakeSandbox(function () use (&$done, $q1) {
            if (!$done) {
                $done = true;
                $controller = app(CodingQuizController::class);
                $controller->retake($this->slug, $this->challenge);
                $controller->start($this->slug, $this->challenge, $q1); // new attempt row already exists
            }

            return FakePythonSandbox::ok('42');
        });

        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertStatus(409);

        $this->assertSame(1, CodingSubmission::count());
        $this->assertTrue(CodingSubmission::firstOrFail()->voided);
        $this->assertSame(0, (int) $this->student->fresh()->xp);
        $this->assertSame(1, CodingQuestionAttempt::count(), 'The new attempt is untouched.');
    }

    public function test_repeated_retake_is_idempotent_and_never_voids_the_new_run(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        $this->fakeSandbox(fn () => FakePythonSandbox::ok('nope'));

        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();
        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(1)'])->assertOk();

        $this->studentClient()->post($this->codingUrl('retake'))->assertRedirect($this->codingUrl('quiz'));
        $this->studentClient()->post($this->codingUrl('retake'))->assertRedirect($this->codingUrl('quiz')); // double click

        $this->assertSame(1, (int) CodingChallengeRetake::firstOrFail()->retake_count, 'A replayed retake must not burn a second retake.');
        $this->assertSame(1, DB::table('coding_question_attempt_archives')->count());
        $this->assertSame('retake', CodingSubmission::firstOrFail()->void_reason);
    }

    public function test_legacy_attempt_without_a_token_is_stamped_once_and_still_protected(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        DB::table('coding_question_attempts')->insert([
            'user_id' => $this->student->id,
            'coding_question_id' => $q1->id,
            'started_at' => now(),
            'expired' => false,
            'attempt_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->fakeSandbox(fn () => FakePythonSandbox::ok('42'));
        $this->studentClient()->postJson($this->codingUrl('submit', $q1), ['code' => 'print(42)'])->assertOk();

        $attempt = CodingQuestionAttempt::firstOrFail();
        $this->assertNotEmpty($attempt->attempt_token);
        $this->assertSame($attempt->attempt_token, CodingSubmission::firstOrFail()->attempt_token);
    }

    public function test_retake_limit_is_still_enforced(): void
    {
        [$q1] = $this->makeCodingChallenge([['tests' => [['', '42', false]]]]);
        CodingChallengeRetake::create(['user_id' => $this->student->id, 'challenge_id' => $this->challenge->id, 'retake_count' => 3]);
        $this->studentClient()->postJson($this->codingUrl('start', $q1))->assertOk();

        $this->studentClient()->post($this->codingUrl('retake'))->assertRedirect(route('challenges.coding.map', $this->slug));
        $this->assertSame(1, CodingQuestionAttempt::count());
    }
}
