<?php

namespace App\Http\Controllers;

use App\Services\AdvancedTopicRecommendationService;
use Illuminate\Support\Facades\Auth;

class AdvancedTopicRecommendationController extends Controller
{
    public function index(AdvancedTopicRecommendationService $service)
    {
        $progress = $service->progressFor(Auth::user());
        $recommendations = $progress->where('eligible', true)->values();
        $moduleResults = $service->moduleResultsFor(Auth::user());

        return view('student.advanced-topic-recommendations', compact('recommendations', 'progress', 'moduleResults'));
    }
}
