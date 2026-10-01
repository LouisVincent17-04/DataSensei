<?php

namespace Tests\Feature\Regression;

use App\Models\AchievementDefinition;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\User;
use App\Models\UserAchievement;
use App\Support\BackgroundArtisanLauncher;
use Database\Seeders\AchievementDefinitionsSeeder;
use Database\Seeders\RanksSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsMcqChallengeWorkflow;
use Tests\TestCase;

/**
 * Reported 2026-09-27: the AI reviewer in the Python IDE and SQL Sandbox did
 * not answer follow-up questions and "forgot" the code that was run; and
 * achievements should be synced for every student.
 */
class Bugs0927ReviewerMemoryAndAchievementSyncTest extends TestCase
{
    use BuildsMcqChallengeWorkflow;
    use RefreshDatabase;

    private const ERROR_RUN = "STDOUT:\nName: Hi Ana\n\nSTDERR:\nTraceback (most recent call last):\n  File \"main.py\", line 3, in <module>\n    print(10 / 0)\nZeroDivisionError: division by zero\n\nInput typed during the run:\nAna\n\nExit code: 1";

    private const CODE = "name = input(\"Name: \")\nprint(\"Hi\", name)\nprint(10 / 0)";

    // ── AI reviewer follow-ups ────────────────────────────────────────

    public function test_follow_up_carries_the_numbered_code_run_facts_and_conversation(): void
    {
        $this->reviewer();
        Http::fake(['*' => Http::response($this->ollama('Line 3 divides 10 by 0, which Python cannot do.'), 200)]);

        $this->chat([
            'code' => self::CODE,
            'run_output' => self::ERROR_RUN,
            'previous_context' => "REVIEWER: Status: Has Issues\n---\nSTUDENT: why did it stop?",
            'question' => 'Which line fails?',
        ])->assertOk()->assertJsonPath('message', 'Line 3 divides 10 by 0, which Python cannot do.');

        Http::assertSent(function (ClientRequest $request): bool {
            $prompt = (string) $request['prompt'];

            return str_contains($prompt, '3 | print(10 / 0)')
                && str_contains($prompt, 'Error reported: ZeroDivisionError (Line 3)')
                && str_contains($prompt, "Input typed during the run:\nAna")
                && str_contains($prompt, 'STUDENT: why did it stop?')
                // The code and the question come last, the conversation first.
                && strpos($prompt, '<conversation_so_far>') < strpos($prompt, '<code_that_was_run')
                && strpos($prompt, '<code_that_was_run') < strpos($prompt, '<student_question>')
                && str_contains((string) $request['system'], 'Name the line number')
                && $request['options']['temperature'] === 0.1;
        });
    }

    public function test_long_code_and_a_long_conversation_are_answered_and_fit_the_model(): void
    {
        $this->reviewer(['code_execution.ollama.num_ctx' => 4096, 'code_execution.ollama.chat_num_predict' => 640]);
        Http::fake(['*' => Http::response($this->ollama('The last line prints the total.'), 200)]);

        $code = implode("\n", array_map(fn (int $i): string => "total_{$i} = compute(value_{$i}) + {$i}", range(1, 900)))
            ."\nprint(total_900)";
        $this->assertGreaterThan(10_000, strlen($code), 'Longer than the old 10,000 character limit.');

        $this->chat([
            'code' => $code,
            'run_output' => "STDOUT:\n900\n\nExit code: 0",
            'previous_context' => str_repeat("REVIEWER: an earlier, fairly long answer about the code.\n---\n", 400),
            'question' => 'What does the last line print?',
        ])->assertOk()->assertJsonPath('message', 'The last line prints the total.');

        Http::assertSent(function (ClientRequest $request): bool {
            $prompt = (string) $request['prompt'];
            $maxChars = (4096 - 640 - 450) * 3;

            return strlen($prompt) <= $maxChars
                && str_contains($prompt, '901 | print(total_900)')
                && str_contains($prompt, "\n1 | total_1 = compute(value_1) + 1")
                && str_contains($prompt, 'What does the last line print?');
        });
    }

