<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\StudentPerformanceCluster;
use App\Models\StudentPerformanceSnapshot;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentPerformanceClusteringService
{
    public function refreshForClass(ClassRoom $class): Collection
    {
        $students = $class->students()->where('role', User::ROLE_USER)->get();

        return $students->map(function (User $student) use ($class) {
            return $this->refreshForStudent($student, $class);
        });
    }

    public function refreshForStudent(User $student, ?ClassRoom $class = null): StudentPerformanceSnapshot
    {
        $classId = $class?->id;

        $assignmentScores = DB::table('assignment_submissions')
            ->when($classId, function ($q) use ($classId) {
                $q->join('class_assignments', 'class_assignments.id', '=', 'assignment_submissions.class_assignment_id')
                  ->where('class_assignments.class_id', $classId);
            })
            ->where('assignment_submissions.student_id', $student->id)
            ->whereIn('assignment_submissions.status', ['submitted', 'late', 'graded'])
            ->where('assignment_submissions.total_points', '>', 0)
            ->selectRaw('(assignment_submissions.score / assignment_submissions.total_points) * 100 as percent')
            ->pluck('percent')
            ->map(fn ($v) => (float) $v);

        $mcqScores = DB::table('challenge_user')
            ->join('challenges', 'challenges.id', '=', 'challenge_user.challenge_id')
            ->leftJoin('challenge_questions', 'challenge_questions.challenge_id', '=', 'challenges.id')
            ->where('challenge_user.user_id', $student->id)
            ->groupBy('challenge_user.id', 'challenge_user.score')
            ->selectRaw('CASE WHEN COUNT(challenge_questions.id) > 0 THEN (challenge_user.score / COUNT(challenge_questions.id)) * 100 ELSE 0 END as percent')
            ->pluck('percent')
            ->map(fn ($v) => (float) $v);

        $codingScores = DB::table('coding_submissions')
            ->where('user_id', $student->id)
            ->where('voided', false)
            ->where('tests_total', '>', 0)
            ->selectRaw('(tests_passed / tests_total) * 100 as percent')
            ->pluck('percent')
            ->map(fn ($v) => (float) $v);

        $scores = $assignmentScores->merge($mcqScores)->merge($codingScores)->filter(fn ($v) => is_numeric($v));
        $averageScore = $scores->isNotEmpty() ? round($scores->avg(), 2) : 0.0;

        $completedActivities = $assignmentScores->count() + $mcqScores->count() + $codingScores->count();
        $missingAssignments = $this->missingAssignments($student, $classId);
        $lateSubmissions = $this->lateSubmissions($student, $classId);
        $antiCheatWarnings = $this->antiCheatWarnings($student, $classId);
        $engagementScore = $this->engagementScore($averageScore, $completedActivities, $missingAssignments, $lateSubmissions, $antiCheatWarnings);
        [$cluster, $risk, $description] = $this->classify($averageScore, $engagementScore, $missingAssignments, $lateSubmissions, $antiCheatWarnings, $completedActivities);

        $snapshot = StudentPerformanceSnapshot::create([
            'student_id' => $student->id,
            'class_id' => $classId,
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

        StudentPerformanceCluster::create([
            'student_id' => $student->id,
            'class_id' => $classId,
            'cluster_label' => $cluster,
            'cluster_description' => $description,
            'average_score_percent' => $averageScore,
            'engagement_score' => $engagementScore,
            'risk_level' => $risk,
            'assigned_at' => now(),
        ]);

        return $snapshot;
    }

    private function missingAssignments(User $student, ?int $classId): int
    {
        if (!$classId) {
            return 0;
        }

        return DB::table('class_assignments')
            ->where('class_id', $classId)
            ->whereIn('status', ['published', 'closed'])
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
            ->where('assignment_submissions.status', 'late')
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
}
