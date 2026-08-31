<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorChallengePoolController extends Controller
{
    private const INSTRUCTOR_CATEGORY_SLUG = 'university-student';

    public function index(Request $request): View
    {
        $universityCategory = ChallengeCategory::query()
            ->where('slug', self::INSTRUCTOR_CATEGORY_SLUG)
            ->first();

        $query = Challenge::query()
            ->active()
            ->whereHas('category', function ($categoryQuery): void {
                $categoryQuery->where('slug', self::INSTRUCTOR_CATEGORY_SLUG);
            })
            ->with('category')
            ->withCount(['questions', 'codingQuestions'])
            ->orderBy('is_coding_challenge')
            ->orderBy('order_index')
            ->orderBy('version_no');

        if ($request->filled('type')) {
            $query->where('is_coding_challenge', $request->input('type') === 'coding');
        }

        $challenges = $query->paginate(15)->withQueryString();

        return view('instructor.challenges.index', compact('universityCategory', 'challenges'));
    }

    public function show(Challenge $challenge): View
    {
        $challenge->loadMissing('category');

        abort_unless(
            $challenge->is_active
            && $challenge->category?->slug === self::INSTRUCTOR_CATEGORY_SLUG,
            404
        );
        abort_if($challenge->is_coding_challenge, 404);

        $challenge->load([
            'questions.options',
        ]);

        return view('instructor.challenges.show', compact('challenge'));
    }
}
