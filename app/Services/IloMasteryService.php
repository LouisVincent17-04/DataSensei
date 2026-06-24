<?php

namespace App\Services;

use App\Models\AssessmentQuestionIlo;
use App\Models\AssignmentSubmission;
use App\Models\IntendedLearningOutcome;
use App\Models\StudentIloMastery;

class IloMasteryService
{
    public function refreshForAssignmentSubmission(AssignmentSubmission $submission): void
    {
        $submission->loadMissing([
            'classAssignment.libraryItem.questions',
            'answers.question',
        ]);

        $classAssignment = $submission->classAssignment;
        if (!$classAssignment || !$classAssignment->libraryItem) {
            return;
        }

        $questionIds = $submission->answers->pluck('assignment_question_id')->filter()->values();
        if ($questionIds->isEmpty()) {
            return;
        }

        $maps = AssessmentQuestionIlo::with('ilo')
            ->where('assessment_source', 'assignment')
            ->whereIn('question_id', $questionIds)
            ->get()
            ->groupBy('ilo_id');

        // If questions were not mapped yet, assign all evidence to module-level ILOs.
        if ($maps->isEmpty()) {
            $ilos = IntendedLearningOutcome::active()
                ->where('module_no', (int) $classAssignment->libraryItem->module_no)
                ->get();

            if ($ilos->isEmpty()) {
                return;
            }

            foreach ($ilos as $ilo) {
                $this->updateMasteryForIlo($submission, $ilo->id, $questionIds->all());
            }

            return;
        }

        foreach ($maps as $iloId => $iloMaps) {
            $mappedQuestionIds = $iloMaps->pluck('question_id')->unique()->values()->all();
            $this->updateMasteryForIlo($submission, (int) $iloId, $mappedQuestionIds);
        }
    }

    private function updateMasteryForIlo(AssignmentSubmission $submission, int $iloId, array $questionIds): void
    {
        $answers = $submission->answers()
            ->whereIn('assignment_question_id', $questionIds)
            ->get();

        if ($answers->isEmpty()) {
            return;
        }

        $total = max(1, $answers->count());
        $correct = $answers->where('is_correct', true)->count();
        $percent = round(($correct / $total) * 100, 2);

        $ilo = IntendedLearningOutcome::find($iloId);
        $threshold = $ilo ? (int) $ilo->mastery_threshold : 75;
        $status = $percent >= $threshold ? 'mastered' : ($percent > 0 ? 'developing' : 'not_started');

        $mastery = StudentIloMastery::firstOrNew([
            'student_id' => $submission->student_id,
            'class_id' => $submission->classAssignment?->class_id,
            'ilo_id' => $iloId,
        ]);

        $mastery->mastery_percent = $percent;
        $mastery->evidence_count = (int) $mastery->evidence_count + $total;
        $mastery->status = $status;
        $mastery->last_evaluated_at = now();
        $mastery->save();
    }
}
