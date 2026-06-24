<?php

namespace App\Http\Controllers;

use App\Services\AdvancedTopicRecommendationService;
use Illuminate\Support\Facades\Auth;

class AdvancedTopicRecommendationController extends Controller
{
    public function index(AdvancedTopicRecommendationService $service)
    {
        $recommendations = $service->recommendationsFor(Auth::user());

        return view('student.advanced-topic-recommendations', compact('recommendations'));
    }
}
