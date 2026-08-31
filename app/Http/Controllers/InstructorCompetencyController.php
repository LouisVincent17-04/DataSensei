<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\CompetencyMonitoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InstructorCompetencyController extends Controller
{
    public function index(Request $request, CompetencyMonitoringService $monitoring): View
    {
        $classes = ClassRoom::query()
            ->forInstructor((int) Auth::id())
            ->active()
            ->withCount('students')
            ->orderBy('name')
            ->get();

        $selectedClass = $classes->firstWhere('id', (int) $request->integer('class_id'))
            ?? $classes->first();

        $migrationRequired = ! Schema::hasTable('competencies')
            || ! Schema::hasTable('student_competency_snapshots')
            || ! Schema::hasTable('student_competency_trends');

        $report = (! $migrationRequired && $selectedClass)
            ? $monitoring->classReport($selectedClass)
            : null;

        return view('instructor.competencies.index', compact(
            'classes',
            'selectedClass',
            'report',
            'migrationRequired',
        ));
    }

    public function refresh(Request $request, CompetencyMonitoringService $monitoring): RedirectResponse
    {
        $validated = $request->validate(['class_id' => ['required', 'integer', 'min:1']]);
        $class = ClassRoom::query()
            ->forInstructor((int) Auth::id())
            ->active()
            ->whereKey((int) $validated['class_id'])
            ->firstOrFail();

        $monitoring->refreshClass($class);

        return redirect()
            ->route('instructor.competencies.index', ['class_id' => $class->id])
            ->with('success', 'Competency snapshots recalculated from the latest evidence.');
    }
}
