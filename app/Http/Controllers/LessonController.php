<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    // 1. Show the Challenge Map
    public function map()
    {
        // Fetch all modules ordered by their index
        $modules = Module::orderBy('order_index', 'asc')->get();
        return view('student.challenges', compact('modules'));
    }

    // 2. Show the Learning Room (Lesson UI)
    public function show(Module $module, $lessonId = null)
    {
        $module->load(['lessons' => function($q) { $q->orderBy('order_index', 'asc'); }]);
        
        $activeLesson = $lessonId 
            ? $module->lessons->where('id', $lessonId)->first() 
            : $module->lessons->first();

        if (!$activeLesson) abort(404, 'No lessons found for this module.');

        $completedLessonIds = Auth::user()->completedLessons->pluck('id')->toArray();
        $totalLessons = $module->lessons->count();
        $completedCount = count(array_intersect($completedLessonIds, $module->lessons->pluck('id')->toArray()));
        $progressPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        return view('student.learning-room', compact('module', 'activeLesson', 'completedLessonIds', 'progressPct'));
    }

    // 3. Mark Lesson Complete & Advance
    public function complete(Request $request, Lesson $lesson)
    {
        Auth::user()->completedLessons()->syncWithoutDetaching([$lesson->id => ['is_completed' => true]]);

        $nextLesson = Lesson::where('module_id', $lesson->module_id)
                            ->where('order_index', '>', $lesson->order_index)
                            ->orderBy('order_index', 'asc')
                            ->first();

        if ($nextLesson) {
            return redirect()->route('lesson.show', ['module' => $lesson->module_id, 'lesson' => $nextLesson->id]);
        }

        return redirect()->route('challenges')->with('success', 'Module Completed!');
    }
}