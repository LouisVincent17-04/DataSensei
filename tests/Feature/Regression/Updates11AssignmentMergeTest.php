<?php

namespace Tests\Feature\Regression;

use App\Models\AssessmentSubmission;
use App\Models\User;
use App\Support\AssignmentToAssessmentMerge;
use App\Support\AuthSessionFingerprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 11: the upgrade that retires the Assignment feature.
 *
 * The schema is built by the real migrations up to just before the merge
 * (2026_10_01_000003), filled with assignment data the way the old feature
 * stored it, and then the merge migration runs. Every class assignment must
 * come out as an assessment with its class link, instructions, dates,
 * questions, submissions, answers, scores, feedback and anti-cheat records;
 * the rerun must duplicate nothing; nothing may award XP, achievements or
 * notifications; and an unconvertible assignment must be kept in the
 * documented archive instead of being lost.
 */
class Updates11AssignmentMergeTest extends TestCase
{
    private const MERGE_MIGRATION = 'migrations/2026_10_01_000003_merge_assignments_into_assessments.php';

    private const LEGACY_TABLES = [
        'class_assignments', 'assignment_submissions', 'assignment_submission_answers',
        'assignment_library_items', 'assignment_questions', 'assignment_question_options', 'assignment_blank_answers',
    ];

    private User $instructor;
    private User $ana;
    private User $ben;
    private int $classId;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $mergePath = realpath(database_path(self::MERGE_MIGRATION));
        $paths = collect(glob(database_path('migrations/*.php')))
            ->map(fn (string $path) => realpath($path))
            ->filter(fn (string $path) => strcmp(basename($path), basename($mergePath)) < 0)
            ->sort()
            ->values()
            ->all();
        $this->artisan('migrate', ['--path' => $paths, '--realpath' => true])->assertExitCode(0);
        $this->assertTrue(Schema::hasTable('class_assignments'), 'The legacy schema exists before the merge.');

