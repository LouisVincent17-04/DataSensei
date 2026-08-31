<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\AssignmentSubmission;
use App\Models\ClassAssignment;
use App\Models\ClassRoom;
use App\Models\StudentIloMastery;
use App\Models\StudentPerformanceSnapshot;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    public function dashboard()
    {
        /** @var User $instructor */
        $instructor = Auth::user();

        $classes = ClassRoom::query()
            ->forInstructor($instructor->id)
            ->active()
            ->withCount('students')
            ->orderBy('name')
            ->get();
        $classIds = $classes->pluck('id');

        $totalStudents = $classIds->isEmpty()
            ? 0
            : DB::table('class_student')
                ->whereIn('class_id', $classIds)
                ->distinct()
                ->count('student_id');

        $publishedWork = $classIds->isEmpty()
            ? 0
            : ClassAssignment::whereIn('class_id', $classIds)->where('status', 'published')->count()
                + Assessment::whereIn('class_id', $classIds)->where('status', 'published')->count();

        $snapshots = $classIds->isEmpty()
            ? collect()
            : StudentPerformanceSnapshot::query()
                ->with(['student:id,name,email', 'classRoom:id,name'])
                ->whereIn('class_id', $classIds)
                ->get();

        $masteryRows = $classIds->isEmpty()
            ? collect()
            : StudentIloMastery::query()
                ->whereIn('class_id', $classIds)
                ->where('evidence_count', '>', 0)
                ->get(['status']);

        $atRiskStudents = $snapshots
            ->where('risk_level', 'high')
            ->unique('student_id')
            ->sortBy('student.name')
            ->values();

        $stats = [
            'active_classes' => $classes->count(),
            'total_students' => $totalStudents,
            'published_work' => $publishedWork,
            'average_score' => $snapshots->isNotEmpty()
                ? (int) round((float) $snapshots->avg('average_score_percent'))
                : 0,
            'mastery_rate' => $masteryRows->isNotEmpty()
                ? (int) round(($masteryRows->where('status', 'mastered')->count() / $masteryRows->count()) * 100)
                : 0,
            'at_risk' => $atRiskStudents->count(),
        ];

        $classMetrics = $classes->map(function (ClassRoom $class) use ($snapshots): array {
            $classSnapshots = $snapshots->where('class_id', $class->id);

            return [
                'class' => $class,
                'average_score' => $classSnapshots->isNotEmpty()
                    ? (int) round((float) $classSnapshots->avg('average_score_percent'))
                    : null,
                'engagement' => $classSnapshots->isNotEmpty()
                    ? (int) round((float) $classSnapshots->avg('engagement_score'))
                    : null,
                'at_risk' => $classSnapshots->where('risk_level', 'high')->count(),
            ];
        });

        $recentWork = $this->recentWork($classIds);
        $recentSubmissions = $this->recentSubmissions($classIds);

        return view('instructor.dashboard', compact(
            'instructor',
            'stats',
            'classMetrics',
            'atRiskStudents',
            'recentWork',
            'recentSubmissions',
        ));
    }

    private function recentWork(Collection $classIds): Collection
    {
        if ($classIds->isEmpty()) {
            return collect();
        }

        $assignments = ClassAssignment::query()
            ->with('classRoom:id,name')
            ->whereIn('class_id', $classIds)
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(fn (ClassAssignment $assignment): array => [
                'type' => 'Assignment',
                'title' => $assignment->title,
                'class_name' => $assignment->classRoom?->name,
                'status' => $assignment->status,
                'at' => $assignment->updated_at,
                'url' => route('instructor.assignments.show', $assignment),
            ]);

        $assessments = Assessment::query()
            ->with('classRoom:id,name')
            ->whereIn('class_id', $classIds)
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(fn (Assessment $assessment): array => [
                'type' => 'Assessment',
                'title' => $assessment->title,
                'class_name' => $assessment->classRoom?->name,
                'status' => $assessment->status,
                'at' => $assessment->updated_at,
                'url' => route('instructor.assessments.builder', $assessment),
            ]);

        return $assignments->concat($assessments)->sortByDesc('at')->take(6)->values();
    }

    private function recentSubmissions(Collection $classIds): Collection
    {
        if ($classIds->isEmpty()) {
            return collect();
        }

        $assignments = AssignmentSubmission::query()
            ->with(['student:id,name', 'classAssignment:id,class_id,title'])
            ->whereHas('classAssignment', fn ($query) => $query->whereIn('class_id', $classIds))
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->latest('submitted_at')
            ->limit(6)
            ->get()
            ->map(fn (AssignmentSubmission $submission): array => [
                'type' => 'Assignment',
                'student' => $submission->student?->name ?? 'Deleted learner',
                'title' => $submission->classAssignment?->title ?? 'Assignment',
                'status' => $submission->status,
                'at' => $submission->submitted_at ?? $submission->updated_at,
            ]);

        $assessments = AssessmentSubmission::query()
            ->with(['student:id,name', 'assessment:id,class_id,title'])
            ->whereHas('assessment', fn ($query) => $query->whereIn('class_id', $classIds))
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->latest('submitted_at')
            ->limit(6)
            ->get()
            ->map(fn (AssessmentSubmission $submission): array => [
                'type' => 'Assessment',
                'student' => $submission->student?->name ?? 'Deleted learner',
                'title' => $submission->assessment?->title ?? 'Assessment',
                'status' => $submission->status,
                'at' => $submission->submitted_at ?? $submission->updated_at,
            ]);

        return $assignments->concat($assessments)->sortByDesc('at')->take(6)->values();
    }
}
