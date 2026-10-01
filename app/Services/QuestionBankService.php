<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\QuestionBankItem;
use Illuminate\Support\Collection;

/**
 * Copies Question Bank questions into assessments, and saves assessment
 * questions back into the bank (DataSensei Updates 11). Every copy is a
 * snapshot: the assessment keeps its own question rows, so later bank edits
 * or archiving never change a published assessment, an attempt, or a grade.
 */
class QuestionBankService
{
    /**
     * Copy the given bank questions into a draft assessment.
     *
     * A TOS-planned assessment is filled slot by slot: each copy completes
     * the first missing planned item it actually fits (see fitsSlot()) and
     * keeps that item's planned points, topic and thinking level. A question
     * that fits no missing item is not added, so the plan is never reported
     * as covered when it is not. An assessment without a TOS grows by one
     * item per question, using the bank question's own points.
     *
     * @param Collection<int, QuestionBankItem> $items
     * @return array{added: int, skipped: int, skipped_full: int, skipped_mismatch: int}
     */
    public function copyIntoAssessment(Assessment $assessment, Collection $items): array
    {
        $usesTos = $assessment->table_of_specification_id !== null;
        $tosModuleNo = $usesTos ? $this->tosModuleNo($assessment) : null;
        $added = 0;
        $skippedFull = 0;
        $skippedMismatch = 0;

        foreach ($items as $item) {
            $item->loadMissing('options');

            if ($usesTos) {
                $missing = $assessment->questions()
                    ->where('question_type', 'unconfigured')
                    ->orderBy('item_number')
                    ->get();

                if ($missing->isEmpty()) {
                    // Every planned TOS item is already filled; adding more
                    // would break the plan's question count.
                    $skippedFull++;

                    continue;
                }

                $slot = $missing->first(fn (AssessmentQuestion $candidate): bool => $this->fitsSlot($item, $candidate, $tosModuleNo));
                if ($slot === null) {
                    // Filling a planned item with a question from another
                    // module or thinking level would misreport the plan.
                    $skippedMismatch++;

                    continue;
                }

                $slot->update([
                    'question_type' => $item->question_type,
                    'question_text' => $item->question_text,
                    'correct_answer' => $item->correct_answer,
                    'answer_explanation' => $item->answer_explanation,
                    'rubric_text' => $item->question_type === 'essay' ? $item->rubric_text : null,
                    'authoring_touched' => true,
                ]);
                $question = $slot;
            } else {
                $question = AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'table_of_specification_row_id' => null,
                    // The bank's ILO is only an organizing tag. Assessment
                    // work stays unlinked from ILOs (DataSensei Updates 5).
                    'ilo_id' => null,
                    'item_number' => (int) $assessment->questions()->max('item_number') + 1,
                    'question_type' => $item->question_type,
                    'question_text' => $item->question_text,
                    'points' => max(1, (int) $item->points),
                    'is_required' => true,
                    'authoring_touched' => true,
                    'correct_answer' => $item->correct_answer,
                    'answer_explanation' => $item->answer_explanation,
                    'rubric_text' => $item->question_type === 'essay' ? $item->rubric_text : null,
                    'topic_title' => mb_substr((string) ($item->topic_title ?: $assessment->title), 0, 189),
                    'bloom_level' => $item->bloom_level,
                    'difficulty_slug' => $item->difficulty_slug,
                ]);
            }

            $question->options()->delete();
            if ($item->question_type === 'multiple_choice') {
                foreach ($item->options->values() as $index => $option) {
                    AssessmentQuestionOption::create([
                        'assessment_question_id' => $question->id,
                        'option_label' => chr(65 + min(25, (int) $index)),
                        'option_text' => $option->option_text,
                        'is_correct' => (bool) $option->is_correct,
                        'order_index' => $index + 1,
                    ]);
                }
            }

            $added++;
        }

        $this->refreshTotals($assessment);

        return [
            'added' => $added,
            'skipped' => $skippedFull + $skippedMismatch,
            'skipped_full' => $skippedFull,
            'skipped_mismatch' => $skippedMismatch,
        ];
    }

    /**
     * Whether a bank question can fill a planned TOS item without
     * misrepresenting the plan. A TOS plans each item by module, topic and
     * thinking level (it carries no ILO since DataSensei Updates 5), so:
     *  - a question tagged with another module never fits;
     *  - when the item plans a thinking level, the question must be tagged
     *    with that same level. An untagged question cannot prove it, so it
     *    does not fit; the instructor can tag it or write the item by hand.
     */
    public function fitsSlot(QuestionBankItem $item, AssessmentQuestion $slot, ?int $tosModuleNo): bool
    {
        if ($tosModuleNo !== null && $item->module_no !== null && (int) $item->module_no !== $tosModuleNo) {
            return false;
        }

        $planned = mb_strtolower(trim((string) $slot->bloom_level));
        if ($planned !== '' && mb_strtolower(trim((string) $item->bloom_level)) !== $planned) {
            return false;
        }

        return true;
    }

    /**
     * For each bank question, the first missing planned item it would fill
     * (null when it fits none), in the order copyIntoAssessment() uses.
     *
     * @param iterable<QuestionBankItem> $items
     * @param Collection<int, AssessmentQuestion> $missingSlots
     * @return array<int, AssessmentQuestion|null> keyed by bank question id
     */
    public function fittingSlots(Assessment $assessment, iterable $items, Collection $missingSlots): array
    {
        $tosModuleNo = $this->tosModuleNo($assessment);
        $fits = [];
        foreach ($items as $item) {
            $fits[(int) $item->id] = $missingSlots->first(fn (AssessmentQuestion $slot): bool => $this->fitsSlot($item, $slot, $tosModuleNo));
        }

        return $fits;
    }

    public function tosModuleNo(Assessment $assessment): ?int
    {
        $assessment->loadMissing('tos');
        $moduleNo = $assessment->tos?->module_no;

        return $moduleNo !== null ? (int) $moduleNo : null;
    }

    /**
     * Save a complete assessment question as a new bank question owned by
     * the instructor. Incomplete questions are not banked.
     */
    public function saveSnapshotFromAssessmentQuestion(AssessmentQuestion $question, int $instructorId, ?int $moduleNo = null): ?QuestionBankItem
    {
        $question->loadMissing('options');
        if ($question->authoringErrors() !== []) {
            return null;
        }

        $item = QuestionBankItem::create([
            'instructor_id' => $instructorId,
            'module_no' => $moduleNo,
            'topic_title' => $question->topic_title,
            'question_type' => $question->question_type,
            'question_text' => $question->question_text,
            'correct_answer' => $question->correct_answer,
            'answer_explanation' => $question->answer_explanation,
            'rubric_text' => $question->rubric_text,
            'points' => max(1, (int) $question->points),
            'difficulty_slug' => $question->difficulty_slug,
            'ilo_id' => $question->ilo_id,
            'bloom_level' => $question->bloom_level,
            'is_archived' => false,
        ]);

        foreach ($question->options->values() as $index => $option) {
            $item->options()->create([
                'option_text' => $option->option_text,
                'is_correct' => (bool) $option->is_correct,
                'order_index' => $index + 1,
            ]);
        }

        return $item;
    }

    private function refreshTotals(Assessment $assessment): void
    {
        $questions = $assessment->questions()->get();
        $assessment->update([
            'total_items' => $questions->count(),
            'total_points' => (int) $questions->sum('points'),
            'draft_saved_at' => now(),
        ]);
    }
}
