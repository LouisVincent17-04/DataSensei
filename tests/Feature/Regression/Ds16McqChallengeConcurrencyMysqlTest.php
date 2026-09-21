<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ChallengeOption;
use App\Models\ChallengeQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * DS-01 / DS-11 / DS-16 under real InnoDB row locking, using separate PHP
 * processes that call the real controllers at the same moment.
 *
 * Skipped unless DS_MYSQL_TEST_DB names an already-migrated MySQL/MariaDB
 * database (optional: DS_MYSQL_TEST_HOST, DS_MYSQL_TEST_PORT,
 * DS_MYSQL_TEST_USER, DS_MYSQL_TEST_PASSWORD; defaults 127.0.0.1/3306/ds/ds).
 */
class Ds16McqChallengeConcurrencyMysqlTest extends TestCase
{
    private const ROUNDS = 3;

    /** @var array<string, string> */
    private array $dbEnv = [];
    /** @var array<int, int> */
    private array $userIds = [];
    /** @var array<int, int> */
    private array $challengeIds = [];
    private ?int $categoryId = null;

    protected function setUp(): void
    {
        parent::setUp();

        $database = getenv('DS_MYSQL_TEST_DB') ?: '';
        if ($database === '') {
            $this->markTestSkipped('Set DS_MYSQL_TEST_DB to a migrated MySQL/MariaDB database to run the concurrency test.');
        }

        $this->dbEnv = [
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => getenv('DS_MYSQL_TEST_HOST') ?: '127.0.0.1',
            'DB_PORT' => getenv('DS_MYSQL_TEST_PORT') ?: '3306',
            'DB_DATABASE' => $database,
            'DB_USERNAME' => getenv('DS_MYSQL_TEST_USER') ?: 'ds',
            'DB_PASSWORD' => getenv('DS_MYSQL_TEST_PASSWORD') ?: 'ds',
        ];

        config()->set('database.default', 'mysql');
        foreach (['host' => 'DB_HOST', 'port' => 'DB_PORT', 'database' => 'DB_DATABASE', 'username' => 'DB_USERNAME', 'password' => 'DB_PASSWORD'] as $key => $env) {
            config()->set("database.connections.mysql.{$key}", $this->dbEnv[$env]);
        }
        DB::purge('mysql');
    }

    protected function tearDown(): void
    {
        if ($this->dbEnv !== []) {
            $attemptIds = DB::table('challenge_attempts')->whereIn('challenge_id', $this->challengeIds)->pluck('id');
            DB::table('challenge_attempt_events')->whereIn('challenge_attempt_id', $attemptIds)->delete();
            DB::table('challenge_attempt_answers')->whereIn('challenge_attempt_id', $attemptIds)->delete();
            DB::table('challenge_attempts')->whereIn('id', $attemptIds)->delete();
            DB::table('challenge_user')->whereIn('challenge_id', $this->challengeIds)->delete();
            $questionIds = DB::table('challenge_questions')->whereIn('challenge_id', $this->challengeIds)->pluck('id');
            DB::table('challenge_options')->whereIn('challenge_question_id', $questionIds)->delete();
            DB::table('challenge_questions')->whereIn('id', $questionIds)->delete();
            DB::table('challenges')->whereIn('id', $this->challengeIds)->delete();
            foreach (['user_achievements', 'user_daily_missions', 'user_missions', 'notifications'] as $table) {
                if (\Illuminate\Support\Facades\Schema::hasTable($table)
                    && \Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id')) {
                    DB::table($table)->whereIn('user_id', $this->userIds)->delete();
                }
            }
            DB::table('users')->whereIn('id', $this->userIds)->delete();
        }

        parent::tearDown();
    }

    public function test_concurrent_autosaves_of_different_questions_both_persist(): void
    {
        for ($round = 1; $round <= self::ROUNDS; $round++) {
            $student = $this->makeUser(User::ROLE_USER);
            [$challenge, $questions] = $this->makeChallenge(true, 1, 4);
            $attempt = $this->makeAttempt($student, $challenge, $questions);

            $jobs = [];
            foreach ($questions as $row) {
                $jobs[] = ['autosave', $student->id, $challenge->id, [
                    'attempt_id' => $attempt->id,
                    'question_id' => $row['question']->id,
                    'option_id' => $row['correct']->id,
                    'seq' => 1,
                ]];
            }

            $outcomes = $this->race($jobs);
            $context = "round {$round}: " . json_encode($outcomes);

            foreach ($outcomes as $outcome) {
                $this->assertSame(200, $outcome['status'] ?? null, $context);
                $this->assertTrue($outcome['body']['ok'] ?? false, $context);
            }

            foreach ($questions as $row) {
                $this->assertSame(
                    (int) $row['correct']->id,
                    (int) DB::table('challenge_attempt_answers')
                        ->where('challenge_attempt_id', $attempt->id)
                        ->where('challenge_question_id', $row['question']->id)
                        ->value('selected_option_id'),
                    $context
                );
            }
            $this->assertSame(4, DB::table('challenge_attempt_answers')->where('challenge_attempt_id', $attempt->id)->count(), $context);
        }
    }

