<?php

namespace Tests\Feature\Regression;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 11 merge on a real InnoDB server, where a table outside
 * the assignment feature holds foreign keys to the assignment tables (as on
 * installs with an older schema or an imported dump). MySQL refuses to drop
 * a referenced table (errors 1217 / 1451), which used to stop the merge
 * part-way. The merge must remove those constraints, keep the referencing
 * columns and rows, convert everything, and drop the old tables.
 *
 * The old assignment tables are rebuilt from their original migrations in
 * the test database for the duration of the test. Skipped unless
 * DS_MYSQL_TEST_DB names a migrated MySQL/MariaDB database without them
 * (optional DS_MYSQL_TEST_HOST/PORT/USER/PASSWORD; defaults
 * 127.0.0.1/3306/ds/ds).
 */
class Updates11MergeForeignKeysMysqlTest extends TestCase
{
    private const LEGACY_MIGRATIONS = [
        '2026_05_26_000001_create_assignment_library_items_table.php',
        '2026_05_26_000002_create_assignment_questions_table.php',
        '2026_05_26_000003_create_assignment_question_options_table.php',
        '2026_05_26_000004_create_assignment_blank_answers_table.php',
        '2026_05_26_000005_create_class_assignments_table.php',
        '2026_05_26_000006_create_assignment_submissions_table.php',
        '2026_05_26_000007_create_assignment_submission_answers_table.php',
        '2026_07_18_000008_bind_anti_cheat_sessions.php',
        '2026_08_30_000001_preserve_timed_submission_answers.php',
        '2026_09_20_000001_add_integrity_outcome_to_assignment_submissions.php',
    ];

    private const LEGACY_TABLES = [
        'assignment_submission_answers', 'assignment_submissions', 'assignment_blank_answers',
        'assignment_question_options', 'assignment_questions', 'class_assignments', 'assignment_library_items',
    ];

    private const OUTSIDE_KEYS = ['u11_test_events_submission_fk', 'u11_test_events_assignment_fk', 'u11_test_events_question_fk'];

    private bool $ready = false;
    /** @var list<int> */
    private array $userIds = [];
    private int $classId = 0;
    private int $assignmentId = 0;
    private int $eventId = 0;
    /** @var array<string, int> */
    private array $maxIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $database = getenv('DS_MYSQL_TEST_DB') ?: '';
        if ($database === '') {
            $this->markTestSkipped('Set DS_MYSQL_TEST_DB to a migrated MySQL/MariaDB database to run this test.');
        }

        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.host', getenv('DS_MYSQL_TEST_HOST') ?: '127.0.0.1');
        config()->set('database.connections.mysql.port', getenv('DS_MYSQL_TEST_PORT') ?: '3306');
        config()->set('database.connections.mysql.database', $database);
        config()->set('database.connections.mysql.username', getenv('DS_MYSQL_TEST_USER') ?: 'ds');
        config()->set('database.connections.mysql.password', getenv('DS_MYSQL_TEST_PASSWORD') ?: 'ds');
        DB::purge('mysql');

