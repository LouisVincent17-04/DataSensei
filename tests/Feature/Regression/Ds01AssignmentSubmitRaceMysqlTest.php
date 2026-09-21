<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * DS-01 / DS-02 on a real InnoDB server (MariaDB here; the production target
 * is MySQL 5.5):
 *  - the integrity migration goes up, down and up again on a populated table
 *    without touching existing rows;
 *  - several separate PHP processes submit the SAME assignment attempt at the
 *    same moment: exactly one graded result and exactly one reward.
 *
 * Skipped unless DS_MYSQL_TEST_DB names an already-migrated database
 * (optional DS_MYSQL_TEST_HOST/PORT/USER/PASSWORD; defaults 127.0.0.1/3306/ds/ds).
 */
class Ds01AssignmentSubmitRaceMysqlTest extends TestCase
{
    private const WORKERS = 4;
    private const ROUNDS = 3;
    private const MIGRATION = 'database/migrations/2026_09_20_000001_add_integrity_outcome_to_assignment_submissions.php';
    private const NEW_COLUMNS = [
        'integrity_status', 'integrity_reason', 'provisional_score',
        'integrity_reviewed_by', 'integrity_reviewed_at', 'rewards_awarded_at',
    ];

    /** @var array<string, string> */
    private array $dbEnv = [];
    /** @var list<int> */
    private array $userIds = [];
    /** @var list<int> */
    private array $classIds = [];
    /** @var list<int> */
    private array $libraryItemIds = [];
    /** @var list<int> */
    private array $missionIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $database = getenv('DS_MYSQL_TEST_DB') ?: '';
        if ($database === '') {
            $this->markTestSkipped('Set DS_MYSQL_TEST_DB to a migrated MySQL/MariaDB database to run this test.');
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
            $assignmentIds = DB::table('class_assignments')->whereIn('class_id', $this->classIds)->pluck('id');
            $submissionIds = DB::table('assignment_submissions')->whereIn('class_assignment_id', $assignmentIds)->pluck('id');
            $questionIds = DB::table('assignment_questions')->whereIn('assignment_library_item_id', $this->libraryItemIds)->pluck('id');

            DB::table('assignment_submission_answers')->whereIn('assignment_submission_id', $submissionIds)->delete();
            DB::table('assignment_submissions')->whereIn('id', $submissionIds)->delete();
            DB::table('class_assignments')->whereIn('id', $assignmentIds)->delete();
            DB::table('assignment_question_options')->whereIn('assignment_question_id', $questionIds)->delete();
            DB::table('assignment_blank_answers')->whereIn('assignment_question_id', $questionIds)->delete();
            DB::table('assignment_questions')->whereIn('id', $questionIds)->delete();
            DB::table('assignment_library_items')->whereIn('id', $this->libraryItemIds)->delete();
            DB::table('student_mission_progress')->whereIn('user_id', $this->userIds)->delete();
            DB::table('mission_definitions')->whereIn('id', $this->missionIds)->delete();
            DB::table('user_achievements')->whereIn('user_id', $this->userIds)->delete();
            DB::table('notifications')->whereIn('user_id', $this->userIds)->delete();
            DB::table('class_student')->whereIn('class_id', $this->classIds)->delete();
            DB::table('classes')->whereIn('id', $this->classIds)->delete();
            DB::table('users')->whereIn('id', $this->userIds)->delete();
        }

