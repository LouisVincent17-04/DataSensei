<?php

namespace Tests\Feature\Regression;

use App\Models\AchievementDefinition;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use App\Models\CodingQuestion;
use App\Models\CodingSubmission;
use App\Models\MissionDefinition;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\GamificationService;
use App\Services\PythonSandboxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\FakePythonSandbox;
use Tests\TestCase;

/**
 * DataSensei Updates 5, task 1: XP comes only from platform (admin) content,
 * the same for every learner. Class and instructor work gives exactly 0 XP,
 * directly or through achievements, missions and streaks.
 */
class Updates5ClassWorkXpTest extends TestCase
{
    use RefreshDatabase;

    private array $categories = [];

    public function test_platform_challenges_award_xp_but_class_challenges_award_none(): void
    {
        $this->paths();
        $this->rewards();
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        [$class, $student] = $this->classWithStudent($instructor);

        [$platform, $platformOptions] = $this->quiz('newbie', 'Platform Quiz');
        [$university, $universityOptions] = $this->quiz('university-student', 'University Quiz');
        [$instructorQuiz, $instructorOptions] = $this->quiz('university-student', 'Instructor Quiz', $instructor);
        $this->give($class, $instructorQuiz, $instructor);

        // Class work first: no XP, no achievement, no mission, no streak.
        $this->finishQuiz($student, 'university-student', $university, $universityOptions);
        $this->finishQuiz($student, 'university-student', $instructorQuiz, $instructorOptions);

        $student->refresh();
        $this->assertSame(0, (int) $student->xp);
        $this->assertSame(0, UserAchievement::where('user_id', $student->id)->count());
        $this->assertSame(0, (int) DB::table('student_mission_progress')->where('user_id', $student->id)->sum('progress_count'));
        $this->assertSame(0, (int) $student->streak);
        $this->assertSame(0, (int) ChallengeAttempt::where('user_id', $student->id)->sum('xp_awarded'));

        // The same work on a DataSensei challenge earns XP and rewards.
        $this->finishQuiz($student, 'newbie', $platform, $platformOptions);

        $student->refresh();
        $attempt = ChallengeAttempt::where('user_id', $student->id)->where('challenge_id', $platform->id)->firstOrFail();
        $this->assertGreaterThan(0, (int) $attempt->xp_awarded);
        $this->assertSame(1, UserAchievement::where('user_id', $student->id)->count(), 'First challenge pass counts platform work.');
        $this->assertSame((int) $attempt->xp_awarded + 50 + 35, (int) $student->xp, 'Challenge XP + achievement (50) + daily mission (35).');

        // The class challenge page says so.
        $this->authenticateAs($student)
            ->get(route('challenges.map', 'university-student'))
            ->assertOk()
            ->assertSee('No XP (class work)');
    }

