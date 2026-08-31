<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CompetencyMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentCompetencyController extends Controller
{
    public function index(Request $request, CompetencyMonitoringService $monitoring): View
    {
        /** @var User $student */
        $student = Auth::user();

        $classes = $student->classesAsStudent()
            ->active()
            ->orderBy('classes.name')
            ->get();

        $selectedClass = $classes->firstWhere('id', (int) $request->integer('class_id'))
            ?? $classes->first();

        $migrationRequired = ! Schema::hasTable('competencies')
            || ! Schema::hasTable('student_competency_snapshots')
            || ! Schema::hasTable('student_competency_trends');

        $report = (! $migrationRequired && $selectedClass)
            ? $monitoring->studentReport($student, $selectedClass)
            : null;

        return view('student.competencies.index', compact(
            'student',
            'classes',
            'selectedClass',
            'report',
            'migrationRequired',
        ));
    }
}
