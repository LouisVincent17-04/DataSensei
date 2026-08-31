<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    // 1. Show the Challenge Map
    public function map()
    {
        $modules = Module::orderBy('order_index', 'asc')->get();
        return view('student.challenges', compact('modules'));
    }

    // 2. Show the Learning Room (Lesson UI)
    public function show(Module $module, $lessonId = null)
    {
        $this->ensureModuleUnlocked($module);
        $module->load(['lessons' => function($q) { $q->orderBy('order_index', 'asc'); }]);
        
        $activeLesson = $lessonId 
            ? $module->lessons->where('id', $lessonId)->first() 
            : $module->lessons->first();

        if (!$activeLesson) abort(404, 'No lessons found for this module.');

        // Fixed the pluck() bug here!
        $completedLessonIds = Auth::user()->completedLessons->pluck('id')->toArray();
        $totalLessons = $module->lessons->count();
        $completedCount = count(array_intersect($completedLessonIds, $module->lessons->pluck('id')->toArray()));
        $progressPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        return view('student.learning-room', compact('module', 'activeLesson', 'completedLessonIds', 'progressPct'));
    }

    // 3. Mark Lesson Complete & Advance
    public function complete(Lesson $lesson)
    {
        $user = Auth::user();
        $module = $lesson->module;
        $this->ensureModuleUnlocked($module);

        // 1. Mark this lesson as complete
        $user->lessons()->syncWithoutDetaching([
            $lesson->id => ['is_completed' => true]
        ]);

        // ─── MODULE PROGRESSION ENGINE ─────────────────────────────
        $totalLessons = $module->lessons()->count();
        
        // Safely count completed lessons for THIS module directly in the DB
        $completedCount = $user->completedLessons()
                               ->where('lessons.module_id', $module->id)
                               ->count();

        // If all lessons are done, mark module as complete & unlock the next!
        if ($completedCount >= $totalLessons && $totalLessons > 0) {
            $user->modules()->syncWithoutDetaching([
                $module->id => ['is_completed' => true, 'is_unlocked' => true]
            ]);
            
            $nextModule = Module::where('order_index', '>', $module->order_index)->orderBy('order_index', 'asc')->first();
            if ($nextModule) {
                $user->modules()->syncWithoutDetaching([
                    $nextModule->id => ['is_unlocked' => true]
                ]);
            }
        }
        // ───────────────────────────────────────────────────────────

        $nextLesson = Lesson::where('module_id', $lesson->module_id)
                            ->where('order_index', '>', $lesson->order_index)
                            ->orderBy('order_index', 'asc')
                            ->first();

        if ($nextLesson) {
            return redirect()->route('lesson.show', ['module' => $lesson->module_id, 'lesson' => $nextLesson->id]);
        }

        return redirect()->route('challenges')->with('success', 'Module Completed!');
    }

    private function ensureModuleUnlocked(Module $module): void
    {
        $user = Auth::user();

        if (! $user->modules()->wherePivot('is_unlocked', true)->exists()) {
            $firstModule = Module::orderBy('order_index')->orderBy('id')->first();
            if ($firstModule) {
                $user->modules()->syncWithoutDetaching([
                    $firstModule->id => ['is_unlocked' => true],
                ]);
            }
        }

        abort_unless(
            $user->modules()
                ->where('modules.id', $module->id)
                ->wherePivot('is_unlocked', true)
                ->exists(),
            403,
            'Complete the preceding module before opening this one.'
        );
    }
}