    public function test_class_coding_challenges_award_no_xp(): void
    {
        $this->paths();
        [$class, $student] = $this->classWithStudent($this->roleUser(User::ROLE_INSTRUCTOR));
        $this->app->instance(PythonSandboxService::class, new FakePythonSandbox(fn () => FakePythonSandbox::ok('42')));

        $platform = $this->codingChallenge('newbie', 'Platform Coding');
        $university = $this->codingChallenge('university-student', 'University Coding');

        foreach ([['newbie', $platform], ['university-student', $university]] as [$slug, $challenge]) {
            $question = $challenge->codingQuestions()->firstOrFail();
            $this->authenticateAs($student)->get(route('challenges.coding.quiz', ['slug' => $slug, 'challenge' => $challenge->id]))->assertOk();
            $this->postJson(route('challenges.coding.start', ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]))->assertOk();
            $this->postJson(route('challenges.coding.submit', ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]), ['code' => 'print(42)'])
                ->assertOk()
                ->assertJsonPath('status', 'passed');
        }

        $platformXp = (int) CodingSubmission::whereIn('coding_question_id', $platform->codingQuestions()->pluck('id'))->value('xp_earned');
        $this->assertGreaterThan(0, $platformXp);
        $this->assertSame(0, (int) CodingSubmission::whereIn('coding_question_id', $university->codingQuestions()->pluck('id'))->value('xp_earned'));
        $this->assertSame($platformXp, (int) $student->fresh()->xp);
    }

    public function test_class_work_never_counts_toward_achievements_or_missions(): void
    {
        $this->paths();
        $student = $this->roleUser();
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        $classQuiz = $this->quiz('newbie', 'Instructor Newbie Quiz', $instructor)[0];
        $universityQuiz = $this->quiz('university-student', 'University Quiz')[0];
        $platformQuiz = $this->quiz('newbie', 'Platform Quiz')[0];

        AchievementDefinition::create([
            'achievement_key' => 'passes', 'name' => 'Passes', 'description' => '', 'icon' => 'P', 'badge_color' => 'blue',
            'xp_reward' => 100, 'criteria_type' => 'challenge_passes', 'criteria_value' => 2, 'is_active' => true, 'sort_order' => 1,
        ]);
        AchievementDefinition::create([
            'achievement_key' => 'assignment_ready', 'name' => 'Assignment Ready', 'description' => '', 'icon' => 'A', 'badge_color' => 'blue',
            'xp_reward' => 100, 'criteria_type' => 'assignment_submissions', 'criteria_value' => 0, 'is_active' => true, 'sort_order' => 2,
        ]);

        foreach ([$classQuiz, $universityQuiz] as $challenge) {
            $this->attempt($student, $challenge, 10, 10);
        }

        $service = app(GamificationService::class);
        $this->assertSame([], $service->evaluateAchievements($student), 'Two class challenges passed, but neither counts.');
        $this->assertSame(0, (int) $student->fresh()->xp);

        $this->attempt($student, $platformQuiz, 10, 10);
        $this->assertSame([], $service->evaluateAchievements($student), 'Only one platform challenge passed so far.');

        // Assignments and assessments give nothing at all.
        $this->assertSame([], $service->awardForAssignmentSubmission($student, new \App\Models\AssignmentSubmission()));
        $this->assertSame([], $service->recordAssessmentSubmission($student, 1));
        $this->assertSame(0, (int) $student->fresh()->xp);
    }

    public function test_the_migration_switches_off_class_work_rewards(): void
    {
        $classAchievement = AchievementDefinition::create([
            'achievement_key' => 'assignment_ready', 'name' => 'Assignment Ready', 'description' => '', 'icon' => 'A', 'badge_color' => 'blue',
            'xp_reward' => 100, 'criteria_type' => 'assignment_submissions', 'criteria_value' => 1, 'is_active' => true, 'sort_order' => 1,
        ]);
        $universityPath = AchievementDefinition::create([
            'achievement_key' => 'path_university_student_complete', 'name' => 'University Path', 'description' => '', 'icon' => 'U', 'badge_color' => 'blue',
            'xp_reward' => 140, 'criteria_type' => 'path_complete', 'criteria_value' => 1, 'is_active' => true, 'sort_order' => 2,
        ]);
        $platformAchievement = AchievementDefinition::create([
            'achievement_key' => 'first_challenge_pass', 'name' => 'First Pass', 'description' => '', 'icon' => 'F', 'badge_color' => 'blue',
            'xp_reward' => 50, 'criteria_type' => 'challenge_passes', 'criteria_value' => 1, 'is_active' => true, 'sort_order' => 3,
        ]);
        $classMission = MissionDefinition::create([
            'mission_key' => 'weekly_assignment_progress', 'title' => 'Assignments', 'description' => '', 'period_type' => 'weekly',
            'target_type' => 'assignment_submissions', 'target_count' => 2, 'xp_reward' => 150, 'is_active' => true, 'sort_order' => 1,
        ]);

        $migration = require database_path('migrations/2026_09_28_000001_turn_off_class_activity_rewards.php');
        $migration->up();
        $migration->up();

        $this->assertFalse((bool) $classAchievement->fresh()->is_active);
        $this->assertFalse((bool) $universityPath->fresh()->is_active);
        $this->assertTrue((bool) $platformAchievement->fresh()->is_active);
        $this->assertFalse((bool) $classMission->fresh()->is_active);
    }

    public function test_class_work_xp_already_earned_can_be_taken_back_once(): void
    {
        $this->paths();
        $student = $this->roleUser(User::ROLE_USER, ['xp' => 1000]);
        $other = $this->roleUser(User::ROLE_USER, ['xp' => 30]);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);

        $platformQuiz = $this->quiz('newbie', 'Platform Quiz')[0];
        $classQuiz = $this->quiz('newbie', 'Instructor Quiz', $instructor)[0];
        $universityQuiz = $this->quiz('university-student', 'University Quiz')[0];
        $this->attempt($student, $platformQuiz, 10, 10, xp: 300);
        $this->attempt($student, $classQuiz, 10, 10, xp: 200);
        $this->attempt($student, $universityQuiz, 10, 10, xp: 150);

        // Coding XP was credited as the gain over the best earlier result, so
        // 60 then 90 on one problem credited 90 in total.
        $university = $this->codingChallenge('university-student', 'University Coding');
        $question = $university->codingQuestions()->firstOrFail();
        $this->submission($student, $question, 60, voided: true);
        $this->submission($student, $question, 90);
        $this->submission($other, $question, 80);

        Artisan::call('xp:remove-class-work', ['--dry-run' => true]);
        $this->assertStringContainsString('Would remove', Artisan::output());
        $this->assertSame(1000, (int) $student->fresh()->xp, 'A dry run changes nothing.');

        Artisan::call('xp:remove-class-work');
        $this->assertSame(1000 - 200 - 150 - 90, (int) $student->fresh()->xp);
        $this->assertSame(0, (int) $other->fresh()->xp, 'A balance never goes below 0.');
        $this->assertSame(300, (int) ChallengeAttempt::where('challenge_id', $platformQuiz->id)->value('xp_awarded'), 'Platform XP stays.');
        $this->assertSame(0, (int) ChallengeAttempt::where('challenge_id', $classQuiz->id)->value('xp_awarded'));
        $this->assertSame(0, (int) CodingSubmission::where('coding_question_id', $question->id)->max('xp_earned'));

        Artisan::call('xp:remove-class-work');
        $this->assertSame(560, (int) $student->fresh()->xp, 'Running it again removes nothing more.');
        $this->assertStringContainsString('No class-work XP to remove.', Artisan::output());
    }

    public function test_instructor_challenges_are_saved_with_no_xp(): void
    {
        $this->paths();
        $instructor = $this->activeInstructor();

        $this->authenticateAs($instructor)->post(route('instructor.challenge-builder.store'), [
            'type' => 'mcq',
            'title' => 'Week 1 quiz',
            'description' => 'Quiz',
            'time_limit_seconds' => 600,
            'base_xp' => 5000,
            'is_active' => 1,
            'questions' => [[
                'question_text' => 'Pick yes',
                'correct_option' => 0,
                'options' => [['option_text' => 'Yes'], ['option_text' => 'No']],
            ]],
        ])->assertRedirect();

        $challenge = Challenge::where('title', 'Week 1 quiz')->firstOrFail();
        $this->assertSame(0, (int) $challenge->base_xp);
        $this->assertFalse($challenge->awardsXp());
    }

    public function test_instructors_cannot_reach_the_admin_xp_settings(): void
    {
        $instructor = $this->activeInstructor();
        $achievement = AchievementDefinition::create([
            'achievement_key' => 'first_challenge_pass', 'name' => 'First Pass', 'description' => '', 'icon' => 'F', 'badge_color' => 'blue',
            'xp_reward' => 50, 'criteria_type' => 'challenge_passes', 'criteria_value' => 1, 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->authenticateAs($instructor)->get(route('admin.gamification.index'))->assertStatus(403);
        $this->delete(route('admin.gamification.achievements.destroy', $achievement))->assertStatus(403);
        $this->post(route('admin.gamification.achievements.sync'))->assertStatus(403);
        $this->assertSame(50, (int) $achievement->fresh()->xp_reward);
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    /** Instructors need an active institution to pass the `active` middleware. */
    private function activeInstructor(): User
    {
        $institution = \App\Models\Institution::create([
            'name' => 'Updates5 Institution',
            'email' => 'updates5-'.Str::lower(Str::random(6)).'@institution.test',
            'status' => 'active',
        ]);

        return $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
    }

    private function paths(): void
    {
        foreach (['newbie' => 'Newbie', 'university-student' => 'University Student', 'intermediate' => 'Intermediate'] as $slug => $name) {
            $this->categories[$slug] = ChallengeCategory::create([
                'name' => $name, 'slug' => $slug, 'target_audience' => 'Students',
                'description' => $name, 'order_index' => count($this->categories) + 1,
            ]);
        }
    }

    private function rewards(): void
    {
        AchievementDefinition::create([
            'achievement_key' => 'first_challenge_pass', 'name' => 'First Challenge Pass', 'description' => '', 'icon' => 'F', 'badge_color' => 'green',
            'xp_reward' => 50, 'criteria_type' => 'challenge_passes', 'criteria_value' => 1, 'is_active' => true, 'sort_order' => 1,
        ]);
        MissionDefinition::create([
            'mission_key' => 'daily_attempt_challenge', 'title' => 'Attempt a Challenge', 'description' => '', 'period_type' => 'daily',
            'target_type' => 'challenge_attempts', 'target_count' => 1, 'xp_reward' => 35, 'is_active' => true, 'sort_order' => 1,
        ]);
    }

    /** @return array{0: ClassRoom, 1: User} */
    private function classWithStudent(User $instructor): array
    {
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'IT 4A', 'section' => 'A', 'is_archived' => false]);
        $student = $this->roleUser();
        $class->students()->attach($student->id, ['enrolled_at' => now()]);

        return [$class, $student];
    }

    private function give(ClassRoom $class, Challenge $challenge, User $instructor): void
    {
        ClassChallengeAssignment::create([
            'class_id' => $class->id, 'challenge_id' => $challenge->id, 'assigned_by' => $instructor->id,
            'title' => $challenge->title, 'status' => ClassChallengeAssignment::STATUS_PUBLISHED,
        ]);
    }

    /** @return array{0: Challenge, 1: array<int, int>} correct option id per question */
    private function quiz(string $slug, string $title, ?User $instructor = null): array
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $this->categories[$slug]->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => false,
            'is_active' => true,
            'content_code' => 'U5-'.Str::upper(Str::random(8)),
            'visibility' => $instructor ? Challenge::VISIBILITY_INSTRUCTOR : Challenge::VISIBILITY_PLATFORM,
            'created_by' => $instructor?->id,
        ]);
        $question = $challenge->questions()->create([
            'challenge_category_id' => $this->categories[$slug]->id, 'question_text' => 'Question?', 'order_index' => 1,
        ]);
        $correct = $question->options()->create(['option_text' => 'Yes', 'is_correct' => true, 'order_index' => 1]);
        $question->options()->create(['option_text' => 'No', 'is_correct' => false, 'order_index' => 2]);

        return [$challenge, [$question->id => $correct->id]];
    }

    private function finishQuiz(User $student, string $slug, Challenge $challenge, array $answers): void
    {
        $this->authenticateAs($student)
            ->get(route('challenges.quiz', ['slug' => $slug, 'challenge' => $challenge->id]))
            ->assertOk();

        $attempt = ChallengeAttempt::where('user_id', $student->id)
            ->where('challenge_id', $challenge->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $this->post(route('challenges.quiz.submit', ['slug' => $slug, 'challenge' => $challenge->id]), [
            'attempt_id' => $attempt->id,
            'answers' => $answers,
        ])->assertRedirect();
    }

    private function codingChallenge(string $slug, string $title): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $this->categories[$slug]->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => true,
            'is_active' => true,
            'content_code' => 'U5C-'.Str::upper(Str::random(8)),
        ]);
        $question = $challenge->codingQuestions()->create([
            'problem_description' => 'Print 42', 'language' => 'python',
            'time_limit_seconds' => 600, 'base_xp' => 100, 'order_index' => 1,
        ]);
        $question->testCases()->create(['input' => null, 'expected_output' => '42', 'is_hidden' => false, 'order_index' => 1]);

        return $challenge;
    }

    private function attempt(User $student, Challenge $challenge, int $score, int $total, int $xp = 0): void
    {
        ChallengeAttempt::create([
            'user_id' => $student->id, 'challenge_id' => $challenge->id, 'attempt_no' => 1,
            'mode' => 'ranked', 'status' => 'submitted',
            'started_at' => now()->subMinutes(5), 'expires_at' => now()->addMinutes(5), 'submitted_at' => now(),
            'time_limit_seconds' => 600, 'time_taken_seconds' => 120, 'score' => $score, 'total_questions' => $total,
            'xp_awarded' => $xp, 'is_ranked' => true, 'is_leaderboard_eligible' => true,
            'question_order' => [], 'option_order' => [],
        ]);
    }

    private function submission(User $student, CodingQuestion $question, int $xp, bool $voided = false): void
    {
        CodingSubmission::create([
            'user_id' => $student->id, 'coding_question_id' => $question->id, 'code' => 'print(42)',
            'language' => 'python', 'status' => 'passed', 'tests_passed' => 1, 'tests_total' => 1,
            'xp_earned' => $xp, 'time_taken_seconds' => 60, 'voided' => $voided,
            'void_reason' => $voided ? 'retake' : null,
        ]);
    }
}
