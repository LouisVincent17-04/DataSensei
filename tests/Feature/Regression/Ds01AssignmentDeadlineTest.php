<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentSubmission;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * DS-01 (class assignments): the server deadline freezes the answers.
 */
class Ds01AssignmentDeadlineTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
        $this->seedAssignmentActors();
        $this->seedAssignmentRewards();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_late_replacement_cannot_improve_a_draft_saved_before_the_deadline(): void
    {
        $q = $this->makeLibraryItem(5);
        $assignmentId = $this->makeClassAssignment($q['item']);
        $attempt = $this->makeAttempt($assignmentId);

        // 09:01 - a wrong MCQ answer is autosaved before the deadline.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:01:00'));
        $this->actAs($this->student)->post(route('student.assignments.autosave', [$assignmentId, $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['wrong']],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => true]);

        // 09:06 - past the 5 minute limit. Autosave refuses the replacement...
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->actAs($this->student)->post(route('student.assignments.autosave', [$assignmentId, $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
            'client_version' => 2,
        ])->assertStatus(409)->assertJson(['saved' => false, 'expired' => true]);

        // ...and so does the final submission.
        $this->actAs($this->student)->post(route('student.assignments.submit', [$assignmentId, $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect(route('student.assignments.result', [$assignmentId, $attempt->id]));

        $attempt->refresh();
        $this->assertSame(0, $attempt->score, 'Late answers must not raise the score.');
        $this->assertSame('graded', $attempt->status);
        $this->assertNotNull($attempt->timed_out_at);
        $this->assertSame([(string) $q['mcq'] => (string) $q['wrong']], $attempt->draft_answers);
        $this->assertDatabaseHas('assignment_submission_answers', [
            'assignment_submission_id' => $attempt->id,
            'assignment_question_id' => $q['mcq'],
            'selected_option_id' => $q['wrong'],
            'points_awarded' => 0,
        ]);
        $this->assertDatabaseHas('assignment_submission_answers', [
            'assignment_submission_id' => $attempt->id,
            'assignment_question_id' => $q['blank'],
            'answer_text' => '',
            'points_awarded' => 0,
        ]);
    }

    public function test_eligible_saved_work_is_preserved_and_graded_after_expiry(): void
    {
        $q = $this->makeLibraryItem(5);
        $assignmentId = $this->makeClassAssignment($q['item']);
        $attempt = $this->makeAttempt($assignmentId);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:04:59'));
        $this->actAs($this->student)->post(route('student.assignments.autosave', [$assignmentId, $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'Pandas'],
            'client_version' => 1,
        ])->assertOk();

        // Exactly at the deadline a late replacement tries to wipe the work.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:05:00'));
        $this->actAs($this->student)->post(route('student.assignments.submit', [$assignmentId, $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['wrong'], $q['blank'] => 'numpy'],
        ])->assertRedirect();

        $attempt->refresh();
        $this->assertSame(10, $attempt->score, 'The draft saved before expiry keeps its score.');
        $this->assertSame('graded', $attempt->status);
        $this->assertNotNull($attempt->timed_out_at);
    }

    public function test_late_answers_without_any_draft_give_an_unanswered_result_not_an_error(): void
    {
        $q = $this->makeLibraryItem(5);
        $assignmentId = $this->makeClassAssignment($q['item']);
        $attempt = $this->makeAttempt($assignmentId, null, Carbon::parse('2026-09-20 08:00:00'));

        $this->actAs($this->student)->post(route('student.assignments.submit', [$assignmentId, $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect(route('student.assignments.result', [$assignmentId, $attempt->id]));

        $attempt->refresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame(0, $attempt->score);
        $this->assertSame(2, DB::table('assignment_submission_answers')->where('assignment_submission_id', $attempt->id)->count());

        $this->actAs($this->student)
            ->get(route('student.assignments.result', [$assignmentId, $attempt->id]))
            ->assertOk()
            ->assertSee('No answer');
    }

    public function test_answers_posted_before_the_deadline_are_still_accepted(): void
    {
        $q = $this->makeLibraryItem(5);
        $assignmentId = $this->makeClassAssignment($q['item']);
        $attempt = $this->makeAttempt($assignmentId);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:04:59'));
        $this->actAs($this->student)->post(route('student.assignments.submit', [$assignmentId, $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect();

        $attempt->refresh();
        $this->assertSame(10, $attempt->score);
        $this->assertNull($attempt->timed_out_at);
    }

    public function test_repeated_finalization_does_not_duplicate_results_or_rewards(): void
    {
        $q = $this->makeLibraryItem(5);
        $assignmentId = $this->makeClassAssignment($q['item']);
        $attempt = $this->makeAttempt($assignmentId);
        $payload = ['answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas']];

        $this->actAs($this->student)->post(route('student.assignments.submit', [$assignmentId, $attempt->id]), $payload)
            ->assertRedirect();
        $xpAfterFirst = (int) $this->student->fresh()->xp;
        // DataSensei Updates 5: class work (an assignment) gives no XP and no
        // mission progress.
        $this->assertSame(0, $xpAfterFirst);
        $this->assertSame(0, $this->missionProgress($this->student));

        // The repeat (double click, browser retry, late auto-submit) is inert.
        for ($i = 0; $i < 2; $i++) {
            $this->actAs($this->student)->post(route('student.assignments.submit', [$assignmentId, $attempt->id]), [
                'answers' => [$q['mcq'] => (string) $q['wrong']],
            ]);
        }

        $this->assertSame(10, $attempt->fresh()->score);
        $this->assertSame($xpAfterFirst, (int) $this->student->fresh()->xp);
        $this->assertSame(0, $this->missionProgress($this->student));
        $this->assertSame(2, DB::table('assignment_submission_answers')->where('assignment_submission_id', $attempt->id)->count());
        $this->assertSame(1, DB::table('notifications')->where('type', 'assignment_graded')->count());
    }

    public function test_the_reward_service_pays_nothing_for_class_work(): void
    {
        $q = $this->makeLibraryItem(5);
        $assignmentId = $this->makeClassAssignment($q['item']);
        $attempt = $this->makeAttempt($assignmentId);
        $attempt->update(['status' => 'graded', 'score' => 10, 'submitted_at' => now(), 'graded_at' => now()]);

        // DataSensei Updates 5: an assignment is class work and gives no XP,
        // achievement or mission progress, however often it is reported.
        $service = app(\App\Services\GamificationService::class);
        $first = $service->awardForAssignmentSubmission($this->student, $attempt->fresh());
        $second = $service->awardForAssignmentSubmission($this->student, $attempt->fresh());

        $this->assertSame([], $first);
        $this->assertSame([], $second);
        $this->assertSame(0, $this->missionProgress($this->student));
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }
}
