<?php

namespace Tests\Feature\Regression\Concerns;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Support\CoreCurriculum;
use Illuminate\Support\Facades\DB;

/**
 * The 24 Core Modules (one lesson each), the Newbie and University Student
 * MCQ challenge of every core module, and Newbie coding challenges for Core
 * Modules 1 and 2 only (the others have none), plus a University Student
 * coding challenge for Core Module 1. Used by the Updates 13 end-to-end
 * simulation.
 */
trait BuildsCoreCurriculum
{
    protected ChallengeCategory $newbie;

    protected ChallengeCategory $university;

    /** @var array<int, Module> position => core module */
    protected array $coreModules = [];

    /** @var array<int, Challenge> position => Newbie MCQ */
    protected array $coreMcq = [];

    /** @var array<int, Challenge> position => Newbie coding challenge */
    protected array $coreCoding = [];

    protected function buildCoreCurriculum(): void
    {
        $this->newbie = ChallengeCategory::create(['name' => 'Newbie', 'slug' => 'newbie', 'target_audience' => 'Beginners', 'description' => 'Newbie', 'order_index' => 1]);
        $this->university = ChallengeCategory::create(['name' => 'University Student', 'slug' => 'university-student', 'target_audience' => 'College', 'description' => 'University', 'order_index' => 2]);

        foreach (CoreCurriculum::MODULES as $index => $entry) {
            $position = $index + 1;
            $module = Module::create([
                'title' => $entry['title'], 'description' => 'Core.', 'order_index' => $position,
                'year_level' => $entry['year'], 'xp_reward' => 100, 'is_published' => true,
            ]);
            Lesson::create(['module_id' => $module->id, 'title' => 'Lesson '.$position, 'content' => '<p>x</p>', 'order_index' => 1]);
            $this->coreModules[$position] = $module;

            foreach ([$this->newbie, $this->university] as $category) {
                $challenge = Challenge::create([
                    'challenge_category_id' => $category->id, 'title' => $entry['title'], 'description' => 'MCQ',
                    'is_coding_challenge' => false, 'order_index' => $position,
                ]);
                if ($category->is($this->newbie)) {
                    $this->coreMcq[$position] = $challenge;
                }
            }
        }

        $this->coreCoding[1] = $this->coreCodingChallenge($this->newbie, 1);
        $this->coreCoding[2] = $this->coreCodingChallenge($this->newbie, 2);
        $this->coreCodingChallenge($this->university, 1);

        CoreCurriculum::sync();
    }

    protected function coreCodingChallenge(ChallengeCategory $category, int $position): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id, 'title' => CoreCurriculum::MODULES[$position - 1]['title'], 'description' => 'Coding',
            'is_coding_challenge' => true, 'order_index' => $position,
        ]);
        $this->codingQuestions($challenge, 2);

        return $challenge;
    }

    protected function codingQuestions(Challenge $challenge, int $count): void
    {
        foreach (range(1, $count) as $order) {
            DB::table('coding_questions')->insert(['challenge_id' => $challenge->id, 'title' => 'Problem '.$order, 'problem_description' => 'Solve it.', 'language' => 'python', 'order_index' => $order, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    protected function attemptChallenge(User $learner, Challenge $challenge, int $score, int $total): void
    {
        $attemptNo = 1 + (int) DB::table('challenge_attempts')->where('user_id', $learner->id)->where('challenge_id', $challenge->id)->max('attempt_no');
        DB::table('challenge_attempts')->insert([
            'user_id' => $learner->id, 'challenge_id' => $challenge->id, 'attempt_no' => $attemptNo, 'mode' => 'practice', 'status' => 'submitted',
            'started_at' => now(), 'submitted_at' => now(), 'time_taken_seconds' => 60, 'score' => $score, 'total_questions' => $total, 'xp_awarded' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    protected function solveChallenge(User $learner, Challenge $challenge): void
    {
        foreach (DB::table('coding_questions')->where('challenge_id', $challenge->id)->pluck('id') as $questionId) {
            DB::table('coding_submissions')->insert([
                'user_id' => $learner->id, 'coding_question_id' => $questionId, 'code' => 'print(1)', 'language' => 'python', 'status' => 'passed',
                'tests_passed' => 3, 'tests_total' => 3, 'xp_earned' => 0, 'time_taken_seconds' => 30, 'voided' => false, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
