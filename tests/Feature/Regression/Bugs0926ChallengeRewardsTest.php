<?php

namespace Tests\Feature\Regression;

use App\Models\ChallengeAttempt;
use App\Models\CodingSubmission;
use App\Models\IdeNode;
use App\Models\IdeWorkspace;
use App\Models\Notification;
use App\Models\StudentMissionProgress;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\GamificationService;
use Database\Seeders\AchievementDefinitionsSeeder;
use Database\Seeders\MissionDefinitionsSeeder;
use Database\Seeders\RanksSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsMcqChallengeWorkflow;
use Tests\TestCase;

/**
 * Reported 2026-09-26 (Bugs.pdf, bug 2): after an MCQ challenge the XP did
 * not reach the dashboard, no achievement was unlocked, the rank did not
 * change, and the attempt was missing from My Submissions.
 */
class Bugs0926ChallengeRewardsTest extends TestCase
{
    use BuildsMcqChallengeWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RanksSeeder::class, AchievementDefinitionsSeeder::class, MissionDefinitionsSeeder::class]);
    }

    public function test_leaving_the_page_a_few_times_keeps_the_score_xp_and_the_rewards(): void
    {
        $student = $this->mcqStudent();
        $student->forceFill(['xp' => 480])->save();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        // Five app switches (screenshots, a notification, ...) reach the
        // suspicious-activity limit.
        for ($i = 0; $i < 5; $i++) {
            $this->actAsStudent($student)
                ->postJson($this->quizUrl($challenge, '/events'), [
                    'attempt_id' => $attempt->id,
                    'event_type' => 'tab_hidden_or_app_switched',
                ])
                ->assertOk();
        }

        $this->actAsStudent($student)
            ->post($this->quizUrl($challenge, '/submit'), [
                'attempt_id' => $attempt->id,
                'answers' => collect($questions)->mapWithKeys(fn ($row) => [$row['question']->id => $row['correct']->id])->all(),
            ])
            ->assertRedirect();

        $attempt->refresh();
        $this->assertSame('submitted', $attempt->status);
        $this->assertSame(100, (int) $attempt->xp_awarded, 'Score XP (base 100 x 100%) is kept; only the speed bonus is withheld.');
        $this->assertFalse((bool) $attempt->is_leaderboard_eligible, 'The attempt stays flagged for the instructor.');

        $student->refresh();
        $this->assertGreaterThanOrEqual(580, (int) $student->xp);

        $earned = UserAchievement::where('user_id', $student->id)
            ->with('achievement')
            ->get()
            ->pluck('achievement.achievement_key')
            ->all();
        $this->assertContains('first_challenge_pass', $earned);
        $this->assertContains('rank_advancer', $earned);

        $mission = StudentMissionProgress::query()
            ->where('user_id', $student->id)
            ->whereHas('mission', fn ($query) => $query->where('mission_key', 'daily_attempt_challenge'))
            ->first();
        $this->assertNotNull($mission);
        $this->assertTrue((bool) $mission->is_completed);

        $rankUp = Notification::where('user_id', $student->id)->where('type', 'rank_up_2')->first();
        $this->assertNotNull($rankUp, 'Crossing 500 XP announces the Practitioner rank.');
        $this->assertStringContainsString('Practitioner', $rankUp->notification_text);

        $this->actAsStudent($student)
            ->get(route('studentDashboard'))
            ->assertOk()
            ->assertSee(number_format((int) $student->xp));
    }

    public function test_a_clean_attempt_still_earns_the_speed_bonus(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        $this->actAsStudent($student)
            ->post($this->quizUrl($challenge, '/submit'), [
                'attempt_id' => $attempt->id,
                'answers' => collect($questions)->mapWithKeys(fn ($row) => [$row['question']->id => $row['correct']->id])->all(),
            ])
            ->assertRedirect();

        $attempt->refresh();
        $this->assertTrue((bool) $attempt->is_leaderboard_eligible);
        $this->assertGreaterThan(100, (int) $attempt->xp_awarded);
    }

    public function test_finished_challenges_are_listed_in_my_submissions(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge(['title' => 'Strings and Spaces']);
        $attempt = $this->startMcqAttempt($student, $challenge);

        $this->actAsStudent($student)
            ->post($this->quizUrl($challenge, '/submit'), [
                'attempt_id' => $attempt->id,
                'answers' => [$questions[0]['question']->id => $questions[0]['correct']->id],
            ])
            ->assertRedirect();

        [$coding] = $this->mcqChallenge(['title' => 'Loops Coding', 'is_coding_challenge' => true, 'content_code' => 'CODE-DS-REG']);
        $question = $coding->codingQuestions()->create([
            'problem_description' => 'Print 42.', 'language' => 'python', 'time_limit_seconds' => 600,
            'base_xp' => 100, 'order_index' => 1, 'title' => 'Print the answer',
        ]);
        CodingSubmission::create([
            'user_id' => $student->id, 'coding_question_id' => $question->id, 'code' => 'print(42)',
            'language' => 'python', 'status' => 'passed', 'tests_passed' => 3, 'tests_total' => 3,
            'xp_earned' => 100, 'time_taken_seconds' => 30, 'voided' => false,
        ]);

        $this->actAsStudent($student)
            ->get(route('student.submissions.index'))
            ->assertOk()
            ->assertSee('Challenge Results')
            ->assertSee('Strings and Spaces')
            ->assertSee('Multiple choice')
            ->assertSee('1/2')
            ->assertSee(route('challenges.quiz.result', ['slug' => 'newbie', 'challenge' => $challenge->id, 'attempt' => $attempt->id]), false)
            ->assertSee('Loops Coding')
            ->assertSee('Print the answer')
            ->assertSee('3/3');
    }

    public function test_achievements_page_catches_up_on_work_saved_earlier(): void
    {
        $student = $this->mcqStudent();
        [$challenge] = $this->mcqChallenge();

        ChallengeAttempt::create([
            'user_id' => $student->id, 'challenge_id' => $challenge->id, 'attempt_no' => 1, 'mode' => 'ranked',
            'status' => 'submitted', 'started_at' => now()->subMinutes(5), 'expires_at' => now()->addMinutes(5),
            'submitted_at' => now()->subMinute(), 'time_limit_seconds' => 600, 'time_taken_seconds' => 240,
            'score' => 2, 'total_questions' => 2, 'xp_awarded' => 100, 'is_ranked' => true, 'is_leaderboard_eligible' => true,
            'question_order' => [], 'option_order' => [],
        ]);
        $this->assertSame(0, UserAchievement::where('user_id', $student->id)->count());

        $this->actAsStudent($student)
            ->get(route('student.achievements.index'))
            ->assertOk()
            ->assertSee('First Challenge Pass')
            ->assertSee('Progress: 0 of 3');

        $this->assertTrue(UserAchievement::where('user_id', $student->id)
            ->whereHas('achievement', fn ($query) => $query->where('achievement_key', 'first_challenge_pass'))
            ->exists());
    }

    public function test_a_code_run_advances_the_run_code_mission_and_first_run_achievement(): void
    {
        $student = $this->mcqStudent();
        $workspace = IdeWorkspace::create(['user_id' => $student->id, 'name' => 'Workspace']);
        $node = IdeNode::create([
            'workspace_id' => $workspace->id, 'parent_id' => null, 'user_id' => $student->id,
            'type' => 'file', 'name' => 'main.py', 'content' => 'print(1)', 'language' => 'python',
        ]);
        DB::table('ide_execution_logs')->insert([
            'node_id' => $node->id, 'user_id' => $student->id, 'output' => '1', 'error' => '',
            'exit_code' => 0, 'execution_time_ms' => 10, 'created_at' => now(), 'updated_at' => now(),
        ]);

        app(GamificationService::class)->recordCodeRun($student);

        $this->assertTrue(UserAchievement::where('user_id', $student->id)
            ->whereHas('achievement', fn ($query) => $query->where('achievement_key', 'first_code_run'))
            ->exists());
        $this->assertTrue(StudentMissionProgress::query()
            ->where('user_id', $student->id)
            ->where('is_completed', true)
            ->whereHas('mission', fn ($query) => $query->where('mission_key', 'daily_run_code'))
            ->exists());
    }
}
