<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentSubmission;
use App\Models\TableOfSpecification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsAssessmentWorkflow;
use Tests\TestCase;

/**
 * Full TOS assessment workflow with the DS-01 / DS-08 / DS-09 repairs in place:
 * author -> publish -> start -> autosave -> refresh -> timeout submit -> result
 * -> essay grading -> student sees grade -> retake.
 */
class Ds01AssessmentWorkflowTest extends TestCase
{
    use BuildsAssessmentWorkflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootAssessmentWorkflow();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        $this->dropAssessmentWorkflowTables();
        parent::tearDown();
    }

    public function test_complete_timed_assessment_workflow(): void
    {
        $start = Carbon::parse('2026-09-10 08:00:00');
        Carbon::setTestNow($start);

        // --- Instructor authors and publishes -------------------------------
        $tos = TableOfSpecification::create([
            'class_id' => $this->classId,
            'module_no' => 1,
            'total_items' => 3,
            'title' => 'Workflow TOS',
            'status' => 'draft',
            'cognitive_distribution' => [],
            'created_by' => $this->instructor->id,
        ]);
        DB::table('table_of_specification_rows')->insert([
            'table_of_specification_id' => $tos->id,
            'topic_title' => 'Probability',
            'subtopic_title' => 'Basics',
            'learning_objective' => 'Reason about simple probabilities.',
            'difficulty_slug' => 'university-student',
            'cognitive_level' => 'Apply',
            'item_count' => 3,
            'default_points' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->instructorClient()->post(route('instructor.assessments.store', $tos), [
            'class_id' => $this->classId,
            'title' => 'Timed Probability Quiz',
            'time_limit_minutes' => 10,
            'max_attempts' => 2,
        ])->assertRedirect();

        $assessment = Assessment::query()->firstOrFail();
        $questions = $assessment->questions()->orderBy('item_number')->get();
        $this->assertCount(3, $questions);

        $definitions = [
            [
                'question_type' => 'multiple_choice',
                'question_text' => 'P(heads) for a fair coin?',
                'points' => 2,
                'option_texts' => ['0.25', '0.5', '1'],
                'correct_option' => 1,
            ],
            [
                'question_type' => 'fill_blank',
                'question_text' => 'The probability of an impossible event is ____.',
                'points' => 2,
                'correct_answer' => "0\nzero",
            ],
            [
                'question_type' => 'essay',
                'question_text' => 'Explain independence.',
                'points' => 4,
                'rubric_text' => 'Definition plus an example.',
            ],
        ];
        foreach ($questions as $index => $question) {
            $this->instructorClient()->patch(
                route('instructor.assessments.questions.update', [$assessment, $question]),
                array_merge($definitions[$index], ['intent' => 'complete_next', 'is_required' => 1])
            )->assertSessionHasNoErrors();
        }

        // A student cannot see the draft.
        $this->studentClient()->get(route('student.assessments.show', $assessment))->assertNotFound();

        $this->instructorClient()
            ->patch(route('instructor.assessments.publish', $assessment))
            ->assertSessionHasNoErrors();
        $assessment->refresh();
        $this->assertSame('published', $assessment->status);
        $this->assertSame(8, $assessment->total_points);

        $questions = $assessment->questions()->with('options')->orderBy('item_number')->get();
        [$mcq, $blank, $essay] = [$questions[0], $questions[1], $questions[2]];
        $correctOption = $mcq->options->firstWhere('is_correct', true);
        $wrongOption = $mcq->options->firstWhere('is_correct', false);

        // --- Student starts; a second start resumes the same attempt --------
        $this->studentClient()->post(route('student.assessments.start', $assessment))->assertRedirect();
        $this->studentClient()->post(route('student.assessments.start', $assessment))->assertRedirect();
        $this->assertSame(1, AssessmentSubmission::count());
        $submission = AssessmentSubmission::firstOrFail();

        $autosaveUrl = route('student.assessments.autosave', [$assessment, $submission]);
        $submitUrl = route('student.assessments.submit', [$assessment, $submission]);

        // --- Autosave, stale version, refresh restores the draft -------------
        Carbon::setTestNow($start->copy()->addMinutes(4));
        $this->studentClient()->postJson($autosaveUrl, [
            'client_version' => 1,
            'answers' => [$mcq->id => (string) $wrongOption->id, $blank->id => '0'],
        ])->assertOk()->assertJson(['saved' => true, 'current_version' => 1]);

        $this->studentClient()->postJson($autosaveUrl, [
            'client_version' => 2,
            'answers' => [
                $mcq->id => (string) $wrongOption->id,
                $blank->id => '0',
                $essay->id => 'Events are independent when one does not change the other.',
            ],
        ])->assertOk()->assertJson(['saved' => true, 'current_version' => 2]);

        // A stale tab replays an older snapshot: rejected, draft untouched.
        $this->studentClient()->postJson($autosaveUrl, [
            'client_version' => 2,
            'answers' => [$blank->id => 'stale'],
        ])->assertOk()->assertJson(['saved' => false, 'stale' => true, 'current_version' => 2]);
        $this->assertSame('0', $submission->fresh()->draft_answers[(string) $blank->id]);

        $this->studentClient()
            ->get(route('student.assessments.take', [$assessment, $submission]))
            ->assertOk()
            ->assertSee('name="answers[' . $blank->id . ']" value="0"', false)
            ->assertSee('Events are independent when one does not change the other.</textarea>', false)
            ->assertSee('data-remaining="360"', false);

        // --- Ownership: another enrolled student cannot touch this attempt ---
        $intruder = fn () => $this->clientFor($this->otherStudent);
        $intruder()->get(route('student.assessments.take', [$assessment, $submission]))->assertForbidden();
        $intruder()->postJson($autosaveUrl, ['client_version' => 9, 'answers' => [$blank->id => 'x']])->assertForbidden();
        $intruder()->post($submitUrl, ['answers' => [$blank->id => 'x']])->assertForbidden();
        $this->assertSame('in_progress', $submission->fresh()->status);
        $this->assertSame(2, $submission->fresh()->draft_version);

        // --- Deadline passes --------------------------------------------------
        Carbon::setTestNow($start->copy()->addMinutes(10)->addSeconds(5));
        $this->studentClient()->postJson($autosaveUrl, [
            'client_version' => 3,
            'answers' => [$mcq->id => (string) $correctOption->id],
        ])->assertStatus(409)->assertJson(['saved' => false, 'expired' => true]);

        // The late submit tries to swap in the correct MCQ choice.
        $this->studentClient()->post($submitUrl, [
            'answers' => [
                $mcq->id => (string) $correctOption->id,
                $blank->id => '0',
                $essay->id => 'A much longer essay written after time ran out.',
            ],
        ])->assertRedirect(route('student.assessments.result', [$assessment, $submission]));

        $submission->refresh();
        $this->assertSame('submitted', $submission->status);
        $this->assertNotNull($submission->timed_out_at);
        $this->assertSame('2.00', $submission->score, 'Only the fill-blank "0" saved before the deadline earns credit.');
        $this->assertSame(3, AssessmentAnswer::count());
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_question_id' => $mcq->id,
            'selected_option_id' => $wrongOption->id,
            'is_correct' => 0,
        ]);
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_question_id' => $essay->id,
            'answer_text' => 'Events are independent when one does not change the other.',
        ]);

        // --- Duplicate submit is a no-op --------------------------------------
        $submittedAt = $submission->submitted_at->format('Y-m-d H:i:s');
        Carbon::setTestNow($start->copy()->addMinutes(11));
        $this->studentClient()->post($submitUrl, [
            'answers' => [$mcq->id => (string) $correctOption->id],
        ])->assertRedirect();
        $submission->refresh();
        $this->assertSame('2.00', $submission->score);
        $this->assertSame($submittedAt, $submission->submitted_at->format('Y-m-d H:i:s'));
        $this->assertSame(3, AssessmentAnswer::count());
        $this->assertSame(0, DB::table('notifications')->where('type', 'assessment_graded')->count());

        // Result page: own attempt only.
        $this->studentClient()
            ->get(route('student.assessments.result', [$assessment, $submission]))
            ->assertOk()
            ->assertSee('<strong>Your answer:</strong> 0</p>', false)
            ->assertSeeText('waiting for instructor grading');
        $intruder()->get(route('student.assessments.result', [$assessment, $submission]))->assertForbidden();

        // --- Instructor grades the essay ---------------------------------------
        $essayAnswer = AssessmentAnswer::where('assessment_question_id', $essay->id)->firstOrFail();
        $gradeUrl = route('instructor.assessments.submissions.grade', [$assessment, $submission]);

        $this->instructorClient()
            ->get(route('instructor.assessments.submissions.show', [$assessment, $submission]))
            ->assertOk()
            ->assertSee('name="scores[' . $essayAnswer->id . ']" value=""', false);

        // Feedback first, no score: stays pending.
        $this->instructorClient()->patch($gradeUrl, [
            'scores' => [$essayAnswer->id => ''],
            'feedbacks' => [$essayAnswer->id => 'Add an example.'],
        ])->assertSessionHasNoErrors();
        $this->assertSame('submitted', $submission->fresh()->status);
        $this->assertNull($submission->fresh()->graded_at);

        $this->instructorClient()->patch($gradeUrl, [
            'scores' => [$essayAnswer->id => '3'],
            'feedbacks' => [$essayAnswer->id => 'Add an example.'],
            'feedback' => 'Solid attempt.',
        ])->assertSessionHasNoErrors();

        $submission->refresh();
        $this->assertSame('graded', $submission->status);
        $this->assertSame('5.00', $submission->score);
        $this->assertSame(1, DB::table('notifications')
            ->where('type', 'assessment_graded')
            ->where('user_id', $this->student->id)
            ->count());

        $this->studentClient()
            ->get(route('student.assessments.result', [$assessment, $submission]))
            ->assertOk()
            ->assertSeeText('Solid attempt.')
            ->assertSeeText('Add an example.')
            ->assertSeeText('5.00/8.00')
            ->assertDontSeeText('waiting for instructor grading');

        // --- Retake within max_attempts ----------------------------------------
        $this->studentClient()
            ->get(route('student.assessments.show', $assessment))
            ->assertOk()
            ->assertSeeText('Attempts used: 1 of 2');
        $this->studentClient()->post(route('student.assessments.start', $assessment))->assertRedirect();
        $second = AssessmentSubmission::where('attempt_no', 2)->firstOrFail();
        $this->assertSame('in_progress', $second->status);
        $this->assertNull($second->draft_answers);

        $this->studentClient()->post(
            route('student.assessments.submit', [$assessment, $second]),
            ['answers' => [
                $mcq->id => (string) $correctOption->id,
                $blank->id => 'Zero',
                $essay->id => 'Second attempt essay.',
            ]]
        )->assertSessionHasNoErrors();
        $this->assertSame('4.00', $second->fresh()->score);
        $this->assertNull($second->fresh()->timed_out_at);

        // First attempt is untouched; a third attempt is refused.
        $this->assertSame('5.00', $submission->fresh()->score);
        $this->studentClient()
            ->post(route('student.assessments.start', $assessment))
            ->assertSessionHasErrors('assessment');
        $this->assertSame(2, AssessmentSubmission::count());
        $this->assertSame(6, AssessmentAnswer::count());
    }
}
