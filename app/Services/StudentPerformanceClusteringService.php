<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\StudentPerformanceCluster;
use App\Models\StudentPerformanceSnapshot;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Deterministic, rule-based performance segmentation.
 *
 * The legacy class/table names still contain "cluster" so existing routes,
 * historical rows, and public methods do not break. This is not an
 * unsupervised machine-learning clustering algorithm and must not be presented
 * as one.
 */
class StudentPerformanceClusteringService
{
    public function refreshForClass(ClassRoom $class): Collection
    {
        $lock = Cache::lock('performance-segmentation:class:' . $class->id, 300);
        if (! $lock->get()) {
            throw ValidationException::withMessages([
                'analytics' => 'Analytics are already being recalculated for this class. Wait a moment and try again.',
            ]);
        }

        try {
            $students = $class->students()
                ->where('role', User::ROLE_USER)
                ->where(function ($query): void {
                    $query->where('users.status', 'active')->orWhereNull('users.status');
                })
                ->get();

            return $students->map(function (User $student) use ($class) {
                return $this->refreshForStudent($student, $class);
            });
        } finally {
            $lock->release();
        }
    }

    /**
     * Read the last saved calculations without mutating data. The old GET pages
     * called refreshForClass(), which made merely opening analytics write rows.
     */
    public function snapshotsForClass(ClassRoom $class): Collection
    {
        $studentIds = $class->students()
            ->where('role', User::ROLE_USER)
            ->where(function ($query): void {
                $query->where('users.status', 'active')->orWhereNull('users.status');
            })
            ->pluck('users.id');

        if ($studentIds->isEmpty()) {
            return collect();
        }

        $reasons = StudentPerformanceCluster::query()
            ->where('class_id', $class->id)
            ->whereIn('student_id', $studentIds)
            ->pluck('cluster_description', 'student_id');

        return StudentPerformanceSnapshot::query()
            ->with('student:id,name,email')
            ->where('class_id', $class->id)
            ->whereIn('student_id', $studentIds)
            ->orderBy('student_id')
            ->get()
            ->each(function (StudentPerformanceSnapshot $snapshot) use ($reasons): void {
                $snapshot->setAttribute('segment_reason', $reasons[$snapshot->student_id] ?? null);
            });
    }

    public function refreshForStudent(User $student, ?ClassRoom $class = null): StudentPerformanceSnapshot
    {
        $classId = $class?->id;

        $assignmentRows = DB::table('assignment_submissions')
            ->when($classId, function ($q) use ($classId) {
                $q->join('class_assignments', 'class_assignments.id', '=', 'assignment_submissions.class_assignment_id')
                  ->where('class_assignments.class_id', $classId);
            })
            ->where('assignment_submissions.student_id', $student->id)
            ->whereIn('assignment_submissions.status', ['submitted', 'late', 'graded'])
            ->where('assignment_submissions.total_points', '>', 0)
            ->get([
                'assignment_submissions.id',
                'assignment_submissions.class_assignment_id',
                'assignment_submissions.attempt_no',
                'assignment_submissions.score',
                'assignment_submissions.total_points',
            ])
            ->groupBy('class_assignment_id')
            ->map(fn (Collection $attempts) => $attempts
                ->sortByDesc(fn ($row): string => $this->attemptSortKey($row))
                ->first());
        $assignmentScores = $assignmentRows
            ->map(fn ($row) => ((float) $row->score / max(1.0, (float) $row->total_points)) * 100)
            ->map(fn ($v) => (float) $v);

        $assessmentRows = DB::table('assessment_submissions')
            ->when($classId, function ($q) use ($classId) {
                $q->join('assessments', 'assessments.id', '=', 'assessment_submissions.assessment_id')
                    ->where('assessments.class_id', $classId);
            })
            ->where('assessment_submissions.student_id', $student->id)
            ->whereIn('assessment_submissions.status', ['submitted', 'late', 'graded'])
            ->whereNotNull('assessment_submissions.graded_at')
            ->where('assessment_submissions.total_points', '>', 0)
            ->get([
                'assessment_submissions.id',
                'assessment_submissions.assessment_id',
                'assessment_submissions.attempt_no',
                'assessment_submissions.score',
                'assessment_submissions.total_points',
            ])
            ->groupBy('assessment_id')
            ->map(fn (Collection $attempts) => $attempts
                ->sortByDesc(fn ($row): string => $this->attemptSortKey($row))
                ->first());
        $assessmentScores = $assessmentRows
            ->map(fn ($row) => ((float) $row->score / max(1.0, (float) $row->total_points)) * 100)
            ->map(fn ($v) => (float) $v);

        // Platform challenges have no class foreign key. They belong only in the
        // learner-wide snapshot; adding them to every class would inflate each
        // instructor's class metrics with unrelated work.
        $mcqScores = $classId
            ? collect()
            : DB::table('challenge_attempts')
                ->where('user_id', $student->id)
                ->where('is_ranked', true)
                ->whereIn('status', ['submitted', 'expired'])
                ->where('total_questions', '>', 0)
                ->selectRaw('(score / total_questions) * 100 as percent')
                ->pluck('percent')
                ->map(fn ($v) => (float) $v);

        $codingScores = $classId
            ? collect()
            : DB::table('coding_submissions')
                ->where('user_id', $student->id)
                ->where('voided', false)
                ->where('tests_total', '>', 0)
                ->selectRaw('(tests_passed / tests_total) * 100 as percent')
                ->pluck('percent')
                ->map(fn ($v) => (float) $v);

        $scores = $assignmentScores
            ->merge($assessmentScores)
            ->merge($mcqScores)
            ->merge($codingScores)
            ->filter(fn ($v) => is_numeric($v));
        $averageScore = $scores->isNotEmpty() ? round($scores->avg(), 2) : 0.0;

        $completedActivities = $assignmentScores->count()
            + $assessmentScores->count()
            + $mcqScores->count()
            + $codingScores->count();
        $missingAssignments = $this->missingAssignments($student, $classId);
        $lateSubmissions = $this->lateSubmissions($student, $classId);
        $antiCheatWarnings = $this->antiCheatWarnings($student, $classId);
        $engagementScore = $this->engagementScore($averageScore, $completedActivities, $missingAssignments, $lateSubmissions, $antiCheatWarnings);
        [$cluster, $risk, $description] = $this->classify($averageScore, $engagementScore, $missingAssignments, $lateSubmissions, $antiCheatWarnings, $completedActivities);

        return DB::transaction(function () use (
            $student,
            $classId,
            $averageScore,
            $completedActivities,
            $missingAssignments,
            $lateSubmissions,
            $antiCheatWarnings,
            $engagementScore,
            $cluster,
            $risk,
            $description,
        ): StudentPerformanceSnapshot {
            User::query()->whereKey($student->id)->lockForUpdate()->firstOrFail();

            $snapshot = StudentPerformanceSnapshot::updateOrCreate([
                'student_id' => $student->id,
                'class_id' => $classId,
            ], [
                'average_score_percent' => $averageScore,
                'average_time_ratio' => null,
                'completed_activities' => $completedActivities,
                'missing_assignments' => $missingAssignments,
                'late_submissions' => $lateSubmissions,
                'anti_cheat_warnings' => $antiCheatWarnings,
                'engagement_score' => $engagementScore,
                'cluster_label' => $cluster,
                'risk_level' => $risk,
                'generated_at' => now(),
            ]);

            StudentPerformanceCluster::updateOrCreate([
                'student_id' => $student->id,
                'class_id' => $classId,
            ], [
                'cluster_label' => $cluster,
                'cluster_description' => $description,
                'average_score_percent' => $averageScore,
                'engagement_score' => $engagementScore,
                'risk_level' => $risk,
                'assigned_at' => now(),
            ]);

            return $snapshot;
        }, 3);
    }

