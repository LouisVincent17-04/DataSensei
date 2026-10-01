<?php

namespace Tests\Feature\Regression;

use App\Models\ModuleLibraryProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * DataSensei Updates 8: the instructor's six reports and the redesigned
 * Class Analytics (the Skills Competency Matrix is gone). An instructor only
 * ever sees their own classes and students; all figures are rule based.
 */
class Updates8InstructorReportsAndAnalyticsTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();
    }

    public function test_the_reports_menu_lists_the_six_instructor_reports(): void
    {
        $html = $this->authenticateAs($this->ana)->get(route('instructor.reports.index'))->assertOk()->getContent();

        foreach (['Class Performance', 'Student Progress', 'Assignments &amp; Assessments', 'Challenges &amp; Coding Challenges', 'Module Assignments', 'Submissions'] as $title) {
            $this->assertStringContainsString('>'.$title.'</a>', $html);
        }
        $this->assertSame(6, substr_count($html, 'class="rp-nav-link'));
        $this->assertStringContainsString('Data Science, DS 4A', $html);
        $this->assertStringNotContainsString('Statistics', strip_tags(substr($html, strpos($html, '<div class="rp">'))));
    }

    public function test_student_progress_shows_each_students_class_progress(): void
    {
        $html = $this->authenticateAs($this->ana)->get(route('instructor.reports.show', ['report' => 'students']))->assertOk()->getContent();

        $sam = $this->row($html, 'Sam Student');
        $this->assertStringContainsString('2 of 2 completed, 2 started', $sam);
        $this->assertStringContainsString('2 of 3 submitted', $sam);
        $this->assertStringContainsString('1 of 1 completed, average 90%', $sam);
        $this->assertStringContainsString('1 attempt, 1 of 1 passed', $sam);
        $this->assertStringContainsString('2 submissions, 1 of 1 completed', $sam);

        $tom = $this->row($html, 'Tom Student');
        $this->assertStringContainsString('0 of 2 completed, 0 started', $tom);
        $this->assertStringContainsString('0 of 3 submitted, 2 missing', $tom);
        $this->assertStringNotContainsString('Zoe Student', $html);
        $this->assertStringContainsString(route('instructor.analytics.student', ['class' => $this->dataScience->id, 'student' => $this->sam->id]), $sam);
    }

    public function test_assignment_and_assessment_report_per_student(): void
    {
        $this->authenticateAs($this->ana);
        $html = $this->get(route('instructor.reports.show', ['report' => 'assignments']))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('#Lia Student.*?Loops Worksheet.*?Submitted late.*?Late.*?6 / 10 \(60%\)#s', $html);
        $this->assertMatchesRegularExpression('#Tom Student.*?Functions Worksheet.*?Missing#s', $html);
        $this->assertMatchesRegularExpression('#Sam Student.*?Midterm Quiz.*?1.*?9 / 10 \(90%\).*?90%.*?Passed.*?Completed#s', $html);
        $this->assertMatchesRegularExpression('#Lia Student.*?Midterm Quiz.*?2.*?5 / 10 \(50%\).*?50%.*?Failed#s', $html);

        $missing = $this->get(route('instructor.reports.show', ['report' => 'assignments', 'status' => 'missing']))->getContent();
        $this->assertSame(3, preg_match_all('#<td[^>]*>\s*Missing\s*</td>#', $missing), 'Tom misses two, Lia one; only past-due work can be missing.');
        $this->assertStringNotContainsString('Upcoming Worksheet', $missing);

        $late = $this->get(route('instructor.reports.show', ['report' => 'assignments', 'status' => 'late']))->getContent();
        $this->assertStringContainsString('Submitted late', $late);
        $this->assertStringNotContainsString('Sam Student', strip_tags(substr($late, strpos($late, 'id="assignments"'), 3000)));
    }

    public function test_challenge_report_and_module_assignments_and_submissions(): void
    {
        $this->authenticateAs($this->ana);

        $challenges = $this->get(route('instructor.reports.show', ['report' => 'challenges']))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('#Sam Student.*?Loops Quiz Challenge.*?8 / 10 \(80%\).*?Completed#s', $challenges);
        $this->assertMatchesRegularExpression('#Sam Student.*?Loops Coding Challenge.*?2.*?100%.*?8 passed, 0 failed.*?Completed.*?2m 30s#s', $challenges);
        $this->assertMatchesRegularExpression('#Lia Student.*?Loops Coding Challenge.*?3.*?12.5%.*?1 passed, 3 failed.*?0 of 2 problems solved#s', $challenges);

        $modules = $this->get(route('instructor.reports.show', ['report' => 'modules']))->assertOk()->getContent();
        $row = $this->row($modules, 'Data Science');
        $this->assertStringContainsString('Python Basics (Version 1)', $row);
        $this->assertStringContainsString('2 of 3', $row);
        $this->assertStringContainsString('1 of 3', $row);
        $this->assertStringContainsString('33.3%', $row);

        $submissions = $this->get(route('instructor.reports.show', ['report' => 'submissions']))->assertOk()->getContent();
        foreach (['Assignment', 'Assessment', 'Challenge', 'Coding challenge'] as $type) {
            $this->assertStringContainsString('>'.$type.'<', str_replace(["\n", '  '], '', $submissions));
        }
        $this->assertStringNotContainsString('Other Class Worksheet', $submissions);
        $coding = $this->get(route('instructor.reports.show', ['report' => 'submissions', 'type' => 'coding', 'status' => 'failed']))->getContent();
        $this->assertSame(3, substr_count($coding, 'Loops Coding Challenge: Sum a list'));
    }

    public function test_instructors_only_reach_their_own_classes(): void
    {
        $this->authenticateAs($this->ana);
        $this->get(route('instructor.reports.show', ['report' => 'students', 'class_id' => $this->statistics->id]))->assertNotFound();
        $this->get(route('instructor.reports.export', ['report' => 'submissions', 'format' => 'csv', 'class_id' => $this->statistics->id]))->assertNotFound();
        $this->get(route('instructor.analytics.index', ['class_id' => $this->statistics->id]))->assertNotFound();
        $this->get(route('instructor.analytics.student', ['class' => $this->statistics->id, 'student' => $this->zoe->id]))->assertNotFound();
        $this->get(route('instructor.analytics.student', ['class' => $this->dataScience->id, 'student' => $this->zoe->id]))->assertNotFound();

        $csv = $this->get(route('instructor.reports.export', ['report' => 'students', 'format' => 'csv']))->assertOk()->streamedContent();
        $this->assertStringContainsString('Sam Student', $csv);
        $this->assertStringNotContainsString('Zoe Student', $csv);

        $this->authenticateAs($this->sam);
        $this->get(route('instructor.reports.index'))->assertForbidden();
        $this->get(route('instructor.reports.export', ['report' => 'classes', 'format' => 'pdf']))->assertForbidden();
        $this->get(route('instructor.analytics.index'))->assertForbidden();
        $this->get(route('instructor.analytics.student', ['class' => $this->dataScience->id, 'student' => $this->sam->id]))->assertForbidden();

        $this->authenticateAs($this->admin);
        $this->get(route('instructor.reports.index'))->assertForbidden();
    }

    public function test_class_analytics_overview_and_students_needing_attention(): void
    {
        $this->authenticateAs($this->ana);

        // Pick a class first.
        $this->get(route('instructor.analytics.index'))->assertOk()->assertSee('Choose a class')->assertSee('Data Science');

        $html = $this->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id]))->assertOk()->getContent();
        foreach (['Overview', 'Students', 'Modules', 'Assignments', 'Assessments', 'Challenges', 'Coding Challenges'] as $tab) {
            $this->assertStringContainsString('>'.$tab.'</a>', $html);
        }
        $tiles = $this->tiles($html);
        $this->assertSame('3', $tiles['Enrolled students']);
        $this->assertSame('2', $tiles['Active students']);
        $this->assertSame('2', $tiles['Assigned modules']);
        $this->assertSame('33.3%', $tiles['Module completion']);
        $this->assertSame('70%', $tiles['Assessment performance']);

        // Rule-based attention list: Lia (scores, coding) and Tom (missing work, modules); not Sam.
        $attention = substr($html, strpos($html, 'id="attention"'));
        $this->assertStringContainsString('Tom Student', $attention);
        $this->assertStringContainsString('Missing 2 assignments', $attention);
        $this->assertStringContainsString('Completed 0 of 2 modules', $attention);
        $this->assertStringContainsString('Lia Student', $attention);
        $this->assertStringContainsString('Assessment average 50%', $attention);
        $this->assertStringContainsString('Challenge average 50%', $attention);
        $this->assertStringContainsString('Repeated failures on 1 coding problem', $attention);
        $this->assertStringNotContainsString('Sam Student', $attention);

        foreach (['Competency', 'competency', 'Mastery', 'mastery', 'Rank', 'Leaderboard', 'ILO'] as $gone) {
            $this->assertStringNotContainsString($gone, strip_tags(substr($html, strpos($html, '<div class="rp">'))));
        }
    }

    public function test_class_analytics_tabs_filters_and_student_detail(): void
    {
        $this->authenticateAs($this->ana);
        $class = ['class_id' => $this->dataScience->id];

        $students = $this->get(route('instructor.analytics.index', $class + ['tab' => 'students', 'status' => 'attention']))->assertOk()->getContent();
        $this->assertStringContainsString('Tom Student', $students);
        $this->assertStringNotContainsString('>Sam Student<', $students);

        $modules = $this->get(route('instructor.analytics.index', $class + ['tab' => 'modules', 'module_id' => $this->ids['m1']]))->assertOk()->getContent();
        $this->assertStringContainsString('Python Basics: students', $modules);
        $this->assertMatchesRegularExpression('#Lia Student\s*</td>\s*<td[^>]*>\s*Started#', $modules);

        $assignments = $this->get(route('instructor.analytics.index', $class + ['tab' => 'assignments']))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('#Loops Worksheet.*?2 of 3 \(66.7%\).*?>\s*1\s*<.*?>\s*1\s*<.*?>\s*1\s*<.*?70%#s', $assignments);
        $this->get(route('instructor.analytics.index', $class + ['tab' => 'assignments', 'from' => now()->addDays(1)->toDateString(), 'to' => now()->addDays(10)->toDateString()]))
            ->assertSee('Upcoming Worksheet')->assertDontSee('Loops Worksheet');

        $this->get(route('instructor.analytics.index', $class + ['tab' => 'assessments']))->assertOk()->assertSee('Midterm Quiz');
        $this->get(route('instructor.analytics.index', $class + ['tab' => 'challenges']))->assertOk()->assertSee('1 of 3 (33.3%)');
        $this->get(route('instructor.analytics.index', $class + ['tab' => 'coding']))->assertOk()->assertSee('Loops Coding Challenge');

        $detail = $this->get(route('instructor.analytics.student', ['class' => $this->dataScience->id, 'student' => $this->lia->id]))->assertOk();
        $detail->assertSee('Lia Student')->assertSee('May need attention')->assertSee('Submitted late')->assertSee('Recent learning activity');
        $detail->assertSee('1 passed, 3 failed');

        $onlyCoding = $this->get(route('instructor.analytics.student', ['class' => $this->dataScience->id, 'student' => $this->lia->id, 'type' => 'coding']))->getContent();
        $activity = substr($onlyCoding, strpos($onlyCoding, 'id="activity"'));
        $this->assertStringContainsString('Coding challenge', $activity);
        $this->assertStringNotContainsString('>Assessment<', $activity);
    }

    public function test_the_skills_competency_matrix_is_gone_from_the_instructor_side(): void
    {
        $this->assertFalse(Route::has('instructor.competencies.index'));
        $this->assertFalse(Route::has('instructor.competencies.refresh'));
        $this->assertFalse(Route::has('instructor.analytics.refresh'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/InstructorCompetencyController.php'));
        $this->assertFileDoesNotExist(resource_path('views/instructor/competencies/index.blade.php'));

        $this->authenticateAs($this->ana);
        $this->get('/instructor/competencies')->assertNotFound();
        $dashboard = $this->get(route('instructor.dashboard'))->assertOk()->getContent();
        $this->assertStringNotContainsString('Skills Competency Matrix', $dashboard);
        $this->assertStringNotContainsString('Competency matrix', $dashboard);
        $this->assertStringNotContainsString('/instructor/competencies', $dashboard);
        $this->assertStringContainsString('Class Analytics', $dashboard);
    }

    public function test_class_module_progress_is_recorded_for_the_student(): void
    {
        $this->authenticateAs($this->tom);
        DB::table('users')->where('id', $this->tom->id)->update(['status' => 'active']);
        $this->authenticateAs($this->tom->fresh());

        $this->get(route('student.modules.show', ['module' => $this->ids['m2'], 'class' => $this->dataScience->id]))
            ->assertOk()
            ->assertSee('Mark module as complete');
        $progress = ModuleLibraryProgress::where('user_id', $this->tom->id)->where('module_library_item_id', $this->ids['m2'])->firstOrFail();
        $this->assertNotNull($progress->opened_at);
        $this->assertNull($progress->completed_at);

        $this->post(route('student.modules.complete', ['module' => $this->ids['m2'], 'class' => $this->dataScience->id]))
            ->assertRedirect(route('student.modules.show', ['module' => $this->ids['m2'], 'class' => $this->dataScience->id]).'#module-completion');
        $this->assertNotNull($progress->fresh()->completed_at);
        $this->get(route('student.modules.show', ['module' => $this->ids['m2'], 'class' => $this->dataScience->id]))->assertSee('You marked this module as complete');
        $this->get(route('modules.index', ['source' => 'class-'.$this->dataScience->id]))->assertSee('You marked this module complete');

        // A module not given to the student's class cannot be completed.
        $this->post(route('student.modules.complete', ['module' => $this->ids['m3']]))->assertNotFound();
        $this->assertFalse(ModuleLibraryProgress::where('module_library_item_id', $this->ids['m3'])->exists());

        // Opening a DataSensei Module lesson records the first visit.
        // (The Modules page above already unlocked it, as the first module.)
        $this->assertNull(DB::table('module_user')->where('user_id', $this->tom->id)->where('module_id', $this->ids['public'])->value('opened_at'));
        $this->get(route('lesson.show', ['module' => $this->ids['public']]))->assertOk();
        $this->assertNotNull(DB::table('module_user')->where('user_id', $this->tom->id)->where('module_id', $this->ids['public'])->value('opened_at'));
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /** @return array<string, string> */
    private function tiles(string $html): array
    {
        preg_match_all('#<span class="rp-tile-label">(.*?)</span><strong class="rp-tile-value">(.*?)</strong>#s', $html, $matches, PREG_SET_ORDER);

        return collect($matches)->mapWithKeys(fn ($m) => [html_entity_decode(trim($m[1])) => html_entity_decode(trim($m[2]))])->all();
    }

    private function row(string $html, string $first): string
    {
        $this->assertSame(1, preg_match('#<tr>\s*<td[^>]*>\s*(?:<a[^>]*>)?\s*'.preg_quote(e($first), '#').'\s*(?:</a>)?\s*</td>(.*?)</tr>#s', $html, $match), 'Row not found: '.$first);

        return $match[0];
    }
}
