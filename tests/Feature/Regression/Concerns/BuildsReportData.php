<?php

namespace Tests\Feature\Regression\Concerns;

use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One small school for the Updates 8 report and Class Analytics tests.
 *
 * Instructor "Ana" teaches "Data Science" with three students. The
 * "worksheets" are homework-purpose assessments (assignments were merged into
 * assessments in DataSensei Updates 11); "Midterm Quiz" has no purpose.
 *   Sam     finished both class modules, submitted both past-due worksheets
 *           on time (8/10), Midterm 9/10, challenge 8/10, solved both
 *           coding problems (60s + 90s)
 *   Lia     opened one module, one worksheet late (6/10) and one missing,
 *           Midterm 4/10 then 5/10, challenge 5/10, failed a coding
 *           problem three times
 *   Tom     nothing at all: both past-due worksheets and the Midterm missing
 * Instructor "Ben" teaches "Statistics" with "Zoe", who must never appear in
 * Ana's reports.
 */
trait BuildsReportData
{
    protected User $admin;

    protected User $ana;

    protected User $ben;

    protected User $sam;

    protected User $lia;

    protected User $tom;

    protected User $zoe;

    protected ClassRoom $dataScience;

    protected ClassRoom $statistics;

    /** @var array<string, int> */
    protected array $ids = [];

    protected function buildReportData(): void
    {
        $institution = Institution::create(['name' => 'Report School', 'email' => 'reports-'.Str::lower(Str::random(6)).'@school.test', 'status' => 'active']);
        $person = fn (string $name, int $role, array $extra = []) => $this->roleUser($role, array_merge([
            'name' => $name,
            'email' => Str::lower($name).'-'.Str::lower(Str::random(5)).'@school.test',
            'institution_id' => $role === User::ROLE_INSTRUCTOR ? $institution->id : null,
        ], $extra));

        $this->admin = $person('Adele Admin', User::ROLE_ADMIN);
        $this->ana = $person('Ana Instructor', User::ROLE_INSTRUCTOR);
        $this->ben = $person('Ben Instructor', User::ROLE_INSTRUCTOR);
        $this->sam = $person('Sam Student', User::ROLE_USER);
        $this->lia = $person('Lia Student', User::ROLE_USER);
        $this->tom = $person('Tom Student', User::ROLE_USER, ['status' => 'disabled']);
        $this->zoe = $person('Zoe Student', User::ROLE_USER);

        $this->dataScience = ClassRoom::create(['instructor_id' => $this->ana->id, 'institution_id' => $institution->id, 'name' => 'Data Science', 'section' => 'DS 4A', 'is_archived' => false]);
        $this->statistics = ClassRoom::create(['instructor_id' => $this->ben->id, 'institution_id' => $institution->id, 'name' => 'Statistics', 'section' => 'ST 2B', 'is_archived' => false]);
        foreach ([$this->sam, $this->lia, $this->tom] as $student) {
            DB::table('class_student')->insert(['class_id' => $this->dataScience->id, 'student_id' => $student->id, 'enrolled_at' => now()->subDays(20), 'created_at' => now(), 'updated_at' => now()]);
        }
        DB::table('class_student')->insert(['class_id' => $this->statistics->id, 'student_id' => $this->zoe->id, 'enrolled_at' => now()->subDays(20), 'created_at' => now(), 'updated_at' => now()]);

        $this->buildModules();
        $this->buildClassWork();
        $this->buildChallenges();
        $this->buildGamification();
    }

