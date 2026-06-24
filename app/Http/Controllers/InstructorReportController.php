<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\StudentPerformanceSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorReportController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->orderBy('name')->get();
        $selectedClass = $classes->firstWhere('id', (int) $request->input('class_id')) ?? $classes->first();

        $snapshots = StudentPerformanceSnapshot::with('student')
            ->when($selectedClass, fn ($q) => $q->where('class_id', $selectedClass->id))
            ->latest('generated_at')
            ->limit(100)
            ->get();

        return view('instructor.reports.index', compact('classes', 'selectedClass', 'snapshots'));
    }
}
