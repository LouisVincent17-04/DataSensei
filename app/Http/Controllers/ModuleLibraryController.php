<?php

namespace App\Http\Controllers;

use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\ModuleLibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleLibraryController extends Controller
{
    /**
     * Display the module library list.
     */
    public function index()
    {
        $modules = ModuleLibraryItem::where('is_active', true)
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

        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $assignedIdsByClass = ClassModuleAssignment::query()
            ->whereIn('class_id', $classes->pluck('id'))
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
     * Display the NetAcad-style learning page for one module version.
     */
    public function show(ModuleLibraryItem $module)
    {
        abort_unless($module->is_active, 404);

        $contentSections = $this->normalizeLongTextContent($module->content_sections);
        $mcqQuestions = $this->normalizeLongTextContent($module->mcq_questions);

        $relatedVersions = ModuleLibraryItem::where('module_no', $module->module_no)
            ->where('is_active', true)
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
    public function assign(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'selected_modules' => ['required', 'array'],
        ]);

        $class = ClassRoom::where('id', $request->input('class_id'))
            ->where('instructor_id', Auth::id())
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

        $assigned = 0;
        $skipped = 0;

        foreach ($validIds as $moduleId) {
            $alreadyAssigned = ClassModuleAssignment::where('class_id', $class->id)
                ->where('module_library_item_id', $moduleId)
                ->where('status', 'active')
                ->exists();

            if ($alreadyAssigned) {
                $skipped++;
                continue;
            }

            ClassModuleAssignment::create([
                'class_id' => $class->id,
                'module_library_item_id' => $moduleId,
                'assigned_by' => Auth::id(),
                'status' => 'active',
                'assigned_at' => now(),
            ]);

            $assigned++;
        }

        $message = "{$assigned} module version(s) successfully assigned to {$class->name}.";

        if ($skipped > 0) {
            $message .= " {$skipped} module(s) were already assigned and were skipped.";
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
