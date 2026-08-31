<?php

namespace App\Http\Controllers;

use App\Models\ModuleLibraryItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StudentModuleLibraryController extends Controller
{
    public function index()
    {
        /** @var User $student */
        $student = Auth::user();
        $classIds = $this->enrolledClassIds($student);
        $isClassScoped = $classIds->isNotEmpty();

        $modules = $this->accessibleModulesQuery($classIds)
            ->orderBy('sort_order')
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->get()
            ->groupBy('module_no');

        return view('student.modules.index', compact('modules', 'isClassScoped'));
    }

    public function show(ModuleLibraryItem $module)
    {
        /** @var User $student */
        $student = Auth::user();
        $classIds = $this->enrolledClassIds($student);

        abort_unless(
            $this->accessibleModulesQuery($classIds)->whereKey($module->id)->exists(),
            404,
        );

        $contentSections = $this->decodeLongText($module->content_sections);
        $mcqQuestions = $this->decodeLongText($module->mcq_questions);

        $relatedVersions = $this->accessibleModulesQuery($classIds)
            ->where('module_no', $module->module_no)
            ->orderBy('version_no')
            ->get();

        return view('student.modules.module_show', compact(
            'module',
            'contentSections',
            'mcqQuestions',
            'relatedVersions'
        ));
    }

    private function decodeLongText($value): array
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

    private function enrolledClassIds(User $student): Collection
    {
        return $student->classesAsStudent()
            ->pluck('classes.id');
    }

    private function accessibleModulesQuery(Collection $classIds): Builder
    {
        $query = ModuleLibraryItem::query();

        if ($classIds->isEmpty()) {
            return $query->active();
        }

        // Class assignments pin a specific content version. Retiring that
        // version from the public library must not remove it from learners who
        // were already assigned it.
        return $query->whereHas('classAssignments', fn (Builder $assignment) => $assignment
            ->whereIn('class_id', $classIds)
            ->where('status', 'active'));
    }
}
