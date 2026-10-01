<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use App\Models\CodingSubmission;
use App\Models\Notification;
use App\Models\User;
use App\Services\ChallengePathUnlockService;
use App\Services\PythonCodePolicyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 4 (revised difficulty progression rule):
 *  - the next level opens only with at least 2 distinct qualifying modules in
 *    the current level; a module qualifies on its own score AND time
 *      Newbie        80% score, 50% time or less  ->  Intermediate
 *      Intermediate  75% score, 70% time or less  ->  Advanced
 *      Advanced      70% score, 80% time or less  ->  Professional
 *    (coding: averages of the items of that one challenge);
 *  - the new level opens only its first module; next modules open in order;
 *  - the eligibility notice is sent once;
 *  - "See more" for the code in the AI reviewer;
 *  - import os allowed in the Python IDE.
 */
class Updates4AdvancedTopicAndReviewerTest extends TestCase
{
    use RefreshDatabase;

    private array $categories = [];

    // ── Next difficulty: MCQ ──────────────────────────────────────────

    public function test_exactly_one_qualifying_module_keeps_the_next_level_locked(): void
    {
        $this->paths();
        $first = $this->mcq('newbie', 'Python Basics', order: 1);
        $second = $this->mcq('newbie', 'Loops and Lists', order: 2);
        $intermediate = $this->mcq('intermediate', 'Intermediate Pandas');
        $student = $this->roleUser();

        // PDF example "Not enough": 90% in 40% qualifies, 77% in 45% does not.
        $this->attempt($student, $first, 90, 100, 240);
        $this->attempt($student, $second, 77, 100, 270);

        $lock = $this->unlocks()->lockInfo($student, 'intermediate', 'mcq');
        $this->assertFalse($lock['unlocked']);
        $this->assertSame(
            'You have 1 of 2 qualifying Newbie modules. Complete one more Newbie module with at least 80% score and 50% time consumed or less to unlock Intermediate.',
            $lock['reason']
        );

        $this->authenticateAs($student)->get(route('challenges.map', 'intermediate'))->assertForbidden();
        $this->get(route('challenges.quiz', ['slug' => 'intermediate', 'challenge' => $intermediate->id]))->assertForbidden();

        $this->get(route('student.advanced-topics.index'))
            ->assertOk()
            ->assertSee('No new level unlocked yet')
            ->assertSee('You have 1 of 2 qualifying Newbie modules. Complete one more Newbie module with at least 80% score and 50% time consumed or less to unlock Intermediate.')
            ->assertSee('No: score below 80%');
    }

