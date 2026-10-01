<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\ClassRoom;
use App\Models\User;
use App\Services\Reports\ClassProgress;
use App\Support\Reports\PerformanceBands;
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
            : Assessment::whereIn('class_id', $classIds)->where('status', 'published')->count();

        // Scores, performance groups and at-risk learners come from the same
        // live source as Class Analytics & At-Risk (DataSensei Updates 12), so
        // the dashboard and that page always agree.
        $progress = app(ClassProgress::class);
        $snapshots = $classes->mapWithKeys(fn (ClassRoom $class) => [$class->id => $progress->forClass($class)]);

        // ILOs are descriptive only (DataSensei Updates 5): no ILO mastery
        // rate. The dashboard counts the modules given to classes instead.
        $assignedModules = $classIds->isEmpty()
            ? 0
            : \App\Models\ClassModuleAssignment::query()
                ->whereIn('class_id', $classIds)
                ->where('status', 'active')
                ->count();

        $atRiskStudents = $classes
            ->flatMap(fn (ClassRoom $class) => collect($snapshots[$class->id]['perStudent'])
                ->filter(fn (array $row) => $row['summary']['attention'] !== [])
                ->map(fn (array $row, int $studentId) => [
                    'student_id' => $studentId,
                    'name' => $row['student']->name,
                    'class' => $class->name,
                    'group' => PerformanceBands::label($row['summary']['performance_group']),
                    'reasons' => array_values($row['summary']['attention']),
                    'url' => route('instructor.analytics.student', ['class' => $class->id, 'student' => $studentId]),
                ])
                ->values())
            ->sortBy('name')
            ->values();

        $averages = $classes->flatMap(fn (ClassRoom $class) => collect($snapshots[$class->id]['perStudent'])
            ->pluck('summary.assessment_average'))
            ->filter(fn ($value) => $value !== null);

        $stats = [
            'active_classes' => $classes->count(),
            'total_students' => $totalStudents,
            'published_work' => $publishedWork,
            'average_score' => $averages->isNotEmpty() ? (int) round((float) $averages->avg()) : null,
            'assigned_modules' => $assignedModules,
            'at_risk' => $atRiskStudents->unique('student_id')->count(),
        ];

        $classMetrics = $classes->map(function (ClassRoom $class) use ($snapshots): array {
            $overview = $snapshots[$class->id]['overview'];

            return [
                'class' => $class,
                'average_score' => $overview['assessment_average'] === null ? null : (int) round((float) $overview['assessment_average']),
                'performance' => $overview['performance'],
                'at_risk' => $overview['attention'],
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

        $assessments = Assessment::query()
            ->with('classRoom:id,name')
            ->whereIn('class_id', $classIds)
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(fn (Assessment $assessment): array => [
                'type' => $assessment->purposeLabel() ?? 'Assessment',
                'title' => $assessment->title,
                'class_name' => $assessment->classRoom?->name,
                'status' => $assessment->status,
                'at' => $assessment->updated_at,
                'url' => route('instructor.assessments.builder', $assessment),
            ]);

        return $assessments->sortByDesc('at')->take(6)->values();
    }

    private function recentSubmissions(Collection $classIds): Collection
    {
        if ($classIds->isEmpty()) {
            return collect();
        }

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

        return $assessments->sortByDesc('at')->take(6)->values();
    }
}
