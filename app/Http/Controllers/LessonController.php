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

        // Fixed the pluck() bug here!
        $completedLessonIds = Auth::user()->completedLessons->pluck('id')->toArray();
        $totalLessons = $module->lessons->count();
        $completedCount = count(array_intersect($completedLessonIds, $module->lessons->pluck('id')->toArray()));
        $progressPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        return view('student.learning-room', compact('module', 'activeLesson', 'completedLessonIds', 'progressPct'));
    }

    public function tryCode(Request $request)
    {
        $code = $request->input('code', '');
        $lessonId = $request->input('lesson_id');
        $returnUrl = route('lesson.show', $lessonId);

        // ── Logic: Is it purely SQL? ──
        // We check for Python-specific keywords (import, print, def, variable assignments)
        $hasPython = preg_match('/(import\s+|print\(|def\s+|=|\n\s+:)/i', $code);
        
        // If it has NO Python keywords and starts with common SQL commands
        $isSqlOnly = !$hasPython && preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER|WITH|PRAGMA)/i', $code);

        if ($isSqlOnly) {
            // Redirect to SQL Sandbox with the code and return URL in session
            return redirect()->route('sql-sandbox.index')
                ->with('pending_sql_code', $code)
                ->with('datasensei_return_url', $returnUrl);
        }

        // Otherwise, redirect to Python IDE
        return redirect()->route('ide.index')
            ->with('pending_python_code', $code)
            ->with('datasensei_return_url', $returnUrl);
    }

    // 3. Mark Lesson Complete & Advance
    public function complete(Request $request, Lesson $lesson)
    {
        $user = Auth::user();
        $module = $lesson->module;

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
}