<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\StudentPerformanceClusteringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorAnalyticsController extends Controller
{
    public function index(Request $request, StudentPerformanceClusteringService $clustering)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->withCount('students')->orderBy('name')->get();
        $selectedClass = $classes->firstWhere('id', (int) $request->input('class_id')) ?? $classes->first();
        $snapshots = collect();

        if ($selectedClass) {
            $snapshots = $clustering->snapshotsForClass($selectedClass);
        }

        $stats = [
            'students' => (int) ($selectedClass?->students_count ?? 0),
            'avg_score' => round((float) $snapshots->avg('average_score_percent'), 2),
            'avg_engagement' => round((float) $snapshots->avg('engagement_score'), 2),
            'at_risk' => $snapshots->where('risk_level', 'high')->count(),
        ];
        $lastCalculatedAt = $snapshots->sortByDesc('generated_at')->first()?->generated_at;

        return view('instructor.analytics.index', compact('classes', 'selectedClass', 'snapshots', 'stats', 'lastCalculatedAt'));
    }

    public function refresh(Request $request, StudentPerformanceClusteringService $clustering): RedirectResponse
    {
        $validated = $request->validate(['class_id' => ['required', 'integer', 'min:1']]);
        $class = ClassRoom::query()
            ->whereKey((int) $validated['class_id'])
            ->where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->firstOrFail();

        $clustering->refreshForClass($class);

        return redirect()
            ->route('instructor.analytics.index', ['class_id' => $class->id])
            ->with('success', 'Class analytics recalculated from the latest saved learning evidence.');
    }
}
