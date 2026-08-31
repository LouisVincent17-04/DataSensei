<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\IntendedLearningOutcome;
use App\Models\TableOfSpecification;
use App\Models\TableOfSpecificationRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TableOfSpecificationService
{
    public const DIFFICULTIES = [
        'newbie' => 'Newbie',
        'university-student' => 'University Student',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'professional' => 'Professional',
    ];

    public const COGNITIVE_LEVELS = [
        'remember' => [
            'label' => 'Remember',
            'explanation' => 'Recall facts, terms, and definitions.',
            'difficulty_slug' => 'newbie',
        ],
        'understand' => [
            'label' => 'Understand',
            'explanation' => 'Explain concepts in your own words.',
            'difficulty_slug' => 'university-student',
        ],
        'apply' => [
            'label' => 'Apply',
            'explanation' => 'Use knowledge to solve a problem.',
            'difficulty_slug' => 'intermediate',
        ],
        'analyze' => [
            'label' => 'Analyze',
            'explanation' => 'Examine relationships, patterns, or differences.',
            'difficulty_slug' => 'advanced',
        ],
    ];

    public const DEFAULT_COGNITIVE_DISTRIBUTION = [
        'remember' => 15,
        'understand' => 25,
        'apply' => 40,
        'analyze' => 20,
    ];

    /**
     * Create the teacher-friendly TOS blueprint used by the 3-step wizard.
     */
    public function createBlueprint(
        ?int $classId,
        int $moduleNo,
        string $title,
        int $totalItems,
        ?string $customCoverage = null
    ): TableOfSpecification {
        $customCoverage = $moduleNo === 0 ? trim((string) $customCoverage) : null;

        if ($moduleNo === 0 && $customCoverage === '') {
            throw ValidationException::withMessages([
                'custom_coverage' => 'Enter the module or coverage you want to use.',
            ]);
        }

        return DB::transaction(function () use ($classId, $moduleNo, $title, $totalItems, $customCoverage) {
            if ($classId !== null) {
                ClassRoom::query()
                    ->whereKey($classId)
                    ->where('instructor_id', Auth::id())
                    ->where('is_archived', false)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $tos = TableOfSpecification::create([
                'class_id' => $classId,
                'module_no' => $moduleNo,
                'custom_coverage' => $customCoverage,
                'total_items' => $totalItems,
                'title' => trim($title),
                'status' => 'draft',
                'cognitive_distribution' => self::DEFAULT_COGNITIVE_DISTRIBUTION,
                'created_by' => Auth::id(),
            ]);

            $this->buildSuggestedRows($tos, self::DEFAULT_COGNITIVE_DISTRIBUTION);
            $this->syncStatus($tos);

            return $tos->fresh()->load('rows.ilo');
        }, 3);
    }

    /**
     * Backward-compatible wrapper for older callers.
     */
    public function createDefault(?int $classId, int $moduleNo): TableOfSpecification
    {
        return $this->createBlueprint(
            $classId,
            $moduleNo,
            'Module ' . $moduleNo . ' Table of Specification',
            50
        );
    }

    /**
     * Rebuild a modern, unlinked blueprint using the requested cognitive percentages.
     */
    public function applySuggestedDistribution(TableOfSpecification $tos, array $distribution, array $weights = []): TableOfSpecification
    {
        $distribution = $this->validatedDistribution($distribution);

        return DB::transaction(function () use ($tos, $distribution, $weights) {
            $lockedTos = TableOfSpecification::query()
                ->whereKey($tos->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedTos->assessments()->exists()) {
                throw ValidationException::withMessages([
                    'distribution' => 'This TOS is already linked to an assessment, so its blueprint can no longer be regenerated.',
                ]);
            }

            $lockedTos->update(['cognitive_distribution' => $distribution]);
            $lockedTos->rows()->delete();
            $this->buildSuggestedRows($lockedTos, $distribution, $weights);
            $this->syncStatus($lockedTos);

            return $lockedTos->fresh()->load('rows.ilo');
        }, 3);
    }

    /**
     * Save direct matrix edits. A partial allocation is allowed so the UI can show
     * "Needs Attention"; going over the requested total is blocked.
     */
    public function updateDistribution(TableOfSpecification $tos, array $counts): TableOfSpecification
    {
        return DB::transaction(function () use ($tos, $counts) {
            $lockedTos = TableOfSpecification::query()
                ->whereKey($tos->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedTos->assessments()->exists()) {
                throw ValidationException::withMessages([
                    'distribution' => 'This TOS is already linked to an assessment. Create a new TOS if you need a different blueprint.',
                ]);
            }

            $rows = TableOfSpecificationRow::query()
                ->where('table_of_specification_id', $lockedTos->id)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $sanitized = [];
            foreach ($counts as $rowId => $count) {
                $rowId = (int) $rowId;
                if (! $rows->has($rowId)) {
                    continue;
                }

                $sanitized[$rowId] = max(0, min(500, (int) $count));
            }

            if (count($sanitized) !== $rows->count()) {
                throw ValidationException::withMessages([
                    'distribution' => 'The TOS matrix is incomplete. Refresh the page and try again.',
                ]);
            }

            $assigned = array_sum($sanitized);
            $target = max(1, (int) $lockedTos->total_items);

            if ($assigned > $target) {
                throw ValidationException::withMessages([
                    'distribution' => "The matrix assigns {$assigned} items, but this TOS only allows {$target}. Reduce the item counts before continuing.",
                ]);
            }

            foreach ($sanitized as $rowId => $count) {
                $rows[$rowId]->update(['item_count' => $count]);
            }

            $this->syncStatus($lockedTos);

            return $lockedTos->fresh()->load('rows.ilo');
        }, 3);
    }

    public function isModernBlueprint(TableOfSpecification $tos): bool
    {
        $tos->loadMissing('rows');
        if ($tos->rows->isEmpty()) {
            return false;
        }

        $canonicalLabels = collect(self::COGNITIVE_LEVELS)
            ->pluck('label')
            ->map(fn (string $label) => strtolower($label))
            ->all();

        foreach ($tos->rows as $row) {
            if (! in_array(strtolower(trim((string) $row->cognitive_level)), $canonicalLabels, true)) {
                return false;
            }
        }

        foreach ($this->groupRowsByCompetency($tos->rows) as $rows) {
            $labels = $rows->pluck('cognitive_level')
                ->map(fn ($label) => strtolower(trim((string) $label)))
                ->sort()
                ->values()
                ->all();

            $expected = collect($canonicalLabels)->sort()->values()->all();
            if ($labels !== $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * Matrix-ready data for the teacher UI.
     */
    public function matrix(TableOfSpecification $tos): array
    {
        $tos->loadMissing('rows.ilo');
        $target = max(1, (int) ($tos->total_items ?: $tos->rows->sum('item_count')));
        $groups = [];

        foreach ($this->groupRowsByCompetency($tos->rows) as $key => $rows) {
            $first = $rows->first();
            $cells = [];

            foreach (self::COGNITIVE_LEVELS as $slug => $definition) {
                $cells[$slug] = $rows->first(
                    fn (TableOfSpecificationRow $row) => strtolower(trim((string) $row->cognitive_level)) === strtolower($definition['label'])
                );
            }

            $groupTotal = (int) $rows->sum('item_count');
            $groups[] = [
                'key' => $key,
                'ilo_id' => $first?->ilo_id,
                'title' => $first?->topic_title ?: ($first?->ilo?->title ?? 'Competency'),
                'objective' => $first?->learning_objective ?: ($first?->ilo?->description ?? null),
                'cells' => $cells,
                'total' => $groupTotal,
                'weight' => round(($groupTotal / $target) * 100, 1),
            ];
        }

        $columnTotals = [];
        foreach (self::COGNITIVE_LEVELS as $slug => $definition) {
            $columnTotals[$slug] = (int) $tos->rows
                ->filter(fn (TableOfSpecificationRow $row) => strtolower(trim((string) $row->cognitive_level)) === strtolower($definition['label']))
                ->sum('item_count');
        }

        return [
            'groups' => $groups,
            'column_totals' => $columnTotals,
            'assigned_items' => (int) $tos->rows->sum('item_count'),
            'target_items' => $target,
        ];
    }

    public function statusSummary(TableOfSpecification $tos): array
    {
        $tos->loadMissing('rows');

        return $this->statusFromCounts(
            (int) $tos->rows->sum('item_count'),
            max(1, (int) ($tos->total_items ?: $tos->rows->sum('item_count')))
        );
    }

    public function statusFromCounts(int $assigned, int $target): array
    {
        $remaining = $target - $assigned;

        if ($assigned > $target) {
            return [
                'code' => 'invalid',
                'label' => 'Invalid',
                'message' => "{$assigned} / {$target} items assigned. Reduce the distribution by " . abs($remaining) . ' item(s).',
                'can_generate' => false,
            ];
        }

        if ($assigned === $target && $target > 0) {
            return [
                'code' => 'complete',
                'label' => 'Complete',
                'message' => "{$assigned} / {$target} items assigned. Your blueprint is ready.",
                'can_generate' => true,
            ];
        }

        return [
            'code' => 'needs_attention',
            'label' => 'Needs Attention',
            'message' => "{$assigned} / {$target} items assigned. {$remaining} item(s) remaining.",
            'can_generate' => false,
        ];
    }

    public function explain(TableOfSpecification $tos): string
    {
        $matrix = $this->matrix($tos);
        $total = max(1, $matrix['target_items']);
        $assigned = $matrix['assigned_items'];

        $largestCognitive = collect($matrix['column_totals'])
            ->sortDesc()
            ->keys()
            ->first();
        $largestCognitiveLabel = self::COGNITIVE_LEVELS[$largestCognitive]['label'] ?? 'the selected cognitive level';
        $largestCognitiveCount = (int) ($matrix['column_totals'][$largestCognitive] ?? 0);
        $largestCognitivePercent = round(($largestCognitiveCount / $total) * 100);

        $largestCompetency = collect($matrix['groups'])->sortByDesc('total')->first();
        $competencyText = $largestCompetency
            ? ' The largest share of questions is focused on ' . $largestCompetency['title'] . '.'
            : '';

        $completionText = $assigned === $total
            ? "Your {$total}-question assessment blueprint is fully allocated."
            : "Your blueprint currently assigns {$assigned} of {$total} questions.";

        return $completionText
            . " It focuses most on {$largestCognitiveLabel} ({$largestCognitivePercent}%), so students will spend more of the assessment at that level."
            . $competencyText;
    }

    private function buildSuggestedRows(TableOfSpecification $tos, array $distribution, array $weights = []): void
    {
        $competencies = IntendedLearningOutcome::active()
            ->where('module_no', $tos->module_no)
            ->orderBy('sort_order')
            ->get();

        if ($competencies->isEmpty()) {
            $customCoverage = trim((string) $tos->custom_coverage);
            $fallbackTitle = $customCoverage !== '' ? $customCoverage : 'General module competency';
            $fallbackDescription = $customCoverage !== ''
                ? 'Demonstrate understanding of ' . $customCoverage . '.'
                : 'Demonstrate understanding of the module coverage.';

            $competencies = collect([
                (object) [
                    'id' => null,
                    'title' => $fallbackTitle,
                    'description' => $fallbackDescription,
                ],
            ]);
        }

        $cognitiveTotals = $this->allocateByPercentages((int) $tos->total_items, $distribution);
        $cellCounts = [];
        $competencyWeights = [];

        foreach ($competencies->values() as $index => $ilo) {
            $key = $ilo->id !== null
                ? 'ilo:' . $ilo->id
                : 'topic:' . strtolower(trim((string) ($ilo->title ?? 'General module competency')));
            $competencyWeights[$index] = max(0, (float) ($weights[$key] ?? 1));
        }

        foreach ($cognitiveTotals as $cognitive => $count) {
            $perCompetency = $this->allocateByWeights($count, $competencyWeights);
            foreach ($perCompetency as $index => $items) {
                $cellCounts[$index][$cognitive] = $items;
            }
        }

        foreach ($competencies->values() as $index => $ilo) {
            foreach (self::COGNITIVE_LEVELS as $cognitive => $definition) {
                TableOfSpecificationRow::create([
                    'table_of_specification_id' => $tos->id,
                    'ilo_id' => $ilo->id ?? null,
                    'topic_title' => $ilo->title ?? 'General module competency',
                    'subtopic_title' => null,
                    'learning_objective' => $ilo->description ?? $ilo->title ?? 'General module competency',
                    'difficulty_slug' => $definition['difficulty_slug'],
                    'item_count' => (int) ($cellCounts[$index][$cognitive] ?? 0),
                    'default_points' => 1,
                    'cognitive_level' => $definition['label'],
                ]);
            }
        }
    }

    private function validatedDistribution(array $distribution): array
    {
        $normalized = [];
        foreach (self::COGNITIVE_LEVELS as $slug => $definition) {
            $normalized[$slug] = max(0, min(100, (int) ($distribution[$slug] ?? 0)));
        }

        if (array_sum($normalized) !== 100) {
            throw ValidationException::withMessages([
                'distribution' => 'The cognitive distribution must total exactly 100%.',
            ]);
        }

        return $normalized;
    }

    private function allocateByPercentages(int $total, array $distribution): array
    {
        $distribution = $this->validatedDistribution($distribution);
        $base = [];
        $remainders = [];
        $used = 0;

        foreach ($distribution as $key => $percentage) {
            $exact = $total * ($percentage / 100);
            $floor = (int) floor($exact);
            $base[$key] = $floor;
            $remainders[$key] = $exact - $floor;
            $used += $floor;
        }

        $left = $total - $used;
        foreach (collect($remainders)->sortDesc()->keys()->take($left) as $key) {
            $base[$key]++;
        }

        return $base;
    }

    private function allocateByWeights(int $total, array $weights): array
    {
        if ($weights === []) {
            return [];
        }

        $weights = array_map(fn ($weight) => max(0, (float) $weight), $weights);
        $weightTotal = array_sum($weights);

        if ($weightTotal <= 0) {
            $weights = array_fill_keys(array_keys($weights), 1.0);
            $weightTotal = (float) count($weights);
        }

        $base = [];
        $remainders = [];
        $used = 0;

        foreach ($weights as $key => $weight) {
            $exact = $total * ($weight / $weightTotal);
            $floor = (int) floor($exact);
            $base[$key] = $floor;
            $remainders[$key] = $exact - $floor;
            $used += $floor;
        }

        $left = $total - $used;
        foreach (collect($remainders)->sortDesc()->keys()->take($left) as $key) {
            $base[$key]++;
        }

        return $base;
    }

    private function groupRowsByCompetency(Collection $rows): Collection
    {
        return $rows->groupBy(function (TableOfSpecificationRow $row): string {
            if ($row->ilo_id !== null) {
                return 'ilo:' . $row->ilo_id;
            }

            return 'topic:' . strtolower(trim((string) $row->topic_title));
        });
    }

    private function syncStatus(TableOfSpecification $tos): void
    {
        $assigned = (int) $tos->rows()->sum('item_count');
        $target = max(1, (int) $tos->total_items);
        $status = $this->statusFromCounts($assigned, $target)['code'];

        $tos->update(['status' => $status]);
    }
}
