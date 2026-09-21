<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\TestCase as CodingTestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * DS-15 — real two-process reproduction on InnoDB (MariaDB locally).
 *
 * Process A submits and is held inside grading; process B performs the retake
 * and starts the new run; A then resumes and tries to commit.
 *
 * Requires a migrated database:
 *   DS_MYSQL_TEST_DB=ds_test_d php vendor/bin/phpunit --filter Ds15CodingRetakeRaceMysqlTest
 */
class Ds15CodingRetakeRaceMysqlTest extends TestCase
{
    private ?string $flagDir = null;
    private array $cleanup = [];

    protected function setUp(): void
    {
        parent::setUp();

        $database = getenv('DS_MYSQL_TEST_DB');
        if (!$database) {
            $this->markTestSkipped('Set DS_MYSQL_TEST_DB to run the InnoDB two-process race test.');
        }

        config()->set('database.connections.mysql', array_merge(config('database.connections.mysql'), $this->mysqlConfig()));
        config()->set('database.default', 'mysql');
        DB::purge('mysql');

        $this->flagDir = sys_get_temp_dir() . '/ds15_' . bin2hex(random_bytes(6));
        mkdir($this->flagDir, 0700, true);
    }

    protected function tearDown(): void
    {
        if ($this->flagDir !== null) {
            foreach ($this->cleanup as [$table, $column, $ids]) {
                DB::table($table)->whereIn($column, $ids)->delete();
            }
            array_map('unlink', glob($this->flagDir . '/*') ?: []);
            @rmdir($this->flagDir);
        }

        parent::tearDown();
    }

    public function test_old_submission_finishing_after_a_retake_is_not_active_in_the_new_run(): void
    {
        $suffix = bin2hex(random_bytes(4));
        $user = User::create([
            'name' => 'Race Student', 'email' => "ds15-race-{$suffix}@example.test",
            'password' => 'TestPassword!123', 'role' => User::ROLE_USER, 'status' => 'active',
        ]);
        $category = ChallengeCategory::create([
            'name' => 'DS15 ' . $suffix, 'slug' => 'ds15-' . $suffix, 'target_audience' => 'test', 'description' => 'race', 'order_index' => 99,
        ]);
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id, 'title' => 'DS15 race ' . $suffix, 'description' => 'race',
            'time_limit_seconds' => 600, 'base_xp' => 100, 'order_index' => 1, 'is_coding_challenge' => true, 'is_active' => true,
        ]);
        $question = CodingQuestion::create([
            'challenge_id' => $challenge->id, 'problem_description' => 'race', 'language' => 'python',
            'order_index' => 1, 'time_limit_seconds' => 600, 'base_xp' => 100,
        ]);
        CodingTestCase::create(['coding_question_id' => $question->id, 'input' => '', 'expected_output' => '42', 'is_hidden' => false, 'order_index' => 1]);
        $oldAttempt = CodingQuestionAttempt::create([
            'user_id' => $user->id, 'coding_question_id' => $question->id, 'started_at' => now(), 'expired' => false,
        ]);

        $this->cleanup = [
            ['coding_submissions', 'user_id', [$user->id]],
            ['coding_question_attempts', 'user_id', [$user->id]],
            ['coding_challenge_retakes', 'user_id', [$user->id]],
            ['test_cases', 'coding_question_id', [$question->id]],
            ['coding_questions', 'id', [$question->id]],
            ['challenges', 'id', [$challenge->id]],
            ['challenge_categories', 'id', [$category->id]],
            ['users', 'id', [$user->id]],
        ];
        if (\Illuminate\Support\Facades\Schema::hasTable('coding_question_attempt_archives')) {
            array_unshift($this->cleanup, ['coding_question_attempt_archives', 'user_id', [$user->id]]);
        }

        $submit = $this->worker('submit', $user->id, $challenge->id, $question->id);
        $retake = $this->worker('retake', $user->id, $challenge->id, $question->id);
        $submit->start();
        $retake->start();
        $retake->wait();
        $submit->wait();

        $this->assertSame(0, $retake->getExitCode(), $retake->getErrorOutput() . $retake->getOutput());
        $this->assertSame(0, $submit->getExitCode(), $submit->getErrorOutput() . $submit->getOutput());
        $result = json_decode($submit->getOutput(), true);

        $newAttempt = CodingQuestionAttempt::where('user_id', $user->id)->firstOrFail();
        $this->assertNotSame($oldAttempt->id, $newAttempt->id, 'The retake + restart really happened during grading.');

        $active = CodingSubmission::where('user_id', $user->id)->where('voided', false)->get();
        $this->assertCount(
            0,
            $active,
            'A submission graded for the OLD attempt became an active result of the NEW run: ' . json_encode($result)
        );
        $this->assertSame(0, (int) $user->fresh()->xp, 'The stale submission must not award XP.');

        $this->assertSame(409, $result['http']);
        $stale = CodingSubmission::where('user_id', $user->id)->firstOrFail();
        $this->assertTrue($stale->voided);
        $this->assertSame('stale_attempt', $stale->void_reason);
        $this->assertNotSame($newAttempt->attempt_token, $stale->attempt_token);
    }

    private function worker(string $mode, int $userId, int $challengeId, int $questionId): Process
    {
        $config = $this->mysqlConfig();
        $process = new Process(
            [PHP_BINARY, base_path('tests/Support/ds15_coding_race_worker.php'), $mode, (string) $userId, (string) $challengeId, (string) $questionId, $this->flagDir],
            base_path(),
            [
                'APP_ENV' => 'testing',
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $config['host'],
                'DB_PORT' => (string) $config['port'],
                'DB_DATABASE' => $config['database'],
                'DB_USERNAME' => $config['username'],
                'DB_PASSWORD' => $config['password'],
                'DB_URL' => '',
                'CACHE_STORE' => 'array',
                'SESSION_DRIVER' => 'array',
                'QUEUE_CONNECTION' => 'sync',
            ]
        );
        $process->setTimeout(90);

        return $process;
    }

    private function mysqlConfig(): array
    {
        return [
            'driver' => 'mysql',
            'url' => null,
            'host' => getenv('DS_MYSQL_TEST_HOST') ?: '127.0.0.1',
            'port' => getenv('DS_MYSQL_TEST_PORT') ?: '3306',
            'database' => (string) getenv('DS_MYSQL_TEST_DB'),
            'username' => getenv('DS_MYSQL_TEST_USER') ?: 'ds',
            'password' => getenv('DS_MYSQL_TEST_PASSWORD') ?: 'ds',
        ];
    }
}
