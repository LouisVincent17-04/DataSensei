<?php

namespace App\Http\Controllers;

use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\ModuleLibraryItem;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModuleLibraryController extends Controller
{
    /**
     * Display the module library list.
     */
    public function index()
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();
        $classIds = $classes->pluck('id');

        $modules = ModuleLibraryItem::query()
            ->where(function ($query) use ($classIds): void {
                $query->where('is_active', true)
                    ->orWhereHas('classAssignments', fn ($assignment) => $assignment
                        ->whereIn('class_id', $classIds)
                        ->where('status', 'active'));
            })
            ->orderBy('sort_order')
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->get();

        $modules->each(function (ModuleLibraryItem $module): void {
            $module->preview_payload = $this->buildPreviewPayload($module);
        });

        $modulesByYear = $modules
            ->groupBy('year_level')
            ->map(fn ($group) => $group->groupBy('module_no'));

        $totalModuleTitles = $modules->groupBy('module_no')->count();
        $totalModuleVersions = $modules->count();

        $assignedIdsByClass = ClassModuleAssignment::query()
            ->whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->get(['class_id', 'module_library_item_id'])
            ->groupBy('class_id')
            ->map(fn ($items) => $items->pluck('module_library_item_id')->values());

        $classes->each(function ($class) use ($assignedIdsByClass): void {
            $class->assigned_module_ids = $assignedIdsByClass
                ->get($class->id, collect())
                ->values()
                ->all();
        });

        return view('instructor.modules.module_list', compact(
            'modulesByYear',
            'totalModuleTitles',
            'totalModuleVersions',
            'classes'
        ));
    }

    /**
     * Display the DataSensei lesson page for one module version.
     */
    public function show(ModuleLibraryItem $module)
    {
        $classIds = ClassRoom::query()
            ->where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->pluck('id');

        $isPinnedToOwnedClass = $module->classAssignments()
            ->whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->exists();

        abort_unless($module->is_active || $isPinnedToOwnedClass, 404);

        $contentSections = $this->normalizeLongTextContent($module->content_sections);
        $mcqQuestions = $this->normalizeLongTextContent($module->mcq_questions);

        $relatedVersions = ModuleLibraryItem::where('module_no', $module->module_no)
            ->where(function ($query) use ($classIds): void {
                $query->where('is_active', true)
                    ->orWhereHas('classAssignments', fn ($assignment) => $assignment
                        ->whereIn('class_id', $classIds)
                        ->where('status', 'active'));
            })
            ->orderBy('version_no')
            ->get();

        return view('instructor.modules.module_show', compact(
            'module',
            'contentSections',
            'mcqQuestions',
            'relatedVersions'
        ));
    }

    /**
     * Assign selected module versions to the chosen class.
     */
    public function assign(Request $request, StudentNotificationService $notifications)
    {
        $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'selected_modules' => ['required', 'array', 'min:1', 'max:100'],
            'selected_modules.*' => ['required', 'integer', 'distinct', 'exists:module_library_items,id'],
        ]);

        $class = ClassRoom::where('id', $request->input('class_id'))
            ->where('instructor_id', Auth::id())
            ->active()
            ->firstOrFail();

        $moduleIds = collect($request->input('selected_modules', []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($moduleIds->isEmpty()) {
            return back()->withErrors([
                'selected_modules' => 'Please select at least one module version.',
            ]);
        }

        $validIds = ModuleLibraryItem::whereIn('id', $moduleIds)
            ->where('is_active', true)
            ->pluck('id');

        if ($validIds->count() !== $moduleIds->unique()->count()) {
            return back()->withErrors([
                'selected_modules' => 'One or more selected module versions are no longer active. Refresh the page and select again.',
            ]);
        }

        [$assigned, $replaced, $skipped, $selectionError, $activatedIds] = DB::transaction(function () use ($class, $validIds): array {
            ClassRoom::query()
                ->whereKey($class->id)
                ->where('instructor_id', Auth::id())
                ->active()
                ->lockForUpdate()
                ->firstOrFail();

            $lockedModules = ModuleLibraryItem::query()
                ->whereIn('id', $validIds)
                ->where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id', 'module_no']);

            if ($lockedModules->count() !== $validIds->count()) {
                return [0, 0, 0, 'changed', []];
            }

            if ($lockedModules->pluck('module_no')->unique()->count() !== $lockedModules->count()) {
                return [0, 0, 0, 'duplicate_module', []];
            }

            $assigned = 0;
            $replaced = 0;
            $skipped = 0;
            $activatedIds = [];

            foreach ($lockedModules as $module) {
                $versionAssignments = ClassModuleAssignment::query()
                    ->where('class_id', $class->id)
                    ->whereHas('moduleLibraryItem', fn ($query) => $query
                        ->where('module_no', $module->module_no))
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $existing = $versionAssignments
                    ->firstWhere('module_library_item_id', $module->id);
                $otherActiveIds = $versionAssignments
                    ->where('status', 'active')
                    ->where('module_library_item_id', '!=', $module->id)
                    ->pluck('id');

                if ($otherActiveIds->isNotEmpty()) {
                    ClassModuleAssignment::query()
                        ->whereIn('id', $otherActiveIds)
                        ->update(['status' => 'archived']);
                    $replaced += $otherActiveIds->count();
                }

                if ($existing?->status === 'active') {
                    $skipped++;
                    continue;
                }

                ClassModuleAssignment::updateOrCreate(
                    [
                        'class_id' => $class->id,
                        'module_library_item_id' => $module->id,
                    ],
                    [
                        'assigned_by' => Auth::id(),
                        'status' => 'active',
                        'assigned_at' => now(),
                    ]
                );
                $assigned++;
                $activatedIds[] = (int) $module->id;
            }

            return [$assigned, $replaced, $skipped, null, $activatedIds];
        }, 3);

        if ($selectionError === 'changed') {
            return back()->withErrors([
                'selected_modules' => 'A selected module version changed while the assignment was being saved. Refresh the page and try again.',
            ]);
        }

        if ($selectionError === 'duplicate_module') {
            return back()->withErrors([
                'selected_modules' => 'Select only one version of each module for a class.',
            ]);
        }

        $message = "{$assigned} module version(s) successfully assigned to {$class->name}.";

        if ($skipped > 0) {
            $message .= " {$skipped} module(s) were already assigned and were skipped.";
        }

        if ($replaced > 0) {
            $message .= " {$replaced} older assigned version(s) were replaced.";
        }

        if ($activatedIds !== []) {
            $moduleTitles = ModuleLibraryItem::query()
                ->whereIn('id', $activatedIds)
                ->orderBy('module_no')
                ->pluck('title')
                ->filter()
                ->values();
            $listedTitles = $moduleTitles->take(3)->map(fn ($title) => '“' . $title . '”')->implode(', ');
            $extraCount = max(0, $moduleTitles->count() - 3);
            $moduleText = $listedTitles !== '' ? $listedTitles : count($activatedIds) . ' module(s)';
            if ($extraCount > 0) {
                $moduleText .= ' and ' . $extraCount . ' more';
            }

            $notifications->sendToClass(
                (int) $class->id,
                'modules_assigned',
                'New learning modules',
                $moduleText . ' ' . ($moduleTitles->count() === 1 ? 'is' : 'are') . ' now available in ' . $class->name . '.',
                route('modules.index'),
                ['class_id' => $class->id, 'module_ids' => $activatedIds],
                'modules-assigned:' . $class->id . ':' . sha1(implode(',', $activatedIds) . now()->format('YmdHis'))
            );
        }

        return back()->with('success', $message);
    }

    private function buildPreviewPayload(ModuleLibraryItem $module): array
    {
        return [
            'id' => $module->id,
            'module_no' => $module->module_no,
            'module_code' => $module->module_code,
            'title' => $module->title,
            'year_level' => $module->year_level,
            'version_name' => $module->version_name,
            'version_code' => $module->version_code,
            'description' => $module->description,
            'estimated_minutes' => $module->estimated_minutes,
            'content_sections' => $this->normalizeLongTextContent($module->content_sections),
            'mcq_questions' => $this->normalizeLongTextContent($module->mcq_questions),
        ];
    }

    private function normalizeLongTextContent($value): array
    {
        if (blank($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
