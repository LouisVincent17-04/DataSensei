<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use Illuminate\Http\Request;

class InstructorChallengePoolController extends Controller
{
    public function index(Request $request)
    {
        $categories = ChallengeCategory::orderBy('order_index')->get();
        $query = Challenge::with('category')
            ->withCount(['questions', 'codingQuestions'])
            ->orderBy('is_coding_challenge')
            ->orderBy('challenge_category_id')
            ->orderBy('order_index');

        if ($request->filled('category_id')) {
            $query->where('challenge_category_id', $request->integer('category_id'));
        }

        if ($request->filled('type')) {
            $query->where('is_coding_challenge', $request->input('type') === 'coding');
        }

        $challenges = $query->paginate(15)->withQueryString();

        return view('instructor.challenges.index', compact('categories', 'challenges'));
    }
}
