<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\StudentPerformanceClusteringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorAtRiskController extends Controller
{
    public function index(Request $request, StudentPerformanceClusteringService $clustering)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->orderBy('name')->get();
        $selectedClass = $classes->firstWhere('id', (int) $request->input('class_id')) ?? $classes->first();
        $snapshots = collect();

        if ($selectedClass) {
            $snapshots = $clustering->snapshotsForClass($selectedClass)
                ->sortByDesc(fn ($snapshot) => $snapshot->risk_level === 'high' ? 2 : ($snapshot->risk_level === 'medium' ? 1 : 0));
        }
        $lastCalculatedAt = $snapshots->sortByDesc('generated_at')->first()?->generated_at;

        return view('instructor.risk.index', compact('classes', 'selectedClass', 'snapshots', 'lastCalculatedAt'));
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
            ->route('instructor.risk.index', ['class_id' => $class->id])
            ->with('success', 'Risk indicators recalculated from the latest saved evidence.');
    }
}
