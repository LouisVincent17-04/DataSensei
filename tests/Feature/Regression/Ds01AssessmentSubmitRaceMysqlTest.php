<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\AssessmentSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * DS-01 idempotency under real InnoDB row locking: several separate PHP
 * processes submit the SAME assessment attempt at the same moment.
 *
 * Since assignments were merged into assessments (DataSensei Updates 11) this
 * also carries the former assignment race coverage: a protected attempt
 * (anti-cheat on) finalizes once with a clear integrity outcome, a blocked one
 * is held once with exactly one lock record, and the Updates 11 migrations
 * rerun safely on a populated MySQL database with MySQL 5.5 compatible types.
 *
 * Skipped unless DS_MYSQL_TEST_DB names an already-migrated MySQL/MariaDB
 * database (optional: DS_MYSQL_TEST_HOST, DS_MYSQL_TEST_PORT,
 * DS_MYSQL_TEST_USER, DS_MYSQL_TEST_PASSWORD; defaults 127.0.0.1/3306/ds/ds).
 */
class Ds01AssessmentSubmitRaceMysqlTest extends TestCase
{
    private const WORKERS = 4;
    private const ROUNDS = 3;

    /** @var array<string, string> */
    private array $dbEnv = [];
    /** @var array<int, int> */
    private array $userIds = [];
    /** @var array<int, int> */
    private array $classIds = [];
    /** @var array<int, int> */
    private array $assessmentIds = [];
    /** @var array<int, int> */
    private array $tosIds = [];

