<?php

namespace Tests\Feature\Regression\Concerns;

use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Support\AntiCheatEventContract;
use App\Support\AuthSessionFingerprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Fixtures for the class-assignment workflow regression tests (DS-01..DS-07).
 * The using test runs the real migrations (RefreshDatabase), so the schema is
 * exactly what the application ships, including the new integrity columns.
 */
trait BuildsAssignmentWorkflow
{
    protected User $instructor;
    protected User $otherInstructor;
    protected User $student;
    protected User $otherStudent;
    protected int $classId;

    protected int $institutionId;

    protected function seedAssignmentActors(): void
    {
        // Instructors are only let in while their institution is active.
        $this->institutionId = (int) DB::table('institutions')->insertGetId([
            'name' => 'Workflow Institution',
            'slug' => 'workflow-institution-' . Str::lower(Str::random(6)),
            'email' => 'institution-' . Str::lower(Str::random(6)) . '@example.test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->instructor = $this->makeUser('Workflow Instructor', User::ROLE_INSTRUCTOR);
        $this->otherInstructor = $this->makeUser('Other Instructor', User::ROLE_INSTRUCTOR);
        $this->student = $this->makeUser('Workflow Student', User::ROLE_USER);
        $this->otherStudent = $this->makeUser('Other Student', User::ROLE_USER);

        $this->classId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'name' => 'Assignment Workflow Class',
            'class_code' => 'W' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([$this->student, $this->otherStudent] as $learner) {
            DB::table('class_student')->insert([
                'class_id' => $this->classId,
                'student_id' => $learner->id,
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function makeUser(string $name, int $role): User
    {
        return User::create([
            'name' => $name,
            'email' => Str::slug($name) . '-' . Str::lower(Str::random(8)) . '@example.test',
            'password' => 'TestPassword!123',
            'role' => $role,
            'status' => 'active',
            'institution_id' => $role === User::ROLE_INSTRUCTOR ? $this->institutionId : null,
        ]);
    }

    protected function actAs(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }

    /**
     * One 5-point MCQ and one 5-point fill-in-the-blank ("pandas").
     *
     * @return array{item: int, mcq: int, correct: int, wrong: int, blank: int}
     */
    protected function makeLibraryItem(int $timeLimitMinutes = 5, bool $active = true): array
    {
        $code = 'WF-' . Str::upper(Str::random(8));
        $itemId = (int) DB::table('assignment_library_items')->insertGetId([
            'module_no' => 1,
            'assignment_code' => $code,
            'title' => 'Workflow Assignment ' . $code,
            'topic_title' => 'Workflow Topic',
            'year_level' => 'First Year',
            'assignment_type' => 'mcq',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => $code . '-V1',
            'time_limit_minutes' => $timeLimitMinutes,
            'total_points' => 10,
            'is_active' => $active,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $mcqId = (int) DB::table('assignment_questions')->insertGetId([
            'assignment_library_item_id' => $itemId,
            'question_type' => 'mcq',
            'question_text' => 'Which option is correct?',
            'points' => 5,
            'order_index' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $correctId = (int) DB::table('assignment_question_options')->insertGetId([
            'assignment_question_id' => $mcqId,
            'option_text' => 'Correct option',
            'is_correct' => true,
            'order_index' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $wrongId = (int) DB::table('assignment_question_options')->insertGetId([
            'assignment_question_id' => $mcqId,
            'option_text' => 'Wrong option',
            'is_correct' => false,
            'order_index' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $blankId = (int) DB::table('assignment_questions')->insertGetId([
            'assignment_library_item_id' => $itemId,
            'question_type' => 'fill_blank',
            'question_text' => 'Which library provides DataFrame?',
            'points' => 5,
            'order_index' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('assignment_blank_answers')->insert([
            'assignment_question_id' => $blankId,
            'answer_text' => 'pandas',
            'is_case_sensitive' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['item' => $itemId, 'mcq' => $mcqId, 'correct' => $correctId, 'wrong' => $wrongId, 'blank' => $blankId];
    }

    protected function makeClassAssignment(int $libraryItemId, string $status = 'published', int $maxAttempts = 1): int
    {
        return (int) DB::table('class_assignments')->insertGetId([
            'class_id' => $this->classId,
            'assignment_library_item_id' => $libraryItemId,
            'assigned_by' => $this->instructor->id,
            'title' => 'Workflow Assignment',
            'max_attempts' => $maxAttempts,
            'status' => $status,
            'assigned_at' => $status === 'published' ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function makeAttempt(int $assignmentId, ?User $student = null, ?Carbon $startedAt = null, int $attemptNo = 1): AssignmentSubmission
    {
        return AssignmentSubmission::create([
            'class_assignment_id' => $assignmentId,
            'student_id' => ($student ?? $this->student)->id,
            'attempt_no' => $attemptNo,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => 10,
            'anti_cheat_session_id' => Str::random(64),
            'started_at' => $startedAt ?? now(),
        ]);
    }

    /** @param array<string, mixed> $overrides */
    protected function setPolicy(array $overrides = []): void
    {
        DB::table('anti_cheat_settings')->where('instructor_id', $this->instructor->id)->delete();
        DB::table('anti_cheat_settings')->insert(array_merge([
            'instructor_id' => $this->instructor->id,
            'class_id' => null,
            'assessment_type' => 'assignment',
            'enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    protected function recordEvent(AssignmentSubmission $submission, string $eventType, ?Carbon $at = null): void
    {
        DB::table('anti_cheat_events')->insert([
            'user_id' => $submission->student_id,
            'class_id' => $this->classId,
            'class_assignment_id' => $submission->class_assignment_id,
            'assignment_submission_id' => $submission->id,
            'assessment_type' => 'assignment',
            'event_type' => $eventType,
            'severity' => AntiCheatEventContract::severityFor($eventType),
            'attempt_session_id' => $submission->anti_cheat_session_id,
            'event_uuid' => (string) Str::uuid(),
            'occurred_at' => $at ?? now(),
            'created_at' => $at ?? now(),
            'updated_at' => $at ?? now(),
        ]);
    }

    /** Record $count logical focus losses, spaced outside the correlation window. */
    protected function recordFocusLosses(AssignmentSubmission $submission, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->recordEvent($submission, 'focus_loss', now()->copy()->subSeconds(200 - ($i * 10)));
        }
    }

    /** One achievement worth 50 XP and one 3-step mission: both observable. */
    protected function seedAssignmentRewards(): void
    {
        DB::table('student_mission_progress')->delete();
        DB::table('user_achievements')->delete();
        DB::table('mission_definitions')->delete();
        DB::table('achievement_definitions')->delete();

        DB::table('achievement_definitions')->insert([
            'achievement_key' => 'assignment_finisher',
            'name' => 'Assignment Finisher',
            'xp_reward' => 50,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('mission_definitions')->insert([
            'mission_key' => 'submit_three_assignments',
            'title' => 'Submit three assignments',
            'period_type' => 'weekly',
            'target_type' => 'submit_assignment',
            'target_count' => 3,
            'xp_reward' => 30,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function missionProgress(User $user): int
    {
        return (int) DB::table('student_mission_progress')->where('user_id', $user->id)->sum('progress_count');
    }
}
