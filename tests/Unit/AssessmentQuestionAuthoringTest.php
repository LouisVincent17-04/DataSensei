<?php

namespace Tests\Unit;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class AssessmentQuestionAuthoringTest extends TestCase
{
    public function test_an_empty_tos_item_is_not_started_and_can_hold_a_partial_draft(): void
    {
        $question = $this->question([
            'question_type' => 'unconfigured',
            'question_text' => null,
        ]);

        $this->assertSame('not_started', $question->authoring_status);
        $this->assertFalse($question->isAuthoringComplete());

        $question->question_type = 'multiple_choice';
        $question->question_text = 'Partially written question';

        $this->assertSame('in_progress', $question->authoring_status);
        $this->assertFalse($question->isAuthoringComplete());
    }

    public function test_multiple_choice_is_complete_only_with_two_choices_and_one_correct_answer(): void
    {
        $question = $this->question([
            'question_type' => 'multiple_choice',
            'question_text' => 'Which value is the mean of 2, 4, and 6?',
        ], [
            ['option_text' => '3', 'is_correct' => false],
            ['option_text' => '4', 'is_correct' => true],
            ['option_text' => '6', 'is_correct' => false],
        ]);

        $this->assertTrue($question->isAuthoringComplete());
        $this->assertSame('complete', $question->authoring_status);

        $question->setRelation('options', new Collection([
            new AssessmentQuestionOption(['option_text' => '4', 'is_correct' => false]),
        ]));

        $this->assertFalse($question->isAuthoringComplete());
        $this->assertContains('Add at least two choices.', $question->authoringErrors());
    }

    public function test_true_false_and_essay_require_their_type_specific_answers(): void
    {
        $trueFalse = $this->question([
            'question_type' => 'true_false',
            'question_text' => 'The median is resistant to extreme values.',
            'correct_answer' => 'True',
        ]);
        $essay = $this->question([
            'question_type' => 'essay',
            'question_text' => 'Explain when a median is preferred over a mean.',
            'rubric_text' => '2 points for identifying skew; 3 points for a valid example.',
        ]);

        $this->assertTrue($trueFalse->isAuthoringComplete());
        $this->assertTrue($essay->isAuthoringComplete());

        $trueFalse->correct_answer = 'Maybe';
        $essay->rubric_text = null;

        $this->assertFalse($trueFalse->isAuthoringComplete());
        $this->assertFalse($essay->isAuthoringComplete());
    }

    /** @param array<int, array<string, mixed>> $options */
    private function question(array $attributes, array $options = []): AssessmentQuestion
    {
        $question = new AssessmentQuestion(array_merge([
            'question_type' => 'unconfigured',
            'question_text' => null,
            'points' => 1,
            'is_required' => true,
        ], $attributes));

        $question->setRelation('options', new Collection(array_map(
            fn (array $option) => new AssessmentQuestionOption($option),
            $options
        )));

        return $question;
    }
}
