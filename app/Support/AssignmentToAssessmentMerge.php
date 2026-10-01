<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 11: converts every class assignment, with its questions,
 * submissions, answers and anti-cheat records, into an assessment before the
 * assignment tables are removed.
 *
 * The conversion is rerun-safe. A converted class assignment is remembered in
 * assessments.legacy_class_assignment_id and a converted submission in
 * assessment_submissions.legacy_assignment_submission_id, so running it again
 * only picks up rows that were not converted yet and can never duplicate
 * records, scores, or reports. No XP or achievements are touched: historical
 * awards stay exactly as recorded, and nothing is re-awarded.
 *
 * A class assignment that cannot be converted (its library item is missing,
 * so there are no questions to copy) is stored verbatim in
 * legacy_assignment_archive together with its submissions and answers,
 * with the reason; see the merge migration for the archive's format.
 *
 * Everything works on plain query-builder rows, because the Assignment models
 * no longer exist once the feature is removed.
 */
final class AssignmentToAssessmentMerge
{
    /** @var array<string, int> */
    private array $summary = [
        'assignments_converted' => 0,
        'assignments_skipped' => 0,
        'assignments_archived' => 0,
        'questions_copied' => 0,
        'submissions_converted' => 0,
        'answers_converted' => 0,
        'events_relinked' => 0,
        'bank_questions_seeded' => 0,
    ];

    /** @return array<string, int> */
    public function convert(): array
    {
        if (! Schema::hasTable('class_assignments')) {
            return $this->summary;
        }

        $this->seedSharedQuestionBank();

        $handled = $this->handledAssignmentIds();

        $assignments = DB::table('class_assignments')
            ->when($handled !== [], fn ($query) => $query->whereNotIn('id', $handled))
            ->orderBy('id')
            ->get();

        foreach ($assignments as $assignment) {
            DB::transaction(function () use ($assignment): void {
                $this->convertAssignment($assignment);
            });
        }

        // Instructor anti-cheat policies now govern assessments.
        if (Schema::hasTable('anti_cheat_settings')) {
            DB::table('anti_cheat_settings')
                ->where('assessment_type', 'assignment')
                ->update(['assessment_type' => 'assessment']);
        }

        return $this->summary;
    }

    /**
     * True only when every class assignment has been converted, so the
     * migration knows it is safe to drop the assignment tables.
     */
    public function fullyConverted(): bool
    {
        if (! Schema::hasTable('class_assignments')) {
            return true;
        }

        $handled = $this->handledAssignmentIds();

        return ! DB::table('class_assignments')
            ->when($handled !== [], fn ($query) => $query->whereNotIn('id', $handled))
            ->exists();
    }

    /**
     * Class assignments already converted into an assessment or stored in
     * the archive.
     *
     * @return list<int>
     */
    private function handledAssignmentIds(): array
    {
        $ids = DB::table('assessments')
            ->whereNotNull('legacy_class_assignment_id')
            ->pluck('legacy_class_assignment_id');

        if (Schema::hasTable('legacy_assignment_archive')) {
            $ids = $ids->merge(DB::table('legacy_assignment_archive')
                ->where('source_table', 'class_assignments')
                ->pluck('source_id'));
        }

        return $ids->map(fn ($id) => (int) $id)->unique()->values()->all();
    }

    private function convertAssignment(object $assignment): void
    {
        $item = DB::table('assignment_library_items')->where('id', $assignment->assignment_library_item_id)->first();
        if ($item === null) {
            // Nothing to copy questions from.
            if (Schema::hasTable('legacy_assignment_archive')) {
                $this->archiveUnconvertible(
                    $assignment,
                    'Not converted: its assignment library item no longer exists, so there were no questions to copy.'
                );
            } else {
                // No archive to write to: the row stays in place.
                $this->summary['assignments_skipped']++;
            }

            return;
        }

        $status = match ((string) $assignment->status) {
            'draft' => 'draft',
            'published' => 'published',
            default => 'closed', // closed and archived both read as closed
        };

        $assessmentId = (int) DB::table('assessments')->insertGetId([
            'table_of_specification_id' => null,
            'class_id' => $assignment->class_id,
            'created_by' => $assignment->assigned_by,
            'title' => $assignment->title,
            'description' => null,
            'instructions' => $assignment->instructions ?: $item->instructions,
            'topic_title' => $item->topic_title,
            'status' => $status,
            'purpose' => 'homework',
            'total_items' => 0,
            'total_points' => 0,
            'time_limit_minutes' => $item->time_limit_minutes ?: null,
            'max_attempts' => max(1, (int) $assignment->max_attempts),
            'available_at' => $assignment->available_at,
            'due_at' => $assignment->due_at,
            'published_at' => $assignment->assigned_at ?: ($status === 'draft' ? null : $assignment->created_at),
            'legacy_class_assignment_id' => $assignment->id,
            'created_at' => $assignment->created_at,
            'updated_at' => $assignment->updated_at,
        ]);

        [$questionMap, $optionMap, $totalPoints, $questionCount] = $this->copyQuestions((int) $item->id, $assessmentId);

        DB::table('assessments')->where('id', $assessmentId)->update([
            'total_items' => $questionCount,
            'total_points' => $totalPoints,
        ]);

        $this->convertSubmissions($assignment, $assessmentId, $questionMap, $optionMap);

        $this->summary['assignments_converted']++;
    }