        parent::tearDown();
    }

    public function test_integrity_migration_goes_up_down_up_on_a_populated_table(): void
    {
        [$assignmentId, , $student] = $this->createAssignment();
        $gradedId = DB::table('assignment_submissions')->insertGetId([
            'class_assignment_id' => $assignmentId,
            'student_id' => $student->id,
            'attempt_no' => 1,
            'status' => 'graded',
            'score' => 7,
            'total_points' => 10,
            'started_at' => '2026-09-01 08:00:00',
            'submitted_at' => '2026-09-01 08:10:00',
            'graded_at' => '2026-09-01 08:10:00',
            'anti_cheat_session_id' => Str::random(64),
            'draft_answers' => json_encode(['1' => 'kept']),
            'created_at' => '2026-09-01 08:00:00',
            'updated_at' => '2026-09-01 08:10:00',
        ]);
        $openId = DB::table('assignment_submissions')->insertGetId([
            'class_assignment_id' => $assignmentId,
            'student_id' => $student->id,
            'attempt_no' => 2,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => 10,
            'started_at' => '2026-09-02 08:00:00',
            'anti_cheat_session_id' => Str::random(64),
            'created_at' => '2026-09-02 08:00:00',
            'updated_at' => '2026-09-02 08:00:00',
        ]);
        $preserved = ['id', 'class_assignment_id', 'student_id', 'attempt_no', 'status', 'score', 'total_points',
            'started_at', 'submitted_at', 'graded_at', 'anti_cheat_session_id', 'draft_answers', 'created_at', 'updated_at'];
        $before = DB::table('assignment_submissions')->whereIn('id', [$gradedId, $openId])->orderBy('id')->get($preserved)->toArray();
        $rowCount = DB::table('assignment_submissions')->count();

        $migration = require base_path(self::MIGRATION);

        $migration->down();
        foreach (self::NEW_COLUMNS as $column) {
            $this->assertFalse(Schema::hasColumn('assignment_submissions', $column), "{$column} dropped by down()");
        }
        $this->assertEquals($before, DB::table('assignment_submissions')->whereIn('id', [$gradedId, $openId])->orderBy('id')->get($preserved)->toArray());
        $migration->down(); // idempotent

        $migration->up();
        $migration->up(); // idempotent
        foreach (self::NEW_COLUMNS as $column) {
            $this->assertTrue(Schema::hasColumn('assignment_submissions', $column), "{$column} added by up()");
        }

        $this->assertSame($rowCount, DB::table('assignment_submissions')->count(), 'no row was deleted');
        $this->assertEquals($before, DB::table('assignment_submissions')->whereIn('id', [$gradedId, $openId])->orderBy('id')->get($preserved)->toArray());

        $graded = DB::table('assignment_submissions')->find($gradedId);
        $open = DB::table('assignment_submissions')->find($openId);
        $this->assertNull($graded->integrity_status);
        $this->assertNull($graded->provisional_score);
        $this->assertSame('2026-09-01 08:10:00', $graded->rewards_awarded_at, 'finished attempts are marked as already rewarded');
        $this->assertNull($open->rewards_awarded_at, 'open attempts can still earn their reward');

        // All nullable, no index, no JSON type: MySQL 5.5 compatible DDL.
        $columns = collect(DB::select(
            'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME IN (' . implode(',', array_fill(0, count(self::NEW_COLUMNS), '?')) . ')',
            array_merge(['assignment_submissions'], self::NEW_COLUMNS)
        ))->keyBy('COLUMN_NAME');
        $this->assertCount(count(self::NEW_COLUMNS), $columns);
        foreach ($columns as $column) {
            $this->assertSame('YES', $column->IS_NULLABLE);
            $this->assertContains($column->DATA_TYPE, ['varchar', 'int', 'bigint', 'datetime']);
        }
    }

    public function test_parallel_submits_of_one_attempt_grade_and_reward_exactly_once(): void
    {
        [, , $student, $q, $missionId] = $this->createAssignment(false);

        for ($round = 1; $round <= self::ROUNDS; $round++) {
            $assignmentId = $this->createClassAssignment($q['item']);
            $submission = AssignmentSubmission::create([
                'class_assignment_id' => $assignmentId,
                'student_id' => $student->id,
                'attempt_no' => 1,
                'status' => 'in_progress',
                'score' => 0,
                'total_points' => 10,
                'anti_cheat_session_id' => Str::random(64),
                'started_at' => now(),
            ]);

            $payload = json_encode([
                '_anti_cheat_session_id' => $submission->anti_cheat_session_id,
                'answers' => [(string) $q['mcq'] => (string) $q['correct'], (string) $q['blank'] => 'pandas'],
            ]);
            $startAt = sprintf('%.4F', microtime(true) + 2.5);

            $processes = [];
            for ($worker = 0; $worker < self::WORKERS; $worker++) {
                $process = new Process(
                    [PHP_BINARY, __DIR__ . '/Support/assignment_submit_worker.php', (string) $assignmentId, (string) $submission->id, (string) $student->id, $startAt, $payload],
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
                $outcomes[] = $decoded;
            }
            $context = "round {$round}: " . json_encode($outcomes);

            foreach ($outcomes as $outcome) {
                $this->assertSame('in_progress', $outcome['status_seen_before'] ?? null, 'every worker really raced. ' . $context);
                $this->assertSame('response', $outcome['outcome'], $context);
                $this->assertStringContainsString('/result', (string) $outcome['location'], $context);
            }
            $this->assertCount(1, array_filter($outcomes, fn (array $outcome) => ! empty($outcome['flash'])), 'exactly one worker finalized. ' . $context);

            $submission->refresh();
            $this->assertSame('graded', $submission->status, $context);
            $this->assertSame(10, $submission->score, $context);
            $this->assertSame('clear', $submission->integrity_status, $context);
            $this->assertSame(1, $submission->draft_version, 'finalized once. ' . $context);
            $this->assertNotNull($submission->rewards_awarded_at, $context);
            $this->assertSame(2, DB::table('assignment_submission_answers')->where('assignment_submission_id', $submission->id)->count(), $context);
            $this->assertSame($round, DB::table('notifications')->where('user_id', $student->id)->where('type', 'assignment_graded')->count(), $context);
            $this->assertSame(
                $round,
                (int) DB::table('student_mission_progress')->where('user_id', $student->id)->where('mission_definition_id', $missionId)->sum('progress_count'),
                'one reward per submission. ' . $context
            );
        }

        // XP equals exactly what the recorded achievements and missions paid.
        $expectedXp = (int) DB::table('user_achievements')
            ->join('achievement_definitions', 'achievement_definitions.id', '=', 'user_achievements.achievement_definition_id')
            ->where('user_achievements.user_id', $student->id)
            ->sum('achievement_definitions.xp_reward')
            + (int) DB::table('student_mission_progress')->where('user_id', $student->id)->sum('xp_awarded');
        $this->assertSame($expectedXp, (int) $student->fresh()->xp);
    }

    /** @return array{0: int, 1: int, 2: User, 3: array<string, int>, 4: int} */
    private function createAssignment(bool $withClassAssignment = true): array
    {
        $token = Str::lower(Str::random(10));
        $instructor = User::create(['name' => 'Race Instructor', 'email' => "asg-race-instructor-{$token}@example.test", 'password' => 'TestPassword!123', 'role' => User::ROLE_INSTRUCTOR, 'status' => 'active']);
        $student = User::create(['name' => 'Race Student', 'email' => "asg-race-student-{$token}@example.test", 'password' => 'TestPassword!123', 'role' => User::ROLE_USER, 'status' => 'active']);
        $this->userIds[] = $instructor->id;
        $this->userIds[] = $student->id;

        $classId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $instructor->id,
            'name' => 'Assignment Race ' . $token,
            'class_code' => 'R' . Str::upper(Str::random(7)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->classIds[] = $classId;
        DB::table('class_student')->insert(['class_id' => $classId, 'student_id' => $student->id, 'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $code = 'AR-' . Str::upper(Str::random(8));
        $itemId = (int) DB::table('assignment_library_items')->insertGetId([
            'module_no' => 1, 'assignment_code' => $code, 'title' => 'Race ' . $code, 'topic_title' => 'Race', 'year_level' => 'First Year',
            'assignment_type' => 'mcq', 'version_no' => 1, 'version_name' => 'Version 1', 'version_code' => $code . '-V1',
            'time_limit_minutes' => 30, 'total_points' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->libraryItemIds[] = $itemId;
        $mcq = (int) DB::table('assignment_questions')->insertGetId(['assignment_library_item_id' => $itemId, 'question_type' => 'mcq', 'question_text' => 'Pick', 'points' => 5, 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $correct = (int) DB::table('assignment_question_options')->insertGetId(['assignment_question_id' => $mcq, 'option_text' => 'Right', 'is_correct' => true, 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('assignment_question_options')->insert(['assignment_question_id' => $mcq, 'option_text' => 'Wrong', 'is_correct' => false, 'order_index' => 2, 'created_at' => now(), 'updated_at' => now()]);
        $blank = (int) DB::table('assignment_questions')->insertGetId(['assignment_library_item_id' => $itemId, 'question_type' => 'fill_blank', 'question_text' => 'Library?', 'points' => 5, 'order_index' => 2, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('assignment_blank_answers')->insert(['assignment_question_id' => $blank, 'answer_text' => 'pandas', 'is_case_sensitive' => false, 'created_at' => now(), 'updated_at' => now()]);

        // A counter mission that cannot complete during the test: its progress
        // is the number of rewards paid.
        $missionId = (int) DB::table('mission_definitions')->insertGetId([
            'mission_key' => 'race_submit_' . $token, 'title' => 'Race mission', 'period_type' => 'weekly',
            'target_type' => 'submit_assignment', 'target_count' => 1000, 'xp_reward' => 5, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->missionIds[] = $missionId;

        $this->raceClassId = $classId;
        $this->raceInstructorId = $instructor->id;
        $assignmentId = $withClassAssignment ? $this->createClassAssignment($itemId) : 0;

        return [$assignmentId, $classId, $student, ['item' => $itemId, 'mcq' => $mcq, 'correct' => $correct, 'blank' => $blank], $missionId];
    }

    private int $raceClassId = 0;
    private int $raceInstructorId = 0;

    private function createClassAssignment(int $itemId): int
    {
        return (int) DB::table('class_assignments')->insertGetId([
            'class_id' => $this->raceClassId, 'assignment_library_item_id' => $itemId, 'assigned_by' => $this->raceInstructorId,
            'title' => 'Race assignment', 'max_attempts' => 3, 'status' => 'published', 'assigned_at' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