        // Instructors are only let in while their institution is active.
        $institutionId = (int) DB::table('institutions')->insertGetId([
            'name' => 'Merge Institution', 'slug' => 'merge-institution-' . Str::lower(Str::random(6)),
            'email' => 'merge-' . Str::lower(Str::random(6)) . '@institution.test', 'status' => 'active',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->instructor = $this->user('Merge Instructor', User::ROLE_INSTRUCTOR, $institutionId);
        $this->ana = $this->user('Ana Student', User::ROLE_USER);
        $this->ben = $this->user('Ben Student', User::ROLE_USER);
        $this->classId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'name' => 'Merge Class',
            'class_code' => 'M' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        foreach ([$this->ana, $this->ben] as $student) {
            DB::table('class_student')->insert(['class_id' => $this->classId, 'student_id' => $student->id, 'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function test_every_assignment_becomes_an_assessment_with_its_work_grades_and_evidence(): void
    {
        $item = $this->libraryItem('Loops Worksheet', true);
        $inactive = $this->libraryItem('Retired Worksheet', false);

        $due = Carbon::parse('2026-09-15 17:00:00');
        $published = $this->classAssignment($item['id'], 'Loops Homework', 'published', [
            'instructions' => 'Answer both items.',
            'available_at' => '2026-09-01 08:00:00',
            'due_at' => $due,
            'max_attempts' => 2,
            'assigned_at' => '2026-09-01 08:00:00',
        ]);
        $closed = $this->classAssignment($item['id'], 'Closed Homework', 'closed');
        $archived = $this->classAssignment($inactive['id'], 'Archived Homework', 'archived');
        $draft = $this->classAssignment($item['id'], 'Draft Homework', 'draft');

        // Ana: graded with feedback. Ben: held for integrity review.
        $anaSub = $this->submission($published, $this->ana->id, [
            'status' => 'graded', 'score' => 8, 'feedback' => 'Good work on the loop.',
            'submitted_at' => '2026-09-10 10:00:00', 'graded_at' => '2026-09-10 10:00:00',
            'integrity_status' => 'clear',
        ]);
        $this->answer($anaSub, $item['mcq'], ['selected_option_id' => $item['correct'], 'is_correct' => true, 'points_awarded' => 5]);
        $this->answer($anaSub, $item['blank'], ['answer_text' => 'For loop', 'is_correct' => true, 'points_awarded' => 3]);

        $benSession = Str::random(64);
        $benSub = $this->submission($published, $this->ben->id, [
            'status' => 'submitted', 'score' => 0, 'provisional_score' => 5,
            'submitted_at' => '2026-09-11 10:00:00', 'anti_cheat_session_id' => $benSession,
            'integrity_status' => 'blocked', 'integrity_reason' => 'Multiple screens were detected.',
        ]);
        $this->answer($benSub, $item['mcq'], ['selected_option_id' => $item['correct'], 'is_correct' => true, 'points_awarded' => 0]);
        $eventId = (int) DB::table('anti_cheat_events')->insertGetId([
            'user_id' => $this->ben->id, 'class_id' => $this->classId,
            'class_assignment_id' => $published, 'assignment_submission_id' => $benSub, 'assignment_question_id' => $item['mcq'],
            'assessment_type' => 'assignment', 'event_type' => 'dual_monitor_detected', 'severity' => 'critical',
            'attempt_session_id' => $benSession, 'event_uuid' => (string) Str::uuid(),
            'occurred_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $settingId = (int) DB::table('anti_cheat_settings')->insertGetId([
            'instructor_id' => $this->instructor->id, 'class_id' => null, 'assessment_type' => 'assignment',
            'enabled' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $before = $this->sideEffects();

        $merge = require database_path(self::MERGE_MIGRATION);
        $merge->up();

        foreach (self::LEGACY_TABLES as $table) {
            $this->assertFalse(Schema::hasTable($table), "{$table} is dropped after a full conversion");
        }
        $this->assertSame(0, DB::table('legacy_assignment_archive')->count(), 'Nothing needed archiving.');

        // Assessments: one per assignment, status mapped, class link and dates kept.
        $assessments = DB::table('assessments')->whereNotNull('legacy_class_assignment_id')->get()->keyBy('legacy_class_assignment_id');
        $this->assertCount(4, $assessments);
        $homework = $assessments[$published];
        $this->assertSame('Loops Homework', $homework->title);
        $this->assertSame('Answer both items.', $homework->instructions);
        $this->assertSame('homework', $homework->purpose);
        $this->assertSame('published', $homework->status);
        $this->assertSame($this->classId, (int) $homework->class_id);
        $this->assertSame($this->instructor->id, (int) $homework->created_by);
        $this->assertSame('2026-09-01 08:00:00', Carbon::parse($homework->available_at)->toDateTimeString());
        $this->assertSame($due->toDateTimeString(), Carbon::parse($homework->due_at)->toDateTimeString());
        $this->assertSame('2026-09-01 08:00:00', Carbon::parse($homework->published_at)->toDateTimeString());
        $this->assertSame(2, (int) $homework->max_attempts);
        $this->assertSame(15, (int) $homework->time_limit_minutes);
        $this->assertSame(2, (int) $homework->total_items);
        $this->assertSame(10, (int) $homework->total_points);
        $this->assertSame('closed', $assessments[$closed]->status);
        $this->assertSame('closed', $assessments[$archived]->status, 'archived reads as closed');
        $this->assertSame('draft', $assessments[$draft]->status);
        $this->assertNull($assessments[$draft]->published_at);

        // Questions: same wording, choices and accepted answers.
        $questions = DB::table('assessment_questions')->where('assessment_id', $homework->id)->orderBy('item_number')->get();
        $this->assertSame(['multiple_choice', 'fill_blank'], $questions->pluck('question_type')->all());
        $this->assertSame("for\nFor loop", $questions[1]->correct_answer);
        $options = DB::table('assessment_question_options')->where('assessment_question_id', $questions[0]->id)->orderBy('order_index')->get();
        $this->assertSame(['A', 'B'], $options->pluck('option_label')->all());
        $this->assertSame(['range(3)', 'print(3)'], $options->pluck('option_text')->all());
        $newCorrect = (int) $options->firstWhere('is_correct', 1)->id;

        // Submissions: scores, feedback, statuses, integrity outcome kept.
        $ana = AssessmentSubmission::where('legacy_assignment_submission_id', $anaSub)->firstOrFail();
        $this->assertSame((int) $homework->id, (int) $ana->assessment_id);
        $this->assertSame('graded', $ana->status);
        $this->assertSame('8.00', $ana->score);
        $this->assertSame('Good work on the loop.', $ana->feedback);
        $this->assertSame('2026-09-10 10:00:00', $ana->graded_at->toDateTimeString());
        $anaAnswers = DB::table('assessment_answers')->where('assessment_submission_id', $ana->id)->orderBy('assessment_question_id')->get();
        $this->assertCount(2, $anaAnswers);
        $this->assertSame($newCorrect, (int) $anaAnswers[0]->selected_option_id, 'the chosen option points at the copied choice');
        $this->assertEquals(5, $anaAnswers[0]->points_awarded);
        $this->assertSame('For loop', $anaAnswers[1]->answer_text);
        $this->assertEquals(3, $anaAnswers[1]->points_awarded);

        $ben = AssessmentSubmission::where('legacy_assignment_submission_id', $benSub)->firstOrFail();
        $this->assertTrue($ben->isHeldForIntegrityReview());
        $this->assertSame('0.00', $ben->score);
        $this->assertSame('5.00', $ben->provisional_score);
        $this->assertSame('Multiple screens were detected.', $ben->integrity_reason);
        $this->assertSame($benSession, $ben->anti_cheat_session_id);

        // Anti-cheat evidence follows the attempt; policies now govern assessments.
        $event = DB::table('anti_cheat_events')->find($eventId);
        $this->assertSame((int) $homework->id, (int) $event->assessment_id);
        $this->assertSame($ben->id, (int) $event->assessment_submission_id);
        $this->assertSame((int) $questions[0]->id, (int) $event->assessment_question_id);
        $this->assertSame('assessment', $event->assessment_type);
        $this->assertSame($published, (int) $event->class_assignment_id, 'the original link is kept for the record');
        $this->assertSame('assessment', DB::table('anti_cheat_settings')->where('id', $settingId)->value('assessment_type'));

        // The active library became the read-only shared pool; the inactive one did not.
        $shared = DB::table('question_bank_items')->whereNull('instructor_id')->get();
        $this->assertSame(['Which loop prints 0, 1, 2?', 'Name the loop that repeats over a sequence.'], $shared->pluck('question_text')->all());
        $this->assertSame(2, DB::table('question_bank_options')->whereIn('question_bank_item_id', $shared->pluck('id'))->count());

        // No XP, achievements, missions or notifications from the conversion.
        $this->assertSame($before, $this->sideEffects());

        // Students and instructors keep working with the converted records.
        $this->actingAs($this->ana)->withSession([AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->ana)])
            ->get(route('student.assessments.result', [$homework->id, $ana->id]))
            ->assertOk()
            ->assertSee('Loops Homework')
            ->assertSee('Good work on the loop.');
        $this->actingAs($this->instructor)->withSession([AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->instructor)])
            ->get(route('instructor.assessments.submissions.show', [$homework->id, $ben->id]))
            ->assertOk()
            ->assertSee('Held by the anti-cheat decision')
            ->assertSee('Multiple screens were detected.')
            ->assertSee('Release and credit the score');

        // Rerunning changes nothing.
        $counts = $this->counts();
        $merge->up();
        $this->assertSame($counts, $this->counts());
        $this->assertSame(array_fill_keys(array_keys((new AssignmentToAssessmentMerge())->convert()), 0), (new AssignmentToAssessmentMerge())->convert());
    }

    public function test_a_partial_run_is_resumed_without_duplicates(): void
    {
        $item = $this->libraryItem('Loops Worksheet', true);
        $first = $this->classAssignment($item['id'], 'First Homework', 'published');
        $this->submission($first, $this->ana->id, ['status' => 'graded', 'score' => 10, 'submitted_at' => now(), 'graded_at' => now()]);

        // A first pass converts what exists; the tables are still there.
        $summary = (new AssignmentToAssessmentMerge())->convert();
        $this->assertSame(1, $summary['assignments_converted']);
        $this->assertSame(1, $summary['submissions_converted']);
        $this->assertSame(2, $summary['bank_questions_seeded']);

        // New work arrives before the migration finishes.
        $second = $this->classAssignment($item['id'], 'Second Homework', 'published');
        $this->submission($second, $this->ben->id, ['status' => 'submitted', 'submitted_at' => now()]);

        $merge = require database_path(self::MERGE_MIGRATION);
        $merge->up();

        $this->assertSame(2, DB::table('assessments')->whereNotNull('legacy_class_assignment_id')->count());
        $this->assertSame(2, DB::table('assessment_submissions')->whereNotNull('legacy_assignment_submission_id')->count());
        $this->assertSame(1, DB::table('assessments')->where('legacy_class_assignment_id', $first)->count());
        $this->assertSame(2, DB::table('question_bank_items')->whereNull('instructor_id')->count(), 'the shared pool is seeded once');
        $this->assertFalse(Schema::hasTable('class_assignments'));
    }

    public function test_an_unconvertible_assignment_is_archived_with_its_work_instead_of_lost(): void
    {
        $item = $this->libraryItem('Loops Worksheet', true);
        $good = $this->classAssignment($item['id'], 'Convertible Homework', 'published');

        // Rows imported with foreign-key checks off: the library item is gone.
        Schema::disableForeignKeyConstraints();
        $orphan = $this->classAssignment(987654, 'Orphaned Homework', 'published', ['instructions' => 'Old instructions']);
        $orphanSub = $this->submission($orphan, $this->ana->id, ['status' => 'graded', 'score' => 7, 'feedback' => 'Kept for the record', 'submitted_at' => now(), 'graded_at' => now()]);
        $orphanAnswer = $this->answer($orphanSub, 123456, ['answer_text' => 'while', 'is_correct' => true, 'points_awarded' => 7]);
        Schema::enableForeignKeyConstraints();

        $merge = require database_path(self::MERGE_MIGRATION);
        $merge->up();

        // The convertible one is converted, the orphan archived, and the old tables dropped.
        $this->assertSame(1, DB::table('assessments')->where('legacy_class_assignment_id', $good)->count());
        $this->assertSame(0, DB::table('assessments')->where('legacy_class_assignment_id', $orphan)->count());
        $this->assertFalse(Schema::hasTable('class_assignments'));

        $archive = DB::table('legacy_assignment_archive')->where('class_assignment_id', $orphan)->get()->keyBy('source_table');
        $this->assertSame(['assignment_submission_answers', 'assignment_submissions', 'class_assignments'], $archive->keys()->sort()->values()->all());
        $this->assertStringContainsString('library item no longer exists', $archive['class_assignments']->reason);
        $this->assertSame('Orphaned Homework', json_decode($archive['class_assignments']->payload, true)['title']);
        $this->assertSame('Old instructions', json_decode($archive['class_assignments']->payload, true)['instructions']);
        $this->assertSame($orphanSub, (int) $archive['assignment_submissions']->source_id);
        $this->assertSame('Kept for the record', json_decode($archive['assignment_submissions']->payload, true)['feedback']);
        $this->assertSame($orphanAnswer, (int) $archive['assignment_submission_answers']->source_id);
        $this->assertSame('while', json_decode($archive['assignment_submission_answers']->payload, true)['answer_text']);

        // Rerunning archives nothing twice.
        $merge->up();
        $this->assertSame(3, DB::table('legacy_assignment_archive')->count());
    }

    public function test_tables_outside_the_feature_that_point_at_assignment_tables_do_not_block_the_merge(): void
    {
        // Some installs carry extra foreign keys to the assignment tables (an
        // older schema, an imported dump). Dropping a referenced table used
        // to stop the migration part-way (MySQL errors 1217 / 1451).
        Schema::create('legacy_assignment_notes', function (\Illuminate\Database\Schema\Blueprint $table): void {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained('assignment_submissions');
            $table->foreignId('class_assignment_id')->constrained('class_assignments');
            $table->string('note');
        });

        $item = $this->libraryItem('Loops Worksheet', true);
        $assignment = $this->classAssignment($item['id'], 'Referenced Homework', 'published');
        $submission = $this->submission($assignment, $this->ana->id, ['status' => 'graded', 'score' => 9, 'submitted_at' => now(), 'graded_at' => now()]);
        DB::table('legacy_assignment_notes')->insert(['assignment_submission_id' => $submission, 'class_assignment_id' => $assignment, 'note' => 'Checked by hand']);

        $merge = require database_path(self::MERGE_MIGRATION);
        $merge->up();

        foreach (self::LEGACY_TABLES as $table) {
            $this->assertFalse(Schema::hasTable($table), "{$table} is dropped");
        }
        // The referencing table and its values stay; only the old link is gone.
        $this->assertSame(
            [['assignment_submission_id' => $submission, 'class_assignment_id' => $assignment, 'note' => 'Checked by hand']],
            DB::table('legacy_assignment_notes')->get(['assignment_submission_id', 'class_assignment_id', 'note'])->map(fn ($row) => (array) $row)->all()
        );
        $this->assertSame('9.00', AssessmentSubmission::where('legacy_assignment_submission_id', $submission)->value('score'));
    }

    public function test_a_merge_that_stopped_during_the_table_drops_is_finished_on_the_next_run(): void
    {
        $item = $this->libraryItem('Loops Worksheet', true);
        $assignment = $this->classAssignment($item['id'], 'Interrupted Homework', 'published');
        $submission = $this->submission($assignment, $this->ana->id, ['status' => 'graded', 'score' => 8, 'feedback' => 'Kept', 'submitted_at' => now(), 'graded_at' => now()]);
        $this->answer($submission, $item['mcq'], ['selected_option_id' => $item['correct'], 'is_correct' => true, 'points_awarded' => 5]);

        // The state an interrupted run leaves: everything converted, the
        // first table dropped, the rest still there, nothing recorded.
        (new AssignmentToAssessmentMerge())->convert();
        Schema::drop('assignment_submission_answers');
        $converted = fn (): array => collect(['assessments', 'assessment_questions', 'assessment_question_options', 'assessment_submissions', 'assessment_answers', 'question_bank_items', 'question_bank_options'])
            ->mapWithKeys(fn (string $table) => [$table => DB::table($table)->count()])
            ->all();
        $before = $converted();

        $merge = require database_path(self::MERGE_MIGRATION);
        $merge->up();

        foreach (self::LEGACY_TABLES as $table) {
            $this->assertFalse(Schema::hasTable($table), "{$table} is dropped");
        }
        $this->assertSame($before, $converted(), 'Nothing is converted twice.');
        $converted = AssessmentSubmission::where('legacy_assignment_submission_id', $submission)->firstOrFail();
        $this->assertSame('8.00', $converted->score);
        $this->assertSame('Kept', $converted->feedback);
        $this->assertSame(1, DB::table('assessment_answers')->where('assessment_submission_id', $converted->id)->count());
    }

    // ── Helpers ────────────────────────────────────────────────────────

    private function user(string $name, int $role, ?int $institutionId = null): User
    {
        return User::create([
            'name' => $name,
            'email' => Str::slug($name) . '-' . Str::lower(Str::random(6)) . '@example.test',
            'password' => 'not-used-here-123',
            'role' => $role,
            'status' => 'active',
            'institution_id' => $institutionId,
        ]);
    }

    /** @return array{id: int, mcq: int, correct: int, wrong: int, blank: int} */
    private function libraryItem(string $title, bool $active): array
    {
        $code = 'MG-' . Str::upper(Str::random(6));
        $id = (int) DB::table('assignment_library_items')->insertGetId([
            'module_no' => 3, 'assignment_code' => $code, 'title' => $title, 'topic_title' => 'Loops',
            'year_level' => 'First Year', 'assignment_type' => 'mixed', 'version_code' => $code . '-V1',
            'instructions' => 'Library instructions', 'time_limit_minutes' => 15, 'total_points' => 10,
            'is_active' => $active, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $mcq = (int) DB::table('assignment_questions')->insertGetId([
            'assignment_library_item_id' => $id, 'question_type' => 'mcq', 'question_text' => $active ? 'Which loop prints 0, 1, 2?' : 'Retired question',
            'points' => 5, 'order_index' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $correct = (int) DB::table('assignment_question_options')->insertGetId(['assignment_question_id' => $mcq, 'option_text' => 'range(3)', 'is_correct' => true, 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $wrong = (int) DB::table('assignment_question_options')->insertGetId(['assignment_question_id' => $mcq, 'option_text' => 'print(3)', 'is_correct' => false, 'order_index' => 2, 'created_at' => now(), 'updated_at' => now()]);
        $blank = (int) DB::table('assignment_questions')->insertGetId([
            'assignment_library_item_id' => $id, 'question_type' => 'fill_blank', 'question_text' => $active ? 'Name the loop that repeats over a sequence.' : 'Retired blank',
            'points' => 5, 'order_index' => 2, 'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach (['for', 'For loop'] as $answer) {
            DB::table('assignment_blank_answers')->insert(['assignment_question_id' => $blank, 'answer_text' => $answer, 'is_case_sensitive' => false, 'created_at' => now(), 'updated_at' => now()]);
        }

        return ['id' => $id, 'mcq' => $mcq, 'correct' => $correct, 'wrong' => $wrong, 'blank' => $blank];
    }

    /** @param array<string, mixed> $overrides */
    private function classAssignment(int $itemId, string $title, string $status, array $overrides = []): int
    {
        return (int) DB::table('class_assignments')->insertGetId(array_merge([
            'class_id' => $this->classId, 'assignment_library_item_id' => $itemId, 'assigned_by' => $this->instructor->id,
            'title' => $title, 'max_attempts' => 1, 'status' => $status,
            'assigned_at' => $status === 'draft' ? null : now()->subDay(),
            'created_at' => now()->subDays(2), 'updated_at' => now(),
        ], $overrides));
    }

    /** @param array<string, mixed> $overrides */
    private function submission(int $assignmentId, int $studentId, array $overrides): int
    {
        return (int) DB::table('assignment_submissions')->insertGetId(array_merge([
            'class_assignment_id' => $assignmentId, 'student_id' => $studentId, 'attempt_no' => 1,
            'status' => 'in_progress', 'score' => 0, 'total_points' => 10, 'started_at' => now()->subHour(),
            'anti_cheat_session_id' => Str::random(64), 'created_at' => now()->subHour(), 'updated_at' => now(),
        ], $overrides));
    }

    /** @param array<string, mixed> $values */
    private function answer(int $submissionId, int $questionId, array $values): int
    {
        return (int) DB::table('assignment_submission_answers')->insertGetId(array_merge([
            'assignment_submission_id' => $submissionId, 'assignment_question_id' => $questionId,
            'is_correct' => false, 'points_awarded' => 0, 'created_at' => now(), 'updated_at' => now(),
        ], $values));
    }

    /** @return array<string, mixed> */
    private function sideEffects(): array
    {
        return [
            'xp' => DB::table('users')->orderBy('id')->pluck('xp')->all(),
            'achievements' => DB::table('user_achievements')->count(),
            'missions' => DB::table('student_mission_progress')->count(),
            'notifications' => DB::table('notifications')->count(),
        ];
    }

    /** @return array<string, int> */
    private function counts(): array
    {
        return collect(['assessments', 'assessment_questions', 'assessment_question_options', 'assessment_submissions', 'assessment_answers', 'question_bank_items', 'question_bank_options', 'anti_cheat_events', 'legacy_assignment_archive'])
            ->mapWithKeys(fn (string $table) => [$table => DB::table($table)->count()])
            ->all();
    }
}
