<?php

namespace App\Services;

use App\Support\SchemaInspector;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * The next-module rule inside one difficulty level (DataSensei Updates 4).
 *
 * It is separate from the next-level rule in ChallengePathUnlockService:
 *
 *  - MCQ: the first module of a level is open; each next module opens once
 *    the learner passes the one before it (70% or more on any finished
 *    attempt, retakes included). A newly unlocked level therefore starts with
 *    only its first module open.
 *  - Coding: the same order the coding map has always used. The first
 *    challenge is open, and the next one opens when the one before it is
 *    solved or started (a run whose retakes are used up must not block the
 *    level).
 *
 * A module the learner already worked on stays open. The challenge maps draw
 * these states; this service lets the quiz pages and the dashboard enforce
 * them, so a direct link cannot skip ahead. Class assignments and
 * instructor-built challenges are checked by their own rules.
 */
class ChallengeModuleAccessService
{
    private array $cache = [];

    /** Whether the learner may open (or keep working on) this challenge. */
    public function isOpen(User $user, Challenge $challenge): bool
    {
        if ($challenge->isInstructorOwned()) {
            return true;
        }

        $states = $this->states($user, (int) $challenge->challenge_category_id, (bool) $challenge->is_coding_challenge);

        // Only the active platform modules of a level are ordered. Anything
        // else (an older version, a draft) is left to the existing checks.
        return $states[(int) $challenge->id]['open'] ?? true;
    }

    /**
     * Open and completed state of every active platform module of a level.
     *
     * @return array<int, array{open: bool, completed: bool}>
     */
    public function states(User $user, int $categoryId, bool $coding): array
    {
        $key = $user->id.':'.$categoryId.':'.($coding ? 'coding' : 'mcq');

        return $this->cache[$key] ??= $coding
            ? $this->codingStates($user, $categoryId)
            : $this->mcqStates($user, $categoryId);
    }

    private function mcqStates(User $user, int $categoryId): array
    {
        $challenges = Challenge::withCount('questions')
            ->platform()
            ->where('challenge_category_id', $categoryId)
            ->where('is_coding_challenge', false)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();

        if ($challenges->isEmpty()) {
            return [];
        }

        $ids = $challenges->pluck('id');
        $attempts = ChallengeAttempt::query()
            ->where('user_id', $user->id)
            ->whereIn('challenge_id', $ids)
            ->get(['challenge_id', 'status', 'score', 'total_questions'])
            ->groupBy('challenge_id');

        $legacy = SchemaInspector::hasTable('challenge_user')
            ? DB::table('challenge_user')
                ->where('user_id', $user->id)
                ->whereIn('challenge_id', $ids)
                ->get(['challenge_id', 'score'])
                ->keyBy('challenge_id')
            : collect();

        $states = [];
        $firstIncompleteSeen = false;

        foreach ($challenges as $challenge) {
            $list = $attempts->get($challenge->id, collect());

            $completed = $list->contains(fn (ChallengeAttempt $attempt): bool =>
                in_array($attempt->status, ['submitted', 'expired'], true)
                && (int) $attempt->total_questions > 0
                && (int) $attempt->score * 100 >= (int) $attempt->total_questions * ChallengePathUnlockService::PASSING_PERCENT
            );

            $legacyRow = $list->isEmpty() ? $legacy->get($challenge->id) : null;
            if (! $completed && $legacyRow && (int) $challenge->questions_count > 0) {
                $completed = (int) $legacyRow->score >= (int) ceil($challenge->questions_count * 0.70);
            }

            $open = $completed || $list->isNotEmpty() || $legacyRow !== null;

            if (! $completed && ! $firstIncompleteSeen) {
                $open = true;
                $firstIncompleteSeen = true;
            }

            $states[(int) $challenge->id] = ['open' => $open, 'completed' => $completed];
        }

        return $states;
    }

    private function codingStates(User $user, int $categoryId): array
    {
        $challenges = Challenge::query()
            ->platform()
            ->where('challenge_category_id', $categoryId)
            ->where('is_coding_challenge', true)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->orderBy('id')
            ->get(['id']);

        if ($challenges->isEmpty()) {
            return [];
        }

        $questionsByChallenge = CodingQuestion::query()
            ->whereIn('challenge_id', $challenges->pluck('id'))
            ->get(['id', 'challenge_id'])
            ->groupBy('challenge_id');
        $questionIds = $questionsByChallenge->flatten()->pluck('id');

        $solved = $questionIds->isEmpty() ? collect() : CodingSubmission::query()
            ->where('user_id', $user->id)
            ->whereIn('coding_question_id', $questionIds)
            ->where('status', 'passed')
            ->where('voided', false)
            ->distinct()
            ->pluck('coding_question_id')
            ->map(fn ($id): int => (int) $id)
            ->flip();
        $submitted = $questionIds->isEmpty() ? collect() : CodingSubmission::query()
            ->where('user_id', $user->id)
            ->whereIn('coding_question_id', $questionIds)
            ->distinct()
            ->pluck('coding_question_id')
            ->map(fn ($id): int => (int) $id)
            ->flip();
        $started = $questionIds->isEmpty() ? collect() : CodingQuestionAttempt::query()
            ->where('user_id', $user->id)
            ->whereIn('coding_question_id', $questionIds)
            ->pluck('coding_question_id')
            ->map(fn ($id): int => (int) $id)
            ->flip();

        $states = [];
        $previousCompleted = false;
        $previousStarted = false;

        foreach ($challenges->values() as $index => $challenge) {
            $ids = $questionsByChallenge->get($challenge->id, collect())->pluck('id')->map(fn ($id): int => (int) $id);

            $completed = $ids->isNotEmpty() && $ids->every(fn (int $id): bool => $solved->has($id));
            $inProgress = $ids->contains(fn (int $id): bool => $started->has($id));
            $worked = $ids->contains(fn (int $id): bool => $submitted->has($id));

            $open = $index === 0 || $completed || $inProgress || $worked || $previousCompleted || $previousStarted;

            $states[(int) $challenge->id] = ['open' => $open, 'completed' => $completed];
            $previousCompleted = $completed;
            $previousStarted = $inProgress;
        }

        return $states;
    }
}
