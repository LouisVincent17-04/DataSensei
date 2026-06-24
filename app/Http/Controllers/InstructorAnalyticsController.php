<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\StudentPerformanceClusteringService;
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
            $snapshots = $clustering->refreshForClass($selectedClass);
        }

        $stats = [
            'students' => $snapshots->count(),
            'avg_score' => round((float) $snapshots->avg('average_score_percent'), 2),
            'avg_engagement' => round((float) $snapshots->avg('engagement_score'), 2),
            'at_risk' => $snapshots->where('risk_level', 'high')->count(),
        ];

        return view('instructor.analytics.index', compact('classes', 'selectedClass', 'snapshots', 'stats'));
    }
}
