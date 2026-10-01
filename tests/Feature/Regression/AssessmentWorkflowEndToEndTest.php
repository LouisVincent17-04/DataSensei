<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * The assessment workflow end to end, through the real routes and middleware
 * (CSRF verification aside): instructor builds and publishes (save ->
 * settings -> questions -> publish), student starts, autosaves, refreshes,
 * submits, reviews the result, instructor sees it, student retakes within
 * max_attempts. Plus the refusal paths.
 */
class AssessmentWorkflowEndToEndTest extends TestCase
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

    public function test_complete_assessment_workflow(): void
    {
        $this->setPolicy(['max_tab_switches' => 2]);

        // Instructor creates a draft with the one-form builder flow...
        $this->actAs($this->instructor)->post(route('instructor.assessments.save'), [
            'class_id' => $this->classId,
            'title' => 'End to end assessment',
            'max_attempts' => 2,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $assessment = Assessment::where('title', 'End to end assessment')->firstOrFail();
        $this->assertSame('draft', $assessment->status);

        // ...tunes the settings (the 5-minute limit arrives here)...
        $this->actAs($this->instructor)->patch(route('instructor.assessments.settings.update', $assessment), [
            'class_id' => $this->classId,
            'title' => 'End to end assessment',
            'purpose' => 'quiz',
            'max_attempts' => 2,
            'time_limit_minutes' => 5,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(5, $assessment->fresh()->time_limit_minutes);

        // ...and writes the two questions on the builder page.
        $this->actAs($this->instructor)->post(route('instructor.assessments.questions.store', $assessment), [
            'question_type' => 'multiple_choice',
            'question_text' => 'Which option is correct?',
            'points' => 5,
            'option_texts' => ['Correct option', 'Wrong option'],
            'correct_option' => 0,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->actAs($this->instructor)->post(route('instructor.assessments.questions.store', $assessment), [
            'question_type' => 'fill_blank',
            'question_text' => 'Which library provides DataFrame?',
            'points' => 5,
            'correct_answer' => 'pandas',
        ])->assertSessionHasNoErrors()->assertRedirect();

        // A draft is invisible to students; publishing opens it.
        $this->actAs($this->student)->get(route('student.assessments.show', $assessment))->assertNotFound();
        $this->actAs($this->instructor)->patch(route('instructor.assessments.publish', $assessment))
            ->assertSessionHasNoErrors()->assertRedirect();
        $assessment->refresh();
        $this->assertSame('published', $assessment->status);
        $this->assertSame(10, $assessment->total_points);

        $questions = $assessment->questions()->with('options')->orderBy('item_number')->get();
        $q = [
            'mcq' => $questions[0]->id,
            'correct' => $questions[0]->options->firstWhere('is_correct', true)->id,
            'wrong' => $questions[0]->options->firstWhere('is_correct', false)->id,
            'blank' => $questions[1]->id,
        ];

        // Student starts; a double click reuses the same attempt.
        $this->actAs($this->student)->post(route('student.assessments.start', $assessment))->assertRedirect();
        $this->actAs($this->student)->post(route('student.assessments.start', $assessment))->assertRedirect();
        $this->assertSame(1, AssessmentSubmission::count());
        $attempt = AssessmentSubmission::firstOrFail();
        $this->assertSame('in_progress', $attempt->status);
        $this->assertNotEmpty($attempt->anti_cheat_session_id);

        // Autosave, then a stale snapshot (same version) is refused.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:01:00'));
        $autosave = route('student.assessments.autosave', [$assessment, $attempt]);
        $this->actAs($this->student)->post($autosave, [
            'answers' => [$q['mcq'] => (string) $q['wrong'], $q['blank'] => 'pandas'],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => true, 'current_version' => 1]);
        $this->actAs($this->student)->post($autosave, [
            'answers' => [$q['mcq'] => (string) $q['correct']],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => false, 'stale' => true, 'current_version' => 1]);
        $this->assertSame((string) $q['wrong'], $attempt->fresh()->draft_answers[(string) $q['mcq']]);

        // Refresh: the draft is rendered back into the form.
        $html = $this->actAs($this->student)->get(route('student.assessments.take', [$assessment, $attempt]))
            ->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/value="' . $q['wrong'] . '"\s+required\s+checked/', $html);
        $this->assertStringContainsString('value="pandas"', $html);

        // Other student: not their submission.
        $this->actAs($this->otherStudent)->get(route('student.assessments.take', [$assessment, $attempt]))->assertForbidden();
        $this->actAs($this->otherStudent)->post($autosave, ['answers' => [], 'client_version' => 5])->assertForbidden();
        $this->actAs($this->otherStudent)->post(route('student.assessments.submit', [$assessment, $attempt]), [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
        ])->assertForbidden();

        // Wrong attempt identity before expiry: refused, attempt stays open.
        $submit = route('student.assessments.submit', [$assessment, $attempt]);
        $this->actAs($this->student)->post($submit, ['_anti_cheat_session_id' => 'wrong'])->assertSessionHasErrors('anti_cheat');
        $this->assertSame('in_progress', $attempt->fresh()->status);

        // Submit with improved answers before the deadline.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:03:00'));
        $this->actAs($this->student)->post($submit, [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
            'answers' => [$q['mcq'] => (string) $q['correct']],
        ])->assertRedirect(route('student.assessments.result', [$assessment, $attempt]));

        $attempt->refresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame('10.00', $attempt->score, 'posted MCQ answer merged over the saved blank answer');
        $this->assertSame('clear', $attempt->integrity_status);
        // DataSensei Updates 5 + 11: class work (an assessment) gives no XP.
        $this->assertSame(0, (int) $this->student->fresh()->xp);

        // Duplicate submit: inert.
        $this->actAs($this->student)->post($submit, [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
            'answers' => [$q['mcq'] => (string) $q['wrong']],
        ]);
        $this->assertSame('10.00', $attempt->fresh()->score);
        $this->assertSame(0, (int) $this->student->fresh()->xp);
        $this->assertSame(0, $this->missionProgress($this->student));

        // Autosave after completion is refused.
        $this->actAs($this->student)->postJson($autosave, ['answers' => [], 'client_version' => 9])
            ->assertStatus(422);
        $this->assertSame('10.00', $attempt->fresh()->score);

        // Result pages; another student cannot read it.
        $this->actAs($this->student)->get(route('student.assessments.result', [$assessment, $attempt]))
            ->assertOk()->assertSee('10.00/10.00');
        $this->actAs($this->student)->get(route('student.submissions.show', $attempt))
            ->assertRedirect(route('student.assessments.result', [$assessment, $attempt]));
        $this->actAs($this->otherStudent)->get(route('student.assessments.result', [$assessment, $attempt]))->assertForbidden();
        $this->actAs($this->otherStudent)->get(route('student.submissions.show', $attempt))->assertForbidden();

        // Instructor sees the submission; a different instructor does not.
        $this->actAs($this->instructor)->get(route('instructor.assessments.submissions', $assessment))
            ->assertOk()->assertSee($this->student->name)->assertSee('10.00 / 10.00');
        $this->actAs($this->instructor)->get(route('instructor.assessments.submissions.show', [$assessment, $attempt]))
            ->assertOk()->assertSee('Correct option');
        $this->actAs($this->otherInstructor)->get(route('instructor.assessments.submissions', $assessment))->assertForbidden();

        // Retake within max_attempts (2): second attempt times out with a saved draft.
        Carbon::setTestNow(Carbon::parse('2026-09-20 10:00:00'));
        $this->actAs($this->student)->post(route('student.assessments.start', $assessment))->assertRedirect();
        $second = AssessmentSubmission::where('attempt_no', 2)->firstOrFail();
        $this->assertNotSame($attempt->anti_cheat_session_id, $second->anti_cheat_session_id);

        Carbon::setTestNow(Carbon::parse('2026-09-20 10:02:00'));
        $this->actAs($this->student)->post(route('student.assessments.autosave', [$assessment, $second]), [
            'answers' => [$q['mcq'] => (string) $q['wrong'], $q['blank'] => 'pandas'],
            'client_version' => 1,
        ])->assertOk();

        // The first attempt's identity does not open the second attempt.
        $this->actAs($this->student)->post(route('student.assessments.submit', [$assessment, $second]), [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
        ])->assertSessionHasErrors('anti_cheat');

        Carbon::setTestNow(Carbon::parse('2026-09-20 10:05:01'));
        $this->actAs($this->student)->post(route('student.assessments.submit', [$assessment, $second]), [
            '_anti_cheat_session_id' => $second->anti_cheat_session_id,
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect();
        $second->refresh();
        $this->assertSame('graded', $second->status);
        $this->assertSame('5.00', $second->score, 'Only the draft saved before the deadline counts.');
        $this->assertNotNull($second->timed_out_at);
        $this->assertSame(0, $this->missionProgress($this->student), 'Class work never counts toward missions (Updates 5).');

        // Attempts are exhausted now.
        $this->actAs($this->student)->post(route('student.assessments.start', $assessment))
            ->assertRedirect()
            ->assertSessionHasErrors('assessment');
        $this->assertSame(2, AssessmentSubmission::count());

        // History is intact.
        $this->assertSame('10.00', $attempt->fresh()->score);
        $this->assertSame(4, DB::table('assessment_answers')->count());
    }
}
