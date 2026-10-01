<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\GamificationService;

class LessonController extends Controller
{
    // 1. Show the Challenge Map
    public function map()
    {
        $modules = Module::published()->orderBy('order_index', 'asc')->get();
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

        $this->recordOpened($module);

        // Fixed the pluck() bug here!
        $completedLessonIds = Auth::user()->completedLessons->pluck('id')->toArray();
        $totalLessons = $module->lessons->count();
        $completedCount = count(array_intersect($completedLessonIds, $module->lessons->pluck('id')->toArray()));
        $progressPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $learningOutcomes = $module->learning_outcomes;
        $reviewQuestions = $module->review_questions;
        $isFirstLesson = $module->lessons->first()?->id === $activeLesson->id;

        return view('student.learning-room', compact('module', 'activeLesson', 'completedLessonIds', 'progressPct', 'learningOutcomes', 'reviewQuestions', 'isFirstLesson'));
    }

    /**
     * The module's embedded review questions (DataSensei Updates 5): check
     * your understanding with instant feedback. Nothing is scored or saved.
     */
    public function review(Module $module)
    {
        $this->ensureModuleUnlocked($module);
        $module->load(['lessons' => function ($q) { $q->orderBy('order_index', 'asc'); }]);

        $reviewQuestions = $module->review_questions;
        abort_if($reviewQuestions === [], 404, 'This module has no review questions.');
        $this->recordOpened($module);

        $completedLessonIds = Auth::user()->completedLessons->pluck('id')->toArray();
        $totalLessons = $module->lessons->count();
        $completedCount = count(array_intersect($completedLessonIds, $module->lessons->pluck('id')->toArray()));
        $progressPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        return view('student.learning-room', [
            'module' => $module,
            'activeLesson' => null,
            'completedLessonIds' => $completedLessonIds,
            'progressPct' => $progressPct,
            'learningOutcomes' => $module->learning_outcomes,
            'reviewQuestions' => $reviewQuestions,
            'isFirstLesson' => false,
            'reviewMode' => true,
        ]);
    }

    // 3. Mark Lesson Complete & Advance
    public function complete(Lesson $lesson)
    {
        $user = Auth::user();
        $module = $lesson->module;
        $this->ensureModuleUnlocked($module);

        $wasCompleted = $user->completedLessons()->where('lessons.id', $lesson->id)->exists();

        // 1. Mark this lesson as complete
        $user->lessons()->syncWithoutDetaching([
            $lesson->id => ['is_completed' => true]
        ]);

        // A first completion counts toward lesson missions, streaks and the
        // lesson achievements. Rewards must never block the lesson flow.
        if (! $wasCompleted && $user->isLearner()) {
            try {
                app(GamificationService::class)->recordLessonCompletion($user, (int) $lesson->id);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

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

            // The next module learners can see (unpublished ones are skipped).
            $nextModule = Module::published()->where('order_index', '>', $module->order_index)->orderBy('order_index', 'asc')->first();
            if ($nextModule) {
                $user->modules()->syncWithoutDetaching([
                    $nextModule->id => ['is_unlocked' => true]
                ]);
            }

            // A completed Core Module may finish the "Core 24 Module
            // Completion" certificate (DataSensei Updates 12). Checked on the
            // server; it never blocks the lesson flow.
            app(\App\Services\CertificateService::class)->afterProgress($user);
        }
        // ───────────────────────────────────────────────────────────

        $nextLesson = Lesson::where('module_id', $lesson->module_id)
                            ->where('order_index', '>', $lesson->order_index)
                            ->orderBy('order_index', 'asc')
                            ->first();

        if ($nextLesson) {
            return redirect()->route('lesson.show', ['module' => $lesson->module_id, 'lesson' => $nextLesson->id]);
        }

        // The last lesson is done: the review questions come next when the
        // module has them, otherwise back to the module page, where the
        // module now shows as completed and the next one as unlocked.
        if ($module->review_questions !== []) {
            return redirect()->route('lesson.review', $module)->with('success', 'Module Completed!');
        }

        return redirect()->route('modules.index')->with('success', 'Module Completed!');
    }

    /**
     * The first time this learner opens the module (DataSensei Updates 8), for
     * the admin Module Report's "users who accessed each module". It never
     * blocks the lesson, for example before the Updates 8 migration has run.
     */
    private function recordOpened(Module $module): void
    {
        try {
            DB::table('module_user')
                ->where('user_id', Auth::id())
                ->where('module_id', $module->id)
                ->whereNull('opened_at')
                ->update(['opened_at' => now()]);
        } catch (QueryException) {
            // The column is added by the Updates 8 migration.
        }
    }

    private function ensureModuleUnlocked(Module $module): void
    {
        $user = Auth::user();

        // An unpublished module is not shown to learners.
        abort_unless((bool) ($module->is_published ?? true), 404);

        if (! $user->modules()->wherePivot('is_unlocked', true)->exists()) {
            $firstModule = Module::published()->orderBy('order_index')->orderBy('id')->first();
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