    /**
     * Store an unconvertible class assignment, its submissions and their
     * answers verbatim in legacy_assignment_archive.
     */
    private function archiveUnconvertible(object $assignment, string $reason): void
    {
        $archive = function (string $table, object $row) use ($assignment, $reason): void {
            DB::table('legacy_assignment_archive')->insertOrIgnore([
                'source_table' => $table,
                'source_id' => (int) $row->id,
                'class_assignment_id' => (int) $assignment->id,
                'reason' => $reason,
                'payload' => json_encode((array) $row),
                'archived_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        };

        $submissions = DB::table('assignment_submissions')
            ->where('class_assignment_id', $assignment->id)
            ->orderBy('id')
            ->get();
        foreach ($submissions as $submission) {
            $archive('assignment_submissions', $submission);
            $answers = Schema::hasTable('assignment_submission_answers')
                ? DB::table('assignment_submission_answers')->where('assignment_submission_id', $submission->id)->orderBy('id')->get()
                : collect();
            foreach ($answers as $answer) {
                $archive('assignment_submission_answers', $answer);
            }
        }
        // The assignment row last: once it is archived, the assignment counts
        // as handled.
        $archive('class_assignments', $assignment);

        $this->summary['assignments_archived']++;
    }

    /**
     * @return array{0: array<int, int>, 1: array<int, int>, 2: int, 3: int}
     */
    private function copyQuestions(int $itemId, int $assessmentId): array
    {
        $questions = DB::table('assignment_questions')
            ->where('assignment_library_item_id', $itemId)
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();
        $item = DB::table('assignment_library_items')->where('id', $itemId)->first();

        $questionMap = [];
        $optionMap = [];
        $totalPoints = 0;
        $number = 0;

        foreach ($questions as $question) {
            $number++;
            $type = $question->question_type === 'mcq' ? 'multiple_choice' : 'fill_blank';
            $correctAnswer = null;

            if ($type === 'fill_blank') {
                // Every accepted answer, one per line: exactly how assessment
                // grading reads them (case-insensitive).
                $correctAnswer = DB::table('assignment_blank_answers')
                        ->where('assignment_question_id', $question->id)
                        ->orderBy('id')
                        ->pluck('answer_text')
                        ->filter(fn ($text) => trim((string) $text) !== '')
                        ->implode("\n") ?: null;
            }

            $newQuestionId = (int) DB::table('assessment_questions')->insertGetId([
                'assessment_id' => $assessmentId,
                'table_of_specification_row_id' => null,
                'ilo_id' => null,
                'item_number' => $number,
                'question_type' => $type,
                'question_text' => $question->question_text,
                'points' => max(1, (int) $question->points),
                'is_required' => true,
                'authoring_touched' => true,
                'correct_answer' => $correctAnswer,
                'answer_explanation' => $question->explanation ?? null,
                'topic_title' => mb_substr((string) ($item->topic_title ?: $item->title), 0, 189),
                'created_at' => $question->created_at,
                'updated_at' => $question->updated_at,
            ]);
            $questionMap[(int) $question->id] = $newQuestionId;
            $totalPoints += max(1, (int) $question->points);
            $this->summary['questions_copied']++;

            if ($type === 'multiple_choice') {
                $options = DB::table('assignment_question_options')
                    ->where('assignment_question_id', $question->id)
                    ->orderBy('order_index')
                    ->orderBy('id')
                    ->get();
                foreach ($options as $index => $option) {
                    // Keyed by the old question as well, so only a value that
                    // really is one of THIS question's options is remapped.
                    $optionMap[(int) $question->id][(int) $option->id] = (int) DB::table('assessment_question_options')->insertGetId([
                        'assessment_question_id' => $newQuestionId,
                        'option_label' => chr(65 + min(25, (int) $index)),
                        'option_text' => $option->option_text,
                        'is_correct' => (bool) $option->is_correct,
                        'order_index' => $index + 1,
                        'created_at' => $option->created_at,
                        'updated_at' => $option->updated_at,
                    ]);
                }
            }
        }

        return [$questionMap, $optionMap, $totalPoints, $number];
    }

    /**
     * @param array<int, int> $questionMap old question id => new question id
     * @param array<int, array<int, int>> $optionMap old question id => [old option id => new option id]
     */
    private function convertSubmissions(object $assignment, int $assessmentId, array $questionMap, array $optionMap): void
    {
        $submissions = DB::table('assignment_submissions')
            ->where('class_assignment_id', $assignment->id)
            ->orderBy('id')
            ->get();

        foreach ($submissions as $submission) {
            $alreadyConverted = DB::table('assessment_submissions')
                ->where('legacy_assignment_submission_id', $submission->id)
                ->exists();
            if ($alreadyConverted) {
                continue;
            }

            $newSubmissionId = (int) DB::table('assessment_submissions')->insertGetId([
                'assessment_id' => $assessmentId,
                'student_id' => $submission->student_id,
                'attempt_no' => $submission->attempt_no,
                'status' => $submission->status,
                'score' => $submission->score,
                'total_points' => $submission->total_points,
                'started_at' => $submission->started_at,
                'submitted_at' => $submission->submitted_at,
                'graded_at' => $submission->graded_at,
                'feedback' => $submission->feedback,
                'draft_answers' => $this->remappedDraftAnswers($submission->draft_answers, $questionMap, $optionMap),
                'draft_version' => $submission->draft_version,
                'draft_saved_at' => $submission->draft_saved_at,
                'timed_out_at' => $submission->timed_out_at,
                'anti_cheat_session_id' => $submission->anti_cheat_session_id,
                'integrity_status' => $submission->integrity_status,
                'integrity_reason' => $submission->integrity_reason,
                'provisional_score' => $submission->provisional_score,
                'integrity_reviewed_by' => $submission->integrity_reviewed_by,
                'integrity_reviewed_at' => $submission->integrity_reviewed_at,
                'legacy_assignment_submission_id' => $submission->id,
                'created_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ]);
            $this->summary['submissions_converted']++;

            // A run that stopped part-way through the table drops may have
            // removed this table already; the submissions it held were all
            // converted before any table was dropped.
            $answers = Schema::hasTable('assignment_submission_answers')
                ? DB::table('assignment_submission_answers')->where('assignment_submission_id', $submission->id)->get()
                : collect();
            foreach ($answers as $answer) {
                $newQuestionId = $questionMap[(int) $answer->assignment_question_id] ?? null;
                if ($newQuestionId === null) {
                    continue;
                }
                DB::table('assessment_answers')->insert([
                    'assessment_submission_id' => $newSubmissionId,
                    'assessment_question_id' => $newQuestionId,
                    'selected_option_id' => $answer->selected_option_id !== null
                        ? ($optionMap[(int) $answer->assignment_question_id][(int) $answer->selected_option_id] ?? null)
                        : null,
                    'answer_text' => $answer->answer_text,
                    'is_correct' => (bool) $answer->is_correct,
                    'points_awarded' => $answer->points_awarded,
                    'created_at' => $answer->created_at,
                    'updated_at' => $answer->updated_at,
                ]);
                $this->summary['answers_converted']++;
            }

            if (Schema::hasTable('anti_cheat_events') && Schema::hasColumn('anti_cheat_events', 'assessment_submission_id')) {
                $this->summary['events_relinked'] += DB::table('anti_cheat_events')
                    ->where('assignment_submission_id', $submission->id)
                    ->update([
                        'assessment_type' => 'assessment',
                        'assessment_id' => $assessmentId,
                        'assessment_submission_id' => $newSubmissionId,
                    ]);

                // Events raised inside one question (a blocked paste, say)
                // keep pointing at that question's copy.
                foreach ($questionMap as $oldQuestionId => $newQuestionId) {
                    DB::table('anti_cheat_events')
                        ->where('assignment_submission_id', $submission->id)
                        ->where('assignment_question_id', $oldQuestionId)
                        ->update(['assessment_question_id' => $newQuestionId]);
                }
            }
        }
    }

    /**
     * Draft answers are keyed by question id; multiple-choice values hold an
     * option id. Both are remapped so an in-progress attempt keeps its work.
     * Only multiple-choice values are treated as option ids: a typed answer
     * such as "1" to a fill-in-the-blank question stays exactly as written.
     *
     * @param array<int, int> $questionMap
     * @param array<int, array<int, int>> $optionMap
     */
    private function remappedDraftAnswers(mixed $draft, array $questionMap, array $optionMap): ?string
    {
        if ($draft === null || $draft === '') {
            return null;
        }

        $decoded = json_decode((string) $draft, true);
        if (! is_array($decoded)) {
            return null;
        }

        $remapped = [];
        foreach ($decoded as $questionId => $value) {
            $newQuestionId = $questionMap[(int) $questionId] ?? null;
            if ($newQuestionId === null) {
                continue;
            }
            $questionOptions = $optionMap[(int) $questionId] ?? null;
            if ($questionOptions !== null && is_numeric($value)) {
                // A multiple-choice draft whose option no longer maps is
                // dropped rather than pointed at an unrelated option.
                if (! isset($questionOptions[(int) $value])) {
                    continue;
                }
                $value = (string) $questionOptions[(int) $value];
            }
            $remapped[(string) $newQuestionId] = $value;
        }

        return json_encode($remapped);
    }

    /**
     * The old admin assignment library doubles as the shared question pool:
     * every question of an active library item becomes a shared Question
     * Bank entry (instructor_id NULL) that any instructor can copy.
     */
    private function seedSharedQuestionBank(): void
    {
        // Every source table must still be there: a run that stopped part-way
        // through the drops can have removed some of them.
        foreach (['question_bank_items', 'question_bank_options', 'assignment_library_items', 'assignment_questions', 'assignment_question_options', 'assignment_blank_answers'] as $table) {
            if (! Schema::hasTable($table)) {
                return;
            }
        }
        if (DB::table('question_bank_items')->whereNull('instructor_id')->exists()) {
            return; // already seeded; rerunning must not duplicate
        }

        $items = DB::table('assignment_library_items')->where('is_active', true)->orderBy('id')->get();
        foreach ($items as $item) {
            $questions = DB::table('assignment_questions')
                ->where('assignment_library_item_id', $item->id)
                ->orderBy('order_index')
                ->get();
            foreach ($questions as $question) {
                $type = $question->question_type === 'mcq' ? 'multiple_choice' : 'fill_blank';
                $correctAnswer = $type === 'fill_blank'
                    ? (DB::table('assignment_blank_answers')
                        ->where('assignment_question_id', $question->id)
                        ->orderBy('id')
                        ->pluck('answer_text')
                        ->filter(fn ($text) => trim((string) $text) !== '')
                        ->implode("\n") ?: null)
                    : null;

                $bankId = (int) DB::table('question_bank_items')->insertGetId([
                    'instructor_id' => null,
                    'module_no' => $item->module_no,
                    'topic_title' => mb_substr((string) ($item->topic_title ?: $item->title), 0, 189),
                    'question_type' => $type,
                    'question_text' => $question->question_text,
                    'correct_answer' => $correctAnswer,
                    'answer_explanation' => $question->explanation ?? null,
                    'points' => max(1, (int) $question->points),
                    'is_archived' => false,
                    'created_at' => $question->created_at,
                    'updated_at' => $question->updated_at,
                ]);
                $this->summary['bank_questions_seeded']++;

                if ($type === 'multiple_choice') {
                    $options = DB::table('assignment_question_options')
                        ->where('assignment_question_id', $question->id)
                        ->orderBy('order_index')
                        ->get();
                    foreach ($options as $index => $option) {
                        DB::table('question_bank_options')->insert([
                            'question_bank_item_id' => $bankId,
                            'option_text' => $option->option_text,
                            'is_correct' => (bool) $option->is_correct,
                            'order_index' => $index + 1,
                            'created_at' => $option->created_at,
                            'updated_at' => $option->updated_at,
                        ]);
                    }
                }
            }
        }
    }
}
