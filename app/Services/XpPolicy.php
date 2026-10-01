<?php

namespace App\Services;

use App\Models\Challenge;

/**
 * Where XP may come from (DataSensei Updates 5, task 1).
 *
 * XP is controlled by the platform (the admin) and is the same for every
 * learner, enrolled in a class or not. It comes only from DataSensei platform
 * content: platform MCQ and coding challenges on levels open to everyone,
 * lessons of the public modules, the Python IDE and SQL Sandbox, and the
 * achievement and mission rewards the admin sets up, counted from that same
 * activity.
 *
 * Nothing tied to a class or an instructor gives XP, directly or through
 * achievements, missions or streaks: instructor-built challenges, the
 * University Student level (it needs a class), assignments, assessments,
 * grading, feedback, module assignment and so on. Those features keep
 * working; they give 0 XP.
 */
final class XpPolicy
{
    /** Levels that are only reachable through a class enrollment. */
    public const CLASS_ONLY_LEVELS = ['university-student'];

    /** Whether finishing this challenge may award XP. */
    public static function challengeAwardsXp(Challenge $challenge): bool
    {
        if ($challenge->isInstructorOwned()) {
            return false;
        }

        $challenge->loadMissing('category');

        return ! in_array((string) $challenge->category?->slug, self::CLASS_ONLY_LEVELS, true);
    }

    /**
     * Selects the ids of the challenges that award XP, for a whereIn()
     * subquery: platform challenges outside the class-only levels.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    public static function selectXpChallengeIds($query): void
    {
        $query->select('challenges.id')
            ->from('challenges')
            ->whereNotIn('challenges.challenge_category_id', function ($categories): void {
                $categories->select('challenge_categories.id')
                    ->from('challenge_categories')
                    ->whereIn('challenge_categories.slug', self::CLASS_ONLY_LEVELS);
            });

        $query->where(function ($visibility): void {
            $visibility->whereNull('challenges.visibility')
                ->orWhere('challenges.visibility', Challenge::VISIBILITY_PLATFORM);
        });
    }

    /**
     * Selects the ids of the coding questions whose challenge awards XP.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    public static function selectXpCodingQuestionIds($query): void
    {
        $query->select('coding_questions.id')
            ->from('coding_questions')
            ->whereIn('coding_questions.challenge_id', function ($challenges): void {
                self::selectXpChallengeIds($challenges);
            });
    }
}