    private function missingAssignments(User $student, ?int $classId): int
    {
        if (!$classId) {
            return 0;
        }

        return DB::table('class_assignments')
            ->where('class_id', $classId)
            ->whereIn('status', ['published', 'closed'])
            ->where(function ($query) {
                $query->where('status', 'closed')
                    ->orWhere(function ($due) {
                        $due->whereNotNull('due_at')->where('due_at', '<', now());
                    });
            })
            ->whereNotExists(function ($q) use ($student) {
                $q->select(DB::raw(1))
                  ->from('assignment_submissions')
                  ->whereColumn('assignment_submissions.class_assignment_id', 'class_assignments.id')
                  ->where('assignment_submissions.student_id', $student->id)
                  ->whereIn('assignment_submissions.status', ['submitted', 'late', 'graded']);
            })
            ->count();
    }

    private function lateSubmissions(User $student, ?int $classId): int
    {
        return DB::table('assignment_submissions')
            ->when($classId, function ($q) use ($classId) {
                $q->join('class_assignments', 'class_assignments.id', '=', 'assignment_submissions.class_assignment_id')
                  ->where('class_assignments.class_id', $classId);
            })
            ->where('assignment_submissions.student_id', $student->id)
            ->whereIn('assignment_submissions.status', ['submitted', 'late', 'graded'])
            ->get([
                'assignment_submissions.id',
                'assignment_submissions.class_assignment_id',
                'assignment_submissions.attempt_no',
                'assignment_submissions.status',
            ])
            ->groupBy('class_assignment_id')
            ->map(fn (Collection $attempts) => $attempts
                ->sortByDesc(fn ($row): string => $this->attemptSortKey($row))
                ->first())
            ->where('status', 'late')
            ->count();
    }

    private function antiCheatWarnings(User $student, ?int $classId): int
    {
        if (!Schema::hasTable('anti_cheat_events')) {
            return 0;
        }

        return DB::table('anti_cheat_events')
            ->where('user_id', $student->id)
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->count();
    }

    private function engagementScore(float $score, int $completed, int $missing, int $late, int $warnings): float
    {
        $value = ($score * 0.55) + (min(100, $completed * 8) * 0.25) + 20;
        $value -= ($missing * 8) + ($late * 6) + ($warnings * 5);
        return round(max(0, min(100, $value)), 2);
    }

    private function classify(float $score, float $engagement, int $missing, int $late, int $warnings, int $completed): array
    {
        if ($completed === 0) {
            return ['Not Enough Data', 'unknown', 'The student has not generated enough learning evidence yet.'];
        }

        if ($score >= 90 && $engagement >= 80 && $warnings === 0) {
            return ['Exceptional Performer', 'low', 'High score, strong engagement, and clean activity records.'];
        }

        if ($score >= 80 && $engagement >= 65) {
            return ['Consistent Performer', 'low', 'Performs well and completes enough learning tasks.'];
        }

        if ($score >= 65 && $engagement >= 45) {
            return ['Improving Learner', 'medium', 'Shows progress but needs continued practice and monitoring.'];
        }

        if ($missing >= 2 || $late >= 2 || $score < 65) {
            return ['At Risk', 'high', 'Low scores, missing work, or repeated late submissions were detected.'];
        }

        return ['Needs Monitoring', 'medium', 'Student activity is acceptable but should be monitored.'];
    }

    private function attemptSortKey(object $row): string
    {
        return str_pad((string) ((int) $row->attempt_no), 10, '0', STR_PAD_LEFT)
            . ':' . str_pad((string) ((int) $row->id), 20, '0', STR_PAD_LEFT);
    }
}