        foreach (self::LEGACY_TABLES as $table) {
            if (Schema::hasTable($table)) {
                $this->markTestSkipped('The test database still has the assignment tables; run it on a database that is already past the merge.');
            }
        }
        $this->ready = true;
    }

    protected function tearDown(): void
    {
        if ($this->ready) {
            foreach (self::OUTSIDE_KEYS as $key) {
                $exists = DB::selectOne(
                    "SELECT COUNT(*) AS n FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'anti_cheat_events' AND CONSTRAINT_NAME = ?",
                    [$key]
                )->n;
                if ($exists) {
                    DB::statement("ALTER TABLE anti_cheat_events DROP FOREIGN KEY `{$key}`");
                }
            }
            Schema::disableForeignKeyConstraints();
            foreach (self::LEGACY_TABLES as $table) {
                Schema::dropIfExists($table);
            }
            Schema::enableForeignKeyConstraints();

            $assessmentIds = DB::table('assessments')->where('legacy_class_assignment_id', $this->assignmentId ?: -1)->pluck('id');
            $submissionIds = DB::table('assessment_submissions')->whereIn('assessment_id', $assessmentIds)->pluck('id');
            $questionIds = DB::table('assessment_questions')->whereIn('assessment_id', $assessmentIds)->pluck('id');
            DB::table('assessment_answers')->whereIn('assessment_submission_id', $submissionIds)->delete();
            DB::table('assessment_submissions')->whereIn('id', $submissionIds)->delete();
            DB::table('assessment_question_options')->whereIn('assessment_question_id', $questionIds)->delete();
            DB::table('assessment_questions')->whereIn('id', $questionIds)->delete();
            DB::table('assessments')->whereIn('id', $assessmentIds)->delete();
            if ($this->eventId) {
                DB::table('anti_cheat_events')->where('id', $this->eventId)->delete();
            }
            foreach ($this->maxIds as $table => $maxId) {
                DB::table($table)->where('id', '>', $maxId)->delete();
            }
            DB::table('class_student')->where('class_id', $this->classId)->delete();
            DB::table('classes')->where('id', $this->classId)->delete();
            DB::table('users')->whereIn('id', $this->userIds)->delete();
        }

        parent::tearDown();
    }

    public function test_foreign_keys_from_other_tables_do_not_stop_the_merge(): void
    {
        foreach (['question_bank_options', 'question_bank_items', 'legacy_assignment_archive'] as $table) {
            $this->maxIds[$table] = (int) DB::table($table)->max('id');
        }

        // The old assignment tables, exactly as their migrations built them.
        foreach (self::LEGACY_MIGRATIONS as $file) {
            (require database_path('migrations/' . $file))->up();
        }
        // Ids far above anything already converted in this database, so the
        // rows cannot be mistaken for converted ones.
        foreach (['class_assignments', 'assignment_submissions', 'assignment_library_items', 'assignment_questions', 'assignment_question_options'] as $table) {
            DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 900000");
        }

        // A table outside the feature that points at three assignment tables.
        DB::statement('ALTER TABLE anti_cheat_events ADD CONSTRAINT u11_test_events_submission_fk FOREIGN KEY (assignment_submission_id) REFERENCES assignment_submissions (id)');
        DB::statement('ALTER TABLE anti_cheat_events ADD CONSTRAINT u11_test_events_assignment_fk FOREIGN KEY (class_assignment_id) REFERENCES class_assignments (id)');
        DB::statement('ALTER TABLE anti_cheat_events ADD CONSTRAINT u11_test_events_question_fk FOREIGN KEY (assignment_question_id) REFERENCES assignment_questions (id)');

        $token = Str::lower(Str::random(8));
        $instructorId = (int) DB::table('users')->insertGetId(['name' => 'FK Instructor', 'email' => "u11-fk-instructor-{$token}@example.test", 'password' => bcrypt('unused-123'), 'role' => 4, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        $studentId = (int) DB::table('users')->insertGetId(['name' => 'FK Student', 'email' => "u11-fk-student-{$token}@example.test", 'password' => bcrypt('unused-123'), 'role' => 1, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        $this->userIds = [$instructorId, $studentId];
        $this->classId = (int) DB::table('classes')->insertGetId(['instructor_id' => $instructorId, 'name' => 'FK Class ' . $token, 'class_code' => Str::upper(Str::random(8)), 'created_at' => now(), 'updated_at' => now()]);
        DB::table('class_student')->insert(['class_id' => $this->classId, 'student_id' => $studentId, 'created_at' => now(), 'updated_at' => now()]);

        $code = 'FK-' . Str::upper(Str::random(6));
        $itemId = (int) DB::table('assignment_library_items')->insertGetId([
            'module_no' => 1, 'assignment_code' => $code, 'title' => 'FK Worksheet', 'topic_title' => 'Loops', 'year_level' => 'First Year',
            'assignment_type' => 'mcq', 'version_code' => $code . '-V1', 'time_limit_minutes' => 15, 'total_points' => 5, 'is_active' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $questionId = (int) DB::table('assignment_questions')->insertGetId(['assignment_library_item_id' => $itemId, 'question_type' => 'mcq', 'question_text' => 'Which loop repeats over a list?', 'points' => 5, 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $correctId = (int) DB::table('assignment_question_options')->insertGetId(['assignment_question_id' => $questionId, 'option_text' => 'for', 'is_correct' => true, 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('assignment_question_options')->insert(['assignment_question_id' => $questionId, 'option_text' => 'if', 'is_correct' => false, 'order_index' => 2, 'created_at' => now(), 'updated_at' => now()]);
        $this->assignmentId = (int) DB::table('class_assignments')->insertGetId([
            'class_id' => $this->classId, 'assignment_library_item_id' => $itemId, 'assigned_by' => $instructorId, 'title' => 'FK Homework',
            'max_attempts' => 1, 'status' => 'published', 'assigned_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $session = Str::random(64);
        $submissionId = (int) DB::table('assignment_submissions')->insertGetId([
            'class_assignment_id' => $this->assignmentId, 'student_id' => $studentId, 'attempt_no' => 1, 'status' => 'graded', 'score' => 5, 'total_points' => 5,
            'started_at' => now()->subMinutes(10), 'submitted_at' => now(), 'graded_at' => now(), 'feedback' => 'Well done', 'anti_cheat_session_id' => $session,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('assignment_submission_answers')->insert(['assignment_submission_id' => $submissionId, 'assignment_question_id' => $questionId, 'selected_option_id' => $correctId, 'is_correct' => true, 'points_awarded' => 5, 'created_at' => now(), 'updated_at' => now()]);
        $this->eventId = (int) DB::table('anti_cheat_events')->insertGetId([
            'user_id' => $studentId, 'class_id' => $this->classId, 'class_assignment_id' => $this->assignmentId, 'assignment_submission_id' => $submissionId,
            'assignment_question_id' => $questionId, 'assessment_type' => 'assignment', 'event_type' => 'focus_loss', 'severity' => 'warning',
            'attempt_session_id' => $session, 'event_uuid' => (string) Str::uuid(), 'occurred_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        (require database_path('migrations/2026_10_01_000003_merge_assignments_into_assessments.php'))->up();

        foreach (self::LEGACY_TABLES as $table) {
            $this->assertFalse(Schema::hasTable($table), "{$table} is dropped");
        }
        $this->assertSame(0, (int) DB::selectOne(
            "SELECT COUNT(*) AS n FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'anti_cheat_events' AND CONSTRAINT_NAME LIKE 'u11_test_events_%'"
        )->n, 'the outside constraints are removed');

        $assessment = DB::table('assessments')->where('legacy_class_assignment_id', $this->assignmentId)->first();
        $this->assertNotNull($assessment, 'the assignment was converted');
        $submission = DB::table('assessment_submissions')->where('legacy_assignment_submission_id', $submissionId)->first();
        $this->assertSame('5.00', (string) $submission->score);
        $this->assertSame('Well done', $submission->feedback);
        $this->assertSame(1, DB::table('assessment_answers')->where('assessment_submission_id', $submission->id)->count());

        // The referencing row and its original values stay, relinked to the copies.
        $event = DB::table('anti_cheat_events')->find($this->eventId);
        $this->assertSame($submissionId, (int) $event->assignment_submission_id);
        $this->assertSame($this->assignmentId, (int) $event->class_assignment_id);
        $this->assertSame((int) $assessment->id, (int) $event->assessment_id);
        $this->assertSame((int) $submission->id, (int) $event->assessment_submission_id);
        $this->assertSame('assessment', $event->assessment_type);
        $this->assertNotNull($event->assessment_question_id);
    }
}
