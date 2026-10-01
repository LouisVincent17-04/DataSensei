<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\ModuleLibraryProgress;
use App\Models\Module;
use App\Models\ModuleLibraryItem;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * The student Modules page (DataSensei Updates 5, task 3).
 *
 * "Module source" chooses what is listed:
 *   datasensei      the public DataSensei modules, by year level (default)
 *   class-{id}      the modules an instructor assigned to one of the
 *                   learner's classes, without year levels
 *
 * Class modules are read from class_module_assignments, the same rows the
 * instructor's assignment writes and the notification points to, and only
 * for classes the learner is enrolled in.
 */
class ModuleController extends Controller
{
    public const SOURCE_DATASENSEI = 'datasensei';

    public function showModules(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $this->ensureFirstModuleUnlocked($user);

        $classes = self::enrolledClasses($user);
        $source = (string) $request->query('source', self::SOURCE_DATASENSEI);
        $selectedClass = null;

        if (str_starts_with($source, 'class-')) {
            $selectedClass = $classes->firstWhere('id', (int) substr($source, strlen('class-')));
        }

        if (! $selectedClass) {
            $source = self::SOURCE_DATASENSEI;
        }

        $classModules = $selectedClass ? self::classModules((int) $selectedClass->id) : collect();
        $completedClassModuleIds = $classModules->isEmpty() ? [] : self::completedClassModuleIds($user, $classModules->pluck('id')->all());

        $modules = Module::with('lessons:id,module_id')->published()->orderBy('order_index', 'asc')->get();

        // Pull exact statuses from the database pivot table
        $unlockedModuleIds = $user->modules()
                                  ->wherePivot('is_unlocked', true)
                                  ->pluck('modules.id')
                                  ->toArray();

        $completedModuleIds = $user->modules()
                                  ->wherePivot('is_completed', true)
                                  ->pluck('modules.id')
                                  ->toArray();

        return view('student.modules', compact(
            'modules',
            'unlockedModuleIds',
            'completedModuleIds',
            'classes',
            'source',
            'selectedClass',
            'classModules',
            'completedClassModuleIds'
        ));
    }

    /**
     * The class modules this learner marked complete (DataSensei Updates 8).
     *
     * @param  list<int>  $moduleIds
     * @return list<int>
     */
    private static function completedClassModuleIds(User $user, array $moduleIds): array
    {
        try {
            return ModuleLibraryProgress::query()
                ->where('user_id', $user->id)
                ->whereIn('module_library_item_id', $moduleIds)
                ->whereNotNull('completed_at')
                ->pluck('module_library_item_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        } catch (QueryException) {
            return [];
        }
    }

    /**
     * The learner's current classes (archived classes are left out).
     *
     * @return Collection<int, ClassRoom>
     */
    public static function enrolledClasses(User $user): Collection
    {
        return $user->classesAsStudent()
            ->active()
            ->orderBy('classes.name')
            ->get(['classes.id', 'classes.name', 'classes.section']);
    }

    /** "IT 4A – Data Science" when the class has a section, else its name. */
    public static function classLabel(ClassRoom $class): string
    {
        $section = trim((string) $class->section);

        return $section !== '' && $section !== trim((string) $class->name)
            ? $section.' – '.$class->name
            : (string) $class->name;
    }

    /**
     * The module versions currently assigned to one class. Only the columns
     * the list shows are read: the content itself can be very large.
     *
     * @return Collection<int, ModuleLibraryItem>
     */
    public static function classModules(int $classId): Collection
    {
        return ModuleLibraryItem::query()
            ->whereHas('classAssignments', fn ($assignment) => $assignment
                ->where('class_id', $classId)
                ->where('status', 'active'))
            ->orderBy('sort_order')
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->get(['id', 'module_no', 'title', 'description', 'estimated_minutes', 'version_name', 'learning_outcomes']);
    }

    private function ensureFirstModuleUnlocked($user): void
    {
        if ($user->modules()->wherePivot('is_unlocked', true)->exists()) {
            return;
        }

        $firstModule = Module::published()->orderBy('order_index')->orderBy('id')->first();
        if ($firstModule) {
            $user->modules()->syncWithoutDetaching([
                $firstModule->id => ['is_unlocked' => true],
            ]);
        }
    }
}
