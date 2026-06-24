<?php

namespace App\Services;

use App\Models\ChallengeCategory;
use App\Models\User;
use Illuminate\Support\Collection;

class AdvancedTopicRecommendationService
{
    private const PATHS = [
        'newbie' => 'Newbie',
        'university-student' => 'University Student',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'professional' => 'Professional',
    ];

    public function recommendationsFor(User $user): Collection
    {
        $unlockService = app(ChallengePathUnlockService::class);
        $items = collect();

        foreach (['mcq' => 'MCQ Challenges', 'coding' => 'Coding Challenges'] as $track => $trackLabel) {
            foreach (array_keys(self::PATHS) as $slug) {
                $summary = $unlockService->performanceSummary($user, $slug, $track);
                if (!($summary['exceptional'] ?? false)) {
                    continue;
                }

                $targetSlug = $this->nextNextSlug($slug) ?? $this->nextSlug($slug);
                if (!$targetSlug) {
                    continue;
                }

                $items->push([
                    'track' => $track,
                    'track_label' => $trackLabel,
                    'source_slug' => $slug,
                    'source_name' => self::PATHS[$slug],
                    'target_slug' => $targetSlug,
                    'target_name' => self::PATHS[$targetSlug],
                    'score' => round((float) ($summary['average_score_percent'] ?? 0), 2),
                    'time_ratio' => $summary['average_time_ratio'] ?? null,
                    'attempts' => $summary['average_attempts'] ?? null,
                    'reason' => 'Exceptional performance: high accuracy plus efficient completion time.',
                    'url' => $track === 'coding'
                        ? route('challenges.coding.map', $targetSlug)
                        : route('challenges.map', $targetSlug),
                ]);
            }
        }

        return $items->unique(fn ($item) => $item['track'] . ':' . $item['target_slug'])->values();
    }

    private function nextSlug(string $slug): ?string
    {
        $keys = array_keys(self::PATHS);
        $index = array_search($slug, $keys, true);
        return $index !== false && isset($keys[$index + 1]) ? $keys[$index + 1] : null;
    }

    private function nextNextSlug(string $slug): ?string
    {
        $keys = array_keys(self::PATHS);
        $index = array_search($slug, $keys, true);
        return $index !== false && isset($keys[$index + 2]) ? $keys[$index + 2] : null;
    }
}