    public function test_questions_about_the_students_own_code_are_answered(): void
    {
        $this->reviewer();
        Http::fake(['*' => Http::response($this->ollama('Yes. The WHERE clause keeps only grade 10.'), 200)]);

        foreach (['Did I write the query correctly?', 'Why does my loop make the program slow?', 'Give me a hint for my code'] as $question) {
            $this->chat(['language' => 'sqlite', 'code' => 'SELECT name FROM students WHERE grade = 10;', 'question' => $question])
                ->assertOk()
                ->assertJsonPath('message', 'Yes. The WHERE clause keeps only grade 10.');
        }
        Http::assertSentCount(3);

        $this->chat(['code' => self::CODE, 'question' => 'Write me the corrected code'])
            ->assertOk()
            ->assertJsonPath('source', 'policy');
        Http::assertSentCount(3);
    }

    public function test_edited_code_and_the_sql_schema_reach_the_reviewer(): void
    {
        $this->reviewer();
        Http::fake(['*' => Http::response($this->ollama('The edited line 3 divides by 2, so it no longer fails.'), 200)]);

        $this->chat([
            'code' => self::CODE,
            'current_code' => str_replace('10 / 0', '10 / 2', self::CODE),
            'run_output' => self::ERROR_RUN,
            'question' => 'Will it work now?',
        ])->assertOk();

        Http::assertSent(fn (ClientRequest $request): bool => str_contains((string) $request['prompt'], '<current_editor_code note="edited after the run, not run yet">')
            && str_contains((string) $request['prompt'], '3 | print(10 / 2)'));

        $this->chat([
            'language' => 'sqlite',
            'code' => 'SELECT nme FROM students;',
            'schema' => "students(id INTEGER PRIMARY KEY, name TEXT, grade INTEGER)",
            'run_output' => 'SQL Error on statement 1: no such column: nme',
            'question' => 'Why does it fail?',
        ])->assertOk();

        Http::assertSent(fn (ClientRequest $request): bool => str_contains((string) $request['prompt'], "<database_schema>\nstudents(id INTEGER PRIMARY KEY, name TEXT, grade INTEGER)")
            && str_contains((string) $request['prompt'], 'language="SQL"'));
    }

    public function test_a_busy_reviewer_queues_the_follow_up_instead_of_skipping_it(): void
    {
        $this->reviewer([
            'code_execution.ollama.background_continuation' => true,
            'code_execution.ollama.max_concurrent_requests' => 1,
        ]);
        $launches = [];
        $this->app->instance(BackgroundArtisanLauncher::class, new class($launches) extends BackgroundArtisanLauncher {
            public function __construct(private array &$launches)
            {
            }

            public function launch(array $arguments): bool
            {
                $this->launches[] = $arguments;

                return true;
            }
        });
        Http::fake();

        // Another review (for example the previous run's, still finishing in
        // the background) holds the only model slot.
        $slot = Cache::lock('datasensei:code-review:slot:0', 60);
        $this->assertTrue($slot->get());

        try {
            $response = $this->chat(['code' => self::CODE, 'run_output' => self::ERROR_RUN, 'question' => 'Which line fails?']);
        } finally {
            $slot->release();
        }

        $response->assertOk()->assertJsonPath('pending', true)->assertJsonPath('source', 'background_review');
        $this->assertCount(1, $launches);
        Http::assertNothingSent();
    }

    public function test_an_unanswered_follow_up_says_so_instead_of_repeating_the_error_summary(): void
    {
        $this->reviewer();
        Http::fake(function (): never {
            throw new ConnectionException('cURL error 7: Failed to connect to 127.0.0.1 port 11434');
        });

        $message = (string) $this->chat(['code' => self::CODE, 'run_output' => self::ERROR_RUN, 'question' => 'Which line fails?'])
            ->assertOk()
            ->json('message');

        $this->assertStringStartsWith('Status: Not Answered', $message);
        $this->assertStringContainsString('ask the same question again', $message);
        $this->assertStringNotContainsString('Steps to Fix', $message);
    }

