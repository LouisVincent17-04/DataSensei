<?php

namespace Tests\Feature\Regression;

use App\Services\GamificationService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * DS-01 (class assessments): the server deadline freezes the answers, and the
 * attempt end combines the time limit with the due date (DataSensei
 * Updates 11): a timed attempt ends at min(started_at + limit, due_at), while
 * untimed work has no hard end and is only marked late after due_at.
 */
class Ds01AssessmentDeadlineTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
        $this->seedAssessmentActors();
        $this->seedAssessmentRewards();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_late_replacement_cannot_improve_a_draft_saved_before_the_deadline(): void
    {
        $q = $this->makeAssessment(5);
        $attempt = $this->makeAttempt($q['assessment']);

        // 09:01 - a wrong MCQ answer is autosaved before the deadline.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:01:00'));
        $this->actAs($this->student)->post(route('student.assessments.autosave', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['wrong']],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => true]);

        // 09:06 - past the 5 minute limit. Autosave refuses the replacement...
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:06:00'));
        $this->actAs($this->student)->post(route('student.assessments.autosave', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
            'client_version' => 2,
        ])->assertStatus(409)->assertJson(['saved' => false, 'expired' => true]);

        // ...and so does the final submission.
        $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect(route('student.assessments.result', [$q['assessment'], $attempt->id]));

        $attempt->refresh();
        $this->assertSame('0.00', $attempt->score, 'Late answers must not raise the score.');
        $this->assertSame('graded', $attempt->status);
        $this->assertNotNull($attempt->timed_out_at);
        $this->assertSame([(string) $q['mcq'] => (string) $q['wrong']], $attempt->draft_answers);
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_submission_id' => $attempt->id,
            'assessment_question_id' => $q['mcq'],
            'selected_option_id' => $q['wrong'],
            'points_awarded' => 0,
        ]);
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_submission_id' => $attempt->id,
            'assessment_question_id' => $q['blank'],
            'answer_text' => '',
            'points_awarded' => 0,
        ]);
    }

    public function test_eligible_saved_work_is_preserved_and_graded_after_expiry(): void
    {
        $q = $this->makeAssessment(5);
        $attempt = $this->makeAttempt($q['assessment']);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:04:59'));
        $this->actAs($this->student)->post(route('student.assessments.autosave', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'Pandas'],
            'client_version' => 1,
        ])->assertOk();

        // Exactly at the deadline a late replacement tries to wipe the work.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:05:00'));
        $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['wrong'], $q['blank'] => 'numpy'],
        ])->assertRedirect();

        $attempt->refresh();
        $this->assertSame('10.00', $attempt->score, 'The draft saved before expiry keeps its score.');
        $this->assertSame('graded', $attempt->status);
        $this->assertNotNull($attempt->timed_out_at);
    }

    public function test_late_answers_without_any_draft_give_an_unanswered_result_not_an_error(): void
    {
        $q = $this->makeAssessment(5);
        $attempt = $this->makeAttempt($q['assessment'], null, Carbon::parse('2026-09-20 08:00:00'));

        $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect(route('student.assessments.result', [$q['assessment'], $attempt->id]));

        $attempt->refresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame('0.00', $attempt->score);
        $this->assertSame(2, DB::table('assessment_answers')->where('assessment_submission_id', $attempt->id)->count());

        $this->actAs($this->student)
            ->get(route('student.assessments.result', [$q['assessment'], $attempt->id]))
            ->assertOk()
            ->assertSee('No answer');
    }

    public function test_answers_posted_before_the_deadline_are_still_accepted(): void
    {
        $q = $this->makeAssessment(5);
        $attempt = $this->makeAttempt($q['assessment']);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:04:59'));
        $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect();

        $attempt->refresh();
        $this->assertSame('10.00', $attempt->score);
        $this->assertNull($attempt->timed_out_at);
    }

    public function test_repeated_finalization_does_not_duplicate_results_or_rewards(): void
    {
        $q = $this->makeAssessment(5);
        $attempt = $this->makeAttempt($q['assessment']);
        $payload = ['answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas']];

        $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), $payload)
            ->assertRedirect();
        $xpAfterFirst = (int) $this->student->fresh()->xp;
        // DataSensei Updates 5 + 11: class work (an assessment) gives no XP
        // and no mission progress.
        $this->assertSame(0, $xpAfterFirst);
        $this->assertSame(0, $this->missionProgress($this->student));

        // The repeat (double click, browser retry, late auto-submit) is inert.
        for ($i = 0; $i < 2; $i++) {
            $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), [
                'answers' => [$q['mcq'] => (string) $q['wrong']],
            ]);
        }

        $this->assertSame('10.00', $attempt->fresh()->score);
        $this->assertSame($xpAfterFirst, (int) $this->student->fresh()->xp);
        $this->assertSame(0, $this->missionProgress($this->student));
        $this->assertSame(2, DB::table('assessment_answers')->where('assessment_submission_id', $attempt->id)->count());
        $this->assertSame(1, DB::table('notifications')->where('type', 'assessment_graded')->count());
    }

    public function test_the_reward_service_pays_nothing_for_class_work(): void
    {
        $q = $this->makeAssessment(5);
        $attempt = $this->makeAttempt($q['assessment']);
        $attempt->update(['status' => 'graded', 'score' => 10, 'submitted_at' => now(), 'graded_at' => now()]);

        // DataSensei Updates 5 + 11: an assessment is class work and gives no
        // XP, achievement or mission progress, however often it is reported.
        $service = app(GamificationService::class);
        $first = $service->recordAssessmentSubmission($this->student, (int) $attempt->id);
        $second = $service->recordAssessmentSubmission($this->student, (int) $attempt->id);

        $this->assertSame([], $first);
        $this->assertSame([], $second);
        $this->assertSame(0, $this->missionProgress($this->student));
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_untimed_work_submitted_after_the_due_date_is_accepted_and_marked_late(): void
    {
        // Homework has no time limit: the due date does not hard-end it.
        $q = $this->makeAssessment(null, ['due_at' => Carbon::parse('2026-09-20 09:30:00')]);
        $attempt = $this->makeAttempt($q['assessment']);

        // Half an hour past the due date the attempt is still open: the draft
        // can be saved and the posted answers are accepted, not frozen.
        Carbon::setTestNow(Carbon::parse('2026-09-20 10:00:00'));
        $this->actAs($this->student)->post(route('student.assessments.autosave', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['wrong']],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => true]);

        $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect(route('student.assessments.result', [$q['assessment'], $attempt->id]));

        $attempt->refresh();
        $this->assertSame('late', $attempt->status, 'Untimed work turned in after due_at is late, not refused.');
        $this->assertSame('10.00', $attempt->score, 'The posted answers were accepted: there was no hard end.');
        $this->assertNull($attempt->timed_out_at);
        $this->assertNotNull($attempt->graded_at);
    }

    public function test_a_timed_assessment_cannot_be_started_after_its_due_date(): void
    {
        $timed = $this->makeAssessment(5, ['due_at' => Carbon::parse('2026-09-20 09:30:00')]);
        $untimed = $this->makeAssessment(null, ['due_at' => Carbon::parse('2026-09-20 09:30:00')]);

        Carbon::setTestNow(Carbon::parse('2026-09-20 10:00:00'));

        // A timed attempt would end at the due date, i.e. immediately: refused.
        $this->actAs($this->student)
            ->post(route('student.assessments.start', $timed['assessment']))
            ->assertForbidden();
        $this->assertSame(0, DB::table('assessment_submissions')->where('assessment_id', $timed['assessment'])->count());

        // Untimed work can still be started after the due date (it will be late).
        $this->actAs($this->student)
            ->post(route('student.assessments.start', $untimed['assessment']))
            ->assertRedirect();
        $this->assertSame(1, DB::table('assessment_submissions')
            ->where('assessment_id', $untimed['assessment'])
            ->where('status', 'in_progress')
            ->count());
    }

    public function test_a_timed_attempt_ends_at_the_due_date_when_it_comes_before_the_time_limit(): void
    {
        // 60 minutes of time limit, but the due date arrives after 10:
        // the attempt hard-ends at min(started_at + limit, due_at) = 09:10.
        $q = $this->makeAssessment(60, ['due_at' => Carbon::parse('2026-09-20 09:10:00')]);
        $attempt = $this->makeAttempt($q['assessment']);

        // The take page counts down to the due date, not the 60-minute limit.
        $this->actAs($this->student)
            ->get(route('student.assessments.take', [$q['assessment'], $attempt->id]))
            ->assertOk()
            ->assertSee('data-remaining="600"', false);

        // A second before the due date the draft still saves...
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:09:59'));
        $this->actAs($this->student)->post(route('student.assessments.autosave', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => true]);

        // ...at the due date the attempt is over, long before the time limit.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:10:00'));
        $this->actAs($this->student)->post(route('student.assessments.autosave', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['blank'] => 'numpy'],
            'client_version' => 2,
        ])->assertStatus(409)->assertJson(['saved' => false, 'expired' => true]);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:12:00'));
        $this->actAs($this->student)->post(route('student.assessments.submit', [$q['assessment'], $attempt->id]), [
            'answers' => [$q['mcq'] => (string) $q['wrong'], $q['blank'] => 'numpy'],
        ])->assertRedirect();

        $attempt->refresh();
        $this->assertSame('10.00', $attempt->score, 'The draft saved before the due date is what gets graded.');
        $this->assertSame('late', $attempt->status, 'Finalized after due_at, so the attempt is marked late.');
        $this->assertNotNull($attempt->timed_out_at);
        $this->assertNotNull($attempt->graded_at);
    }
}
