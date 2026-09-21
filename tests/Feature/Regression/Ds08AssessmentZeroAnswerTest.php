<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSubmission;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Regression\Concerns\BuildsAssessmentWorkflow;
use Tests\TestCase;

/**
 * DS-08: a correct answer of "0" must survive validation, storage,
 * accepted-answer normalization, grading and display.
 */
class Ds08AssessmentZeroAnswerTest extends TestCase
{
    use BuildsAssessmentWorkflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootAssessmentWorkflow();
    }

    protected function tearDown(): void
    {
        $this->dropAssessmentWorkflowTables();
        parent::tearDown();
    }

    public static function storedAnswerProvider(): array
    {
        return [
            'zero, fill blank, complete' => ['fill_blank', 'complete_next', '0', '0'],
            'zero, short answer, complete' => ['short_answer', 'complete_next', '0', '0'],
            'zero, fill blank, draft' => ['fill_blank', 'draft', '0', '0'],
            'zero point zero' => ['fill_blank', 'complete_next', '0.0', '0.0'],
            'word zero' => ['short_answer', 'complete_next', 'zero', 'zero'],
            'multiline list with zero first' => ['short_answer', 'complete_next', "0\nzero", "0\nzero"],
            'multiline list with zero last' => ['short_answer', 'complete_next', "zero\n0", "zero\n0"],
        ];
    }

    #[DataProvider('storedAnswerProvider')]
    public function test_authoring_stores_zero_like_correct_answers_verbatim(
        string $type,
        string $intent,
        string $posted,
        string $expected
    ): void {
        [$assessment, $question] = $this->draftAssessmentWithOneItem();

        $this->instructorClient()->patch(
            route('instructor.assessments.questions.update', [$assessment, $question]),
            [
                'intent' => $intent,
                'question_type' => $type,
                'question_text' => 'How many modes does a uniform sample have?',
                'points' => 2,
                'is_required' => 1,
                'correct_answer' => $posted,
            ]
        )->assertRedirect()->assertSessionHasNoErrors();

        $question->refresh();
        $this->assertSame($expected, $question->correct_answer);
        $this->assertSame([], $question->authoringErrors());
        $this->assertSame('complete', $question->authoring_status);
    }

    public function test_zero_answer_survives_update_is_shown_in_the_builder_and_can_be_published(): void
    {
        [$assessment, $question] = $this->draftAssessmentWithOneItem();
        $payload = [
            'intent' => 'complete_next',
            'question_type' => 'fill_blank',
            'question_text' => 'log(1) = ____',
            'points' => 2,
            'is_required' => 1,
            'correct_answer' => 'one',
        ];
        $url = route('instructor.assessments.questions.update', [$assessment, $question]);

        $this->instructorClient()->patch($url, $payload)->assertSessionHasNoErrors();
        $this->assertSame('one', $question->fresh()->correct_answer);

        // Update the existing item so the accepted answer becomes "0".
        $this->instructorClient()
            ->patch($url, array_merge($payload, ['correct_answer' => '0']))
            ->assertSessionHasNoErrors();
        $this->assertSame('0', $question->fresh()->correct_answer);

        $this->instructorClient()
            ->get(route('instructor.assessments.builder', ['assessment' => $assessment, 'item' => 1]))
            ->assertOk()
            ->assertSee('name="correct_answer" placeholder="For short answer, enter one accepted answer per line." >0</textarea>', false);

        $this->instructorClient()
            ->patch(route('instructor.assessments.publish', $assessment))
            ->assertSessionHasNoErrors();
        $this->assertSame('published', $assessment->fresh()->status);
    }

    public static function blankAnswerProvider(): array
    {
        return [
            'empty string' => [''],
            'spaces only' => ['   '],
            'newlines only' => ["\n\r\n"],
            'omitted' => [null],
        ];
    }

    #[DataProvider('blankAnswerProvider')]
    public function test_blank_correct_answer_is_still_rejected_when_completing_an_item(?string $posted): void
    {
        [$assessment, $question] = $this->draftAssessmentWithOneItem();
        $payload = [
            'intent' => 'complete_next',
            'question_type' => 'fill_blank',
            'question_text' => 'Blank answers are not acceptable.',
            'points' => 1,
            'is_required' => 1,
        ];
        if ($posted !== null) {
            $payload['correct_answer'] = $posted;
        }

        $this->instructorClient()
            ->patch(route('instructor.assessments.questions.update', [$assessment, $question]), $payload)
            ->assertSessionHasErrors('correct_answer');

        $this->assertNull($question->fresh()->correct_answer);
        $this->assertSame('unconfigured', $question->fresh()->question_type);
    }

    public function test_blank_correct_answer_in_a_draft_save_is_stored_as_null_and_blocks_publishing(): void
    {
        [$assessment, $question] = $this->draftAssessmentWithOneItem();

        $this->instructorClient()->patch(
            route('instructor.assessments.questions.update', [$assessment, $question]),
            [
                'intent' => 'draft',
                'question_type' => 'short_answer',
                'question_text' => 'Work in progress.',
                'points' => 1,
                'correct_answer' => '',
            ]
        )->assertSessionHasNoErrors();

        $this->assertNull($question->fresh()->correct_answer);

        $this->instructorClient()
            ->patch(route('instructor.assessments.publish', $assessment))
            ->assertSessionHasErrors('assessment');
        $this->assertSame('draft', $assessment->fresh()->status);
    }

    public static function gradingProvider(): array
    {
        return [
            // [accepted list, student answer, expected correct]
            'zero matches zero' => ['0', '0', true],
            'padded zero matches zero' => ['0', ' 0 ', true],
            // Collection::contains() compares loosely, so the app has always
            // treated numeric strings as equal ("5" accepts "5.0"). DS-08 keeps
            // that matching rule unchanged and only stops "0" from being dropped.
            'existing numeric-string rule: 5 accepts 5.0' => ['5', '5.0', true],
            'existing numeric-string rule applies to zero too' => ['0', '0.0', true],
            'zero does not accept a non-zero number' => ['0', '0.5', false],
            'blank is never correct' => ['0', '', false],
            'word zero does not match digit' => ['0', 'zero', false],
            'list: zero' => ["0\nzero", '0', true],
            'list: word, case-insensitive' => ["0\nzero", 'Zero', true],
            'list with blank lines: zero' => ["zero\n\n 0 \n", '0', true],
            'list: other number' => ["0\nzero", '1', false],
            'list: blank' => ["0\nzero", '', false],
            'accepted 0.0 matches 0.0' => ['0.0', '0.0', true],
            'accepted 0.0 accepts 0 under the existing numeric-string rule' => ['0.0', '0', true],
            'accepted 0.0 rejects text' => ['0.0', 'zero', false],
            'ordinary answers keep working' => ["mean\naverage", 'AVERAGE', true],
        ];
    }

    #[DataProvider('gradingProvider')]
    public function test_grading_keeps_zero_in_the_accepted_answer_list(string $accepted, string $studentAnswer, bool $expectedCorrect): void
    {
        [$assessment, $questions] = $this->makeAssessment([], [
            ['question_type' => 'short_answer', 'points' => 4, 'correct_answer' => $accepted],
        ]);
        $submission = $this->startAttempt($assessment);

        $this->studentClient()->post(
            route('student.assessments.submit', [$assessment, $submission]),
            ['answers' => [$questions[0]->id => $studentAnswer]]
        )->assertRedirect(route('student.assessments.result', [$assessment, $submission]));

        $answer = AssessmentAnswer::where('assessment_submission_id', $submission->id)->firstOrFail();
        $this->assertSame($expectedCorrect, $answer->is_correct);
        $this->assertSame($expectedCorrect ? '4.00' : '0.00', $answer->points_awarded);
        $this->assertSame($expectedCorrect ? '4.00' : '0.00', $submission->fresh()->score);
        $this->assertSame('graded', $submission->fresh()->status);
    }

    public function test_required_item_accepts_zero_and_rejects_blank(): void
    {
        [$assessment, $questions] = $this->makeAssessment([], [
            ['question_type' => 'fill_blank', 'points' => 2, 'correct_answer' => '0', 'is_required' => true],
        ]);
        $submission = $this->startAttempt($assessment);
        $url = route('student.assessments.submit', [$assessment, $submission]);

        $this->studentClient()
            ->post($url, ['answers' => [$questions[0]->id => '']])
            ->assertSessionHasErrors('answers.' . $questions[0]->id);
        $this->assertSame('in_progress', $submission->fresh()->status);

        $this->studentClient()
            ->post($url, ['answers' => [$questions[0]->id => '0']])
            ->assertSessionHasNoErrors();
        $this->assertSame('2.00', $submission->fresh()->score);
    }

    public function test_zero_answers_are_displayed_to_student_and_instructor_and_restored_from_a_draft(): void
    {
        [$assessment, $questions] = $this->makeAssessment([], [
            ['question_type' => 'fill_blank', 'points' => 2, 'correct_answer' => '0'],
            ['question_type' => 'essay', 'points' => 3, 'rubric_text' => 'Explain.'],
        ]);
        $submission = $this->startAttempt($assessment);

        $this->studentClient()->postJson(
            route('student.assessments.autosave', [$assessment, $submission]),
            ['client_version' => 1, 'answers' => [$questions[0]->id => '0', $questions[1]->id => '0']]
        )->assertOk()->assertJson(['saved' => true]);

        $this->studentClient()
            ->get(route('student.assessments.take', [$assessment, $submission]))
            ->assertOk()
            ->assertSee('name="answers[' . $questions[0]->id . ']" value="0"', false)
            ->assertSee('placeholder="Write your answer here...">0</textarea>', false);

        $this->studentClient()->post(
            route('student.assessments.submit', [$assessment, $submission]),
            ['answers' => [$questions[0]->id => '0', $questions[1]->id => '0']]
        )->assertSessionHasNoErrors();

        $essay = AssessmentAnswer::where('assessment_question_id', $questions[1]->id)->firstOrFail();
        $this->assertSame('0', $essay->answer_text);
        $this->assertNull($essay->is_correct, 'An essay answered "0" is an answer and must wait for review.');
        $this->assertSame('submitted', $submission->fresh()->status);

        $this->studentClient()
            ->get(route('student.assessments.result', [$assessment, $submission]))
            ->assertOk()
            ->assertSee('<strong>Your answer:</strong> 0</p>', false)
            ->assertDontSeeText('No answer');

        $this->instructorClient()
            ->get(route('instructor.assessments.submissions.show', [$assessment, $submission]))
            ->assertOk()
            ->assertSee('<b>Student answer</b>0</div>', false)
            ->assertDontSeeText('No answer');
    }

    public function test_unanswered_items_still_show_no_answer(): void
    {
        [$assessment, $questions] = $this->makeAssessment([], [
            ['question_type' => 'fill_blank', 'points' => 2, 'correct_answer' => '0'],
        ]);
        $submission = $this->startAttempt($assessment);

        $this->studentClient()->post(
            route('student.assessments.submit', [$assessment, $submission]),
            ['answers' => [$questions[0]->id => '']]
        )->assertSessionHasNoErrors();

        $this->studentClient()
            ->get(route('student.assessments.result', [$assessment, $submission]))
            ->assertOk()
            ->assertSeeText('No answer');
        $this->instructorClient()
            ->get(route('instructor.assessments.submissions.show', [$assessment, $submission]))
            ->assertOk()
            ->assertSeeText('No answer');
    }

    /** @return array{0: Assessment, 1: AssessmentQuestion} */
    private function draftAssessmentWithOneItem(): array
    {
        [$assessment, $questions] = $this->makeAssessment(
            ['status' => 'draft', 'published_at' => null, 'draft_last_item' => 1],
            [[
                'question_type' => 'unconfigured',
                'question_text' => null,
                'points' => 1,
                'is_required' => true,
                'authoring_touched' => false,
            ]]
        );

        return [$assessment, $questions[0]];
    }
}
