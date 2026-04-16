<?php

namespace App\Http\Controllers;

use App\Models\ChallengeCategory;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengesController extends Controller
{
    // ─── PATH SELECTION PAGE ────────────────────────────────────────────────────

    public function index()
    {
        $categories = ChallengeCategory::orderBy('order_index', 'asc')->get();

        $hasUniversity = Auth::check() && !empty(Auth::user()->organization_id);

        if ($categories->isEmpty()) {
            $categories = collect([
                (object)[
                    'name' => 'Newbie',
                    'slug' => 'newbie',
                    'target_audience' => 'Just starting, little to no background in data',
                    'description' => 'Learn the absolute basics of Python and data literacy from scratch. No prior coding experience required.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>'
                ],
                (object)[
                    'name' => 'University Student',
                    'slug' => 'university-student',
                    'target_audience' => 'Currently studying Data Science, CS, or related fields',
                    'description' => 'Bridge the gap between academic theory and practical application. Focus on algorithms, stats, and real coding.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>'
                ],
                (object)[
                    'name' => 'Intermediate',
                    'slug' => 'intermediate',
                    'target_audience' => 'Has basics (Python, stats) and can do small projects',
                    'description' => 'Level up your skills. Dive into data wrangling, machine learning fundamentals, and visualization techniques.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>'
                ],
                (object)[
                    'name' => 'Advanced',
                    'slug' => 'advanced',
                    'target_audience' => 'Strong skills, can build models and real-world systems',
                    'description' => 'Tackle complex datasets, deep learning, optimization, and scalable data pipelines.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" /><line stroke-linecap="round" stroke-linejoin="round" x1="8.59" y1="13.51" x2="15.42" y2="17.49" /><line stroke-linecap="round" stroke-linejoin="round" x1="15.41" y1="6.51" x2="8.59" y2="10.49" /></svg>'
                ],
                (object)[
                    'name' => 'Professional',
                    'slug' => 'professional',
                    'target_audience' => 'Working in industry (Data Analyst, Data Scientist, ML Engineer)',
                    'description' => 'Master MLOps, system architecture, big data frameworks, and high-level strategy for enterprise systems.',
                    'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7.52a2 2 0 00-1-1.73l-6-3.46a2 2 0 00-2 0l-6 3.46a2 2 0 00-1 1.73v6.93a2 2 0 001 1.73l6 3.46a2 2 0 002 0l6-3.46a2 2 0 001-1.73V7.52z" /></svg>'
                ],
            ]);
        }

        return view('student.challenges', compact('categories', 'hasUniversity'));
    }

    // ─── CHALLENGE MAP ──────────────────────────────────────────────────────────

    public function map($slug)
    {
        $category = ChallengeCategory::where('slug', $slug)->firstOrFail();

        $challenges = Challenge::where('challenge_category_id', $category->id)
                               ->orderBy('id', 'asc')
                               ->get();

        /*
         * FIX: A challenge is only "completed" (and unlocks the next one) when
         * the user has a PASSING attempt (score / total_questions >= 0.7).
         * Previously, any attempt — including score=0 — was counted as done,
         * which meant retrying showed "Submit Challenge" instead of starting
         * fresh because the node was already green.
         */
        $completedChallengeIds = [];

        if (Auth::check()) {
            foreach ($challenges as $ch) {
                $totalQuestions = $ch->questions()->count();

                if ($totalQuestions === 0) continue;

                $passingThreshold = (int) ceil($totalQuestions * 0.7);

                $hasPassed = DB::table('challenge_user')
                    ->where('user_id', Auth::id())
                    ->where('challenge_id', $ch->id)
                    ->where('score', '>=', $passingThreshold)
                    ->exists();

                if ($hasPassed) {
                    $completedChallengeIds[] = $ch->id;
                }
            }
        }

        // Also pass best scores per challenge so the map can show them
        $bestScores = [];
        if (Auth::check()) {
            $rows = DB::table('challenge_user')
                ->where('user_id', Auth::id())
                ->whereIn('challenge_id', $challenges->pluck('id'))
                ->select('challenge_id', DB::raw('MAX(score) as best_score'), DB::raw('MAX(xp_awarded) as best_xp'))
                ->groupBy('challenge_id')
                ->get();

            foreach ($rows as $row) {
                $bestScores[$row->challenge_id] = [
                    'score'  => $row->best_score,
                    'xp'     => $row->best_xp,
                ];
            }
        }

        return view('student.challenges-map', compact(
            'slug', 'category', 'challenges', 'completedChallengeIds', 'bestScores'
        ));
    }

    // ─── ORGANIZATION ENROLL ────────────────────────────────────────────────────

    public function enrollOrganization(Request $request)
    {
        $request->validate(['invite_code' => 'required|string']);

        $org = DB::table('organizations')->where('invite_code', $request->invite_code)->first();

        if ($org) {
            $user = Auth::user();
            $user->organization_id = $org->id;
            $user->save();

            return back()->with('success', 'Successfully enrolled in ' . $org->name . '! Your university path is now unlocked.');
        }

        return back()->withErrors(['invite_code' => 'Invalid invite code. Please check and try again.']);
    }

    // ─── SHOW QUIZ ──────────────────────────────────────────────────────────────

    public function showQuiz($slug, $challenge_id)
    {
        $challenge = Challenge::with(['questions.options' => function ($q) {
            $q->inRandomOrder();
        }])->findOrFail($challenge_id);

        return view('student.challenge-quiz', compact('slug', 'challenge'));
    }

    // ─── SUBMIT QUIZ ────────────────────────────────────────────────────────────

    public function submitQuiz(Request $request, $slug, $challenge_id)
    {
        $challenge = Challenge::with('questions.options')->findOrFail($challenge_id);
        $user      = Auth::user();

        $answers        = $request->input('answers', []);
        $timeTaken      = (int) $request->input('time_taken_seconds', $challenge->time_limit_seconds);
        $correctCount   = 0;
        $totalQuestions = $challenge->questions->count();

        // Grade
        foreach ($challenge->questions as $question) {
            if (isset($answers[$question->id])) {
                $selected = $question->options->firstWhere('id', $answers[$question->id]);
                if ($selected && $selected->is_correct) {
                    $correctCount++;
                }
            }
        }

        $scorePercentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) : 0;
        $earnedXp        = (int) round($challenge->base_xp * $scorePercentage);

        // Time bonus only on a passing attempt
        if ($scorePercentage >= 0.7) {
            $secondsSaved = max(0, $challenge->time_limit_seconds - $timeTaken);
            $earnedXp    += (int) round($secondsSaved * 2);
        }

        /*
         * FIX: Use updateOrInsert keyed on (user_id, challenge_id) so that a
         * retry REPLACES the previous attempt rather than stacking duplicates.
         * We only update if the new score is better (so the student can't lose
         * progress by retrying poorly).
         */
        $existing = DB::table('challenge_user')
            ->where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->first();

        if ($existing) {
            // Only overwrite if this attempt scored higher
            if ($correctCount > $existing->score) {
                // Refund old XP, grant new XP
                $xpDiff = $earnedXp - $existing->xp_awarded;

                DB::table('challenge_user')
                    ->where('user_id', $user->id)
                    ->where('challenge_id', $challenge->id)
                    ->update([
                        'score'              => $correctCount,
                        'time_taken_seconds' => $timeTaken,
                        'xp_awarded'         => $earnedXp,
                        'updated_at'         => now(),
                    ]);

                if ($xpDiff > 0) {
                    $user->increment('xp', $xpDiff);
                }

                $message = "New best! Score: {$correctCount}/{$totalQuestions}. You earned {$earnedXp} XP total.";
            } else {
                $message = "Score: {$correctCount}/{$totalQuestions}. Your previous best stands — keep trying!";
            }
        } else {
            // First attempt — just insert
            DB::table('challenge_user')->insert([
                'user_id'            => $user->id,
                'challenge_id'       => $challenge->id,
                'score'              => $correctCount,
                'time_taken_seconds' => $timeTaken,
                'xp_awarded'         => $earnedXp,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            $user->increment('xp', $earnedXp);

            $passed  = $scorePercentage >= 0.7;
            $message = $passed
                ? "Challenge Passed! Score: {$correctCount}/{$totalQuestions}. You earned {$earnedXp} XP."
                : "Score: {$correctCount}/{$totalQuestions}. You need 70% to pass — retry when you're ready!";
        }

        return redirect()->route('challenges.map', $slug)->with('success', $message);
    }
}