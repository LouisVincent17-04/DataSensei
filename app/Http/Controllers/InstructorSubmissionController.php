<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->orderBy('name')->get();
        $classIds = $classes->pluck('id');

        $query = AssignmentSubmission::with(['student', 'classAssignment.classRoom', 'classAssignment.libraryItem'])
            ->whereHas('classAssignment', function ($q) use ($classIds, $request) {
                $q->whereIn('class_id', $classIds);
                if ($request->filled('class_id')) {
                    $q->where('class_id', $request->integer('class_id'));
                }
            })
            ->latest('submitted_at')
            ->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $submissions = $query->paginate(15)->withQueryString();

        return view('instructor.submissions.index', compact('classes', 'submissions'));
    }
}
