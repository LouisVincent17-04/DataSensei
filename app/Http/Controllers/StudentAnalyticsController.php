<?php

namespace App\Http\Controllers;

use App\Services\StudentAnalyticsService;
use Illuminate\Support\Facades\Auth;

class StudentAnalyticsController extends Controller
{
    public function index(StudentAnalyticsService $service)
    {
        $analytics = $service->build(Auth::user());

        return view('student.analytics.index', compact('analytics'));
    }
}
