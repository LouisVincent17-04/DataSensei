<?php

namespace App\Http\Controllers;

use App\Models\AssessmentSubmission;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Every assessment attempt across the instructor's classes, including
 * attempts held by an anti-cheat decision (DataSensei Updates 11).
 */
class InstructorSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->orderBy('name')->get();
        $classIds = $classes->pluck('id');

        $query = AssessmentSubmission::with(['student', 'assessment.classRoom'])
            ->whereHas('assessment', function ($q) use ($classIds, $request) {
                $q->whereIn('class_id', $classIds);
                if ($request->filled('class_id')) {
                    $q->where('class_id', $request->integer('class_id'));
                }
            })
            ->latest('submitted_at')
            ->latest('created_at');

        if ($request->input('status') === 'held') {
            $query->where('status', 'submitted')
                ->whereIn('integrity_status', [
                    AssessmentSubmission::INTEGRITY_BLOCKED,
                    AssessmentSubmission::INTEGRITY_REVIEW_REQUIRED,
                ]);
        } elseif ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $submissions = $query->paginate(15)->withQueryString();

        return view('instructor.submissions.index', compact('classes', 'submissions'));
    }
}