    public function test_parallel_submits_finalize_once_and_award_xp_once(): void
    {
        for ($round = 1; $round <= self::ROUNDS; $round++) {
            $student = $this->makeUser(User::ROLE_USER);
            [$challenge, $questions] = $this->makeChallenge(true, 1, 2);
            $attempt = $this->makeAttempt($student, $challenge, $questions);

            $answers = [];
            foreach ($questions as $row) {
                $answers[$row['question']->id] = $row['correct']->id;
            }

            $jobs = [];
            for ($worker = 0; $worker < 4; $worker++) {
                $jobs[] = ['submit', $student->id, $challenge->id, ['attempt_id' => $attempt->id, 'answers' => $answers]];
            }

            $outcomes = $this->race($jobs);
            $context = "round {$round}: " . json_encode($outcomes);

            foreach ($outcomes as $outcome) {
                $this->assertSame(302, $outcome['status'] ?? null, $context);
                $this->assertStringContainsString("/result/{$attempt->id}", (string) $outcome['location'], $context);
            }

            $finalizers = array_filter($outcomes, fn (array $o) => str_contains((string) ($o['flash'] ?? ''), 'Challenge submitted.'));
            $this->assertCount(1, $finalizers, $context);

            $attempt->refresh();
            $this->assertSame('submitted', $attempt->status, $context);
            $this->assertSame(2, (int) $attempt->score, $context);
            $this->assertGreaterThan(0, (int) $attempt->xp_awarded, $context);
            $this->assertSame((int) $attempt->xp_awarded, (int) DB::table('users')->where('id', $student->id)->value('xp'), 'XP awarded exactly once. ' . $context);
            $this->assertSame(1, DB::table('challenge_user')->where('user_id', $student->id)->where('challenge_id', $challenge->id)->count(), $context);
            $this->assertSame(1, DB::table('challenge_attempts')->where('user_id', $student->id)->where('challenge_id', $challenge->id)->count(), $context);
        }
    }

    public function test_publish_racing_with_a_new_start_never_leaves_an_unusable_attempt(): void
    {
        for ($round = 1; $round <= self::ROUNDS; $round++) {
            $admin = $this->makeUser(User::ROLE_ADMIN);
            $student = $this->makeUser(User::ROLE_USER);
            $code = 'MCQ-RACE-' . Str::upper(Str::random(8));
            [$versionA, $questionsA] = $this->makeChallenge(true, 1, 2, $code);
            [$versionB] = $this->makeChallenge(false, 2, 2, $code);

            $outcomes = $this->race([
                ['start', $student->id, $versionA->id, []],
                ['publish', $admin->id, $versionB->id, []],
            ]);
            $context = "round {$round}: " . json_encode($outcomes);
            [$start, $publish] = $outcomes;

            $this->assertSame(302, $publish['status'] ?? null, $context);
            $this->assertFalse((bool) DB::table('challenges')->where('id', $versionA->id)->value('is_active'), $context);
            $this->assertTrue((bool) DB::table('challenges')->where('id', $versionB->id)->value('is_active'), $context);

            $attempts = ChallengeAttempt::where('user_id', $student->id)->where('challenge_id', $versionA->id)->get();

            if (($start['status'] ?? null) === 200) {
                // The start won the lock: the attempt exists and must stay usable on the now inactive version.
                $this->assertCount(1, $attempts, $context);
                $save = $this->race([['autosave', $student->id, $versionA->id, [
                    'attempt_id' => $attempts[0]->id,
                    'question_id' => $questionsA[0]['question']->id,
                    'option_id' => $questionsA[0]['correct']->id,
                    'seq' => 1,
                ]]])[0];
                $this->assertSame(200, $save['status'] ?? null, $context . json_encode($save));

                $submit = $this->race([['submit', $student->id, $versionA->id, ['attempt_id' => $attempts[0]->id]]])[0];
                $this->assertSame(302, $submit['status'] ?? null, $context . json_encode($submit));
                $this->assertSame('submitted', $attempts[0]->fresh()->status, $context);
                $this->assertSame(1, (int) $attempts[0]->fresh()->score, $context);
            } else {
                // Publication won: no attempt may exist on the inactive version.
                $this->assertSame(404, $start['status'] ?? null, $context);
                $this->assertCount(0, $attempts, $context);
            }

            // Either way a fresh start on A is refused afterwards unless it resumes the existing attempt.
            $other = $this->makeUser(User::ROLE_USER);
            $late = $this->race([['start', $other->id, $versionA->id, []]])[0];
            $this->assertSame(404, $late['status'] ?? null, $context . json_encode($late));
        }
    }

