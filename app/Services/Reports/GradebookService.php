<?php

namespace App\Services\Reports;

use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Support\Reports\Columns;
use App\Support\Reports\ReportFormat as F;
use App\Support\Reports\SubmissionOutcome;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Gradebook rows (DataSensei Updates 12), shared by the student's own
 * gradebook and the instructor's class gradebook. Only the assessments the
 * instructor published in the class appear: public challenges and personal
 * practice are never part of a class grade.
 *
 * The final (overall) grade of a student in a class is the average of their
 * graded assessments, each counting equally with its best graded attempt,
 * the rule Class Analytics and the performance groups use.
 *
 * Privacy is decided by the caller's query, never by the page: the student
 * gradebook asks only for the signed-in student's id, and the instructor
 * gradebook only for classes the instructor owns. This service never accepts
 * a student id from a request.
 *
 * One row per published or closed assessment of a class. For each student it
 * gives the attempt that counts (the best graded attempt, the same rule as
 * Class Analytics), its score, percentage, pass or fail against the
 * assessment's own passing score (or the 70% project pass mark), the state
 * and the instructor's feedback.
 *
 * States:
 *   upcoming        published, not open yet
 *   not_started     open, nothing turned in
 *   in_progress     started, not turned in
 *   missing         nothing turned in and the due date passed (or closed)
 *   awaiting_grade  turned in; some answers still need the instructor
 *   held            turned in; held for an integrity review, no credit yet
 *   graded          graded (late is shown with it)
 */
class GradebookService
{
    public const STATES = [
        'upcoming' => 'Not open yet',
        'not_started' => 'Not started',
        'in_progress' => 'In progress',
        'missing' => 'Missing',
        'awaiting_grade' => 'Awaiting grade',
        'held' => 'Held for integrity review',
        'graded' => 'Graded',
    ];

    public const TONES = [
        'missing' => 'bad',
        'awaiting_grade' => 'warn',
        'held' => 'warn',
        'in_progress' => 'warn',
    ];

