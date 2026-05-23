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
     * Display the module library with all active module versions grouped by year.
     */
    public function index()
    {
        $modules = ModuleLibraryItem::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        /*
            Build the nested structure the blade expects:
            $modulesByYear = [
                'Year 1' => Collection keyed by module_no [
                    1 => Collection of ModuleLibraryItem (all versions for module 1),
                    ...
                ],
                ...
            ]
        */
        $modulesByYear = $modules
            ->groupBy('year_level')
            ->map(fn ($group) => $group->groupBy('module_no'));

        $totalModuleTitles  = $modules->groupBy('module_no')->count();
        $totalModuleVersions = $modules->count();

        // Only non-archived classes belonging to the authenticated instructor
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        return view('instructor.modules.module_list', compact(
            'modulesByYear',
            'totalModuleTitles',
            'totalModuleVersions',
            'classes',
        ));
    }

    /**
     * Assign the selected module versions to the chosen class.
     *
     * The form posts:
     *   class_id            = integer
     *   selected_modules[]  = [ module_no => module_library_item_id | "" ]
     */
    public function assign(Request $request)
    {
        $request->validate([
            'class_id'         => ['required', 'integer', 'exists:classes,id'],
            'selected_modules' => ['required', 'array'],
        ]);

        // Verify the class belongs to the authenticated instructor
        $class = ClassRoom::where('id', $request->input('class_id'))
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        // Filter out the "Do not include" (empty-string) entries
        $moduleIds = collect($request->input('selected_modules', []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($moduleIds->isEmpty()) {
            return back()->withErrors(['selected_modules' => 'Please select at least one module version.']);
        }

        // Verify all submitted IDs are real, active learning modules
        $validIds = ModuleLibraryItem::whereIn('id', $moduleIds)
            ->where('is_active', true)
            ->pluck('id');

        $assigned = 0;
        $skipped  = 0;  // already assigned to this class

        foreach ($validIds as $moduleId) {
            $alreadyAssigned = ClassModuleAssignment::where('class_id', $class->id)
                ->where('module_library_item_id', $moduleId)
                ->exists();

            if ($alreadyAssigned) {
                $skipped++;
                continue;
            }

            ClassModuleAssignment::create([
                'class_id'           => $class->id,
                'module_library_item_id' => $moduleId,
                'assigned_by'        => Auth::id(),
                'status'             => 'active',
                'assigned_at'        => now(),
            ]);

            $assigned++;
        }

        $message = "{$assigned} module version(s) successfully assigned to {$class->name}.";

        if ($skipped > 0) {
            $message .= " {$skipped} module(s) were already assigned and were skipped.";
        }

        return back()->with('success', $message);
    }
}