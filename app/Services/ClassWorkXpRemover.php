<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Takes back the XP that was earned on class work before DataSensei
 * Updates 5 made class work give no XP.
 *
 * Only XP that can be traced exactly is removed: the XP recorded on
 * challenge attempts (challenge_attempts.xp_awarded) and coding submissions
 * (coding_submissions.xp_earned) of instructor-built challenges and of the
 * University Student level. Achievement and mission XP cannot be traced back
 * to class work, so it stays.
 *
 * Coding XP was credited as the gain over the learner's best earlier result
 * on the same problem, so the XP a learner received for one problem is the
 * highest xp_earned of their submissions on it.
 *
 * After the XP is taken back the recorded XP on those rows is set to 0, so
 * running it again removes nothing more. A balance never goes below 0.
 */
class ClassWorkXpRemover
{
    /**
     * @return array{users: int, xp: int, attempts: int, submissions: int, by_user: array<int, array{email: string, xp: int}>}
     */
    public function run(bool $dryRun = false): array
    {
        $summary = ['users' => 0, 'xp' => 0, 'attempts' => 0, 'submissions' => 0, 'by_user' => []];

        $classChallenges = fn ($query) => $this->selectClassWorkChallengeIds($query);

        $mcq = DB::table('challenge_attempts')
            ->whereIn('challenge_id', $classChallenges)
            ->where('xp_awarded', '>', 0)
            ->select('user_id', DB::raw('SUM(xp_awarded) as xp'), DB::raw('COUNT(*) as rows_count'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $codingPerQuestion = DB::table('coding_submissions')
            ->whereIn('coding_question_id', function ($questions) use ($classChallenges): void {
                $questions->select('coding_questions.id')
                    ->from('coding_questions')
                    ->whereIn('coding_questions.challenge_id', $classChallenges);
            })
            ->where('xp_earned', '>', 0)
            ->select('user_id', 'coding_question_id', DB::raw('MAX(xp_earned) as xp'), DB::raw('COUNT(*) as rows_count'))
            ->groupBy('user_id', 'coding_question_id')
            ->get();

        $coding = [];
        foreach ($codingPerQuestion as $row) {
            $userId = (int) $row->user_id;
            $coding[$userId]['xp'] = ($coding[$userId]['xp'] ?? 0) + (int) $row->xp;
            $coding[$userId]['rows'] = ($coding[$userId]['rows'] ?? 0) + (int) $row->rows_count;
        }

        $userIds = collect(array_keys($coding))->merge($mcq->keys()->map(fn ($id): int => (int) $id))->unique()->sort()->values();

        foreach ($userIds as $userId) {
            $xp = (int) ($mcq[$userId]->xp ?? 0) + (int) ($coding[$userId]['xp'] ?? 0);
            if ($xp <= 0) {
                continue;
            }

            $removed = $dryRun ? $this->preview($userId, $xp) : $this->takeBack($userId, $classChallenges);

            if ($removed <= 0) {
                continue;
            }

            $summary['users']++;
            $summary['xp'] += $removed;
            $summary['attempts'] += (int) ($mcq[$userId]->rows_count ?? 0);
            $summary['submissions'] += (int) ($coding[$userId]['rows'] ?? 0);
            $summary['by_user'][$userId] = [
                'email' => (string) User::query()->whereKey($userId)->value('email'),
                'xp' => $removed,
            ];
        }

        return $summary;
    }

    /** The XP that would be removed, limited by the current balance. */
    private function preview(int $userId, int $xp): int
    {
        return min($xp, max(0, (int) User::query()->whereKey($userId)->value('xp')));
    }

    /**
     * Removes one learner's class-work XP and zeroes the recorded XP on those
     * rows, in one transaction, reading the rows again under the user lock so
     * a concurrent run cannot remove the same XP twice.
     */
    private function takeBack(int $userId, \Closure $classChallenges): int
    {
        return DB::transaction(function () use ($userId, $classChallenges): int {
            $user = User::query()->whereKey($userId)->lockForUpdate()->first();
            if (! $user) {
                return 0;
            }

            $mcqXp = (int) DB::table('challenge_attempts')
                ->where('user_id', $userId)
                ->whereIn('challenge_id', $classChallenges)
                ->where('xp_awarded', '>', 0)
                ->sum('xp_awarded');

            $codingXp = (int) DB::table('coding_submissions')
                ->where('user_id', $userId)
                ->whereIn('coding_question_id', function ($questions) use ($classChallenges): void {
                    $questions->select('coding_questions.id')
                        ->from('coding_questions')
                        ->whereIn('coding_questions.challenge_id', $classChallenges);
                })
                ->where('xp_earned', '>', 0)
                ->groupBy('coding_question_id')
                ->select(DB::raw('MAX(xp_earned) as best'))
                ->pluck('best')
                ->sum();

            $total = $mcqXp + $codingXp;
            if ($total <= 0) {
                return 0;
            }

            $removed = min($total, max(0, (int) $user->xp));
            $user->forceFill(['xp' => max(0, (int) $user->xp - $total)])->save();

            DB::table('challenge_attempts')
                ->where('user_id', $userId)
                ->whereIn('challenge_id', $classChallenges)
                ->where('xp_awarded', '>', 0)
                ->update(['xp_awarded' => 0]);

            DB::table('coding_submissions')
                ->where('user_id', $userId)
                ->whereIn('coding_question_id', function ($questions) use ($classChallenges): void {
                    $questions->select('coding_questions.id')
                        ->from('coding_questions')
                        ->whereIn('coding_questions.challenge_id', $classChallenges);
                })
                ->where('xp_earned', '>', 0)
                ->update(['xp_earned' => 0]);

            return $removed;
        }, 3);
    }

    /**
     * Challenges that are class work: instructor-built ones and every
     * challenge on a class-only level (University Student).
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    private function selectClassWorkChallengeIds($query): void
    {
        $query->select('challenges.id')
            ->from('challenges')
            ->where(function ($classWork): void {
                $classWork->where('challenges.visibility', \App\Models\Challenge::VISIBILITY_INSTRUCTOR)
                    ->orWhereIn('challenges.challenge_category_id', function ($categories): void {
                        $categories->select('challenge_categories.id')
                            ->from('challenge_categories')
                            ->whereIn('challenge_categories.slug', XpPolicy::CLASS_ONLY_LEVELS);
                    });
            });
    }
}
