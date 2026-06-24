<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\IntendedLearningOutcome;
use App\Models\StudentIloMastery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorMasteryController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->orderBy('name')->get();
        $selectedClass = $classes->firstWhere('id', (int) $request->input('class_id')) ?? $classes->first();
        $moduleNo = (int) $request->input('module_no', 1);

        $ilos = IntendedLearningOutcome::active()
            ->where('module_no', $moduleNo)
            ->orderBy('sort_order')
            ->get();

        $masteries = StudentIloMastery::with(['student', 'ilo'])
            ->when($selectedClass, fn ($q) => $q->where('class_id', $selectedClass->id))
            ->whereIn('ilo_id', $ilos->pluck('id'))
            ->orderByDesc('mastery_percent')
            ->paginate(20)
            ->withQueryString();

        return view('instructor.mastery.index', compact('classes', 'selectedClass', 'moduleNo', 'ilos', 'masteries'));
    }
}