    private function buildModules(): void
    {
        $module = fn (int $no, string $title) => DB::table('module_library_items')->insertGetId([
            'module_no' => $no, 'module_code' => 'RPT-'.$no.'-'.Str::upper(Str::random(4)), 'title' => $title, 'year_level' => 'Year 1',
            'version_no' => 1, 'version_name' => 'Version 1', 'version_code' => 'V1', 'estimated_minutes' => 30,
            'content_sections' => '[]', 'mcq_questions' => '[]', 'sort_order' => $no, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->ids['m1'] = $module(1, 'Python Basics');
        $this->ids['m2'] = $module(2, 'Pandas Basics');
        $this->ids['m3'] = $module(3, 'Unassigned Module');
        foreach (['m1', 'm2'] as $key) {
            DB::table('class_module_assignments')->insert(['class_id' => $this->dataScience->id, 'module_library_item_id' => $this->ids[$key], 'assigned_by' => $this->ana->id, 'status' => 'active', 'assigned_at' => now()->subDays(10), 'created_at' => now()->subDays(10), 'updated_at' => now()]);
        }
        DB::table('class_module_assignments')->insert(['class_id' => $this->statistics->id, 'module_library_item_id' => $this->ids['m1'], 'assigned_by' => $this->ben->id, 'status' => 'active', 'assigned_at' => now()->subDays(10), 'created_at' => now(), 'updated_at' => now()]);

        foreach (['m1', 'm2'] as $key) {
            DB::table('module_library_progress')->insert(['user_id' => $this->sam->id, 'module_library_item_id' => $this->ids[$key], 'class_id' => $this->dataScience->id, 'opened_at' => now()->subDays(5), 'last_opened_at' => now()->subDays(2), 'completed_at' => now()->subDays(2), 'created_at' => now(), 'updated_at' => now()]);
        }
        DB::table('module_library_progress')->insert(['user_id' => $this->lia->id, 'module_library_item_id' => $this->ids['m1'], 'class_id' => $this->dataScience->id, 'opened_at' => now()->subDays(4), 'last_opened_at' => now()->subDays(4), 'created_at' => now(), 'updated_at' => now()]);

        // A public DataSensei Module: Sam finished it, Lia opened it.
        $this->ids['public'] = DB::table('modules')->insertGetId(['title' => 'Intro to Python', 'description' => 'Basics.', 'order_index' => 1, 'year_level' => 'Year 1', 'xp_reward' => 100, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()]);
        $lesson = DB::table('lessons')->insertGetId(['module_id' => $this->ids['public'], 'title' => 'Hello', 'content' => '<p>Hi</p>', 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('module_user')->insert(['user_id' => $this->sam->id, 'module_id' => $this->ids['public'], 'is_unlocked' => true, 'is_completed' => true, 'opened_at' => now()->subDays(3), 'created_at' => now(), 'updated_at' => now()]);
        DB::table('module_user')->insert(['user_id' => $this->lia->id, 'module_id' => $this->ids['public'], 'is_unlocked' => true, 'is_completed' => false, 'opened_at' => now()->subDay(), 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lesson_user')->insert(['user_id' => $this->sam->id, 'lesson_id' => $lesson, 'is_completed' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function buildClassWork(): void
    {
        // The worksheets were class assignments before DataSensei Updates 11
        // merged assignments into assessments; they are now homework-purpose
        // assessments, exactly what the merge converts an assignment into.
        $homework = fn (string $title, $due, string $class = 'dataScience') => DB::table('assessments')->insertGetId([
            'class_id' => $this->{$class}->id, 'created_by' => $this->{$class === 'dataScience' ? 'ana' : 'ben'}->id, 'title' => $title, 'topic_title' => 'Python',
            'purpose' => 'homework', 'status' => 'published', 'total_items' => 1, 'total_points' => 10, 'max_attempts' => 2,
            'available_at' => now()->subDays(15), 'due_at' => $due, 'published_at' => now()->subDays(15), 'created_at' => now()->subDays(15), 'updated_at' => now(),
        ]);
        $this->ids['a1'] = $homework('Loops Worksheet', now()->subDays(3));
        $this->ids['a2'] = $homework('Functions Worksheet', now()->subDays(2));
        $this->ids['a3'] = $homework('Upcoming Worksheet', now()->addDays(5));
        $this->ids['a_other'] = $homework('Other Class Worksheet', now()->subDay(), 'statistics');

        $submit = fn (int $assessmentId, User $student, string $status, int $score, $submittedAt, int $attempt = 1) => DB::table('assessment_submissions')->insert([
            'assessment_id' => $assessmentId, 'student_id' => $student->id, 'attempt_no' => $attempt, 'status' => $status, 'score' => $score, 'total_points' => 10,
            'started_at' => $submittedAt, 'submitted_at' => $submittedAt, 'graded_at' => $submittedAt, 'created_at' => $submittedAt, 'updated_at' => $submittedAt,
        ]);
        $submit($this->ids['a1'], $this->sam, 'graded', 8, now()->subDays(4));
        $submit($this->ids['a1'], $this->lia, 'late', 6, now()->subDays(1));
        $submit($this->ids['a2'], $this->sam, 'graded', 8, now()->subDays(3));
        $submit($this->ids['a_other'], $this->zoe, 'graded', 10, now()->subDays(2));

        $tos = DB::table('table_of_specifications')->insertGetId(['class_id' => $this->dataScience->id, 'module_no' => 1, 'title' => 'TOS', 'status' => 'final', 'created_by' => $this->ana->id, 'created_at' => now(), 'updated_at' => now()]);
        $this->ids['x1'] = DB::table('assessments')->insertGetId([
            'table_of_specification_id' => $tos, 'class_id' => $this->dataScience->id, 'created_by' => $this->ana->id, 'title' => 'Midterm Quiz', 'status' => 'published',
            'total_items' => 10, 'total_points' => 10, 'max_attempts' => 2, 'due_at' => now()->subDay(), 'published_at' => now()->subDays(10), 'created_at' => now()->subDays(10), 'updated_at' => now(),
        ]);
        $attempt = fn (User $student, int $no, int $score, $at) => DB::table('assessment_submissions')->insert([
            'assessment_id' => $this->ids['x1'], 'student_id' => $student->id, 'attempt_no' => $no, 'status' => 'graded', 'score' => $score, 'total_points' => 10,
            'started_at' => $at, 'submitted_at' => $at, 'graded_at' => $at, 'created_at' => $at, 'updated_at' => $at,
        ]);
        $attempt($this->sam, 1, 9, now()->subDays(3));
        $attempt($this->lia, 1, 4, now()->subDays(3));
        $attempt($this->lia, 2, 5, now()->subDays(2));
    }

    private function buildChallenges(): void
    {
        $category = DB::table('challenge_categories')->insertGetId(['name' => 'University Student', 'slug' => 'university-student', 'target_audience' => 'Classes', 'description' => 'Class level', 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $challenge = fn (string $title, bool $coding) => DB::table('challenges')->insertGetId([
            'challenge_category_id' => $category, 'title' => $title, 'description' => $title, 'order_index' => 1, 'is_coding_challenge' => $coding ? 1 : 0,
            'content_code' => 'RPT-'.Str::upper(Str::random(6)), 'version_code' => 'V1', 'is_active' => true, 'created_by' => $this->ana->id, 'visibility' => 'instructor', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->ids['c1'] = $challenge('Loops Quiz Challenge', false);
        $this->ids['k1'] = $challenge('Loops Coding Challenge', true);
        foreach (['c1', 'k1'] as $key) {
            DB::table('class_challenge_assignments')->insert(['class_id' => $this->dataScience->id, 'challenge_id' => $this->ids[$key], 'assigned_by' => $this->ana->id, 'status' => 'published', 'due_at' => now()->addDays(3), 'created_at' => now()->subDays(6), 'updated_at' => now()]);
        }

        $try = fn (User $student, int $score, $at) => DB::table('challenge_attempts')->insert([
            'user_id' => $student->id, 'challenge_id' => $this->ids['c1'], 'attempt_no' => 1, 'mode' => 'practice', 'status' => 'submitted', 'started_at' => $at, 'submitted_at' => $at,
            'time_taken_seconds' => 120, 'score' => $score, 'total_questions' => 10, 'xp_awarded' => 0, 'created_at' => $at, 'updated_at' => $at,
        ]);
        $try($this->sam, 8, now()->subDays(2));
        $try($this->lia, 5, now()->subDays(2));

        $question = fn (string $title, int $order) => DB::table('coding_questions')->insertGetId(['challenge_id' => $this->ids['k1'], 'title' => $title, 'problem_description' => $title, 'language' => 'python', 'order_index' => $order, 'created_at' => now(), 'updated_at' => now()]);
        $this->ids['q1'] = $question('Sum a list', 1);
        $this->ids['q2'] = $question('Count words', 2);
        $run = fn (User $student, int $questionId, string $status, int $passed, int $time, $at) => DB::table('coding_submissions')->insert([
            'user_id' => $student->id, 'coding_question_id' => $questionId, 'code' => 'print(1)', 'language' => 'python', 'status' => $status,
            'tests_passed' => $passed, 'tests_total' => 4, 'xp_earned' => $status === 'passed' ? 20 : 0, 'time_taken_seconds' => $time, 'voided' => false, 'created_at' => $at, 'updated_at' => $at,
        ]);
        $run($this->sam, $this->ids['q1'], 'passed', 4, 60, now()->subDays(1));
        $run($this->sam, $this->ids['q2'], 'passed', 4, 90, now()->subDays(1));
        foreach ([1, 2, 3] as $n) {
            $run($this->lia, $this->ids['q1'], 'failed', 1, 30, now()->subHours(10 - $n));
        }
    }

    private function buildGamification(): void
    {
        $achievement = DB::table('achievement_definitions')->insertGetId(['achievement_key' => 'first_run', 'name' => 'First Code Run', 'description' => 'Run code.', 'icon' => 'FCR', 'badge_color' => 'blue', 'xp_reward' => 25, 'criteria_type' => 'code_runs', 'criteria_value' => 1, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('user_achievements')->insert(['user_id' => $this->sam->id, 'achievement_definition_id' => $achievement, 'unlocked_at' => now()->subDay(), 'created_at' => now(), 'updated_at' => now()]);
        $mission = fn (string $key, string $title, string $period, int $xp) => DB::table('mission_definitions')->insertGetId(['mission_key' => $key, 'title' => $title, 'description' => $title, 'period_type' => $period, 'target_type' => 'code_runs', 'target_count' => 1, 'xp_reward' => $xp, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $daily = $mission('daily_run', 'Run Code Today', 'daily', 20);
        $weekly = $mission('weekly_run', 'Weekly Practice', 'weekly', 100);
        DB::table('student_mission_progress')->insert(['user_id' => $this->sam->id, 'mission_definition_id' => $daily, 'period_start' => now()->toDateString(), 'progress_count' => 1, 'is_completed' => true, 'completed_at' => now()->subHour(), 'xp_awarded' => 20, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('student_mission_progress')->insert(['user_id' => $this->lia->id, 'mission_definition_id' => $daily, 'period_start' => now()->toDateString(), 'progress_count' => 1, 'is_completed' => true, 'completed_at' => now()->subHour(), 'xp_awarded' => 20, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('student_mission_progress')->insert(['user_id' => $this->sam->id, 'mission_definition_id' => $weekly, 'period_start' => now()->startOfWeek()->toDateString(), 'progress_count' => 1, 'is_completed' => true, 'completed_at' => now()->subHour(), 'xp_awarded' => 100, 'created_at' => now(), 'updated_at' => now()]);
    }
}
