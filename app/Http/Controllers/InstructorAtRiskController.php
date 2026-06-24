<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\StudentPerformanceClusteringService;
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
            $snapshots = $clustering->refreshForClass($selectedClass)
                ->sortByDesc(fn ($snapshot) => $snapshot->risk_level === 'high' ? 2 : ($snapshot->risk_level === 'medium' ? 1 : 0));
        }

        return view('instructor.risk.index', compact('classes', 'selectedClass', 'snapshots'));
    }
}
