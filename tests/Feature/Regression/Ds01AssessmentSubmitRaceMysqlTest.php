<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\AssessmentSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * DS-01 idempotency under real InnoDB row locking: several separate PHP
 * processes submit the SAME assessment attempt at the same moment.
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

            $answers = json_encode([
                (string) $questions[0]->id => (string) $correct->id,
                (string) $questions[1]->id => '0',
            ]);
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
                        $answers,
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

            $context = "round {$round}: " . json_encode($outcomes);

            // Every worker saw an in-progress attempt, so they really did race.
            foreach ($outcomes as $outcome) {
                $this->assertSame('in_progress', $outcome['status_seen_before'] ?? null, $context);
                $this->assertSame('response', $outcome['outcome'], $context);
                $this->assertStringContainsString('/result', (string) $outcome['location'], $context);
            }

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

    /** @return array{0: Assessment, 1: array<int, AssessmentQuestion>, 2: AssessmentSubmission, 3: User} */
    private function createAttempt(): array
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
        ]);

        return [$assessment, [$mcq->fresh('options'), $blank], $submission, $student];
    }
}
