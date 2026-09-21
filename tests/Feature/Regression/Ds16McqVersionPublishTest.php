<?php

namespace Tests\Feature\Regression;

use App\Models\ChallengeAttempt;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Regression\Concerns\BuildsMcqChallengeWorkflow;
use Tests\TestCase;

/**
 * DS-16: publishing version B must not interrupt an attempt in progress on A.
 */
class Ds16McqVersionPublishTest extends TestCase
{
    use BuildsMcqChallengeWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
    }

    private function publishAsAdmin($challenge)
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'status' => 'active']);

        return $this->actingAs($admin)
            ->withSession([AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($admin)])
            ->from('/admin/challenges')
            ->patch("/admin/challenges/{$challenge->id}/status");
    }

    public function test_attempt_on_old_version_finishes_after_new_version_is_published(): void
    {
        $student = $this->mcqStudent();
        [$versionA, $questionsA] = $this->mcqChallenge();
        [$versionB] = $this->mcqChallenge([
            'version_no' => 2, 'version_name' => 'Version 2', 'version_code' => 'V2', 'is_active' => false,
        ]);

        $attempt = $this->startMcqAttempt($student, $versionA);

        $response = $this->publishAsAdmin($versionB)->assertRedirect();
        $response->assertSessionHas('success', fn ($message) => str_contains($message, '1 learner has an attempt in progress on the previous version'));

        $this->assertFalse((bool) $versionA->fresh()->is_active);
        $this->assertTrue((bool) $versionB->fresh()->is_active);

        $this->actAsStudent($student);

        // Resume page, autosave, heartbeat and events keep working on A.
        $page = $this->get($this->quizUrl($versionA))->assertOk();
        $this->assertSame($attempt->id, $page->viewData('attempt')->id);

        foreach ($questionsA as $row) {
            $this->postJson($this->quizUrl($versionA, '/autosave'), [
                'attempt_id' => $attempt->id,
                'question_id' => $row['question']->id,
                'option_id' => $row['correct']->id,
            ])->assertOk();
        }

        $this->postJson($this->quizUrl($versionA, '/heartbeat'), ['attempt_id' => $attempt->id])
            ->assertOk()->assertJson(['status' => 'in_progress']);

        $this->postJson($this->quizUrl($versionA, '/events'), [
            'attempt_id' => $attempt->id,
            'event_type' => 'tab_hidden_or_app_switched',
            'details' => ['answered' => 2],
        ])->assertOk();

        $this->post($this->quizUrl($versionA, '/submit'), ['attempt_id' => $attempt->id])
            ->assertRedirect($this->quizUrl($versionA, "/result/{$attempt->id}"));

        $attempt->refresh();
        $this->assertSame('submitted', $attempt->status);
        $this->assertSame(2, (int) $attempt->score);
        $this->assertGreaterThan(0, (int) $attempt->xp_awarded);

        // Result stays visible, without a retake link into the inactive version.
        $this->get($this->quizUrl($versionA, "/result/{$attempt->id}"))
            ->assertOk()
            ->assertSee('Attempt History')
            ->assertDontSee('Retake Challenge');

        // A repeated submit is still idempotent on the inactive version.
        $xp = (int) $student->fresh()->xp;
        $this->post($this->quizUrl($versionA, '/submit'), ['attempt_id' => $attempt->id])
            ->assertRedirect($this->quizUrl($versionA, "/result/{$attempt->id}"));
        $this->assertSame($xp, (int) $student->fresh()->xp);

        // No NEW attempt (retake) can start on the inactive version.
        $this->get($this->quizUrl($versionA))->assertNotFound();
        $this->assertSame(1, ChallengeAttempt::where('challenge_id', $versionA->id)->count());
    }

    public function test_new_attempts_go_to_the_published_version_only(): void
    {
        $student = $this->mcqStudent();
        $other = $this->mcqStudent();
        [$versionA] = $this->mcqChallenge();
        [$versionB] = $this->mcqChallenge([
            'version_no' => 2, 'version_name' => 'Version 2', 'version_code' => 'V2', 'is_active' => false,
        ]);

        $this->startMcqAttempt($student, $versionA);
        $this->publishAsAdmin($versionB)->assertRedirect();

        // A learner without an attempt on A cannot start one there.
        $this->actAsStudent($other)->get($this->quizUrl($versionA))->assertNotFound();
        $this->assertSame(0, ChallengeAttempt::where('user_id', $other->id)->count());

        $attemptB = $this->startMcqAttempt($other, $versionB);
        $this->assertSame($versionB->id, (int) $attemptB->challenge_id);
    }

    public function test_other_users_cannot_use_the_continuing_attempt_on_the_inactive_version(): void
    {
        $owner = $this->mcqStudent();
        $intruder = $this->mcqStudent();
        [$versionA, $questionsA] = $this->mcqChallenge();
        [$versionB] = $this->mcqChallenge([
            'version_no' => 2, 'version_name' => 'Version 2', 'version_code' => 'V2', 'is_active' => false,
        ]);

        $attempt = $this->startMcqAttempt($owner, $versionA);
        $this->publishAsAdmin($versionB)->assertRedirect();

        $this->actAsStudent($intruder);

        $this->postJson($this->quizUrl($versionA, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $questionsA[0]['question']->id,
            'option_id' => $questionsA[0]['correct']->id,
        ])->assertNotFound();
        $this->postJson($this->quizUrl($versionA, '/heartbeat'), ['attempt_id' => $attempt->id])->assertNotFound();
        $this->post($this->quizUrl($versionA, '/submit'), ['attempt_id' => $attempt->id])->assertNotFound();
        $this->get($this->quizUrl($versionA, "/result/{$attempt->id}"))->assertNotFound();

        // The attempt id of version A is not accepted through version B either.
        $this->actAsStudent($owner);
        $this->postJson($this->quizUrl($versionB, '/heartbeat'), ['attempt_id' => $attempt->id])->assertNotFound();

        $this->assertSame('in_progress', $attempt->fresh()->status);
    }

    public function test_publishing_without_active_attempts_has_no_continuation_notice(): void
    {
        [$versionA] = $this->mcqChallenge();
        [$versionB] = $this->mcqChallenge([
            'version_no' => 2, 'version_name' => 'Version 2', 'version_code' => 'V2', 'is_active' => false,
        ]);

        $this->publishAsAdmin($versionB)
            ->assertRedirect()
            ->assertSessionHas('success', 'MCQ challenge version published. Other versions of this challenge were deactivated.');

        $this->assertFalse((bool) $versionA->fresh()->is_active);
    }

    public function test_plain_deactivation_with_an_active_attempt_is_still_deferred_with_an_explanation(): void
    {
        $student = $this->mcqStudent();
        [$versionA] = $this->mcqChallenge();
        $this->startMcqAttempt($student, $versionA);

        $this->publishAsAdmin($versionA)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertTrue((bool) $versionA->fresh()->is_active);
    }
}
