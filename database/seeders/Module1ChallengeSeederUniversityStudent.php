<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module1ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (! $category) {
            $this->command->error('University Student category not found. Run ChallengeCategorySeeder first.');
            return;
        }

        $title = 'Basics of Python Programming';

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', $title)
            ->where('is_coding_challenge', 0)
            ->delete();

        $this->command->info('Creating Module 1 — Basics of Python Programming (University Student) [MCQ]...');

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'A detailed 50-item University Student MCQ challenge for Basics of Python Programming. Items include concepts, scenarios, debugging, interpretation, and decision-making.',
            'time_limit_seconds' => 1500,
            'base_xp' => 650,
            'order_index' => 1,
            'is_coding_challenge' => 0,
        ]);

        $qaData = [
            [
                'q' => 'Item 1: In Basics of Python Programming, which action best supports academic application and small scenario analysis when starting a new task involving problem framing?',
                'opts' => [
                    ['text' => 'Define the goal, inputs, assumptions, and expected output before choosing a method', 'correct' => true],
                    ['text' => 'Choose the most complex tool immediately', 'correct' => false],
                    ['text' => 'Ignore the data context and focus only on the final number', 'correct' => false],
                    ['text' => 'Skip checking because the topic name already explains the answer', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 2: A learner working on Basics of Python Programming gets a result that looks correct. What should they do next at the University Student level?',
                'opts' => [
                    ['text' => 'Submit immediately without checking', 'correct' => false],
                    ['text' => 'Validate the result with examples, assumptions, and possible edge cases', 'correct' => true],
                    ['text' => 'Change the result until it looks impressive', 'correct' => false],
                    ['text' => 'Remove notes to make the work shorter', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 3: Which mistake most commonly weakens work in Python Fundamentals when dealing with assumptions?',
                'opts' => [
                    ['text' => 'Writing down the problem statement', 'correct' => false],
                    ['text' => 'Comparing output with expected behavior', 'correct' => false],
                    ['text' => 'Making assumptions invisible and failing to test them', 'correct' => true],
                    ['text' => 'Explaining limitations clearly', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 4: For Basics of Python Programming, why is interpretation important after computation or analysis?',
                'opts' => [
                    ['text' => 'It replaces the need for correct computation', 'correct' => false],
                    ['text' => 'It guarantees the method has no limitations', 'correct' => false],
                    ['text' => 'It makes all datasets equivalent', 'correct' => false],
                    ['text' => 'It connects the result to the original question and supports a decision', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 5: Which response shows the best University Student practice when a method in Python Fundamentals fails on one test case?',
                'opts' => [
                    ['text' => 'Inspect the failing input, trace the logic, and update the method without breaking passing cases', 'correct' => true],
                    ['text' => 'Delete the failing case', 'correct' => false],
                    ['text' => 'Change the expected answer to match the wrong output', 'correct' => false],
                    ['text' => 'Assume the software is always wrong', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 6: What is the best reason to keep notes or comments while solving Basics of Python Programming tasks?',
                'opts' => [
                    ['text' => 'They slow down every program intentionally', 'correct' => false],
                    ['text' => 'They make assumptions, decisions, and limitations easier to review later', 'correct' => true],
                    ['text' => 'They replace testing', 'correct' => false],
                    ['text' => 'They hide incorrect reasoning', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 7: In a Python Fundamentals assessment, which evidence best shows mastery beyond memorization?',
                'opts' => [
                    ['text' => 'Repeating a definition without context', 'correct' => false],
                    ['text' => 'Choosing the longest answer every time', 'correct' => false],
                    ['text' => 'Correctly applying the concept to a new scenario and explaining why it works', 'correct' => true],
                    ['text' => 'Avoiding examples', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 8: Which situation is most likely an edge case in Basics of Python Programming?',
                'opts' => [
                    ['text' => 'A normal example copied from the instruction only', 'correct' => false],
                    ['text' => 'A chart title', 'correct' => false],
                    ['text' => 'A file name that is easy to read', 'correct' => false],
                    ['text' => 'A boundary, missing, zero, repeated, extreme, or unexpected input that can change behavior', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 9: When comparing two approaches in Python Fundamentals, what should a University Student learner prioritize?',
                'opts' => [
                    ['text' => 'Accuracy, assumptions, interpretability, cost, and fitness to the problem', 'correct' => true],
                    ['text' => 'Whichever approach has the fanciest name', 'correct' => false],
                    ['text' => 'Only the approach used first in class', 'correct' => false],
                    ['text' => 'The one that avoids all documentation', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 10: What does a strong final answer in Basics of Python Programming include?',
                'opts' => [
                    ['text' => 'Only a screenshot', 'correct' => false],
                    ['text' => 'A clear result, method summary, evidence, limitations, and next step', 'correct' => true],
                    ['text' => 'Only raw code without context', 'correct' => false],
                    ['text' => 'Only a claim that it works', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 11: In Basics of Python Programming, which action best supports academic application and small scenario analysis when starting a new task involving reproducibility?',
                'opts' => [
                    ['text' => 'Choose the most complex tool immediately', 'correct' => false],
                    ['text' => 'Ignore the data context and focus only on the final number', 'correct' => false],
                    ['text' => 'Define the goal, inputs, assumptions, and expected output before choosing a method', 'correct' => true],
                    ['text' => 'Skip checking because the topic name already explains the answer', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 12: A learner working on Basics of Python Programming gets a result that looks correct. What should they do next at the University Student level?',
                'opts' => [
                    ['text' => 'Submit immediately without checking', 'correct' => false],
                    ['text' => 'Change the result until it looks impressive', 'correct' => false],
                    ['text' => 'Remove notes to make the work shorter', 'correct' => false],
                    ['text' => 'Validate the result with examples, assumptions, and possible edge cases', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 13: Which mistake most commonly weakens work in Python Fundamentals when dealing with bias check?',
                'opts' => [
                    ['text' => 'Making assumptions invisible and failing to test them', 'correct' => true],
                    ['text' => 'Writing down the problem statement', 'correct' => false],
                    ['text' => 'Comparing output with expected behavior', 'correct' => false],
                    ['text' => 'Explaining limitations clearly', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 14: For Basics of Python Programming, why is interpretation important after computation or analysis?',
                'opts' => [
                    ['text' => 'It replaces the need for correct computation', 'correct' => false],
                    ['text' => 'It connects the result to the original question and supports a decision', 'correct' => true],
                    ['text' => 'It guarantees the method has no limitations', 'correct' => false],
                    ['text' => 'It makes all datasets equivalent', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 15: Which response shows the best University Student practice when a method in Python Fundamentals fails on one test case?',
                'opts' => [
                    ['text' => 'Delete the failing case', 'correct' => false],
                    ['text' => 'Change the expected answer to match the wrong output', 'correct' => false],
                    ['text' => 'Inspect the failing input, trace the logic, and update the method without breaking passing cases', 'correct' => true],
                    ['text' => 'Assume the software is always wrong', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 16: What is the best reason to keep notes or comments while solving Basics of Python Programming tasks?',
                'opts' => [
                    ['text' => 'They slow down every program intentionally', 'correct' => false],
                    ['text' => 'They replace testing', 'correct' => false],
                    ['text' => 'They hide incorrect reasoning', 'correct' => false],
                    ['text' => 'They make assumptions, decisions, and limitations easier to review later', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 17: In a Python Fundamentals assessment, which evidence best shows mastery beyond memorization?',
                'opts' => [
                    ['text' => 'Correctly applying the concept to a new scenario and explaining why it works', 'correct' => true],
                    ['text' => 'Repeating a definition without context', 'correct' => false],
                    ['text' => 'Choosing the longest answer every time', 'correct' => false],
                    ['text' => 'Avoiding examples', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 18: Which situation is most likely an edge case in Basics of Python Programming?',
                'opts' => [
                    ['text' => 'A normal example copied from the instruction only', 'correct' => false],
                    ['text' => 'A boundary, missing, zero, repeated, extreme, or unexpected input that can change behavior', 'correct' => true],
                    ['text' => 'A chart title', 'correct' => false],
                    ['text' => 'A file name that is easy to read', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 19: When comparing two approaches in Python Fundamentals, what should a University Student learner prioritize?',
                'opts' => [
                    ['text' => 'Whichever approach has the fanciest name', 'correct' => false],
                    ['text' => 'Only the approach used first in class', 'correct' => false],
                    ['text' => 'Accuracy, assumptions, interpretability, cost, and fitness to the problem', 'correct' => true],
                    ['text' => 'The one that avoids all documentation', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 20: What does a strong final answer in Basics of Python Programming include?',
                'opts' => [
                    ['text' => 'Only a screenshot', 'correct' => false],
                    ['text' => 'Only raw code without context', 'correct' => false],
                    ['text' => 'Only a claim that it works', 'correct' => false],
                    ['text' => 'A clear result, method summary, evidence, limitations, and next step', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 21: In Basics of Python Programming, which action best supports academic application and small scenario analysis when starting a new task involving problem framing?',
                'opts' => [
                    ['text' => 'Define the goal, inputs, assumptions, and expected output before choosing a method', 'correct' => true],
                    ['text' => 'Choose the most complex tool immediately', 'correct' => false],
                    ['text' => 'Ignore the data context and focus only on the final number', 'correct' => false],
                    ['text' => 'Skip checking because the topic name already explains the answer', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 22: A learner working on Basics of Python Programming gets a result that looks correct. What should they do next at the University Student level?',
                'opts' => [
                    ['text' => 'Submit immediately without checking', 'correct' => false],
                    ['text' => 'Validate the result with examples, assumptions, and possible edge cases', 'correct' => true],
                    ['text' => 'Change the result until it looks impressive', 'correct' => false],
                    ['text' => 'Remove notes to make the work shorter', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 23: Which mistake most commonly weakens work in Python Fundamentals when dealing with assumptions?',
                'opts' => [
                    ['text' => 'Writing down the problem statement', 'correct' => false],
                    ['text' => 'Comparing output with expected behavior', 'correct' => false],
                    ['text' => 'Making assumptions invisible and failing to test them', 'correct' => true],
                    ['text' => 'Explaining limitations clearly', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 24: For Basics of Python Programming, why is interpretation important after computation or analysis?',
                'opts' => [
                    ['text' => 'It replaces the need for correct computation', 'correct' => false],
                    ['text' => 'It guarantees the method has no limitations', 'correct' => false],
                    ['text' => 'It makes all datasets equivalent', 'correct' => false],
                    ['text' => 'It connects the result to the original question and supports a decision', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 25: Which response shows the best University Student practice when a method in Python Fundamentals fails on one test case?',
                'opts' => [
                    ['text' => 'Inspect the failing input, trace the logic, and update the method without breaking passing cases', 'correct' => true],
                    ['text' => 'Delete the failing case', 'correct' => false],
                    ['text' => 'Change the expected answer to match the wrong output', 'correct' => false],
                    ['text' => 'Assume the software is always wrong', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 26: What is the best reason to keep notes or comments while solving Basics of Python Programming tasks?',
                'opts' => [
                    ['text' => 'They slow down every program intentionally', 'correct' => false],
                    ['text' => 'They make assumptions, decisions, and limitations easier to review later', 'correct' => true],
                    ['text' => 'They replace testing', 'correct' => false],
                    ['text' => 'They hide incorrect reasoning', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 27: In a Python Fundamentals assessment, which evidence best shows mastery beyond memorization?',
                'opts' => [
                    ['text' => 'Repeating a definition without context', 'correct' => false],
                    ['text' => 'Choosing the longest answer every time', 'correct' => false],
                    ['text' => 'Correctly applying the concept to a new scenario and explaining why it works', 'correct' => true],
                    ['text' => 'Avoiding examples', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 28: Which situation is most likely an edge case in Basics of Python Programming?',
                'opts' => [
                    ['text' => 'A normal example copied from the instruction only', 'correct' => false],
                    ['text' => 'A chart title', 'correct' => false],
                    ['text' => 'A file name that is easy to read', 'correct' => false],
                    ['text' => 'A boundary, missing, zero, repeated, extreme, or unexpected input that can change behavior', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 29: When comparing two approaches in Python Fundamentals, what should a University Student learner prioritize?',
                'opts' => [
                    ['text' => 'Accuracy, assumptions, interpretability, cost, and fitness to the problem', 'correct' => true],
                    ['text' => 'Whichever approach has the fanciest name', 'correct' => false],
                    ['text' => 'Only the approach used first in class', 'correct' => false],
                    ['text' => 'The one that avoids all documentation', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 30: What does a strong final answer in Basics of Python Programming include?',
                'opts' => [
                    ['text' => 'Only a screenshot', 'correct' => false],
                    ['text' => 'A clear result, method summary, evidence, limitations, and next step', 'correct' => true],
                    ['text' => 'Only raw code without context', 'correct' => false],
                    ['text' => 'Only a claim that it works', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 31: In Basics of Python Programming, which action best supports academic application and small scenario analysis when starting a new task involving reproducibility?',
                'opts' => [
                    ['text' => 'Choose the most complex tool immediately', 'correct' => false],
                    ['text' => 'Ignore the data context and focus only on the final number', 'correct' => false],
                    ['text' => 'Define the goal, inputs, assumptions, and expected output before choosing a method', 'correct' => true],
                    ['text' => 'Skip checking because the topic name already explains the answer', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 32: A learner working on Basics of Python Programming gets a result that looks correct. What should they do next at the University Student level?',
                'opts' => [
                    ['text' => 'Submit immediately without checking', 'correct' => false],
                    ['text' => 'Change the result until it looks impressive', 'correct' => false],
                    ['text' => 'Remove notes to make the work shorter', 'correct' => false],
                    ['text' => 'Validate the result with examples, assumptions, and possible edge cases', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 33: Which mistake most commonly weakens work in Python Fundamentals when dealing with bias check?',
                'opts' => [
                    ['text' => 'Making assumptions invisible and failing to test them', 'correct' => true],
                    ['text' => 'Writing down the problem statement', 'correct' => false],
                    ['text' => 'Comparing output with expected behavior', 'correct' => false],
                    ['text' => 'Explaining limitations clearly', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 34: For Basics of Python Programming, why is interpretation important after computation or analysis?',
                'opts' => [
                    ['text' => 'It replaces the need for correct computation', 'correct' => false],
                    ['text' => 'It connects the result to the original question and supports a decision', 'correct' => true],
                    ['text' => 'It guarantees the method has no limitations', 'correct' => false],
                    ['text' => 'It makes all datasets equivalent', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 35: Which response shows the best University Student practice when a method in Python Fundamentals fails on one test case?',
                'opts' => [
                    ['text' => 'Delete the failing case', 'correct' => false],
                    ['text' => 'Change the expected answer to match the wrong output', 'correct' => false],
                    ['text' => 'Inspect the failing input, trace the logic, and update the method without breaking passing cases', 'correct' => true],
                    ['text' => 'Assume the software is always wrong', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 36: What is the best reason to keep notes or comments while solving Basics of Python Programming tasks?',
                'opts' => [
                    ['text' => 'They slow down every program intentionally', 'correct' => false],
                    ['text' => 'They replace testing', 'correct' => false],
                    ['text' => 'They hide incorrect reasoning', 'correct' => false],
                    ['text' => 'They make assumptions, decisions, and limitations easier to review later', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 37: In a Python Fundamentals assessment, which evidence best shows mastery beyond memorization?',
                'opts' => [
                    ['text' => 'Correctly applying the concept to a new scenario and explaining why it works', 'correct' => true],
                    ['text' => 'Repeating a definition without context', 'correct' => false],
                    ['text' => 'Choosing the longest answer every time', 'correct' => false],
                    ['text' => 'Avoiding examples', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 38: Which situation is most likely an edge case in Basics of Python Programming?',
                'opts' => [
                    ['text' => 'A normal example copied from the instruction only', 'correct' => false],
                    ['text' => 'A boundary, missing, zero, repeated, extreme, or unexpected input that can change behavior', 'correct' => true],
                    ['text' => 'A chart title', 'correct' => false],
                    ['text' => 'A file name that is easy to read', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 39: When comparing two approaches in Python Fundamentals, what should a University Student learner prioritize?',
                'opts' => [
                    ['text' => 'Whichever approach has the fanciest name', 'correct' => false],
                    ['text' => 'Only the approach used first in class', 'correct' => false],
                    ['text' => 'Accuracy, assumptions, interpretability, cost, and fitness to the problem', 'correct' => true],
                    ['text' => 'The one that avoids all documentation', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 40: What does a strong final answer in Basics of Python Programming include?',
                'opts' => [
                    ['text' => 'Only a screenshot', 'correct' => false],
                    ['text' => 'Only raw code without context', 'correct' => false],
                    ['text' => 'Only a claim that it works', 'correct' => false],
                    ['text' => 'A clear result, method summary, evidence, limitations, and next step', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 41: In Basics of Python Programming, which action best supports academic application and small scenario analysis when starting a new task involving problem framing?',
                'opts' => [
                    ['text' => 'Define the goal, inputs, assumptions, and expected output before choosing a method', 'correct' => true],
                    ['text' => 'Choose the most complex tool immediately', 'correct' => false],
                    ['text' => 'Ignore the data context and focus only on the final number', 'correct' => false],
                    ['text' => 'Skip checking because the topic name already explains the answer', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 42: A learner working on Basics of Python Programming gets a result that looks correct. What should they do next at the University Student level?',
                'opts' => [
                    ['text' => 'Submit immediately without checking', 'correct' => false],
                    ['text' => 'Validate the result with examples, assumptions, and possible edge cases', 'correct' => true],
                    ['text' => 'Change the result until it looks impressive', 'correct' => false],
                    ['text' => 'Remove notes to make the work shorter', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 43: Which mistake most commonly weakens work in Python Fundamentals when dealing with assumptions?',
                'opts' => [
                    ['text' => 'Writing down the problem statement', 'correct' => false],
                    ['text' => 'Comparing output with expected behavior', 'correct' => false],
                    ['text' => 'Making assumptions invisible and failing to test them', 'correct' => true],
                    ['text' => 'Explaining limitations clearly', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 44: For Basics of Python Programming, why is interpretation important after computation or analysis?',
                'opts' => [
                    ['text' => 'It replaces the need for correct computation', 'correct' => false],
                    ['text' => 'It guarantees the method has no limitations', 'correct' => false],
                    ['text' => 'It makes all datasets equivalent', 'correct' => false],
                    ['text' => 'It connects the result to the original question and supports a decision', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 45: Which response shows the best University Student practice when a method in Python Fundamentals fails on one test case?',
                'opts' => [
                    ['text' => 'Inspect the failing input, trace the logic, and update the method without breaking passing cases', 'correct' => true],
                    ['text' => 'Delete the failing case', 'correct' => false],
                    ['text' => 'Change the expected answer to match the wrong output', 'correct' => false],
                    ['text' => 'Assume the software is always wrong', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 46: What is the best reason to keep notes or comments while solving Basics of Python Programming tasks?',
                'opts' => [
                    ['text' => 'They slow down every program intentionally', 'correct' => false],
                    ['text' => 'They make assumptions, decisions, and limitations easier to review later', 'correct' => true],
                    ['text' => 'They replace testing', 'correct' => false],
                    ['text' => 'They hide incorrect reasoning', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 47: In a Python Fundamentals assessment, which evidence best shows mastery beyond memorization?',
                'opts' => [
                    ['text' => 'Repeating a definition without context', 'correct' => false],
                    ['text' => 'Choosing the longest answer every time', 'correct' => false],
                    ['text' => 'Correctly applying the concept to a new scenario and explaining why it works', 'correct' => true],
                    ['text' => 'Avoiding examples', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 48: Which situation is most likely an edge case in Basics of Python Programming?',
                'opts' => [
                    ['text' => 'A normal example copied from the instruction only', 'correct' => false],
                    ['text' => 'A chart title', 'correct' => false],
                    ['text' => 'A file name that is easy to read', 'correct' => false],
                    ['text' => 'A boundary, missing, zero, repeated, extreme, or unexpected input that can change behavior', 'correct' => true],
                ],
            ],
            [
                'q' => 'Item 49: When comparing two approaches in Python Fundamentals, what should a University Student learner prioritize?',
                'opts' => [
                    ['text' => 'Accuracy, assumptions, interpretability, cost, and fitness to the problem', 'correct' => true],
                    ['text' => 'Whichever approach has the fanciest name', 'correct' => false],
                    ['text' => 'Only the approach used first in class', 'correct' => false],
                    ['text' => 'The one that avoids all documentation', 'correct' => false],
                ],
            ],
            [
                'q' => 'Item 50: What does a strong final answer in Basics of Python Programming include?',
                'opts' => [
                    ['text' => 'Only a screenshot', 'correct' => false],
                    ['text' => 'A clear result, method summary, evidence, limitations, and next step', 'correct' => true],
                    ['text' => 'Only raw code without context', 'correct' => false],
                    ['text' => 'Only a claim that it works', 'correct' => false],
                ],
            ],
        ];

        foreach ($qaData as $item) {
            $question = ChallengeQuestion::create([
                'challenge_id' => $challenge->id,
                'challenge_category_id' => $category->id,
                'question_text' => $item['q'],
            ]);

            $correctCount = 0;
            foreach ($item['opts'] as $option) {
                if ($option['correct']) {
                    $correctCount++;
                }

                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text' => $option['text'],
                    'is_correct' => $option['correct'],
                ]);
            }

            if ($correctCount !== 1) {
                throw new \RuntimeException('Each MCQ item must have exactly one correct answer. Failed question: ' . $item['q']);
            }
        }

        $this->command->info('Module 1 MCQ (University Student) seeded — 1 challenge, 50 questions, 200 options.');
    }
}
