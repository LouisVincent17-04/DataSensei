<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Advanced Topic Recommendations (DataSensei Updates 4, revised rule).
 *
 * A level is recommended, and opened, when the learner has at least two
 * different qualifying modules in the level below it:
 *
 *   Newbie        80% score, 50% time consumed or less  ->  Intermediate
 *   Intermediate  75% score, 70% time consumed or less  ->  Advanced
 *   Advanced      70% score, 80% time consumed or less  ->  Professional
 *
 * The rules and the unlocking live in ChallengePathUnlockService; this service
 * only presents them: how many qualifying modules the learner has, what is
 * left, and each module's result.
 */
class AdvancedTopicRecommendationService
{
    private const TRACKS = [
        'mcq' => 'MCQ challenges',
        'coding' => 'Coding challenges',
    ];

    public function __construct(private readonly ChallengePathUnlockService $unlocks)
    {
    }

    /**
     * Every rule for both tracks, with the learner's progress toward it.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function progressFor(User $user): Collection
    {
        $rows = collect();

        foreach (self::TRACKS as $track => $trackLabel) {
            $locks = $this->unlocks->buildPathLocks($user, $track);

            foreach (ChallengePathUnlockService::ADVANCEMENT_RULES as $source => $rule) {
                $summary = $this->unlocks->performanceSummary($user, $source, $track);
                $target = $rule['target'];
                $sourceName = $this->unlocks->pathName($source);
                $targetName = $this->unlocks->pathName($target);
                $sourceOpen = (bool) ($locks[$source]['unlocked'] ?? false);
                $targetLock = $locks[$target] ?? [];
                $count = (int) $summary['qualifying_modules'];
                $eligible = ($targetLock['unlock_type'] ?? null) === 'qualifying_modules';

                if ($eligible) {
                    $status = 'Unlocked';
                    $message = "You have {$count} qualifying {$sourceName} modules. {$targetName} is unlocked: start with its first module.";
                } elseif ($targetLock['unlocked'] ?? false) {
                    $status = 'Open';
                    $message = "{$targetName} is already open because you started it earlier. You have {$count} of "
                        .ChallengePathUnlockService::REQUIRED_QUALIFYING_MODULES." qualifying {$sourceName} modules.";
                } elseif (! $sourceOpen) {
                    $status = 'Locked';
                    $message = "Unlock {$sourceName} first. ".$this->unlocks->ruleSentence($source);
                } else {
                    $status = 'Not yet';
                    $message = $this->unlocks->progressMessage($source, $count);
                }

                $rows->push([
                    'track' => $track,
                    'track_label' => $trackLabel,
                    'source_slug' => $source,
                    'source_name' => $sourceName,
                    'target_slug' => $target,
                    'target_name' => $targetName,
                    'qualifying' => $count,
                    'required' => ChallengePathUnlockService::REQUIRED_QUALIFYING_MODULES,
                    'total_modules' => (int) $summary['total_items'],
                    'required_score' => $rule['score'],
                    'required_time' => $rule['time'],
                    'eligible' => $eligible,
                    'status' => $status,
                    'message' => $message,
                    'qualifying_modules' => collect($summary['modules'])->where('qualifies', true)->values()->all(),
                    'url' => $track === 'coding'
                        ? route('challenges.coding.map', $target)
                        : route('challenges.map', $target),
                ]);
            }
        }

        return $rows;
    }

    /**
     * The levels the learner has qualified for.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function recommendationsFor(User $user): Collection
    {
        return $this->progressFor($user)->where('eligible', true)->values();
    }

    /**
     * Results of the modules the learner has worked on in each ruled level,
     * with whether each one counts toward the next level.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function moduleResultsFor(User $user): Collection
    {
        $rows = collect();

        foreach (self::TRACKS as $track => $trackLabel) {
            foreach (ChallengePathUnlockService::ADVANCEMENT_RULES as $source => $rule) {
                $summary = $this->unlocks->performanceSummary($user, $source, $track);

                foreach ($summary['modules'] as $module) {
                    $started = $module['finished'] || (int) ($module['items_finished'] ?? 0) > 0;
                    if (! $started) {
                        continue;
                    }

                    $note = $module['note'];
                    if (! $module['finished'] && $module['items_total'] !== null) {
                        $note = 'Not finished yet ('.$module['items_finished'].' of '.$module['items_total'].' items submitted)';
                    }

                    $rows->push([
                        'track' => $track,
                        'track_label' => $trackLabel,
                        'level' => $this->unlocks->pathName($source),
                        'module' => $module['title'],
                        'score' => $module['score'],
                        'time' => $module['time'],
                        'qualifies' => $module['qualifies'],
                        'note' => $note,
                        'required_score' => $rule['score'],
                        'required_time' => $rule['time'],
                    ]);
                }
            }
        }

        return $rows;
    }
}
