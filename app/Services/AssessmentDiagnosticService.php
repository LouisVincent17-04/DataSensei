<?php

namespace App\Services;

use App\Models\AssessmentSubmission;
use App\Models\StudentAssessmentDiagnostic;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssessmentDiagnosticService
{
    public function refresh(AssessmentSubmission $submission): void
    {
        DB::transaction(function () use ($submission): void {
            User::query()->whereKey($submission->student_id)->lockForUpdate()->firstOrFail();

            // A later attempt is the learner's current diagnostic state. Regrading
            // an older attempt must not overwrite results from that newer attempt.
            $latestSubmission = AssessmentSubmission::query()
                ->where('assessment_id', $submission->assessment_id)
                ->where('student_id', $submission->student_id)
                ->whereIn('status', ['submitted', 'late', 'graded'])
                ->orderByDesc('attempt_no')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (! $latestSubmission) {
                return;
            }

            $latestSubmission->load([
                'assessment.questions',
                'answers.question',
            ]);

            $assessment = $latestSubmission->assessment;
            $answersByQuestion = $latestSubmission->answers->keyBy('assessment_question_id');

            $groups = $assessment->questions->groupBy(function ($question) {
                return $question->table_of_specification_row_id
                    ? 'row:' . $question->table_of_specification_row_id
                    : implode('|', [
                        $question->topic_title,
                        $question->learning_objective,
                        $question->bloom_level,
                        $question->difficulty_slug,
                    ]);
            });

            foreach ($groups as $questions) {
                $this->storeGroup($latestSubmission, $questions, $answersByQuestion);
            }
        }, 3);
    }

    private function storeGroup(AssessmentSubmission $submission, Collection $questions, Collection $answersByQuestion): void
    {
        $first = $questions->first();
        $answers = $questions
            ->map(fn ($question) => $answersByQuestion->get($question->id))
            ->filter();

        $answeredCount = $answers->filter(function ($answer) {
            return $answer->selected_option_id !== null
                || trim((string) $answer->answer_text) !== '';
        })->count();

        $manualReviewPending = $answers->contains(function ($answer) {
            return $answer->question
                && $answer->question->question_type === 'essay'
                && $answer->is_correct === null;
        });

        $possible = (float) $questions->sum('points');
        $earned = (float) $answers->sum('points_awarded');
        $mastery = $possible > 0 ? round(($earned / $possible) * 100, 2) : 0;

        $label = $manualReviewPending
            ? 'Pending Review'
            : $this->proficiencyLabel($mastery);

        StudentAssessmentDiagnostic::updateOrCreate(
            [
                'student_id' => $submission->student_id,
                'assessment_id' => $submission->assessment_id,
                'table_of_specification_row_id' => $first->table_of_specification_row_id,
            ],
            [
                'class_id' => $submission->assessment->class_id,
                'ilo_id' => $first->ilo_id,
                'topic_title' => $first->topic_title,
                'subtopic_title' => $first->subtopic_title,
                'learning_objective' => $first->learning_objective,
                'bloom_level' => $first->bloom_level,
                'difficulty_slug' => $first->difficulty_slug,
                'item_count' => $questions->count(),
                'answered_count' => $answeredCount,
                'correct_count' => $answers->where('is_correct', true)->count(),
                'earned_points' => $earned,
                'possible_points' => $possible,
                'mastery_percent' => $mastery,
                'proficiency_label' => $label,
                'manual_review_pending' => $manualReviewPending,
                'calculated_at' => now(),
            ]
        );
    }

    private function proficiencyLabel(float $mastery): string
    {
        return match (true) {
            $mastery >= 85 => 'Mastered',
            $mastery >= 75 => 'Proficient',
            $mastery >= 60 => 'Developing',
            default => 'Needs Support',
        };
    }
}
