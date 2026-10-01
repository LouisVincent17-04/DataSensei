<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\StudentPerformanceClusteringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * At-Risk Alerts (DataSensei Updates 12): merged into Class Analytics.
 *
 * The instructor now reads performance groups and at-risk students in one
 * area, Class Analytics & At-Risk (InstructorAnalyticsController), which
 * computes them live from the class's saved work with the configured rules.
 * /instructor/risk is kept so old links and bookmarks still work: it opens
 * that page on the at-risk list of the same class. Only the instructor's own
 * classes are carried over; any other class id is dropped.
 *
 * "Recalculate" still refreshes the stored performance snapshots that other
 * pages read, then returns to the same place.
 */
class InstructorAtRiskController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $classId = (int) $request->query('class_id');
        $owned = $classId > 0 && ClassRoom::query()
            ->whereKey($classId)
            ->where('instructor_id', Auth::id())
            ->exists();

        // Keep a message from "Recalculate" for the page it lands on.
        $request->session()->reflash();

        return redirect()->to(route('instructor.analytics.index', $owned ? ['class_id' => $classId] : []).'#attention');
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
