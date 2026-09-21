<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsAssessmentWorkflow;
use Tests\TestCase;

/**
 * DS-09: blank / omitted essay scores must not silently become zero, an
 * explicit 0 must stay a valid score, and unscored essays stay pending review.
 */
class Ds09EssayScoreSemanticsTest extends TestCase
{
    use BuildsAssessmentWorkflow;

    private Assessment $assessment;
    private AssessmentSubmission $submission;
    private AssessmentAnswer $essayOne;
    private AssessmentAnswer $essayTwo;
    private AssessmentAnswer $autoGraded;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootAssessmentWorkflow();
        Carbon::setTestNow(Carbon::parse('2026-09-01 09:00:00'));

        [$this->assessment, $questions] = $this->makeAssessment([], [
            ['question_type' => 'short_answer', 'points' => 2, 'correct_answer' => 'median'],
            ['question_type' => 'essay', 'points' => 5, 'rubric_text' => 'Rubric one.'],
            ['question_type' => 'essay', 'points' => 3, 'rubric_text' => 'Rubric two.'],
        ]);
        $this->submission = $this->startAttempt($this->assessment);

        $this->studentClient()->post(
            route('student.assessments.submit', [$this->assessment, $this->submission]),
            ['answers' => [
                $questions[0]->id => 'median',
                $questions[1]->id => 'First essay response.',
                $questions[2]->id => 'Second essay response.',
            ]]
        )->assertSessionHasNoErrors();

        $answers = AssessmentAnswer::where('assessment_submission_id', $this->submission->id)
            ->get()
            ->keyBy('assessment_question_id');
        $this->autoGraded = $answers[$questions[0]->id];
        $this->essayOne = $answers[$questions[1]->id];
        $this->essayTwo = $answers[$questions[2]->id];

