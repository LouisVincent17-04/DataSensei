<?php

namespace App\Services;

use App\Models\IntendedLearningOutcome;
use App\Models\TableOfSpecification;
use App\Models\TableOfSpecificationRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TableOfSpecificationService
{
    public const DIFFICULTIES = [
        'newbie' => 'Newbie',
        'university-student' => 'University Student',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'professional' => 'Professional',
    ];

    public function createDefault(?int $classId, int $moduleNo): TableOfSpecification
    {
        return DB::transaction(function () use ($classId, $moduleNo) {
            $tos = TableOfSpecification::create([
                'class_id' => $classId,
                'module_no' => $moduleNo,
                'title' => 'Module ' . $moduleNo . ' Table of Specification',
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            $ilos = IntendedLearningOutcome::active()
                ->where('module_no', $moduleNo)
                ->orderBy('sort_order')
                ->get();

            if ($ilos->isEmpty()) {
                $ilos = collect([(object) ['id' => null, 'title' => 'General module competency']]);
            }

            foreach ($ilos as $ilo) {
                foreach (self::DIFFICULTIES as $slug => $label) {
                    TableOfSpecificationRow::create([
                        'table_of_specification_id' => $tos->id,
                        'ilo_id' => $ilo->id ?? null,
                        'topic_title' => $ilo->title ?? 'General module competency',
                        'difficulty_slug' => $slug,
                        'item_count' => in_array($slug, ['newbie', 'university-student'], true) ? 5 : 3,
                        'cognitive_level' => $this->defaultCognitiveLevel($slug),
                    ]);
                }
            }

            return $tos->load('rows.ilo');
        });
    }

    private function defaultCognitiveLevel(string $slug): string
    {
        return match ($slug) {
            'newbie' => 'Remembering / Understanding',
            'university-student' => 'Understanding / Applying',
            'intermediate' => 'Applying / Analyzing',
            'advanced' => 'Analyzing / Evaluating',
            'professional' => 'Evaluating / Creating',
            default => 'Understanding',
        };
    }
}