    /**
     * The published and closed assessments of a class, oldest due first.
     *
     * @return Collection<int, Assessment>
     */
    public function assessments(ClassRoom $class): Collection
    {
        return Assessment::query()
            ->where('class_id', $class->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->orderBy('id')
            ->get(['id', 'class_id', 'title', 'purpose', 'status', 'due_at', 'available_at', 'max_attempts', 'passing_score_percent', 'total_points']);
    }

    /**
     * One student's gradebook rows in one class. The caller passes the
     * signed-in student's own id.
     *
     * @return list<array<string, mixed>>
     */
    public function forStudent(ClassRoom $class, int $studentId): array
    {
        $assessments = $this->assessments($class);
        $cells = $this->cells($assessments, [$studentId]);

        return $assessments->map(fn (Assessment $assessment) => ['assessment' => $assessment] + $cells[$studentId][$assessment->id])
            ->values()
            ->all();
    }

    /**
     * The whole class: every enrolled student against every assessment.
     *
     * @return array{assessments: Collection<int, Assessment>, students: Collection<int, object>, cells: array<int, array<int, array<string, mixed>>>, averages: array<int, ?float>, assessmentAverages: array<int, ?float>}
     */
    public function forClass(ClassRoom $class): array
    {
        $assessments = $this->assessments($class);
        $students = DB::table('class_student')
            ->join('users', 'users.id', '=', 'class_student.student_id')
            ->where('class_student.class_id', $class->id)
            ->orderBy('users.name')
            ->orderBy('users.id')
            ->get(['users.id', 'users.name', 'users.email'])
            ->keyBy('id');

        $cells = $this->cells($assessments, $students->keys()->map(fn ($id) => (int) $id)->all());

        $averages = [];
        foreach ($students as $id => $student) {
            $averages[(int) $id] = self::average(collect($cells[(int) $id] ?? [])->pluck('percent'));
        }

        $assessmentAverages = [];
        foreach ($assessments as $assessment) {
            $assessmentAverages[$assessment->id] = self::average(collect($cells)->map(fn (array $row) => $row[$assessment->id]['percent'] ?? null));
        }

        return [
            'assessments' => $assessments,
            'students' => $students,
            'cells' => $cells,
            'averages' => $averages,
            'assessmentAverages' => $assessmentAverages,
            // The class average of the students' final grades.
            'classAverage' => self::average(collect($averages)),
        ];
    }

    /**
     * A student's overall class grade from their gradebook rows: the final
     * grade (the average of the graded assessments, each counting equally,
     * the project's rule) and the points earned out of the points possible
     * on those graded assessments.
     *
     * @param  list<array<string, mixed>>  $rows  from forStudent()
     * @return array{percent: ?float, earned: ?string, possible: ?string, graded: int, total: int, final: bool, status: string}
     */
    public static function overall(array $rows): array
    {
        $graded = array_values(array_filter($rows, fn (array $row) => $row['state'] === 'graded'));
        $earned = array_sum(array_map(fn (array $row) => (float) $row['score'], $graded));
        $possible = array_sum(array_map(fn (array $row) => (float) $row['total'], $graded));
        $number = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
        $final = $rows !== [] && count($graded) === count($rows);

        return [
            'percent' => self::average(collect($graded)->pluck('percent')),
            'earned' => $graded === [] ? null : $number($earned),
            'possible' => $graded === [] ? null : $number($possible),
            'graded' => count($graded),
            'total' => count($rows),
            'final' => $final,
            'status' => $rows === [] ? 'No assessments yet' : ($final ? 'Final' : 'In progress'),
        ];
    }

    /**
     * Every attempt one student made on the class's assessments, oldest
     * first, for the instructor's review of that student. The caller checks
     * the student is enrolled in the instructor's own class.
     *
     * @return array<int, list<object>>  assessment id => attempts
     */
    public function history(ClassRoom $class, int $studentId): array
    {
        $assessmentIds = $this->assessments($class)->pluck('id')->all();
        if ($assessmentIds === []) {
            return [];
        }

        $attempts = DB::table('assessment_submissions')
            ->whereIn('assessment_id', $assessmentIds)
            ->where('student_id', $studentId)
            ->orderBy('attempt_no')
            ->orderBy('id')
            ->get(Columns::pick('assessment_submissions', ['id', 'assessment_id', 'attempt_no', 'status', 'score', 'total_points'], ['started_at', 'submitted_at', 'graded_at', 'integrity_status']));
        $unscored = SubmissionOutcome::unscoredSubmissionIds($attempts->whereIn('status', ['late', 'submitted'])->pluck('id'));

        $history = [];
        foreach ($attempts as $attempt) {
            $isFinal = SubmissionOutcome::isFinal($attempt, isset($unscored[(int) $attempt->id]));
            $attempt->percent = $isFinal ? ClassProgress::scorePercent($attempt->score, $attempt->total_points) : null;
            $attempt->state_label = match (true) {
                $isFinal => $attempt->status === 'late' ? 'Graded (late)' : 'Graded',
                $attempt->status === 'in_progress' => 'In progress',
                SubmissionOutcome::isDone($attempt) && SubmissionOutcome::isHeld($attempt) => 'Held for integrity review',
                SubmissionOutcome::isDone($attempt) => 'Awaiting grade',
                default => ucfirst(str_replace('_', ' ', (string) $attempt->status)),
            };
            $history[(int) $attempt->assessment_id][] = $attempt;
        }

        return $history;
    }

    /** Average of the graded percentages, or null when nothing is graded. */
    public static function average(Collection $percents): ?float
    {
        $graded = $percents->filter(fn ($p) => $p !== null);

        return $graded->isEmpty() ? null : round((float) $graded->avg(), 1);
    }

    /**
     * @param  Collection<int, Assessment>  $assessments
     * @param  list<int>  $studentIds
     * @return array<int, array<int, array<string, mixed>>>  student id => assessment id => cell
     */
    private function cells(Collection $assessments, array $studentIds): array
    {
        $cells = [];
        if ($studentIds === []) {
            return $cells;
        }

        $submissions = $assessments->isEmpty() ? collect() : DB::table('assessment_submissions')
            ->whereIn('assessment_id', $assessments->pluck('id')->all())
            ->whereIn('student_id', $studentIds)
            ->orderBy('attempt_no')
            ->orderBy('id')
            ->get(Columns::pick('assessment_submissions', ['id', 'assessment_id', 'student_id', 'attempt_no', 'status', 'score', 'total_points'], ['submitted_at', 'graded_at', 'feedback', 'integrity_status', 'integrity_reviewed_at']));

        $unscored = SubmissionOutcome::unscoredSubmissionIds($submissions->whereIn('status', ['late', 'submitted'])->pluck('id'));
        $answerFeedback = $submissions->isEmpty() ? collect() : DB::table('assessment_answers')
            ->whereIn('assessment_submission_id', $submissions->pluck('id')->all())
            ->whereNotNull('instructor_feedback')
            ->where('instructor_feedback', '!=', '')
            ->select('assessment_submission_id', DB::raw('COUNT(*) as total'))
            ->groupBy('assessment_submission_id')
            ->pluck('total', 'assessment_submission_id');

        $byStudent = $submissions->groupBy('student_id');
        $now = CarbonImmutable::now();

        foreach ($studentIds as $studentId) {
            $own = $byStudent->get($studentId, collect())->groupBy('assessment_id');
            foreach ($assessments as $assessment) {
                $cells[$studentId][$assessment->id] = $this->cell(
                    $assessment,
                    $own->get($assessment->id, collect()),
                    $unscored,
                    $answerFeedback,
                    $now
                );
            }
        }

        return $cells;
    }

    /**
     * @param  Collection<int, object>  $subs  one student's attempts on one assessment
     * @param  array<int, true>  $unscored
     * @return array<string, mixed>
     */
    private function cell(Assessment $assessment, Collection $subs, array $unscored, Collection $answerFeedback, CarbonImmutable $now): array
    {
        $done = $subs->filter(fn ($s) => SubmissionOutcome::isDone($s));
        $graded = $done->filter(fn ($s) => SubmissionOutcome::isFinal($s, isset($unscored[(int) $s->id])));
        $percentOf = fn ($s) => ClassProgress::scorePercent($s->score, $s->total_points);

        $counted = $graded->sortByDesc(fn ($s) => [$percentOf($s) ?? -1, (int) $s->attempt_no])->first()
            ?? $done->sortByDesc(fn ($s) => (int) $s->attempt_no)->first();
        $percent = $graded->isEmpty() ? null : $percentOf($counted);
        $passMark = (int) ($assessment->passing_score_percent ?: F::PASS_PERCENT);

        $state = match (true) {
            $graded->isNotEmpty() => 'graded',
            $counted !== null && SubmissionOutcome::isHeld($counted) => 'held',
            $counted !== null => 'awaiting_grade',
            $subs->contains(fn ($s) => $s->status === 'in_progress') => 'in_progress',
            $assessment->status === 'published' && $assessment->available_at !== null && $assessment->available_at->greaterThan($now) => 'upcoming',
            ClassProgress::isPastDue($assessment) => 'missing',
            default => 'not_started',
        };

        $late = $counted !== null && ClassProgress::isLate($counted, $assessment->due_at);
        $notCredited = $counted !== null && $state === 'graded'
            && ($counted->integrity_status ?? null) === 'blocked'
            && ! empty($counted->integrity_reviewed_at);

        $number = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');

        return [
            'state' => $state,
            'state_label' => self::STATES[$state].($late && in_array($state, ['graded', 'awaiting_grade'], true) ? ' (late)' : ''),
            'tone' => $state === 'graded' ? ($percent !== null && $percent >= $passMark ? 'good' : 'bad') : (self::TONES[$state] ?? null),
            'late' => $late,
            'attempts' => $done->count(),
            'max_attempts' => (int) ($assessment->max_attempts ?? 0),
            'submission_id' => $counted?->id,
            'score' => $state === 'graded' ? $number($counted->score) : null,
            'total' => $counted ? $number($counted->total_points) : ((float) $assessment->total_points > 0 ? $number($assessment->total_points) : null),
            'score_text' => $state === 'graded' ? $number($counted->score).' / '.$number($counted->total_points) : F::NONE,
            'percent' => $percent,
            'pass_mark' => $passMark,
            'passed' => $percent === null ? null : $percent >= $passMark,
            'not_credited' => $notCredited,
            'feedback' => $counted && trim((string) $counted->feedback) !== '' ? trim((string) $counted->feedback) : null,
            'answer_feedback' => $counted ? (int) ($answerFeedback[$counted->id] ?? 0) : 0,
            'submitted_at' => $counted?->submitted_at,
            'graded_at' => $state === 'graded' ? $counted->graded_at : null,
        ];
    }
}