    public function test_two_qualifying_modules_open_the_next_level_with_only_its_first_module(): void
    {
        $this->paths();
        $first = $this->mcq('newbie', 'Python Basics', order: 1);
        $second = $this->mcq('newbie', 'Loops and Lists', order: 2);
        $moduleOne = $this->mcq('intermediate', 'Intermediate Module One', order: 1);
        $moduleTwo = $this->mcq('intermediate', 'Intermediate Module Two', order: 2);
        $student = $this->roleUser();

        // PDF example: 86% in 45% and 82% in 49%.
        $this->attempt($student, $first, 86, 100, 270);
        $this->attempt($student, $second, 82, 100, 294);

        $lock = $this->unlocks()->lockInfo($student, 'intermediate', 'mcq');
        $this->assertTrue($lock['unlocked']);
        $this->assertSame('qualifying_modules', $lock['unlock_type']);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'advanced', 'mcq')['unlocked']);

        $this->authenticateAs($student)
            ->get(route('student.advanced-topics.index'))
            ->assertOk()
            ->assertSee('Intermediate is unlocked for MCQ challenges')
            ->assertSee('You have 2 qualifying Newbie modules. Intermediate is unlocked: start with its first module.')
            ->assertSee('Open Intermediate');

        $this->get(route('challenges.map', 'intermediate'))->assertOk()->assertSee('Intermediate Module One');

        // Only the first module of the new level is open, on the dashboard too.
        $this->get(route('challenges.quiz', ['slug' => 'intermediate', 'challenge' => $moduleOne->id]))->assertOk();
        $this->get(route('challenges.quiz', ['slug' => 'intermediate', 'challenge' => $moduleTwo->id]))->assertForbidden();
        $this->get(route('studentDashboard'))
            ->assertOk()
            ->assertSee('Intermediate Module One')
            ->assertDontSee('Intermediate Module Two');

        // Next module: passing module one (70%) opens module two.
        ChallengeAttempt::where('user_id', $student->id)->where('challenge_id', $moduleOne->id)
            ->update(['status' => 'submitted', 'score' => 7, 'total_questions' => 10, 'time_taken_seconds' => 500]);
        $this->get(route('challenges.quiz', ['slug' => 'intermediate', 'challenge' => $moduleTwo->id]))->assertOk();
    }

    public function test_three_or_more_qualifying_modules_do_not_repeat_the_unlock_or_the_notice(): void
    {
        $this->paths();
        $modules = [
            $this->mcq('newbie', 'Module A', order: 1),
            $this->mcq('newbie', 'Module B', order: 2),
            $this->mcq('newbie', 'Module C', order: 3),
        ];
        $student = $this->roleUser();

        $this->attempt($student, $modules[0], 9, 10, 120);
        $this->attempt($student, $modules[1], 8, 10, 300);

        $service = $this->unlocks();
        $messages = $service->notifyExceptionalUnlocks($student, 'mcq');
        $this->assertCount(1, $messages);
        $this->assertStringContainsString('You have 2 qualifying Newbie modules in MCQ challenges, so you are now eligible for Intermediate.', $messages[0]);

        $this->attempt($student, $modules[2], 10, 10, 60);
        $this->assertSame([], $service->notifyExceptionalUnlocks($student, 'mcq'));
        $this->assertSame([], $this->unlocks()->notifyExceptionalUnlocks($student, 'mcq'));

        $this->assertSame(1, Notification::where('user_id', $student->id)->where('type', 'exceptional_unlock_mcq_intermediate')->count());
        $this->assertSame(0, Notification::where('user_id', $student->id)->where('type', 'like', 'exceptional_unlock_coding%')->count());
        $this->assertSame(3, $this->unlocks()->performanceSummary($student, 'newbie', 'mcq')['qualifying_modules']);
        $this->assertSame('qualifying_modules', $this->unlocks()->lockInfo($student, 'intermediate', 'mcq')['unlock_type']);
    }

    public function test_a_module_must_pass_both_score_and_time(): void
    {
        $this->paths();
        $cases = [
            'score passes, time fails' => [[90, 100, 306], false],   // 90%, 51%
            'time passes, score fails' => [[79, 100, 180], false],   // 79%, 30%
            'exactly 80% in exactly 50%' => [[80, 100, 300], true],
        ];

        foreach ($cases as $label => [[$score, $total, $seconds], $opens]) {
            Challenge::query()->update(['is_active' => false]);
            $good = $this->mcq('newbie', 'Good '.$label, order: 1);
            $tested = $this->mcq('newbie', 'Tested '.$label, order: 2);
            $student = $this->roleUser();

            $this->attempt($student, $good, 10, 10, 60);
            $this->attempt($student, $tested, $score, $total, $seconds);

            $this->assertSame($opens, $this->unlocks()->lockInfo($student, 'intermediate', 'mcq')['unlocked'], $label);
        }
    }

    public function test_intermediate_and_advanced_rules(): void
    {
        $this->paths();
        $newbieA = $this->mcq('newbie', 'Newbie A', order: 1);
        $newbieB = $this->mcq('newbie', 'Newbie B', order: 2);
        $intermediateA = $this->mcq('intermediate', 'Intermediate A', 1000, 1);
        $intermediateB = $this->mcq('intermediate', 'Intermediate B', 1000, 2);
        $advancedA = $this->mcq('advanced', 'Advanced A', 1000, 1);
        $advancedB = $this->mcq('advanced', 'Advanced B', 1000, 2);
        $student = $this->roleUser();
        $this->attempt($student, $newbieA, 10, 10, 60);
        $this->attempt($student, $newbieB, 10, 10, 60);

        // Intermediate: 75% in 70% of the time qualifies; 74% or 71% does not.
        $this->attempt($student, $intermediateA, 75, 100, 700);
        $this->attempt($student, $intermediateB, 74, 100, 100);
        $this->attempt($student, $intermediateB, 90, 100, 710, ranked: false, attemptNo: 2);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'advanced', 'mcq')['unlocked']);
        $this->attempt($student, $intermediateB, 75, 100, 700, ranked: false, attemptNo: 3);
        $this->assertSame('qualifying_modules', $this->unlocks()->lockInfo($student, 'advanced', 'mcq')['unlock_type']);

        // Advanced: 70% in 80% of the time qualifies; 81% of the time does not.
        $this->attempt($student, $advancedA, 70, 100, 800);
        $this->attempt($student, $advancedB, 95, 100, 810);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'professional', 'mcq')['unlocked']);
        $this->attempt($student, $advancedB, 70, 100, 800, ranked: false, attemptNo: 2);
        $this->assertSame('qualifying_modules', $this->unlocks()->lockInfo($student, 'professional', 'mcq')['unlock_type']);
    }

    public function test_results_from_other_levels_or_the_same_module_are_not_mixed(): void
    {
        $this->paths();
        $newbie = $this->mcq('newbie', 'Newbie Module', order: 1);
        $this->mcq('newbie', 'Newbie Module Two', order: 2);
        $university = $this->mcq('university-student', 'University Module');
        $student = $this->roleUser();

        // One qualifying Newbie module, retaken twice, plus a qualifying
        // result on another level: still one qualifying Newbie module.
        $this->attempt($student, $newbie, 9, 10, 120);
        $this->attempt($student, $newbie, 10, 10, 60, ranked: false, attemptNo: 2);
        $this->attempt($student, $newbie, 10, 10, 30, ranked: false, attemptNo: 3);
        $this->attempt($student, $university, 10, 10, 30);

        $summary = $this->unlocks()->performanceSummary($student, 'newbie', 'mcq');
        $this->assertSame(1, $summary['qualifying_modules']);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'intermediate', 'mcq')['unlocked']);
    }

    public function test_a_retake_can_make_a_module_qualify_through_the_quiz_pages(): void
    {
        $this->paths();
        [$first, $firstOptions] = $this->quizChallenge('newbie', 'Python Basics', 1);
        [$second, $secondOptions] = $this->quizChallenge('newbie', 'Loops and Lists', 2);
        $this->mcq('intermediate', 'Intermediate Pandas');
        $student = $this->roleUser();
        $this->authenticateAs($student);

        // First (ranked) try: 1 of 2 correct, 50%. The next module stays locked.
        $this->submitQuiz($student, $first, [$firstOptions[0]['correct'], $firstOptions[1]['wrong']]);
        $this->get(route('challenges.quiz', ['slug' => 'newbie', 'challenge' => $second->id]))->assertForbidden();

        // A retake at 100% passes the module, opens the next one and qualifies.
        $this->submitQuiz($student, $first, [$firstOptions[0]['correct'], $firstOptions[1]['correct']]);
        $this->get(route('challenges.map', 'newbie'))->assertOk()->assertSee('Challenge Completed');
        $this->assertFalse($this->unlocks()->lockInfo($student, 'intermediate', 'mcq')['unlocked']);

        $this->submitQuiz($student, $second, [$secondOptions[0]['correct'], $secondOptions[1]['correct']]);

        $this->assertSame('qualifying_modules', $this->unlocks()->lockInfo($student, 'intermediate', 'mcq')['unlock_type']);
        $this->assertSame(1, Notification::where('user_id', $student->id)->where('type', 'exceptional_unlock_mcq_intermediate')->count());
        $this->get(route('challenges.map', 'intermediate'))->assertOk()->assertSee('Intermediate Pandas');

        // XP still comes from the first (ranked) attempt only.
        $this->assertSame(0, (int) ChallengeAttempt::where('user_id', $student->id)->where('challenge_id', $first->id)->where('is_ranked', false)->sum('xp_awarded'));
    }

    // ── Next difficulty: coding ───────────────────────────────────────

    public function test_coding_averages_use_only_the_items_of_that_module(): void
    {
        $this->paths();
        // PDF example: four items, 82.5% average score and 47.5% average time.
        $intro = $this->coding('newbie', 'Introduction to Python', [600, 600, 600, 600], 1);
        $mixed = $this->coding('newbie', 'Loops', [600, 600], 2);
        $third = $this->coding('newbie', 'Functions', [600], 3);
        $intermediateOne = $this->coding('intermediate', 'Intermediate Coding One', [600], 1);
        $intermediateTwo = $this->coding('intermediate', 'Intermediate Coding Two', [600], 2);
        $student = $this->roleUser();

        foreach ([[18, 240], [16, 300], [17, 270], [15, 330]] as $index => [$passed, $seconds]) {
            $this->submission($student, $this->item($intro, $index), $passed, 20, $seconds);
        }

        // 100% in 20% and 60% in 90%: 80% score but 55% time on average. Mixed
        // into one level average with the module above it would pass (81.7%,
        // 50%), but each module is judged on its own items.
        $this->submission($student, $this->item($mixed, 0), 5, 5, 120);
        $this->submission($student, $this->item($mixed, 1), 3, 5, 540);

        $summary = $this->unlocks()->performanceSummary($student, 'newbie', 'coding');
        $intro = collect($summary['modules'])->firstWhere('title', 'Introduction to Python');
        $this->assertTrue($intro['qualifies']);
        $this->assertEqualsWithDelta(82.5, $intro['score'], 0.001);
        $this->assertEqualsWithDelta(47.5, $intro['time'], 0.001);
        $this->assertFalse(collect($summary['modules'])->firstWhere('title', 'Loops')['qualifies']);
        $this->assertSame(1, $summary['qualifying_modules']);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'intermediate', 'coding')['unlocked']);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'intermediate', 'mcq')['unlocked']);

        // A second qualifying coding module opens Intermediate coding only.
        $this->submission($student, $this->item($third, 0), 5, 5, 180);
        $this->assertSame('qualifying_modules', $this->unlocks()->lockInfo($student, 'intermediate', 'coding')['unlock_type']);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'intermediate', 'mcq')['unlocked']);

        $this->authenticateAs($student)
            ->get(route('student.advanced-topics.index'))
            ->assertOk()
            ->assertSee('Intermediate is unlocked for coding challenges')
            ->assertSee('Introduction to Python: <strong>82.5%</strong> score, <strong>47.5%</strong> time consumed', false);

        $this->get(route('challenges.coding.quiz', ['slug' => 'intermediate', 'challenge' => $intermediateOne->id]))->assertOk();
        $this->get(route('challenges.coding.quiz', ['slug' => 'intermediate', 'challenge' => $intermediateTwo->id]))->assertForbidden();
    }

    public function test_coding_modules_need_every_item_and_count_each_run_once(): void
    {
        $this->paths();
        $first = $this->coding('newbie', 'Coding One', [600, 600], 1);
        $second = $this->coding('newbie', 'Coding Two', [600], 2);
        $student = $this->roleUser();

        // An earlier run replaced by a retake qualified (100% in 20% and 30%);
        // the current run has only one item so far. The module counts once.
        $this->submission($student, $this->item($first, 0), 5, 5, 120, voided: true, reason: 'retake', generation: 1);
        $this->submission($student, $this->item($first, 1), 5, 5, 180, voided: true, reason: 'retake', generation: 1);
        $this->submission($student, $this->item($first, 0), 5, 5, 60);

        // Results that arrived after a retake (stale) never count.
        $this->submission($student, $this->item($second, 0), 5, 5, 60, voided: true, reason: 'stale_attempt', generation: 1);

        $summary = $this->unlocks()->performanceSummary($student, 'newbie', 'coding');
        $this->assertSame(1, $summary['qualifying_modules']);
        $this->assertFalse($this->unlocks()->lockInfo($student, 'intermediate', 'coding')['unlocked']);

        // Only one of two items submitted: not finished, so not qualifying.
        $partial = $this->roleUser();
        $this->submission($partial, $this->item($first, 0), 5, 5, 30);
        $this->submission($partial, $this->item($second, 0), 5, 5, 30);
        $this->assertSame(1, $this->unlocks()->performanceSummary($partial, 'newbie', 'coding')['qualifying_modules']);
        $this->assertFalse($this->unlocks()->lockInfo($partial, 'intermediate', 'coding')['unlocked']);
    }

    // ── Existing progress ─────────────────────────────────────────────

    public function test_levels_a_student_already_started_stay_open(): void
    {
        $this->paths();
        $this->mcq('newbie', 'Newbie Module');
        $intermediate = $this->mcq('intermediate', 'Intermediate Module');
        $advanced = $this->mcq('advanced', 'Advanced Module');

        $started = $this->roleUser();
        $this->attempt($started, $intermediate, 5, 10, 500);
        $this->assertSame('existing_progress', $this->unlocks()->lockInfo($started, 'intermediate', 'mcq')['unlock_type']);
        $this->assertFalse($this->unlocks()->lockInfo($started, 'advanced', 'mcq')['unlocked']);
        $this->authenticateAs($started)
            ->get(route('challenges.quiz', ['slug' => 'intermediate', 'challenge' => $intermediate->id]))
            ->assertOk();

        $further = $this->roleUser();
        $this->attempt($further, $advanced, 5, 10, 500);
        $this->assertTrue($this->unlocks()->lockInfo($further, 'intermediate', 'mcq')['unlocked']);
        $this->assertTrue($this->unlocks()->lockInfo($further, 'advanced', 'mcq')['unlocked']);
        $this->assertFalse($this->unlocks()->lockInfo($further, 'professional', 'mcq')['unlocked']);

        // Work on a challenge a class was given opens that challenge, not the level.
        $classStudent = $this->roleUser();
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        $class = ClassRoom::create([
            'instructor_id' => $instructor->id, 'name' => 'Section A', 'section' => 'A', 'is_archived' => false,
        ]);
        $class->students()->attach($classStudent->id, ['enrolled_at' => now()]);
        ClassChallengeAssignment::create([
            'class_id' => $class->id, 'challenge_id' => $intermediate->id, 'assigned_by' => $instructor->id,
            'title' => 'Week 3', 'status' => ClassChallengeAssignment::STATUS_PUBLISHED,
        ]);
        $this->attempt($classStudent, $intermediate, 10, 10, 60);
        $this->assertFalse($this->unlocks()->lockInfo($classStudent, 'intermediate', 'mcq')['unlocked']);
    }

    // ── AI reviewer See more ──────────────────────────────────────────

    public function test_the_reviewer_can_reveal_the_whole_code(): void
    {
        $student = $this->roleUser();
        \App\Models\IdeWorkspace::create(['user_id' => $student->id, 'name' => 'Workspace']);

        foreach ([route('ide.index'), route('sql-sandbox.index')] as $url) {
            $this->authenticateAs($student)
                ->get($url)
                ->assertOk()
                ->assertSee('function _addCode(code)', false)
                ->assertSee('rb-code-full', false)
                ->assertSee('See more', false)
                ->assertDontSee('const preview = code.length > 220', false)
                ->assertDontSee('const preview = sql.length > 220', false);
        }
    }

    // ── import os ─────────────────────────────────────────────────────

    public function test_import_os_passes_the_server_check_but_destructive_calls_do_not(): void
    {
        $policy = app(PythonCodePolicyService::class);

        $this->assertSame([], $policy->violations("import os\nprint(os.listdir(os.getcwd()))\nos.makedirs('out', exist_ok=True)\n"));
        $this->assertNotSame([], $policy->violations("import os\nos.remove('data.csv')\n"));
        $this->assertNotSame([], $policy->violations("import os\nos.system('dir')\n"));
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function unlocks(): ChallengePathUnlockService
    {
        // A fresh service each time, so no summary is cached between checks.
        return new ChallengePathUnlockService();
    }

    private function paths(): void
    {
        foreach (['newbie' => 'Newbie', 'university-student' => 'University Student', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'professional' => 'Professional'] as $slug => $name) {
            $this->categories[$slug] = ChallengeCategory::create([
                'name' => $name, 'slug' => $slug, 'target_audience' => 'Students',
                'description' => $name.' challenges', 'order_index' => count($this->categories) + 1,
            ]);
        }
    }

    private function mcq(string $slug, string $title, int $limit = 600, int $order = 1): Challenge
    {
        return $this->quizChallenge($slug, $title, $order, $limit, 1)[0];
    }

    /**
     * @return array{0: Challenge, 1: array<int, array{correct: int, wrong: int}>}
     */
    private function quizChallenge(string $slug, string $title, int $order, int $limit = 600, int $questions = 2): array
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $this->categories[$slug]->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => $limit,
            'base_xp' => 100,
            'order_index' => $order,
            'is_coding_challenge' => false,
            'is_active' => true,
            'content_code' => 'U4-'.Str::upper(Str::random(8)),
        ]);

        $options = [];
        for ($i = 1; $i <= $questions; $i++) {
            $question = $challenge->questions()->create([
                'challenge_category_id' => $this->categories[$slug]->id, 'question_text' => "Question {$i}?", 'order_index' => $i,
            ]);
            $options[] = [
                'question' => $question->id,
                'correct' => $question->options()->create(['option_text' => 'Yes', 'is_correct' => true, 'order_index' => 1])->id,
                'wrong' => $question->options()->create(['option_text' => 'No', 'is_correct' => false, 'order_index' => 2])->id,
            ];
        }

        return [$challenge, $options];
    }

    /** @param array<int, int> $optionIds one chosen option per question, in question order */
    private function submitQuiz(User $student, Challenge $challenge, array $optionIds): void
    {
        $this->authenticateAs($student)
            ->get(route('challenges.quiz', ['slug' => 'newbie', 'challenge' => $challenge->id]))
            ->assertOk();

        $attempt = ChallengeAttempt::where('user_id', $student->id)
            ->where('challenge_id', $challenge->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->firstOrFail();

        $questionIds = $challenge->questions()->orderBy('order_index')->pluck('id')->all();

        $this->post(route('challenges.quiz.submit', ['slug' => 'newbie', 'challenge' => $challenge->id]), [
            'attempt_id' => $attempt->id,
            'answers' => array_combine($questionIds, $optionIds),
        ])->assertRedirect();

        $this->assertSame('submitted', $attempt->fresh()->status);
    }

    /** @param array<int, int> $limits time limit of each item, in seconds */
    private function coding(string $slug, string $title, array $limits, int $order = 1): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $this->categories[$slug]->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => array_sum($limits),
            'base_xp' => 100,
            'order_index' => $order,
            'is_coding_challenge' => true,
            'is_active' => true,
            'content_code' => 'U4C-'.Str::upper(Str::random(8)),
        ]);

        foreach ($limits as $index => $limit) {
            $question = $challenge->codingQuestions()->create([
                'problem_description' => 'Problem '.($index + 1), 'language' => 'python',
                'time_limit_seconds' => $limit, 'base_xp' => 50, 'order_index' => $index + 1,
            ]);
            $question->testCases()->create(['input' => null, 'expected_output' => '42', 'is_hidden' => false, 'order_index' => 1]);
        }

        return $challenge;
    }

    private function item(Challenge $challenge, int $index)
    {
        return $challenge->codingQuestions()->orderBy('order_index')->get()[$index];
    }

    private function attempt(User $student, Challenge $challenge, int $score, int $total, int $seconds, bool $ranked = true, int $attemptNo = 1): void
    {
        ChallengeAttempt::create([
            'user_id' => $student->id, 'challenge_id' => $challenge->id, 'attempt_no' => $attemptNo,
            'mode' => $ranked ? 'ranked' : 'practice', 'status' => 'submitted',
            'started_at' => now()->subSeconds($seconds), 'expires_at' => now()->addMinutes(10),
            'submitted_at' => now(), 'time_limit_seconds' => $challenge->time_limit_seconds,
            'time_taken_seconds' => $seconds, 'score' => $score, 'total_questions' => $total,
            'xp_awarded' => 0, 'is_ranked' => $ranked, 'is_leaderboard_eligible' => $ranked,
            'question_order' => [], 'option_order' => [],
        ]);
    }

    private function submission(User $student, $question, int $passed, int $total, int $seconds, bool $voided = false, ?string $reason = null, ?int $generation = null): void
    {
        CodingSubmission::create([
            'user_id' => $student->id, 'coding_question_id' => $question->id, 'code' => 'print(42)',
            'language' => 'python', 'status' => $passed === $total ? 'passed' : 'failed',
            'tests_passed' => $passed, 'tests_total' => $total, 'xp_earned' => 0,
            'time_taken_seconds' => $seconds, 'voided' => $voided, 'void_reason' => $reason,
            'attempt_generation' => $generation,
        ]);
    }
}
