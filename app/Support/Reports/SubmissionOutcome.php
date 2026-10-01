<?php

namespace App\Support\Reports;

use Illuminate\Support\Facades\DB;

/**
 * What an assessment attempt counts as in grades (DataSensei Updates 12).
 *
 * Shared by Class Analytics (performance groups, at-risk) and both
 * gradebooks, so a score is treated the same way everywhere:
 *
 *   held      turned in, but held for an integrity review: no credit yet
 *   awaiting  turned in, but some answers still need the instructor's grade
 *             (the stored score is only provisional)
 *   final     graded: the score counts. A late attempt is final once it is
 *             fully scored. An attempt the instructor reviewed and kept
 *             blocked is final with the score the instructor left (0).
 *
 * Works on Eloquent models and plain query rows alike.
 */
final class SubmissionOutcome
{
    public const DONE = ['submitted', 'late', 'graded'];

    public const HELD_INTEGRITY = ['blocked', 'review_required'];

    public static function isDone(object $submission): bool
    {
        return in_array((string) ($submission->status ?? ''), self::DONE, true);
    }

    public static function isHeld(object $submission): bool
    {
        return ($submission->status ?? null) === 'submitted'
            && in_array((string) ($submission->integrity_status ?? ''), self::HELD_INTEGRITY, true);
    }

    /**
     * Whether the attempt's score is its grade.
     *
     * @param  bool  $hasUnscoredAnswers  some answer still has no correctness
     *                                    (an essay waiting for the instructor)
     */
    public static function isFinal(object $submission, bool $hasUnscoredAnswers = false): bool
    {
        $status = (string) ($submission->status ?? '');

        if ($status === 'graded') {
            return $submission->score !== null;
        }

        if ($status === 'late') {
            return $submission->score !== null
                && (! empty($submission->graded_at) || ! $hasUnscoredAnswers);
        }

        return false;
    }

    /**
     * Ids of the given attempts that still have an answer without a score.
     *
     * @param  iterable<int|string>  $submissionIds
     * @return array<int, true>
     */
    public static function unscoredSubmissionIds(iterable $submissionIds): array
    {
        $ids = [];
        foreach ($submissionIds as $id) {
            $ids[] = (int) $id;
        }

        if ($ids === []) {
            return [];
        }

        $pending = [];
        foreach (array_chunk(array_values(array_unique($ids)), 500) as $chunk) {
            DB::table('assessment_answers')
                ->whereIn('assessment_submission_id', $chunk)
                ->whereNull('is_correct')
                ->distinct()
                ->pluck('assessment_submission_id')
                ->each(function ($id) use (&$pending): void {
                    $pending[(int) $id] = true;
                });
        }

        return $pending;
    }
}