    public function test_the_pages_keep_the_reviewer_context_and_send_it(): void
    {
        $student = $this->roleUser();
        \App\Models\IdeWorkspace::create(['user_id' => $student->id, 'name' => 'Workspace']);

        $this->authenticateAs($student)
            ->get(route('ide.index'))
            ->assertOk()
            ->assertSee("const STATE_KEY = \"datasensei:reviewer:ide:{$student->id}\"", false)
            ->assertSee("form.append('current_code'", false)
            ->assertSee('editorSnapshot', false)
            ->assertDontSee('CODE_GEN_RE', false);

        $this->get(route('sql-sandbox.index'))
            ->assertOk()
            ->assertSee("const STATE_KEY = \"datasensei:reviewer:sql:{$student->id}\"", false)
            ->assertSee("_history.push({ role: 'user', content: question });", false)
            ->assertSee("form.append('schema',   _schema());", false)
            ->assertSee("if (!type.includes('text/event-stream'))", false)
            ->assertDontSee('CODE_GEN_RE', false);
    }

    // ── Achievement sync ──────────────────────────────────────────────

    public function test_sync_unlocks_what_every_student_already_earned_once(): void
    {
        $this->seed([RanksSeeder::class, AchievementDefinitionsSeeder::class]);
        [$challenge] = $this->mcqChallenge();
        $first = $this->mcqStudent();
        $second = $this->mcqStudent();
        $idle = $this->mcqStudent();
        $this->passedAttempt($first, $challenge);
        $this->passedAttempt($second, $challenge);
        $second->forceFill(['xp' => 650])->save();

        $this->artisan('achievements:sync')
            ->expectsOutputToContain('Students checked: 3')
            ->expectsOutputToContain('Achievements unlocked: 3')
            ->assertExitCode(0);

        $this->assertTrue($this->has($first, 'first_challenge_pass'));
        $this->assertTrue($this->has($second, 'first_challenge_pass'));
        $this->assertTrue($this->has($second, 'rank_advancer'));
        $this->assertSame(0, UserAchievement::where('user_id', $idle->id)->count());
        $this->assertSame(50, (int) $first->fresh()->xp, 'The achievement XP reward is added once.');

        $this->artisan('achievements:sync')
            ->expectsOutputToContain('Achievements unlocked: 0')
            ->assertExitCode(0);
        $this->assertSame(3, UserAchievement::count());
    }

    /**
     * The rule behind an achievement is a system setting (DataSensei Updates
     * 7): the admin page no longer shows or accepts it, so a posted rule type,
     * valid or not, leaves the stored rule as it is.
     */
    public function test_admin_can_sync_achievements_and_cannot_change_their_rule(): void
    {
        $this->seed([AchievementDefinitionsSeeder::class]);
        [$challenge] = $this->mcqChallenge();
        $student = $this->mcqStudent();
        $this->passedAttempt($student, $challenge);
        $admin = $this->roleUser(User::ROLE_ADMIN);

        $this->authenticateAs($admin)
            ->get(route('admin.gamification.index'))
            ->assertOk()
            ->assertSee('Sync achievements for all students')
            ->assertDontSee('Different MCQ challenges passed (70% or more)')
            ->assertDontSee('Criteria Type');

        $this->post(route('admin.gamification.achievements.sync'))
            ->assertRedirect(route('admin.gamification.index'))
            ->assertSessionHas('success', fn (string $message): bool => str_contains($message, '1 newly unlocked'));
        $this->assertTrue($this->has($student, 'first_challenge_pass'));

        $rule = AchievementDefinition::where('achievement_key', 'coding_starter')->firstOrFail();
        $payload = [
            'achievement_key' => $rule->achievement_key, 'name' => $rule->name, 'xp_reward' => $rule->xp_reward,
            'criteria_value' => 1, 'sort_order' => 1, 'is_active' => 1,
        ];

        // Achievements cannot be edited at all since DataSensei Updates 9.
        $this->put('/admin/gamification/achievements/'.$rule->id, $payload + ['criteria_type' => 'xp_total', 'criteria_value' => 5])
            ->assertStatus(405);
        $this->assertSame('coding_passes', $rule->fresh()->criteria_type);
        $this->assertSame(1, (int) $rule->fresh()->criteria_value);
    }

