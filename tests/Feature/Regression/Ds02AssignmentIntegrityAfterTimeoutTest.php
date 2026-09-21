<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentSubmission;
use App\Services\AntiCheatPolicyService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * DS-02: the integrity decision survives the timer, saved work is preserved
 * separately from credit, and the instructor resolves a held attempt.
 */
class Ds02AssignmentIntegrityAfterTimeoutTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    /** @var array{item: int, mcq: int, correct: int, wrong: int, blank: int} */
    private array $q;
    private int $assignmentId;
    private AssignmentSubmission $attempt;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
        $this->seedAssignmentActors();
        $this->seedAssignmentRewards();
        $this->setPolicy(['max_tab_switches' => 2]);

        $this->q = $this->makeLibraryItem(5);
        $this->assignmentId = $this->makeClassAssignment($this->q['item'], 'published', 2);
        $this->attempt = $this->makeAttempt($this->assignmentId);
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
            route('student.assignments.submit', [$this->assignmentId, $this->attempt->id]),
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
        $this->submit()->assertRedirect(route('student.assignments.result', [$this->assignmentId, $this->attempt->id]));

        $attempt = $this->attempt->fresh();
        $this->assertSame('submitted', $attempt->status, 'A blocked attempt awaits review; it is not graded.');
        $this->assertSame('blocked', $attempt->integrity_status);
        $this->assertStringContainsString('focus-loss limit', (string) $attempt->integrity_reason);
        $this->assertSame(0, $attempt->score);
        $this->assertSame(10, $attempt->provisional_score);
        $this->assertNull($attempt->graded_at);
        $this->assertNotNull($attempt->timed_out_at);

        // Saved work is preserved for review, without credit.
        $this->assertDatabaseHas('assignment_submission_answers', [
            'assignment_submission_id' => $attempt->id,
            'assignment_question_id' => $this->q['mcq'],
            'selected_option_id' => $this->q['correct'],
            'is_correct' => 1,
            'points_awarded' => 0,
        ]);
        $this->assertDatabaseHas('assignment_submission_answers', [
            'assignment_submission_id' => $attempt->id,
            'assignment_question_id' => $this->q['blank'],
            'answer_text' => 'pandas',
            'points_awarded' => 0,
        ]);

        // No XP, no mission progress, no "graded" notification.
        $this->assertSame(0, (int) $this->student->fresh()->xp);
        $this->assertSame(0, $this->missionProgress($this->student));
        $this->assertSame(0, DB::table('notifications')->where('type', 'assignment_graded')->count());
        $this->assertSame(1, DB::table('notifications')->where('type', 'assignment_held_for_review')->count());

        $this->actAs($this->student)
            ->get(route('student.assignments.result', [$this->assignmentId, $attempt->id]))
            ->assertOk()
            ->assertSee('Held for instructor review')
            ->assertSee('0/10');
    }

    public function test_missing_identity_is_rejected_before_expiry_and_never_becomes_acceptable_by_waiting(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:02:00'));
        $this->actAs($this->student)->post(route('student.assignments.submit', [$this->assignmentId, $this->attempt->id]), [])
            ->assertSessionHasErrors('anti_cheat');
        $this->assertSame('in_progress', $this->attempt->fresh()->status);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        foreach ([[], ['_anti_cheat_session_id' => 'forged-identity']] as $index => $payload) {
            $attempt = $index === 0 ? $this->attempt : $this->makeAttemptWithDraft(2);
            $this->actAs($this->student)->post(
                route('student.assignments.submit', [$this->assignmentId, $attempt->id]),
                $payload + ['answers' => [$this->q['mcq'] => (string) $this->q['correct']]]
            )->assertRedirect();

            $attempt->refresh();
            $this->assertSame('submitted', $attempt->status);
            $this->assertSame('review_required', $attempt->integrity_status);
            $this->assertSame(0, $attempt->score);
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
        $this->assertSame(10, $attempt->score);
        $this->assertNull($attempt->provisional_score);
        $this->assertSame(50, (int) $this->student->fresh()->xp);
    }

    public function test_held_attempt_still_counts_towards_max_attempts(): void
    {
        DB::table('class_assignments')->where('id', $this->assignmentId)->update(['max_attempts' => 1]);
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit()->assertRedirect();

        $this->actAs($this->student)
            ->post(route('student.assignments.start', $this->assignmentId))
            ->assertRedirect(route('student.assignments.result', [$this->assignmentId, $this->attempt->id]));
        $this->assertSame(1, AssignmentSubmission::where('class_assignment_id', $this->assignmentId)->count());
    }

    public function test_instructor_can_release_a_held_attempt_once(): void
    {
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit();

        $this->actAs($this->instructor)
            ->get(route('instructor.assignments.show', $this->assignmentId))
            ->assertOk()
            ->assertSee('Held for integrity review')
            ->assertSee('Release score')
            ->assertSee('Keep blocked');

        $release = route('instructor.assignments.submissions.release', [$this->assignmentId, $this->attempt->id]);

        // Only the owner of the class may decide.
        $this->actAs($this->otherInstructor)->patch($release)->assertForbidden();
        $this->actAs($this->student)->patch($release)->assertForbidden();
        $this->assertSame('submitted', $this->attempt->fresh()->status);

        $this->actAs($this->instructor)->patch($release)->assertRedirect();
        $this->actAs($this->instructor)->patch($release)->assertRedirect(); // repeated click

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame(10, $attempt->score);
        $this->assertSame('clear', $attempt->integrity_status);
        $this->assertSame($this->instructor->id, $attempt->integrity_reviewed_by);
        $this->assertNotNull($attempt->integrity_reviewed_at);
        $this->assertNotNull($attempt->graded_at);
        $this->assertSame(10, (int) DB::table('assignment_submission_answers')->where('assignment_submission_id', $attempt->id)->sum('points_awarded'));
        $this->assertSame(50, (int) $this->student->fresh()->xp, 'XP is awarded exactly once, at release.');
        $this->assertSame(1, $this->missionProgress($this->student));
    }

    public function test_instructor_can_keep_a_held_attempt_blocked(): void
    {
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit();

        $keep = route('instructor.assignments.submissions.keep-blocked', [$this->assignmentId, $this->attempt->id]);
        $this->actAs($this->otherInstructor)->patch($keep)->assertForbidden();
        $this->actAs($this->instructor)->patch($keep)->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame(0, $attempt->score);
        $this->assertSame('blocked', $attempt->integrity_status);
        $this->assertSame($this->instructor->id, $attempt->integrity_reviewed_by);
        $this->assertSame(0, (int) $this->student->fresh()->xp);

        // A later release attempt is inert: the decision was already made.
        $this->actAs($this->instructor)
            ->patch(route('instructor.assignments.submissions.release', [$this->assignmentId, $attempt->id]));
        $this->assertSame(0, $this->attempt->fresh()->score);
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_submission_of_another_assignment_cannot_be_resolved_through_this_one(): void
    {
        $other = $this->makeClassAssignment($this->makeLibraryItem(5)['item']);
        $this->recordFocusLosses($this->attempt, 3);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->submit();

        $this->actAs($this->instructor)
            ->patch(route('instructor.assignments.submissions.release', [$other, $this->attempt->id]))
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

    private function makeAttemptWithDraft(int $attemptNo): AssignmentSubmission
    {
        $attempt = $this->makeAttempt($this->assignmentId, null, Carbon::parse('2026-09-20 09:00:00'), $attemptNo);
        $attempt->update([
            'draft_answers' => [(string) $this->q['mcq'] => (string) $this->q['correct']],
            'draft_version' => 1,
        ]);

        return $attempt;
    }
}