    private const U11_MIGRATIONS = [
        'database/migrations/2026_10_01_000001_extend_assessments_for_homework_and_anticheat.php',
        'database/migrations/2026_10_01_000002_create_question_bank_tables.php',
        'database/migrations/2026_10_01_000003_merge_assignments_into_assessments.php',
    ];

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
        config()->set('database.connections.mysql.host', $this->dbEnv['DB_HOST']);
        config()->set('database.connections.mysql.port', $this->dbEnv['DB_PORT']);
        config()->set('database.connections.mysql.database', $this->dbEnv['DB_DATABASE']);
        config()->set('database.connections.mysql.username', $this->dbEnv['DB_USERNAME']);
        config()->set('database.connections.mysql.password', $this->dbEnv['DB_PASSWORD']);
        DB::purge('mysql');
    }

    protected function tearDown(): void
    {
        if ($this->dbEnv !== []) {
            foreach ($this->assessmentIds as $assessmentId) {
                $submissionIds = DB::table('assessment_submissions')->where('assessment_id', $assessmentId)->pluck('id');
                $questionIds = DB::table('assessment_questions')->where('assessment_id', $assessmentId)->pluck('id');
                DB::table('assessment_answers')->whereIn('assessment_submission_id', $submissionIds)->delete();
                DB::table('student_assessment_diagnostics')->where('assessment_id', $assessmentId)->delete();
                DB::table('assessment_submissions')->whereIn('id', $submissionIds)->delete();
                DB::table('assessment_question_options')->whereIn('assessment_question_id', $questionIds)->delete();
                DB::table('assessment_questions')->whereIn('id', $questionIds)->delete();
                DB::table('assessments')->where('id', $assessmentId)->delete();
            }
            DB::table('table_of_specifications')->whereIn('id', $this->tosIds)->delete();
            DB::table('anti_cheat_events')->whereIn('user_id', $this->userIds)->delete();
            DB::table('anti_cheat_settings')->whereIn('instructor_id', $this->userIds)->delete();
            DB::table('notifications')->whereIn('user_id', $this->userIds)->delete();
            DB::table('class_student')->whereIn('class_id', $this->classIds)->delete();
            DB::table('classes')->whereIn('id', $this->classIds)->delete();
            DB::table('users')->whereIn('id', $this->userIds)->delete();
        }

        parent::tearDown();
    }

    public function test_parallel_submits_of_one_attempt_finalize_exactly_once(): void
    {
        for ($round = 1; $round <= self::ROUNDS; $round++) {
            [$assessment, $questions, $submission, $student] = $this->createAttempt();
            $correct = $questions[0]->options->firstWhere('is_correct', true);

            [$outcomes, $context] = $this->race($assessment, $submission, $student, [
                (string) $questions[0]->id => (string) $correct->id,
                (string) $questions[1]->id => '0',
            ], "round {$round}");

            // Exactly one worker finalized (it alone gets the success flash).
            $finalizers = array_filter($outcomes, fn (array $outcome) => ! empty($outcome['flash']));
            $this->assertCount(1, $finalizers, $context);

            $submission->refresh();
            $this->assertSame('graded', $submission->status, $context);
            $this->assertSame('5.00', $submission->score, $context);
            $this->assertSame(1, $submission->draft_version, 'draft_version is bumped once per finalization. ' . $context);
            $this->assertSame(
                2,
                DB::table('assessment_answers')->where('assessment_submission_id', $submission->id)->count(),
                $context
            );
            $this->assertSame(
                1,
                DB::table('notifications')
                    ->where('user_id', $student->id)
                    ->where('type', 'assessment_graded')
                    ->count(),
                $context
            );
            $this->assertSame(
                1,
                DB::table('assessment_submissions')
                    ->where('assessment_id', $assessment->id)
                    ->where('student_id', $student->id)
                    ->count(),
                $context
            );
        }
    }

    public function test_parallel_submits_of_a_protected_attempt_finalize_once_with_a_clear_outcome(): void
    {
        for ($round = 1; $round <= self::ROUNDS; $round++) {
            [$assessment, $questions, $submission, $student, $instructorId] = $this->createAttempt(true);
            $this->enablePolicy($instructorId);
            $correct = $questions[0]->options->firstWhere('is_correct', true);

            [$outcomes, $context] = $this->race($assessment, $submission, $student, [
                'answers' => [
                    (string) $questions[0]->id => (string) $correct->id,
                    (string) $questions[1]->id => '0',
                ],
                '_anti_cheat_session_id' => $submission->anti_cheat_session_id,
            ], "protected round {$round}");

            $this->assertCount(1, array_filter($outcomes, fn (array $o) => ! empty($o['flash'])), 'exactly one worker finalized. ' . $context);

            $submission->refresh();
            $this->assertSame('graded', $submission->status, $context);
            $this->assertSame('5.00', $submission->score, $context);
            $this->assertSame('clear', $submission->integrity_status, $context);
            $this->assertNull($submission->provisional_score, $context);
            $this->assertSame(1, $submission->draft_version, 'finalized once. ' . $context);
            $this->assertSame(1, DB::table('notifications')->where('user_id', $student->id)->where('type', 'assessment_graded')->count(), $context);
            $this->assertSame(0, DB::table('anti_cheat_events')->where('assessment_submission_id', $submission->id)->count(), $context);
        }
    }

    public function test_parallel_finalizes_of_a_blocked_attempt_hold_it_exactly_once(): void
    {
        for ($round = 1; $round <= self::ROUNDS; $round++) {
            [$assessment, $questions, $submission, $student, $instructorId, $classId] = $this->createAttempt(true);
            $this->enablePolicy($instructorId, ['detect_dual_monitor' => true, 'block_dual_monitor' => true]);
            DB::table('anti_cheat_events')->insert([
                'user_id' => $student->id,
                'class_id' => $classId,
                'assessment_id' => $assessment->id,
                'assessment_submission_id' => $submission->id,
                'assessment_type' => 'assessment',
                'event_type' => 'dual_monitor_detected',
                'severity' => 'critical',
                'attempt_session_id' => $submission->anti_cheat_session_id,
                'event_uuid' => (string) Str::uuid(),
                'occurred_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $correct = $questions[0]->options->firstWhere('is_correct', true);

            [$outcomes, $context] = $this->race($assessment, $submission, $student, [
                'answers' => [
                    (string) $questions[0]->id => (string) $correct->id,
                    (string) $questions[1]->id => '0',
                ],
                '_anti_cheat_session_id' => $submission->anti_cheat_session_id,
                '_anti_cheat_finalize' => '1',
            ], "blocked round {$round}");

            // One worker held the attempt (error flash); nobody got a success flash.
            $this->assertCount(1, array_filter($outcomes, fn (array $o) => ! empty($o['flash_error'])), $context);
            $this->assertCount(0, array_filter($outcomes, fn (array $o) => ! empty($o['flash'])), $context);

            $submission->refresh();
            $this->assertSame('submitted', $submission->status, $context);
            $this->assertSame('0.00', $submission->score, 'no credit while held. ' . $context);
            $this->assertSame('5.00', $submission->provisional_score, $context);
            $this->assertSame('blocked', $submission->integrity_status, $context);
            $this->assertNull($submission->graded_at, $context);
            $this->assertSame(1, $submission->draft_version, 'finalized once. ' . $context);
            $this->assertSame(
                0.0,
                (float) DB::table('assessment_answers')->where('assessment_submission_id', $submission->id)->sum('points_awarded'),
                $context
            );
            $this->assertSame(1, DB::table('notifications')->where('user_id', $student->id)->where('type', 'assessment_held_for_review')->count(), $context);
            $this->assertSame(0, DB::table('notifications')->where('user_id', $student->id)->where('type', 'assessment_graded')->count(), $context);
            // The recorded violation is the evidence; no extra lock record.
            $this->assertSame(
                0,
                DB::table('anti_cheat_events')
                    ->where('assessment_submission_id', $submission->id)
                    ->where('event_type', 'locked_attempt_finalized')
                    ->count(),
                $context
            );
        }
    }

    public function test_parallel_finalizes_of_a_client_locked_attempt_record_one_lock(): void
    {
        for ($round = 1; $round <= self::ROUNDS; $round++) {
            // The browser locked itself, but no violation event reached the server.
            [$assessment, $questions, $submission, $student, $instructorId] = $this->createAttempt(true);
            $this->enablePolicy($instructorId);
            $correct = $questions[0]->options->firstWhere('is_correct', true);

            [$outcomes, $context] = $this->race($assessment, $submission, $student, [
                'answers' => [
                    (string) $questions[0]->id => (string) $correct->id,
                    (string) $questions[1]->id => '0',
                ],
                '_anti_cheat_session_id' => $submission->anti_cheat_session_id,
                '_anti_cheat_finalize' => '1',
            ], "client-locked round {$round}");

            $this->assertCount(1, array_filter($outcomes, fn (array $o) => ! empty($o['flash_error'])), $context);

            $submission->refresh();
            $this->assertSame('submitted', $submission->status, $context);
            $this->assertSame('review_required', $submission->integrity_status, $context);
            $this->assertSame('0.00', $submission->score, $context);
            $this->assertSame('5.00', $submission->provisional_score, $context);
            $this->assertSame(1, $submission->draft_version, 'finalized once. ' . $context);
            $this->assertSame(1, DB::table('notifications')->where('user_id', $student->id)->where('type', 'assessment_held_for_review')->count(), $context);
            $this->assertSame(
                1,
                DB::table('anti_cheat_events')
                    ->where('assessment_submission_id', $submission->id)
                    ->where('event_type', 'locked_attempt_finalized')
                    ->count(),
                'exactly one lock record. ' . $context
            );
        }
    }

    public function test_updates_11_migrations_rerun_safely_with_mysql_55_compatible_columns(): void
    {
        [$assessment, , $submission] = $this->createAttempt(true);
        $submission->forceFill(['status' => 'graded', 'score' => 5, 'submitted_at' => now(), 'graded_at' => now()])->save();

        $tables = ['assessments', 'assessment_submissions', 'assessment_questions', 'assessment_answers', 'question_bank_items', 'question_bank_options', 'anti_cheat_events', 'anti_cheat_settings'];
        $counts = collect($tables)->mapWithKeys(fn (string $t) => [$t => DB::table($t)->count()])->all();
        $row = (array) DB::table('assessment_submissions')->where('id', $submission->id)->first();
        $assessmentRow = (array) DB::table('assessments')->where('id', $assessment->id)->first();

        foreach (self::U11_MIGRATIONS as $file) {
            $migration = require base_path($file);
            $migration->up();
            $migration->up();
        }

        $this->assertSame($counts, collect($tables)->mapWithKeys(fn (string $t) => [$t => DB::table($t)->count()])->all(), 'no row added or removed');
        $this->assertEquals($row, (array) DB::table('assessment_submissions')->where('id', $submission->id)->first());
        $this->assertEquals($assessmentRow, (array) DB::table('assessments')->where('id', $assessment->id)->first());

        foreach (['class_assignments', 'assignment_submissions', 'assignment_submission_answers', 'assignment_library_items', 'assignment_questions', 'assignment_question_options', 'assignment_blank_answers'] as $legacy) {
            $this->assertFalse(Schema::hasTable($legacy), "{$legacy} is gone after the merge");
        }

        $newColumns = [
            'assessments' => ['purpose', 'topic_title', 'passing_score_percent', 'legacy_class_assignment_id'],
            'assessment_submissions' => ['anti_cheat_session_id', 'integrity_status', 'integrity_reason', 'provisional_score', 'integrity_reviewed_by', 'integrity_reviewed_at', 'legacy_assignment_submission_id'],
            'anti_cheat_events' => ['assessment_id', 'assessment_submission_id', 'assessment_question_id'],
        ];
        foreach ($newColumns as $table => $columns) {
            $found = collect(DB::select(
                'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME IN (' . implode(',', array_fill(0, count($columns), '?')) . ')',
                array_merge([$table], $columns)
            ))->keyBy('COLUMN_NAME');
            $this->assertCount(count($columns), $found, "{$table} has every Updates 11 column");
            foreach ($found as $column) {
                $this->assertSame('YES', $column->IS_NULLABLE, "{$table}.{$column->COLUMN_NAME} is nullable, so existing rows need no backfill");
                $this->assertContains($column->DATA_TYPE, ['varchar', 'tinyint', 'int', 'bigint', 'decimal', 'datetime', 'timestamp'], "{$table}.{$column->COLUMN_NAME}");
            }
        }

        // The retired enum is a plain VARCHAR now, so 'assessment' fits on MySQL 5.5.
        foreach (['anti_cheat_events', 'anti_cheat_settings'] as $table) {
            $type = DB::selectOne(
                "SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'assessment_type'",
                [$table]
            );
            $this->assertSame('varchar', $type->DATA_TYPE, "{$table}.assessment_type");
        }

        // No native JSON anywhere in the touched tables (MySQL 5.5 has none).
        $json = DB::select(
            "SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND DATA_TYPE = 'json' AND TABLE_NAME IN (" . implode(',', array_fill(0, count($tables), '?')) . ')',
            $tables
        );
        $this->assertSame([], $json);
    }

    /**
     * Fire WORKERS separate PHP processes at the same attempt at the same moment.
     *
     * @param array<string, mixed> $payload answers map, or the full form payload
     * @return array{0: list<array<string, mixed>>, 1: string}
     */
    private function race(Assessment $assessment, AssessmentSubmission $submission, User $student, array $payload, string $label): array
    {
        $json = json_encode($payload);
        $startAt = sprintf('%.4F', microtime(true) + 2.5);

        $processes = [];
        for ($worker = 0; $worker < self::WORKERS; $worker++) {
            $process = new Process(
                [
                    PHP_BINARY,
                    __DIR__ . '/Support/assessment_submit_worker.php',
                    (string) $assessment->id,
                    (string) $submission->id,
                    (string) $student->id,
                    $startAt,
                    $json,
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
            $outcomes[] = $decoded;
        }

        $context = "{$label}: " . json_encode($outcomes);

        // Every worker saw an in-progress attempt, so they really did race.
        foreach ($outcomes as $outcome) {
            $this->assertSame('in_progress', $outcome['status_seen_before'] ?? null, $context);
            $this->assertSame('response', $outcome['outcome'], $context);
            $this->assertStringContainsString('/result', (string) $outcome['location'], $context);
        }

        return [$outcomes, $context];
    }

    /** @param array<string, mixed> $overrides */
    private function enablePolicy(int $instructorId, array $overrides = []): void
    {
        DB::table('anti_cheat_settings')->insert(array_merge([
            'instructor_id' => $instructorId,
            'class_id' => null,
            'assessment_type' => 'assessment',
            'enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    /** @return array{0: Assessment, 1: array<int, AssessmentQuestion>, 2: AssessmentSubmission, 3: User, 4: int, 5: int} */
    private function createAttempt(bool $protected = false): array
    {
        $token = Str::lower(Str::random(10));

        $instructor = User::create([
            'name' => 'Race Instructor',
            'email' => "race-instructor-{$token}@example.test",
            'password' => 'TestPassword!123',
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
        ]);
        $student = User::create([
            'name' => 'Race Student',
            'email' => "race-student-{$token}@example.test",
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
        $this->userIds[] = $instructor->id;
        $this->userIds[] = $student->id;

        $classId = DB::table('classes')->insertGetId([
            'instructor_id' => $instructor->id,
            'name' => 'Race Class ' . $token,
            'class_code' => strtoupper(substr($token, 0, 8)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->classIds[] = $classId;
        DB::table('class_student')->insert([
            'class_id' => $classId,
            'student_id' => $student->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tosId = DB::table('table_of_specifications')->insertGetId([
            'class_id' => $classId,
            'module_no' => 1,
            'title' => 'Race TOS ' . $token,
            'created_by' => $instructor->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->tosIds[] = $tosId;

        $assessment = Assessment::create([
            'table_of_specification_id' => $tosId,
            'class_id' => $classId,
            'created_by' => $instructor->id,
            'title' => 'Race Assessment ' . $token,
            'status' => 'published',
            'published_at' => now(),
            'total_items' => 2,
            'total_points' => 5,
            'max_attempts' => 1,
        ]);
        $this->assessmentIds[] = $assessment->id;

        $mcq = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'item_number' => 1,
            'question_type' => 'multiple_choice',
            'question_text' => 'Pick the right one.',
            'points' => 3,
            'is_required' => true,
            'topic_title' => 'Race',
        ]);
        foreach ([['Right', true], ['Wrong', false]] as $index => [$text, $isCorrect]) {
            AssessmentQuestionOption::create([
                'assessment_question_id' => $mcq->id,
                'option_label' => chr(65 + $index),
                'option_text' => $text,
                'is_correct' => $isCorrect,
                'order_index' => $index + 1,
            ]);
        }
        $blank = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'item_number' => 2,
            'question_type' => 'fill_blank',
            'question_text' => 'Impossible event probability?',
            'points' => 2,
            'is_required' => true,
            'correct_answer' => '0',
            'topic_title' => 'Race',
        ]);

        $submission = AssessmentSubmission::create([
            'assessment_id' => $assessment->id,
            'student_id' => $student->id,
            'attempt_no' => 1,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => 5,
            'started_at' => now(),
            'anti_cheat_session_id' => $protected ? Str::random(64) : null,
        ]);

        return [$assessment, [$mcq->fresh('options'), $blank], $submission, $student, (int) $instructor->id, (int) $classId];
    }
}
