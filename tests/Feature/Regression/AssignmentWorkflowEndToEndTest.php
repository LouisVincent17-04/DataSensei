<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentSubmission;
use App\Models\ClassAssignment;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * The assignment workflow end to end, through the real routes and middleware
 * (CSRF verification aside): instructor creates and publishes, student starts,
 * autosaves, refreshes, submits, reviews the result, instructor sees it,
 * student retakes within max_attempts. Plus the refusal paths.
 */
class AssignmentWorkflowEndToEndTest extends TestCase
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

    public function test_complete_assignment_workflow(): void
    {
        $q = $this->makeLibraryItem(5);
        $this->setPolicy(['max_tab_switches' => 2]);

        // Instructor creates a draft, then publishes it with the dedicated action.
        $this->actAs($this->instructor)->post(route('instructor.assignments.store'), [
            'class_id' => $this->classId,
            'assignment_library_item_id' => $q['item'],
            'title' => 'End to end assignment',
            'max_attempts' => 2,
            'status' => 'draft',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $assignment = ClassAssignment::where('title', 'End to end assignment')->firstOrFail();

        $this->actAs($this->student)->get(route('student.assignments.show', $assignment))->assertNotFound();
        $this->actAs($this->instructor)->patch(route('instructor.assignments.publish', $assignment))->assertRedirect();

        // Student starts; a double click reuses the same attempt.
        $this->actAs($this->student)->post(route('student.assignments.start', $assignment))->assertRedirect();
        $this->actAs($this->student)->post(route('student.assignments.start', $assignment))->assertRedirect();
        $this->assertSame(1, AssignmentSubmission::count());
        $attempt = AssignmentSubmission::firstOrFail();
        $this->assertSame('in_progress', $attempt->status);
        $this->assertNotEmpty($attempt->anti_cheat_session_id);

        // Autosave, then a stale snapshot (same version) is refused.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:01:00'));
        $autosave = route('student.assignments.autosave', [$assignment, $attempt]);
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
        $html = $this->actAs($this->student)->get(route('student.assignments.take', [$assignment, $attempt]))
            ->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/value="' . $q['wrong'] . '"\s+checked/', $html);
        $this->assertStringContainsString('value="pandas"', $html);

        // Other student: not their submission.
        $this->actAs($this->otherStudent)->get(route('student.assignments.take', [$assignment, $attempt]))->assertForbidden();
        $this->actAs($this->otherStudent)->post($autosave, ['answers' => [], 'client_version' => 5])->assertForbidden();
        $this->actAs($this->otherStudent)->post(route('student.assignments.submit', [$assignment, $attempt]), [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
        ])->assertForbidden();

        // Wrong attempt identity before expiry: refused, attempt stays open.
        $submit = route('student.assignments.submit', [$assignment, $attempt]);
        $this->actAs($this->student)->post($submit, ['_anti_cheat_session_id' => 'wrong'])->assertSessionHasErrors('anti_cheat');
        $this->assertSame('in_progress', $attempt->fresh()->status);

        // Submit with improved answers before the deadline.
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:03:00'));
        $this->actAs($this->student)->post($submit, [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
            'answers' => [$q['mcq'] => (string) $q['correct']],
        ])->assertRedirect(route('student.assignments.result', [$assignment, $attempt]));

        $attempt->refresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame(10, $attempt->score, 'posted MCQ answer merged over the saved blank answer');
        $this->assertSame('clear', $attempt->integrity_status);
        $this->assertSame(50, (int) $this->student->fresh()->xp);

        // Duplicate submit: inert, single XP award.
        $this->actAs($this->student)->post($submit, [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
            'answers' => [$q['mcq'] => (string) $q['wrong']],
        ]);
        $this->assertSame(10, $attempt->fresh()->score);
        $this->assertSame(50, (int) $this->student->fresh()->xp);
        $this->assertSame(1, $this->missionProgress($this->student));

        // Autosave after completion is refused.
        $this->actAs($this->student)->postJson($autosave, ['answers' => [], 'client_version' => 9])
            ->assertStatus(422);
        $this->assertSame(10, $attempt->fresh()->score);

        // Result pages; another student cannot read it.
        $this->actAs($this->student)->get(route('student.assignments.result', [$assignment, $attempt]))
            ->assertOk()->assertSee('10/10');
        $this->actAs($this->student)->get(route('student.submissions.show', $attempt))->assertOk();
        $this->actAs($this->otherStudent)->get(route('student.assignments.result', [$assignment, $attempt]))->assertForbidden();
        $this->actAs($this->otherStudent)->get(route('student.submissions.show', $attempt))->assertForbidden();

        // Instructor sees the submission; a different instructor does not.
        $this->actAs($this->instructor)->get(route('instructor.assignments.show', $assignment))
            ->assertOk()->assertSee($this->student->name)->assertSee('10/10');
        $this->actAs($this->otherInstructor)->get(route('instructor.assignments.show', $assignment))->assertForbidden();

        // Retake within max_attempts (2): second attempt times out with a saved draft.
        Carbon::setTestNow(Carbon::parse('2026-09-20 10:00:00'));
        $this->actAs($this->student)->post(route('student.assignments.start', $assignment))->assertRedirect();
        $second = AssignmentSubmission::where('attempt_no', 2)->firstOrFail();
        $this->assertNotSame($attempt->anti_cheat_session_id, $second->anti_cheat_session_id);

        Carbon::setTestNow(Carbon::parse('2026-09-20 10:02:00'));
        $this->actAs($this->student)->post(route('student.assignments.autosave', [$assignment, $second]), [
            'answers' => [$q['blank'] => 'pandas'],
            'client_version' => 1,
        ])->assertOk();

        // The first attempt's identity does not open the second attempt.
        $this->actAs($this->student)->post(route('student.assignments.submit', [$assignment, $second]), [
            '_anti_cheat_session_id' => $attempt->anti_cheat_session_id,
        ])->assertSessionHasErrors('anti_cheat');

        Carbon::setTestNow(Carbon::parse('2026-09-20 10:05:01'));
        $this->actAs($this->student)->post(route('student.assignments.submit', [$assignment, $second]), [
            '_anti_cheat_session_id' => $second->anti_cheat_session_id,
            'answers' => [$q['mcq'] => (string) $q['correct'], $q['blank'] => 'pandas'],
        ])->assertRedirect();
        $second->refresh();
        $this->assertSame('graded', $second->status);
        $this->assertSame(5, $second->score, 'Only the draft saved before the deadline counts.');
        $this->assertNotNull($second->timed_out_at);
        $this->assertSame(2, $this->missionProgress($this->student));

        // Attempts are exhausted now.
        $this->actAs($this->student)->post(route('student.assignments.start', $assignment))
            ->assertRedirect(route('student.assignments.result', [$assignment, $second]));
        $this->assertSame(2, AssignmentSubmission::count());

        // History is intact.
        $this->assertSame(10, $attempt->fresh()->score);
        $this->assertSame(4, DB::table('assignment_submission_answers')->count());
    }
}
