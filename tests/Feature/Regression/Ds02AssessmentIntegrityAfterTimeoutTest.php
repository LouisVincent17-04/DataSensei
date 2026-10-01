<?php

namespace Tests\Feature\Regression;

use App\Models\AssessmentSubmission;
use App\Services\AntiCheatPolicyService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * DS-02: the integrity decision survives the timer, saved work is preserved
 * separately from credit, and the instructor resolves a held attempt.
 */
class Ds02AssessmentIntegrityAfterTimeoutTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    /** @var array{assessment: int, mcq: int, correct: int, wrong: int, blank: int} */
    private array $q;
    private int $assessmentId;
    private AssessmentSubmission $attempt;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
        $this->seedAssessmentActors();
        $this->seedAssessmentRewards();
        $this->setPolicy(['max_tab_switches' => 2]);

        $this->q = $this->makeAssessment(5, ['max_attempts' => 2]);
        $this->assessmentId = $this->q['assessment'];
        $this->attempt = $this->makeAttempt($this->assessmentId);
        $this->attempt->update([
            'draft_answers' => [(string) $this->q['mcq'] => (string) $this->q['correct'], (string) $this->q['blank'] => 'pandas'],
            'draft_version' => 1,
            'draft_saved_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function submit(array $extra = [])
    {
        return $this->actAs($this->student)->post(
            route('student.assessments.submit', [$this->assessmentId, $this->attempt->id]),
            array_merge(['_anti_cheat_session_id' => $this->attempt->anti_cheat_session_id], $extra)
        );
    }

    public function test_attempt_blocked_before_expiry_stays_blocked_after_expiry(): void
    {
        $this->recordFocusLosses($this->attempt, 3);

        // Before expiry the ordinary submit is refused, as it always was.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:02:00'));
        $this->submit()->assertSessionHasErrors('anti_cheat');
        $this->assertSame('in_progress', $this->attempt->fresh()->status);

        // Waiting for the timer must not turn it into an ordinary graded attempt.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit()->assertRedirect(route('student.assessments.result', [$this->assessmentId, $this->attempt->id]));

        $attempt = $this->attempt->fresh();
        $this->assertSame('submitted', $attempt->status, 'A blocked attempt awaits review; it is not graded.');
        $this->assertSame('blocked', $attempt->integrity_status);
        $this->assertStringContainsString('focus-loss limit', (string) $attempt->integrity_reason);
        $this->assertSame('0.00', $attempt->score);
        $this->assertSame('10.00', $attempt->provisional_score);
        $this->assertNull($attempt->graded_at);
        $this->assertNotNull($attempt->timed_out_at);

        // Saved work is preserved for review, without credit.
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_submission_id' => $attempt->id,
            'assessment_question_id' => $this->q['mcq'],
            'selected_option_id' => $this->q['correct'],
            'is_correct' => 1,
            'points_awarded' => 0,
        ]);
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_submission_id' => $attempt->id,
            'assessment_question_id' => $this->q['blank'],
            'answer_text' => 'pandas',
            'points_awarded' => 0,
        ]);

        // No XP, no mission progress, no "graded" notification.
        $this->assertSame(0, (int) $this->student->fresh()->xp);
        $this->assertSame(0, $this->missionProgress($this->student));
        $this->assertSame(0, DB::table('notifications')->where('type', 'assessment_graded')->count());
        $this->assertSame(1, DB::table('notifications')->where('type', 'assessment_held_for_review')->count());

        $this->actAs($this->student)
            ->get(route('student.assessments.result', [$this->assessmentId, $attempt->id]))
            ->assertOk()
            ->assertSee('held for instructor review')
            ->assertSee('0.00/10.00');
    }

    public function test_missing_identity_is_rejected_before_expiry_and_never_becomes_acceptable_by_waiting(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:02:00'));
        $this->actAs($this->student)->post(route('student.assessments.submit', [$this->assessmentId, $this->attempt->id]), [])
            ->assertSessionHasErrors('anti_cheat');
        $this->assertSame('in_progress', $this->attempt->fresh()->status);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        foreach ([[], ['_anti_cheat_session_id' => 'forged-identity']] as $index => $payload) {
            $attempt = $index === 0 ? $this->attempt : $this->makeAttemptWithDraft(2);
            $this->actAs($this->student)->post(
                route('student.assessments.submit', [$this->assessmentId, $attempt->id]),
                $payload + ['answers' => [$this->q['mcq'] => (string) $this->q['correct']]]
            )->assertRedirect();

            $attempt->refresh();
            $this->assertSame('submitted', $attempt->status);
            $this->assertSame('review_required', $attempt->integrity_status);
            $this->assertSame('0.00', $attempt->score);
            $this->assertNull($attempt->graded_at);
        }

        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_valid_identity_without_violations_is_graded_normally_after_expiry(): void
    {
        $this->recordFocusLosses($this->attempt, 2); // within the allowance

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit()->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame('clear', $attempt->integrity_status);
        $this->assertSame('10.00', $attempt->score);
        $this->assertNull($attempt->provisional_score);
        // DataSensei Updates 5 + 11: class work (an assessment) gives no XP.
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_held_attempt_still_counts_towards_max_attempts(): void
    {
        DB::table('assessments')->where('id', $this->assessmentId)->update(['max_attempts' => 1]);
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit()->assertRedirect();

        // Attempts are exhausted: no fresh attempt appears.
        $this->actAs($this->student)
            ->post(route('student.assessments.start', $this->assessmentId))
            ->assertRedirect()
            ->assertSessionHasErrors('assessment');
        $this->assertSame(1, AssessmentSubmission::where('assessment_id', $this->assessmentId)->count());
    }

    public function test_instructor_can_release_a_held_attempt_once(): void
    {
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit();

        $this->actAs($this->instructor)
            ->get(route('instructor.assessments.submissions', $this->assessmentId))
            ->assertOk()
            ->assertSee('Held for review');
        $this->actAs($this->instructor)
            ->get(route('instructor.assessments.submissions.show', [$this->assessmentId, $this->attempt->id]))
            ->assertOk()
            ->assertSee('Held by the anti-cheat decision')
            ->assertSee('Release and credit the score')
            ->assertSee('Keep blocked, no credit');

        $release = route('instructor.assessments.submissions.integrity.release', [$this->assessmentId, $this->attempt->id]);

        // Only the owner of the class may decide.
        $this->actAs($this->otherInstructor)->patch($release)->assertForbidden();
        $this->actAs($this->student)->patch($release)->assertForbidden();
        $this->assertSame('submitted', $this->attempt->fresh()->status);

        $this->actAs($this->instructor)->patch($release)->assertRedirect();
        $this->actAs($this->instructor)->patch($release)->assertRedirect(); // repeated click

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame('10.00', $attempt->score);
        $this->assertSame('clear', $attempt->integrity_status);
        $this->assertSame($this->instructor->id, $attempt->integrity_reviewed_by);
        $this->assertNotNull($attempt->integrity_reviewed_at);
        $this->assertNotNull($attempt->graded_at);
        $this->assertSame(10, (int) DB::table('assessment_answers')->where('assessment_submission_id', $attempt->id)->sum('points_awarded'));
        // DataSensei Updates 5 + 11: releasing the attempt grades it, but
        // class work gives no XP or mission progress.
        $this->assertSame(0, (int) $this->student->fresh()->xp, 'Releasing class work gives no XP.');
        $this->assertSame(0, $this->missionProgress($this->student));
    }

    public function test_instructor_can_keep_a_held_attempt_blocked(): void
    {
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit();

        $keep = route('instructor.assessments.submissions.integrity.block', [$this->assessmentId, $this->attempt->id]);
        $this->actAs($this->otherInstructor)->patch($keep)->assertForbidden();
        $this->actAs($this->instructor)->patch($keep)->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame('0.00', $attempt->score);
        $this->assertSame('blocked', $attempt->integrity_status);
        $this->assertSame($this->instructor->id, $attempt->integrity_reviewed_by);
        $this->assertSame(0, (int) $this->student->fresh()->xp);

        // A later release attempt is inert: the decision was already made.
        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.submissions.integrity.release', [$this->assessmentId, $attempt->id]));
        $this->assertSame('0.00', $this->attempt->fresh()->score);
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_submission_of_another_assessment_cannot_be_resolved_through_this_one(): void
    {
        $other = $this->makeAssessment(5)['assessment'];
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit();

        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.submissions.integrity.release', [$other, $this->attempt->id]))
            ->assertNotFound();
        $this->assertSame('submitted', $this->attempt->fresh()->status);
    }

    public function test_policy_service_reports_identity_independently_of_time(): void
    {
        $service = app(AntiCheatPolicyService::class);
        $this->assertFalse($service->attemptIdentityMatches($this->attempt, null));
        $this->assertFalse($service->attemptIdentityMatches($this->attempt, ''));
        $this->assertFalse($service->attemptIdentityMatches($this->attempt, 'x'));
        $this->assertTrue($service->attemptIdentityMatches($this->attempt, $this->attempt->anti_cheat_session_id));
    }

    private function makeAttemptWithDraft(int $attemptNo): AssessmentSubmission
    {
        $attempt = $this->makeAttempt($this->assessmentId, null, Carbon::parse('2026-09-20 09:00:00'), $attemptNo);
        $attempt->update([
            'draft_answers' => [(string) $this->q['mcq'] => (string) $this->q['correct']],
            'draft_version' => 1,
        ]);

        return $attempt;
    }
}