        $this->submission->refresh();
        $this->assertSame('submitted', $this->submission->status);
        $this->assertNull($this->submission->graded_at);
        $this->assertSame('2.00', $this->submission->score);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        $this->dropAssessmentWorkflowTables();
        parent::tearDown();
    }

    public function test_blank_score_field_does_not_become_zero_and_keeps_the_submission_pending(): void
    {
        $this->grade([
            'scores' => [$this->essayOne->id => '', $this->essayTwo->id => ''],
            'feedbacks' => [$this->essayOne->id => 'Looking at this later.'],
        ])->assertSessionHasNoErrors();

        $this->assertPending();
        $this->assertNull($this->essayOne->fresh()->is_correct);
        $this->assertNull($this->essayTwo->fresh()->is_correct);
        $this->assertSame('Looking at this later.', $this->essayOne->fresh()->instructor_feedback);
        $this->assertSame('2.00', $this->submission->fresh()->score);
        $this->assertSame(0, DB::table('notifications')->where('type', 'assessment_graded')->count());
        $this->assertDatabaseHas('student_assessment_diagnostics', [
            'assessment_id' => $this->assessment->id,
            'manual_review_pending' => 1,
        ]);
    }

    public function test_omitted_scores_key_does_not_finalize_or_zero_anything(): void
    {
        $this->grade(['feedback' => 'Overall note only.'])->assertSessionHasNoErrors();

        $this->assertPending();
        $this->assertSame('Overall note only.', $this->submission->fresh()->feedback);
        $this->assertNull($this->essayOne->fresh()->is_correct);
        $this->assertSame('2.00', $this->submission->fresh()->score);
    }

    public function test_explicit_zero_is_a_real_score_and_finalizes_when_every_essay_is_scored(): void
    {
        $this->grade([
            'scores' => [$this->essayOne->id => '0', $this->essayTwo->id => 0],
        ])->assertSessionHasNoErrors();

        $submission = $this->submission->fresh();
        $this->assertSame('graded', $submission->status);
        $this->assertNotNull($submission->graded_at);
        $this->assertSame('2.00', $submission->score);
        $this->assertFalse($this->essayOne->fresh()->is_correct);
        $this->assertSame('0.00', $this->essayOne->fresh()->points_awarded);
        $this->assertFalse($this->essayTwo->fresh()->is_correct);
        $this->assertSame(1, DB::table('notifications')->where('type', 'assessment_graded')->count());

        // The stored zero is shown as a number, not as a blank field.
        $this->instructorClient()
            ->get(route('instructor.assessments.submissions.show', [$this->assessment, $this->submission]))
            ->assertOk()
            ->assertSee('name="scores[' . $this->essayOne->id . ']" value="0.00"', false);
    }

    public function test_partial_grading_stays_pending_then_completes_without_losing_the_first_score(): void
    {
        $this->grade([
            'scores' => [$this->essayOne->id => '4.5', $this->essayTwo->id => ''],
            'feedbacks' => [$this->essayOne->id => 'Good structure.'],
        ])->assertSessionHasNoErrors()->assertSessionHas('success', fn ($message) => str_contains($message, 'Item 3'));

        $this->assertPending();
        $this->assertSame('4.50', $this->essayOne->fresh()->points_awarded);
        $this->assertFalse($this->essayOne->fresh()->is_correct);
        $this->assertNull($this->essayTwo->fresh()->is_correct);
        $this->assertSame('6.50', $this->submission->fresh()->score);

        // The form now shows the stored score for essay one and blank for essay two.
        $this->instructorClient()
            ->get(route('instructor.assessments.submissions.show', [$this->assessment, $this->submission]))
            ->assertOk()
            ->assertSee('name="scores[' . $this->essayOne->id . ']" value="4.50"', false)
            ->assertSee('name="scores[' . $this->essayTwo->id . ']" value=""', false);

        // Student still sees a preliminary result.
        $this->studentClient()
            ->get(route('student.assessments.result', [$this->assessment, $this->submission]))
            ->assertOk()
            ->assertSeeText('waiting for instructor grading');

        // Second pass: only the remaining essay is posted; essay one is omitted.
        Carbon::setTestNow(Carbon::parse('2026-09-01 10:00:00'));
        $this->grade([
            'scores' => [$this->essayTwo->id => '3'],
        ])->assertSessionHasNoErrors();

        $submission = $this->submission->fresh();
        $this->assertSame('graded', $submission->status);
        $this->assertSame('2026-09-01 10:00:00', $submission->graded_at->format('Y-m-d H:i:s'));
        $this->assertSame('9.50', $submission->score);
        $this->assertSame('4.50', $this->essayOne->fresh()->points_awarded);
        $this->assertSame('Good structure.', $this->essayOne->fresh()->instructor_feedback);
        $this->assertTrue($this->essayTwo->fresh()->is_correct);
        $this->assertSame(1, DB::table('notifications')->where('type', 'assessment_graded')->count());
        $this->assertDatabaseHas('student_assessment_diagnostics', [
            'assessment_id' => $this->assessment->id,
            'manual_review_pending' => 0,
        ]);
    }

    public function test_feedback_only_update_never_erases_grades_or_changes_status(): void
    {
        $this->grade([
            'scores' => [$this->essayOne->id => '5', $this->essayTwo->id => '1.25'],
            'feedbacks' => [$this->essayOne->id => 'Excellent.', $this->essayTwo->id => 'Thin.'],
            'feedback' => 'First pass.',
        ])->assertSessionHasNoErrors();
        $gradedAt = $this->submission->fresh()->graded_at->format('Y-m-d H:i:s');
        $this->assertSame('8.25', $this->submission->fresh()->score);

        Carbon::setTestNow(Carbon::parse('2026-09-02 08:00:00'));

        // 1. Feedback-only PATCH without any scores key.
        $this->grade(['feedback' => 'Second pass, wording only.'])->assertSessionHasNoErrors();
        // 2. Browser form with both score fields cleared and one feedback edited.
        $this->grade([
            'scores' => [$this->essayOne->id => '', $this->essayTwo->id => ''],
            'feedbacks' => [$this->essayOne->id => 'Excellent, cite sources next time.', $this->essayTwo->id => 'Thin.'],
            'feedback' => 'Second pass, wording only.',
        ])->assertSessionHasNoErrors();

        $submission = $this->submission->fresh();
        $this->assertSame('graded', $submission->status);
        $this->assertSame('8.25', $submission->score);
        $this->assertSame($gradedAt, $submission->graded_at->format('Y-m-d H:i:s'));
        $this->assertSame('Second pass, wording only.', $submission->feedback);
        $this->assertSame('5.00', $this->essayOne->fresh()->points_awarded);
        $this->assertTrue($this->essayOne->fresh()->is_correct);
        $this->assertSame('1.25', $this->essayTwo->fresh()->points_awarded);
        $this->assertSame('Excellent, cite sources next time.', $this->essayOne->fresh()->instructor_feedback);
        $this->assertSame('Thin.', $this->essayTwo->fresh()->instructor_feedback);
        $this->assertSame(1, DB::table('notifications')->where('type', 'assessment_graded')->count());
    }

    public function test_changing_a_score_later_regrades_and_recomputes_from_stored_values(): void
    {
        $this->grade(['scores' => [$this->essayOne->id => '5', $this->essayTwo->id => '3']])
            ->assertSessionHasNoErrors();
        $this->assertSame('10.00', $this->submission->fresh()->score);

        Carbon::setTestNow(Carbon::parse('2026-09-03 08:00:00'));
        $this->grade(['scores' => [$this->essayOne->id => '0']])->assertSessionHasNoErrors();

        $submission = $this->submission->fresh();
        $this->assertSame('graded', $submission->status);
        $this->assertSame('5.00', $submission->score);
        $this->assertSame('3.00', $this->essayTwo->fresh()->points_awarded);
        $this->assertFalse($this->essayOne->fresh()->is_correct);
        $this->assertSame('2026-09-03 08:00:00', $submission->graded_at->format('Y-m-d H:i:s'));
    }

    public function test_invalid_scores_are_rejected_and_nothing_is_saved(): void
    {
        foreach (['-1', 'abc', '5.01'] as $bad) {
            $this->grade([
                'scores' => [$this->essayOne->id => $bad, $this->essayTwo->id => '1'],
                'feedback' => 'Should not be saved.',
            ])->assertSessionHasErrors('scores.' . $this->essayOne->id);
        }

        $this->assertPending();
        $this->assertNull($this->submission->fresh()->feedback);
        $this->assertNull($this->essayTwo->fresh()->is_correct);

        $this->instructorClient()
            ->from(route('instructor.assessments.submissions.show', [$this->assessment, $this->submission]))
            ->followingRedirects()
            ->patch(
                route('instructor.assessments.submissions.grade', [$this->assessment, $this->submission]),
                ['scores' => [$this->essayOne->id => '99']]
            )
            ->assertOk()
            ->assertSeeText('Item 2')
            ->assertSee('name="scores[' . $this->essayOne->id . ']" value="99"', false);
    }

    public function test_scores_posted_for_auto_graded_or_foreign_answers_are_ignored(): void
    {
        $this->grade([
            'scores' => [
                $this->autoGraded->id => '0',
                999999 => '3',
                $this->essayOne->id => '2',
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame('2.00', $this->autoGraded->fresh()->points_awarded);
        $this->assertTrue($this->autoGraded->fresh()->is_correct);
        $this->assertSame('4.00', $this->submission->fresh()->score);
        $this->assertPending();
    }

    public function test_late_submission_keeps_late_status_and_only_gets_graded_at_when_fully_scored(): void
    {
        $this->submission->update(['status' => 'late']);

        $this->grade(['scores' => [$this->essayOne->id => '1']])->assertSessionHasNoErrors();
        $this->assertSame('late', $this->submission->fresh()->status);
        $this->assertNull($this->submission->fresh()->graded_at);

        $this->grade(['scores' => [$this->essayTwo->id => '0']])->assertSessionHasNoErrors();
        $this->assertSame('late', $this->submission->fresh()->status);
        $this->assertNotNull($this->submission->fresh()->graded_at);
        $this->assertSame('3.00', $this->submission->fresh()->score);
    }

    public function test_another_instructor_cannot_grade(): void
    {
        $this->clientFor($this->otherStudent)
            ->patch(
                route('instructor.assessments.submissions.grade', [$this->assessment, $this->submission]),
                ['scores' => [$this->essayOne->id => '5']]
            )
            ->assertStatus(403);
        $this->assertNull($this->essayOne->fresh()->is_correct);
    }

    private function grade(array $payload)
    {
        return $this->instructorClient()->patch(
            route('instructor.assessments.submissions.grade', [$this->assessment, $this->submission]),
            $payload
        );
    }

    private function assertPending(): void
    {
        $submission = $this->submission->fresh();
        $this->assertSame('submitted', $submission->status);
        $this->assertNull($submission->graded_at);
    }
}