    public function test_path_rules_unlock_once_every_platform_challenge_of_the_path_is_passed(): void
    {
        [$one] = $this->mcqChallenge(['content_code' => 'PATH-ONE']);
        [$two] = $this->mcqChallenge(['content_code' => 'PATH-TWO', 'title' => 'Second']);
        // A class challenge in the same path never blocks finishing the path.
        $this->mcqChallenge(['content_code' => 'PATH-CLASS', 'title' => 'Class quiz', 'visibility' => Challenge::VISIBILITY_INSTRUCTOR]);
        AchievementDefinition::create([
            'achievement_key' => 'path_newbie_complete', 'name' => 'Newbie Path Clear', 'description' => 'All newbie MCQs.',
            'icon' => 'NP', 'badge_color' => 'green', 'xp_reward' => 120, 'criteria_type' => 'path_complete',
            'criteria_value' => 1, 'sort_order' => 1, 'is_active' => true,
        ]);
        $student = $this->mcqStudent();
        $this->passedAttempt($student, $one);

        $this->artisan('achievements:sync')->assertExitCode(0);
        $this->assertFalse($this->has($student, 'path_newbie_complete'));

        $this->passedAttempt($student, $two);
        $this->artisan('achievements:sync')->assertExitCode(0);
        $this->assertTrue($this->has($student, 'path_newbie_complete'));
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function reviewer(array $overrides = []): void
    {
        Cache::flush();
        config(array_merge([
            'cache.default' => 'array',
            'code_execution.ollama.url' => 'http://127.0.0.1:11434/api/generate',
            'code_execution.ollama.background_continuation' => false,
            'code_execution.review.fast_path' => false,
        ], $overrides));
    }

    private function chat(array $data)
    {
        static $student = null;
        $student ??= $this->roleUser();

        return $this->authenticateAs($student)->postJson(route('api.code-review'), array_merge([
            'mode' => 'chat',
            'language' => 'python',
        ], $data));
    }

    private function ollama(string $response): array
    {
        return ['model' => 'qwen2.5-coder:1.5b-instruct', 'response' => $response, 'done' => true,
            'done_reason' => 'stop', 'total_duration' => 1_000_000_000, 'prompt_eval_count' => 80, 'eval_count' => 24];
    }

    private function passedAttempt(User $student, Challenge $challenge): void
    {
        ChallengeAttempt::create([
            'user_id' => $student->id, 'challenge_id' => $challenge->id, 'attempt_no' => 1, 'mode' => 'ranked',
            'status' => 'submitted', 'started_at' => now()->subMinutes(5), 'expires_at' => now()->addMinutes(5),
            'submitted_at' => now()->subMinute(), 'time_limit_seconds' => 600, 'time_taken_seconds' => 400,
            'score' => 2, 'total_questions' => 2, 'xp_awarded' => 0, 'is_ranked' => true, 'is_leaderboard_eligible' => true,
            'question_order' => [], 'option_order' => [],
        ]);
    }

    private function has(User $student, string $key): bool
    {
        return UserAchievement::where('user_id', $student->id)
            ->whereHas('achievement', fn ($query) => $query->where('achievement_key', $key))
            ->exists();
    }
}
