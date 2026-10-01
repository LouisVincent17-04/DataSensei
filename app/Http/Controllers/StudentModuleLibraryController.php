<?php

namespace App\Http\Controllers;

use App\Models\ModuleLibraryItem;
use App\Models\ModuleLibraryProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class modules for students (DataSensei Updates 5, task 3).
 *
 * The instructor module library is only reachable through a class: a
 * student sees a module version while it is assigned (status "active") to a
 * class they are enrolled in. The list itself is on the Modules page, under
 * "Module source"; this controller opens one class module.
 *
 * DataSensei Updates 8: opening a class module is recorded (first and last
 * time), and the student can mark it complete at the end of the module. The
 * instructor's Class Analytics and Reports read that progress.
 */
class StudentModuleLibraryController extends Controller
{
    /** The list now lives on the Modules page (Module source = the class). */
    public function index(): RedirectResponse
    {
        /** @var User $student */
        $student = Auth::user();
        $firstClass = ModuleController::enrolledClasses($student)->first();

        return redirect()->route('modules.index', $firstClass ? ['source' => 'class-'.$firstClass->id] : []);
    }

    public function show(Request $request, ModuleLibraryItem $module)
    {
        /** @var User $student */
        $student = Auth::user();
        $classIds = ModuleController::enrolledClasses($student)->pluck('id');

        // Opened from one class: only that class's assignment counts.
        $requestedClass = $request->integer('class');
        if ($requestedClass > 0 && $classIds->contains($requestedClass)) {
            $classIds = collect([$requestedClass]);
        }

        abort_unless(
            $this->accessibleModulesQuery($classIds)->whereKey($module->id)->exists(),
            404,
        );

        $contentSections = $module->content_sections;
        $mcqQuestions = $module->mcq_questions;

        $relatedVersions = $this->accessibleModulesQuery($classIds)
            ->where('module_no', $module->module_no)
            ->orderBy('version_no')
            ->get(['id', 'module_no', 'version_name', 'version_code', 'version_no']);

        $backClass = $classIds->count() === 1 ? $classIds->first() : null;
        $progress = $this->recordOpened($student, $module, $backClass);

        return view('student.modules.module_show', [
            'module' => $module,
            'contentSections' => $contentSections,
            'mcqQuestions' => $mcqQuestions,
            'relatedVersions' => $relatedVersions,
            'learningOutcomes' => $module->learning_outcomes,
            'backRoute' => route('modules.index', $backClass ? ['source' => 'class-'.$backClass] : []),
            'versionClassId' => $backClass,
            'completion' => [
                'completed_at' => $progress?->completed_at,
                'url' => route('student.modules.complete', array_filter(['module' => $module->id, 'class' => $backClass])),
            ],
        ]);
    }

    /** The student finished reading the module and marks it complete. */
    public function complete(Request $request, ModuleLibraryItem $module): RedirectResponse
    {
        /** @var User $student */
        $student = Auth::user();
        $classIds = ModuleController::enrolledClasses($student)->pluck('id');

        abort_unless(
            $this->accessibleModulesQuery($classIds)->whereKey($module->id)->exists(),
            404,
        );

        $requestedClass = $request->integer('class');
        $classId = $requestedClass > 0 && $classIds->contains($requestedClass) ? $requestedClass : null;
        $progress = $this->recordOpened($student, $module, $classId);

        if ($progress && $progress->completed_at === null) {
            $progress->forceFill(['completed_at' => now()])->save();
        }

        // Completing a class module may complete a class certificate's
        // requirement (DataSensei Updates 13).
        app(\App\Services\CertificateService::class)->afterProgress($student);

        return redirect()
            ->to(route('student.modules.show', array_filter(['module' => $module->id, 'class' => $classId])).'#module-completion')
            ->with('success', 'Module marked as complete.');
    }

    /**
     * Records that the student opened the module: the first time, and the
     * latest. Progress is never allowed to break the page, for example
     * before the Updates 8 migration has run.
     */
    private function recordOpened(User $student, ModuleLibraryItem $module, ?int $classId): ?ModuleLibraryProgress
    {
        try {
            $progress = ModuleLibraryProgress::firstOrNew([
                'user_id' => $student->id,
                'module_library_item_id' => $module->id,
            ]);
            $now = now();
            $progress->opened_at ??= $now;
            $progress->last_opened_at = $now;
            $progress->class_id ??= $classId;
            $progress->save();

            return $progress;
        } catch (QueryException) {
            // Opened twice at the same moment (unique key), or the table is
            // not there yet: read what is stored, if anything.
            try {
                return ModuleLibraryProgress::query()
                    ->where('user_id', $student->id)
                    ->where('module_library_item_id', $module->id)
                    ->first();
            } catch (QueryException) {
                return null;
            }
        }
    }

    /**
     * Module versions assigned (and still active) to one of these classes.
     * A class assignment pins a content version, so a version retired from
     * the library stays readable for the classes that were given it.
     */
    private function accessibleModulesQuery(Collection $classIds): Builder
    {
        return ModuleLibraryItem::query()->whereHas('classAssignments', fn (Builder $assignment) => $assignment
            ->whereIn('class_id', $classIds->isEmpty() ? [0] : $classIds->all())
            ->where('status', 'active'));
    }
}
