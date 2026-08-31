<?php

namespace App\Services;

use App\Models\AssessmentQuestionIlo;
use App\Models\AssessmentSubmission;
use App\Models\AssignmentSubmission;
use App\Models\IntendedLearningOutcome;
use App\Models\StudentIloMastery;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class IloMasteryService
{
    /**
     * Recalculate only ILOs that have an explicit question-to-ILO mapping.
     * Unmapped assignment questions are intentionally excluded: a module match
     * alone is not academically valid evidence that a particular ILO was tested.
     */
    public function refreshForAssignmentSubmission(AssignmentSubmission $submission): void
    {
        $submission->loadMissing('classAssignment.libraryItem.questions');

        $classAssignment = $submission->classAssignment;
        if (! $classAssignment || ! $classAssignment->libraryItem || ! $classAssignment->class_id) {
            return;
        }

        $questionIds = $classAssignment->libraryItem->questions->pluck('id');
        if ($questionIds->isEmpty()) {
            return;
        }

        $iloIds = AssessmentQuestionIlo::query()
            ->where('assessment_source', 'assignment')
            ->whereIn('question_id', $questionIds)
            ->pluck('ilo_id')
            ->unique()
            ->values();

        $this->refreshIlos(
            (int) $submission->student_id,
            (int) $classAssignment->class_id,
            $iloIds,
        );
    }

    /**
     * Manual/TOS assessments carry their academically selected ILO directly on
     * each assessment question, so they can contribute to the same mastery row.
     */
    public function refreshForAssessmentSubmission(AssessmentSubmission $submission): void
    {
        $submission->loadMissing('assessment.questions');

        $assessment = $submission->assessment;
        if (! $assessment || ! $assessment->class_id) {
            return;
        }

        $iloIds = $assessment->questions
            ->pluck('ilo_id')
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        $this->refreshIlos(
            (int) $submission->student_id,
            (int) $assessment->class_id,
            $iloIds,
        );
    }

    private function refreshIlos(int $studentId, int $classId, Collection $iloIds): void
    {
        if ($iloIds->isEmpty()) {
            return;
        }

        $ilos = IntendedLearningOutcome::active()->whereIn('id', $iloIds)->get();
        if ($ilos->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($studentId, $classId, $ilos): void {
            User::query()->whereKey($studentId)->lockForUpdate()->firstOrFail();

            foreach ($ilos as $ilo) {
                $this->recalculateMastery($studentId, $classId, $ilo);
            }
        }, 3);
    }

    private function recalculateMastery(
        int $studentId,
        int $classId,
        IntendedLearningOutcome $ilo,
    ): void {
        $latestAssignmentSubmissionIds = $this->latestAssignmentSubmissionIds($studentId, $classId);
        $latestAssessmentSubmissionIds = $this->latestAssessmentSubmissionIds($studentId, $classId);

        $assignmentEvidence = collect();
        if ($latestAssignmentSubmissionIds->isNotEmpty()) {
            $assignmentEvidence = DB::table('assignment_submission_answers as answer')
                ->join('assignment_questions as question', 'question.id', '=', 'answer.assignment_question_id')
                ->join('assessment_question_ilos as map', function ($join): void {
                    $join->on('map.question_id', '=', 'answer.assignment_question_id')
                        ->where('map.assessment_source', '=', 'assignment');
                })
                ->whereIn('answer.assignment_submission_id', $latestAssignmentSubmissionIds)
                ->where('map.ilo_id', $ilo->id)
                ->get([
                    'answer.points_awarded',
                    'question.points',
                    'map.weight',
                ]);
        }

        $assessmentEvidence = collect();
        if ($latestAssessmentSubmissionIds->isNotEmpty()) {
            $assessmentEvidence = DB::table('assessment_answers as answer')
                ->join('assessment_questions as question', 'question.id', '=', 'answer.assessment_question_id')
                ->whereIn('answer.assessment_submission_id', $latestAssessmentSubmissionIds)
                ->where('question.ilo_id', $ilo->id)
                ->get([
                    'answer.points_awarded',
                    'question.points',
                ]);
        }

        $earned = 0.0;
        $possible = 0.0;

        foreach ($assignmentEvidence as $answer) {
            $weight = max(1, (int) $answer->weight);
            $earned += max(0, (float) $answer->points_awarded) * $weight;
            $possible += max(0, (float) $answer->points) * $weight;
        }

        foreach ($assessmentEvidence as $answer) {
            $earned += max(0, (float) $answer->points_awarded);
            $possible += max(0, (float) $answer->points);
        }

        $evidenceCount = $assignmentEvidence->count() + $assessmentEvidence->count();
        $percent = $possible > 0
            ? round(min(100, ($earned / $possible) * 100), 2)
            : 0.0;
        $threshold = max(1, (int) $ilo->mastery_threshold);
        $status = $evidenceCount < 1 || $possible <= 0
            ? 'not_started'
            : ($percent >= $threshold ? 'mastered' : 'developing');

        StudentIloMastery::updateOrCreate([
            'student_id' => $studentId,
            'class_id' => $classId,
            'ilo_id' => $ilo->id,
        ], [
            'mastery_percent' => $percent,
            'evidence_count' => $evidenceCount,
            'status' => $status,
            'last_evaluated_at' => now(),
        ]);
    }

    /**
     * MySQL 5.5 has no window functions, so latest-attempt selection is kept in
     * application code instead of ROW_NUMBER()/CTEs.
     */
    private function latestAssignmentSubmissionIds(int $studentId, int $classId): Collection
    {
        return DB::table('assignment_submissions as submission')
            ->join('class_assignments as class_assignment', 'class_assignment.id', '=', 'submission.class_assignment_id')
            ->where('submission.student_id', $studentId)
            ->where('class_assignment.class_id', $classId)
            ->whereIn('submission.status', ['submitted', 'late', 'graded'])
            ->get(['submission.id', 'submission.class_assignment_id', 'submission.attempt_no'])
            ->groupBy('class_assignment_id')
            ->map(fn (Collection $rows) => $rows
                ->sortByDesc(fn ($row): string => str_pad((string) $row->attempt_no, 10, '0', STR_PAD_LEFT)
                    . ':' . str_pad((string) $row->id, 20, '0', STR_PAD_LEFT))
                ->first()->id)
            ->values();
    }

    private function latestAssessmentSubmissionIds(int $studentId, int $classId): Collection
    {
        return DB::table('assessment_submissions as submission')
            ->join('assessments as assessment', 'assessment.id', '=', 'submission.assessment_id')
            ->where('submission.student_id', $studentId)
            ->where('assessment.class_id', $classId)
            ->whereIn('submission.status', ['submitted', 'late', 'graded'])
            ->whereNotNull('submission.graded_at')
            ->get(['submission.id', 'submission.assessment_id', 'submission.attempt_no'])
            ->groupBy('assessment_id')
            ->map(fn (Collection $rows) => $rows
                ->sortByDesc(fn ($row): string => str_pad((string) $row->attempt_no, 10, '0', STR_PAD_LEFT)
                    . ':' . str_pad((string) $row->id, 20, '0', STR_PAD_LEFT))
                ->first()->id)
            ->values();
    }
}
