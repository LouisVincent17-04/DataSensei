<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Services\ClassChallengePractice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The platform's University Student challenges, for instructors to review
 * and share with their classes for practice (DataSensei Updates 9). Other
 * instructors' own challenges are never listed here.
 */
class InstructorChallengePoolController extends Controller
{
    private const INSTRUCTOR_CATEGORY_SLUG = ClassChallengePractice::CATEGORY_SLUG;

    public function index(Request $request, ClassChallengePractice $practice): View
    {
        $universityCategory = ChallengeCategory::query()
            ->where('slug', self::INSTRUCTOR_CATEGORY_SLUG)
            ->first();

        $query = Challenge::query()
            ->platform()
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

        $classIds = $practice->classesFor((int) Auth::id())->pluck('id');
        $sharedCounts = ClassChallengeAssignment::query()
            ->whereIn('challenge_id', $challenges->getCollection()->pluck('id'))
            ->whereIn('class_id', $classIds)
            ->where('status', ClassChallengeAssignment::STATUS_PUBLISHED)
            ->get(['challenge_id'])
            ->countBy('challenge_id')
            ->all();

        return view('instructor.challenges.index', compact('universityCategory', 'challenges', 'sharedCounts'));
    }

    public function show(Challenge $challenge, ClassChallengePractice $practice): View
    {
        $challenge->loadMissing('category');

        abort_unless(
            ! $challenge->isInstructorOwned()
            && $challenge->is_active
            && $challenge->category?->slug === self::INSTRUCTOR_CATEGORY_SLUG,
            404
        );

        $challenge->load($challenge->is_coding_challenge
            ? ['codingQuestions.testCases']
            : ['questions.options']);

        return view('instructor.challenges.show', [
            'challenge' => $challenge,
            'practiceClasses' => $practice->classesFor((int) Auth::id()),
            'sharedClassIds' => $practice->sharedClassIds($challenge, (int) Auth::id()),
        ]);
    }
}