    /**
     * @param array<int, array{0: string, 1: int, 2: int, 3: array}> $jobs
     * @return array<int, array<string, mixed>>
     */
    private function race(array $jobs): array
    {
        $startAt = sprintf('%.4F', microtime(true) + 2.5);
        $processes = [];

        foreach ($jobs as [$action, $userId, $challengeId, $payload]) {
            $process = new Process(
                [
                    PHP_BINARY,
                    __DIR__ . '/Support/mcq_challenge_worker.php',
                    $action,
                    (string) $userId,
                    (string) $challengeId,
                    $startAt,
                    json_encode($payload),
                ],
                base_path(),
                array_merge($this->dbEnv, [
                    'APP_ENV' => 'testing',
                    'DB_URL' => '',
                    'SESSION_DRIVER' => 'array',
                    'CACHE_STORE' => 'array',
                    'QUEUE_CONNECTION' => 'sync',
                ])
            );
            $process->setTimeout(60);
            $process->start();
            $processes[] = $process;
        }

        $outcomes = [];
        foreach ($processes as $process) {
            $process->wait();
            $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput() . $process->getOutput());
            $lines = array_values(array_filter(explode("\n", trim($process->getOutput()))));
            $decoded = json_decode((string) end($lines), true);
            $this->assertIsArray($decoded, 'Worker output was not JSON: ' . $process->getOutput());
            $this->assertNotSame('exception', $decoded['outcome'] ?? null, json_encode($decoded));
            $outcomes[] = $decoded;
        }

        return $outcomes;
    }

    private function makeUser(int $role): User
    {
        $user = User::create([
            'name' => 'MCQ Race User',
            'email' => 'mcq-race-' . Str::lower(Str::random(12)) . '@example.test',
            'password' => 'TestPassword!123',
            'role' => $role,
            'status' => 'active',
        ]);
        $this->userIds[] = $user->id;

        return $user;
    }

    /** @return array{0: Challenge, 1: array<int, array{question: ChallengeQuestion, correct: ChallengeOption}>} */
    private function makeChallenge(bool $active, int $versionNo, int $questionCount, ?string $contentCode = null): array
    {
        $category = ChallengeCategory::firstOrCreate(['slug' => 'newbie'], [
            'name' => 'Newbie',
            'target_audience' => 'Tests',
            'description' => 'Concurrency test category',
            'order_index' => 99,
        ]);
        $this->categoryId = $category->id;

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'content_code' => $contentCode ?: 'MCQ-RACE-' . Str::upper(Str::random(8)),
            'title' => 'Race challenge ' . Str::random(6),
            'description' => 'Concurrency test',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => false,
            'version_no' => $versionNo,
            'version_name' => 'Version ' . $versionNo,
            'version_code' => 'V' . $versionNo,
            'is_active' => $active,
        ]);
        $this->challengeIds[] = $challenge->id;

        $questions = [];
        for ($i = 1; $i <= $questionCount; $i++) {
            $question = ChallengeQuestion::create([
                'challenge_id' => $challenge->id,
                'challenge_category_id' => $category->id,
                'question_text' => "Race question {$i}",
                'order_index' => $i,
            ]);
            $correct = ChallengeOption::create(['challenge_question_id' => $question->id, 'option_text' => 'right', 'is_correct' => true, 'order_index' => 1]);
            ChallengeOption::create(['challenge_question_id' => $question->id, 'option_text' => 'wrong', 'is_correct' => false, 'order_index' => 2]);
            $questions[] = ['question' => $question, 'correct' => $correct];
        }

        return [$challenge, $questions];
    }

    private function makeAttempt(User $student, Challenge $challenge, array $questions): ChallengeAttempt
    {
        $questionIds = array_map(fn (array $row): int => (int) $row['question']->id, $questions);

        return ChallengeAttempt::create([
            'user_id' => $student->id,
            'challenge_id' => $challenge->id,
            'attempt_no' => 1,
            'mode' => 'ranked',
            'status' => 'in_progress',
            'started_at' => now()->subMinute(),
            'expires_at' => now()->addMinutes(9),
            'last_seen_at' => now(),
            'time_limit_seconds' => 600,
            'total_questions' => count($questionIds),
            'is_ranked' => true,
            'is_leaderboard_eligible' => true,
            'suspicious_event_count' => 0,
            'question_order' => $questionIds,
            'option_order' => [],
        ]);
    }
}
