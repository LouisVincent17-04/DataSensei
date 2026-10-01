<?php

namespace Tests\Unit;

use App\Models\AssessmentSubmission;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The timed-submission answer snapshot (draft_answers, draft_version,
 * draft_saved_at, timed_out_at) must survive every schema step:
 *   - 2026_08_30 adds the columns and never removes them;
 *   - after DataSensei Updates 11 the assignment tables are gone, so that
 *     migration must still run where only assessment_submissions exists;
 *   - the Updates 11 merge (2026_10_01_000003) carries every assignment
 *     attempt's snapshot into assessment_submissions, remapped to the new
 *     question and option ids, before the assignment tables are dropped.
 */
class TimedSubmissionAnswerMigrationTest extends TestCase
{
    private const SNAPSHOT_MIGRATION = 'migrations/2026_08_30_000001_preserve_timed_submission_answers.php';
    private const MERGE_MIGRATION = 'migrations/2026_10_01_000003_merge_assignments_into_assessments.php';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('assessment_submissions');
        Schema::dropIfExists('assignment_submissions');
        parent::tearDown();
    }

    /** @param list<string> $tables */
    private function bareSubmissionTables(array $tables): void
    {
        foreach ($tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->string('status')->default('in_progress');
            });
        }
    }

    public function test_snapshot_and_timeout_columns_are_added_and_preserved(): void
    {
        // An installation upgrading from before Updates 11 still has both
        // tables when this migration runs (it is ordered before the merge).
        $this->bareSubmissionTables(['assignment_submissions', 'assessment_submissions']);
        $migration = require database_path(self::SNAPSHOT_MIGRATION);

        $migration->up();

        foreach (['assignment_submissions', 'assessment_submissions'] as $tableName) {
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_answers'));
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_version'));
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_saved_at'));
            $this->assertTrue(Schema::hasColumn($tableName, 'timed_out_at'));
        }

        $migration->down();

        foreach (['assignment_submissions', 'assessment_submissions'] as $tableName) {
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_answers'));
            $this->assertTrue(Schema::hasColumn($tableName, 'timed_out_at'));
        }
    }

    public function test_the_snapshot_migration_still_runs_once_the_assignment_tables_are_gone(): void
    {
        $this->bareSubmissionTables(['assessment_submissions']);
        $migration = require database_path(self::SNAPSHOT_MIGRATION);

        $migration->up();
        $migration->up();

        $this->assertFalse(Schema::hasTable('assignment_submissions'), 'The removed table is skipped, not recreated.');
        foreach (['draft_answers', 'draft_version', 'draft_saved_at', 'timed_out_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('assessment_submissions', $column), $column);
        }

        $migration->down();
        $this->assertTrue(Schema::hasColumn('assessment_submissions', 'draft_answers'));
        $this->assertTrue(Schema::hasColumn('assessment_submissions', 'timed_out_at'));
    }

    public function test_the_merge_carries_every_attempts_answer_snapshot_into_assessment_submissions(): void
    {
        $this->migrateUpToTheMerge();

        $instructor = $this->user('Merge Instructor', User::ROLE_INSTRUCTOR);
        $student = $this->user('Merge Student', User::ROLE_USER);
        $classId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $instructor->id,
            'name' => 'Merge Class',
            'class_code' => 'M' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // An assessment that already exists before the merge, so the copied
        // questions and options get ids that differ from the legacy ones and
        // the remapping is observable.
        $this->existingAssessment($classId, $instructor->id);

        [$assignmentId, $legacy] = $this->legacyAssignment($classId, $instructor->id);

        $savedAt = now()->subMinutes(10)->startOfSecond();
        $timedOutAt = now()->subMinutes(5)->startOfSecond();
        $inProgress = $this->legacySubmission($assignmentId, $student->id, 1, [
            'status' => 'in_progress',
            'draft_answers' => json_encode([
                (string) $legacy['mcq'] => (string) $legacy['correct'],
                (string) $legacy['blank'] => 'pandas',
                '999999' => 'answer to a question that no longer exists',
            ]),
            'draft_version' => 4,
            'draft_saved_at' => $savedAt,
        ]);
        $timedOut = $this->legacySubmission($assignmentId, $student->id, 2, [
            'status' => 'graded',
            'score' => 0,
            'submitted_at' => $timedOutAt,
            'graded_at' => $timedOutAt,
            'draft_answers' => json_encode([(string) $legacy['mcq'] => (string) $legacy['wrong']]),
            'draft_version' => 7,
            'draft_saved_at' => $savedAt,
            'timed_out_at' => $timedOutAt,
        ]);
        $corrupt = $this->legacySubmission($assignmentId, $student->id, 3, [
            'status' => 'in_progress',
            'draft_answers' => '{not json',
            'draft_version' => 2,
        ]);
        $empty = $this->legacySubmission($assignmentId, $student->id, 4, [
            'status' => 'in_progress',
            'draft_answers' => null,
            'draft_version' => 0,
        ]);
        // A typed blank answer that happens to equal an old option id stays
        // as written; only multiple-choice values are option ids.
        $numeric = $this->legacySubmission($assignmentId, $student->id, 5, [
            'status' => 'in_progress',
            'draft_answers' => json_encode([
                (string) $legacy['mcq'] => (string) $legacy['wrong'],
                (string) $legacy['blank'] => (string) $legacy['correct'],
            ]),
            'draft_version' => 1,
        ]);
        // A multiple-choice value that is not one of that question's options
        // is dropped instead of being pointed at an unrelated option.
        $stray = $this->legacySubmission($assignmentId, $student->id, 6, [
            'status' => 'in_progress',
            'draft_answers' => json_encode([(string) $legacy['mcq'] => '999999']),
            'draft_version' => 1,
        ]);

        $merge = require database_path(self::MERGE_MIGRATION);
        $merge->up();

        foreach (['class_assignments', 'assignment_submissions', 'assignment_questions', 'assignment_question_options'] as $table) {
            $this->assertFalse(Schema::hasTable($table), "{$table} is dropped after a full conversion");
        }

        $assessmentId = (int) DB::table('assessments')->where('legacy_class_assignment_id', $assignmentId)->value('id');
        $this->assertGreaterThan(0, $assessmentId);
        $newMcq = (int) DB::table('assessment_questions')->where('assessment_id', $assessmentId)->where('question_type', 'multiple_choice')->value('id');
        $newBlank = (int) DB::table('assessment_questions')->where('assessment_id', $assessmentId)->where('question_type', 'fill_blank')->value('id');
        $newCorrect = (int) DB::table('assessment_question_options')->where('assessment_question_id', $newMcq)->where('is_correct', true)->value('id');
        $newWrong = (int) DB::table('assessment_question_options')->where('assessment_question_id', $newMcq)->where('is_correct', false)->value('id');
        $this->assertNotSame($legacy['mcq'], $newMcq, 'The fixture must make remapping observable.');
        $this->assertNotSame($legacy['correct'], $newCorrect, 'The fixture must make remapping observable.');

        $converted = fn (int $legacyId): AssessmentSubmission => AssessmentSubmission::query()
            ->where('legacy_assignment_submission_id', $legacyId)
            ->firstOrFail();

        // The unfinished attempt keeps its work, keyed by the new ids.
        $resumed = $converted($inProgress);
        $this->assertSame($assessmentId, (int) $resumed->assessment_id);
        $this->assertSame('in_progress', $resumed->status);
        $this->assertSame([
            (string) $newMcq => (string) $newCorrect,
            (string) $newBlank => 'pandas',
        ], $resumed->draft_answers);
        $this->assertSame(4, $resumed->draft_version);
        $this->assertSame($savedAt->toDateTimeString(), $resumed->draft_saved_at->toDateTimeString());
        $this->assertNull($resumed->timed_out_at);
        $this->assertNotSame('', (string) $resumed->anti_cheat_session_id);

        // The timed-out attempt keeps its graded snapshot and timeout evidence.
        $expired = $converted($timedOut);
        $this->assertSame('graded', $expired->status);
        $this->assertSame([(string) $newMcq => (string) $newWrong], $expired->draft_answers);
        $this->assertSame(7, $expired->draft_version);
        $this->assertSame($timedOutAt->toDateTimeString(), $expired->timed_out_at->toDateTimeString());

        // An unreadable or empty snapshot becomes "no draft", never garbage.
        $this->assertNull($converted($corrupt)->draft_answers);
        $this->assertSame(2, $converted($corrupt)->draft_version);
        $this->assertNull($converted($empty)->draft_answers);

        $this->assertSame([
            (string) $newMcq => (string) $newWrong,
            (string) $newBlank => (string) $legacy['correct'],
        ], $converted($numeric)->draft_answers);
        $this->assertSame([], $converted($stray)->draft_answers);

        // Rerunning after the tables are gone does nothing and duplicates nothing.
        $merge->up();
        $this->assertSame(6, AssessmentSubmission::query()->whereNotNull('legacy_assignment_submission_id')->count());
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    /** Runs every real migration that is ordered before the Updates 11 merge. */
    private function migrateUpToTheMerge(): void
    {
        $mergePath = realpath(database_path(self::MERGE_MIGRATION));
        $paths = collect(glob(database_path('migrations/*.php')))
            ->map(fn (string $path) => realpath($path))
            ->filter(fn (string $path) => strcmp(basename($path), basename($mergePath)) < 0)
            ->sort()
            ->values()
            ->all();

        $this->artisan('migrate', ['--path' => $paths, '--realpath' => true])->assertExitCode(0);

        $this->assertTrue(Schema::hasTable('class_assignments'));
        $this->assertTrue(Schema::hasColumn('assignment_submissions', 'draft_answers'));
        $this->assertTrue(Schema::hasColumn('assessment_submissions', 'legacy_assignment_submission_id'));
    }

    private function user(string $name, int $role): User
    {
        return User::create([
            'name' => $name,
            'email' => Str::slug($name) . '-' . Str::lower(Str::random(6)) . '@example.test',
            'password' => 'not-used-in-this-unit-test',
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function existingAssessment(int $classId, int $instructorId): void
    {
        $assessmentId = (int) DB::table('assessments')->insertGetId([
            'class_id' => $classId,
            'created_by' => $instructorId,
            'title' => 'Existing Assessment',
            'status' => 'draft',
            'total_items' => 5,
            'total_points' => 5,
            'max_attempts' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $questionId = (int) DB::table('assessment_questions')->insertGetId([
                'assessment_id' => $assessmentId,
                'item_number' => $i,
                'question_type' => 'multiple_choice',
                'question_text' => "Existing question {$i}",
                'points' => 1,
                'is_required' => true,
                'topic_title' => 'Existing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ([true, false] as $index => $correct) {
                DB::table('assessment_question_options')->insert([
                    'assessment_question_id' => $questionId,
                    'option_label' => chr(65 + $index),
                    'option_text' => $correct ? 'Yes' : 'No',
                    'is_correct' => $correct,
                    'order_index' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /** @return array{0: int, 1: array{mcq: int, correct: int, wrong: int, blank: int}} */
    private function legacyAssignment(int $classId, int $instructorId): array
    {
        $code = 'TS-' . Str::upper(Str::random(6));
        $itemId = (int) DB::table('assignment_library_items')->insertGetId([
            'module_no' => 1,
            'assignment_code' => $code,
            'title' => 'Timed Worksheet',
            'topic_title' => 'Pandas',
            'year_level' => 'First Year',
            'assignment_type' => 'mixed',
            'version_code' => $code . '-V1',
            'time_limit_minutes' => 5,
            'total_points' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $mcq = (int) DB::table('assignment_questions')->insertGetId([
            'assignment_library_item_id' => $itemId,
            'question_type' => 'mcq',
            'question_text' => 'Which option is correct?',
            'points' => 5,
            'order_index' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $correct = (int) DB::table('assignment_question_options')->insertGetId([
            'assignment_question_id' => $mcq, 'option_text' => 'Correct option', 'is_correct' => true, 'order_index' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $wrong = (int) DB::table('assignment_question_options')->insertGetId([
            'assignment_question_id' => $mcq, 'option_text' => 'Wrong option', 'is_correct' => false, 'order_index' => 2,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $blank = (int) DB::table('assignment_questions')->insertGetId([
            'assignment_library_item_id' => $itemId,
            'question_type' => 'fill_blank',
            'question_text' => 'Which library provides DataFrame?',
            'points' => 5,
            'order_index' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('assignment_blank_answers')->insert([
            'assignment_question_id' => $blank, 'answer_text' => 'pandas', 'is_case_sensitive' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $assignmentId = (int) DB::table('class_assignments')->insertGetId([
            'class_id' => $classId,
            'assignment_library_item_id' => $itemId,
            'assigned_by' => $instructorId,
            'title' => 'Timed Worksheet',
            'max_attempts' => 5,
            'status' => 'published',
            'assigned_at' => now()->subDay(),
            'created_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        return [$assignmentId, ['mcq' => $mcq, 'correct' => $correct, 'wrong' => $wrong, 'blank' => $blank]];
    }

    /** @param array<string, mixed> $overrides */
    private function legacySubmission(int $assignmentId, int $studentId, int $attemptNo, array $overrides): int
    {
        return (int) DB::table('assignment_submissions')->insertGetId(array_merge([
            'class_assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'attempt_no' => $attemptNo,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => 10,
            'started_at' => now()->subMinutes(15),
            'anti_cheat_session_id' => Str::random(64),
            'created_at' => now()->subMinutes(15),
            'updated_at' => now(),
        ], $overrides));
    }
}
