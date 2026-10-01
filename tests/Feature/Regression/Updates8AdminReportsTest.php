<?php

namespace Tests\Feature\Regression;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * DataSensei Updates 8, task 1, admin side: the old "Reports & Moderation"
 * page is replaced by seven reports (Users, Modules, Classes, Assessments,
 * Challenges & Coding Challenges, Gamification, Audit Logs), each built from
 * saved records, filterable, printable and exportable as PDF and CSV, and
 * open to admins only. The Assessments report was "Assessments & Assignments"
 * until DataSensei Updates 11 merged assignments into assessments; converted
 * assignments are homework-purpose assessments in it now.
 */
class Updates8AdminReportsTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();
    }

    public function test_the_reports_menu_lists_exactly_the_seven_reports(): void
    {
        $html = $this->authenticateAs($this->admin)->get(route('admin.reports.index'))->assertOk()->getContent();

        foreach (['Users', 'Modules', 'Classes', 'Assessments', 'Challenges &amp; Coding Challenges', 'Gamification', 'Audit Logs'] as $title) {
            $this->assertStringContainsString('>'.$title.'</a>', $html);
        }
        $this->assertSame(7, substr_count($html, 'class="rp-nav-link'));
        foreach (['Reports &amp; Moderation', 'System Health', 'Assignment Anti-Cheat Events', 'Challenge Attempt Flags', 'Mastery', 'Rank', 'Assessments &amp; Assignments'] as $gone) {
            $this->assertStringNotContainsString($gone, $html);
        }
    }

    public function test_user_report_counts_roles_status_and_new_registrations(): void
    {
        DB::table('users')->where('id', $this->zoe->id)->update(['created_at' => now()->subDays(60)]);
        $this->authenticateAs($this->admin);

        $response = $this->get(route('admin.reports.show', ['report' => 'users']))->assertOk();
        $summary = $this->summary($response->getContent());
        $this->assertSame('7', $summary['Total users']);
        $this->assertSame('6', $summary['Active']);
        $this->assertSame('1', $summary['Inactive']);
        $this->assertSame('6', $summary['New in the last 30 days']);
        $response->assertSee('Users by role')->assertSee('Tom Student');

        $learners = $this->summary($this->get(route('admin.reports.show', ['report' => 'users', 'role' => 'learner', 'status' => 'active']))->getContent());
        $this->assertSame('3', $learners['Total users']);

        $this->get(route('admin.reports.show', ['report' => 'users', 'q' => 'Zoe']))->assertSee('Zoe Student')->assertDontSee('Sam Student');

        $range = $this->get(route('admin.reports.show', ['report' => 'users', 'from' => now()->subDays(70)->toDateString(), 'to' => now()->subDays(50)->toDateString()]));
        $this->assertSame('1', $this->summary($range->getContent())['New registrations']);
        $range->assertSee('Users registered in the selected range')->assertSee('Zoe Student')->assertDontSee('Sam Student');

        $this->from(route('admin.reports.show', ['report' => 'users']))
            ->get(route('admin.reports.show', ['report' => 'users', 'from' => '2026-09-10', 'to' => '2026-09-01']))
            ->assertSessionHasErrors('to');
    }

    public function test_module_report_shows_datasensei_and_class_modules_with_access_and_completion(): void
    {
        $html = $this->authenticateAs($this->admin)->get(route('admin.reports.show', ['report' => 'modules']))->assertOk()->getContent();

        $public = $this->row($html, 'Intro to Python');
        $this->assertStringContainsString('DataSensei Module', $public);
        $this->assertMatchesRegularExpression('#<td[^>]*>\s*2\s*</td>\s*<td[^>]*>\s*1\s*</td>\s*<td[^>]*>\s*50%#', $public, 'Two learners opened it, one completed it.');

        $class = $this->row($html, 'Python Basics');
        $this->assertStringContainsString('Class Module', $class);
        $this->assertMatchesRegularExpression('#<td[^>]*>\s*2\s*</td>\s*<td[^>]*>\s*2\s*</td>\s*<td[^>]*>\s*1\s*</td>#', $class, 'Two classes, two opened, one completed.');

        $this->get(route('admin.reports.show', ['report' => 'modules', 'type' => 'datasensei']))->assertSee('Intro to Python')->assertDontSee('Pandas Basics');
    }

    public function test_class_report_shows_instructor_enrolment_modules_and_participation(): void
    {
        $html = $this->authenticateAs($this->admin)->get(route('admin.reports.show', ['report' => 'classes']))->assertOk()->getContent();

        $row = $this->row($html, 'Data Science, DS 4A');
        $this->assertStringContainsString('Ana Instructor', $row);
        $this->assertStringContainsString('2 of 3 students (66.7%)', $row, 'Sam and Lia took part in the last 30 days, Tom did not.');
        $this->assertStringContainsString('Statistics, ST 2B', $html);
    }

    public function test_assessment_report_covers_quizzes_and_converted_homework(): void
    {
        $html = $this->authenticateAs($this->admin)->get(route('admin.reports.show', ['report' => 'assessments']))->assertOk()->getContent();

        $quiz = $this->row($html, 'Midterm Quiz');
        $this->assertMatchesRegularExpression('#<td data-label="Type"[^>]*>\s*Assessment\s*</td>#', $quiz, 'An assessment without a purpose is labelled Assessment.');
        // Three attempts (9, 4, 5 of 10): average 60%; best attempts Sam 90% pass, Lia 50% fail; 2 of 3 students.
        $this->assertMatchesRegularExpression('#>\s*3\s*</td>\s*<td[^>]*>\s*60%\s*</td>\s*<td[^>]*>\s*1\s*</td>\s*<td[^>]*>\s*1\s*</td>\s*<td[^>]*>\s*66.7% \(2 of 3\)#', $quiz);

        // Former assignments are homework-purpose assessments since Updates 11.
        $loops = $this->row($html, 'Loops Worksheet');
        $this->assertMatchesRegularExpression('#<td data-label="Type"[^>]*>\s*Homework\s*</td>#', $loops);
        // Two attempts (8 and 6 of 10): average 70%; Sam passes, Lia fails; 2 of 3 students completed it.
        $this->assertMatchesRegularExpression('#>\s*2\s*</td>\s*<td[^>]*>\s*70%\s*</td>\s*<td[^>]*>\s*1\s*</td>\s*<td[^>]*>\s*1\s*</td>\s*<td[^>]*>\s*66.7% \(2 of 3\)#', $loops);
        // Nobody has attempted the upcoming worksheet yet.
        $this->assertMatchesRegularExpression('#>\s*0\s*</td>\s*<td[^>]*>\s*—\s*</td>\s*<td[^>]*>\s*0\s*</td>\s*<td[^>]*>\s*0\s*</td>\s*<td[^>]*>\s*0% \(0 of 3\)#', $this->row($html, 'Upcoming Worksheet'));

        $summary = $this->summary($html);
        $this->assertSame('5', $summary['Assessments'], 'Midterm Quiz and the four worksheets.');
        $this->assertSame('1', $summary['Late submissions'], "Lia's Loops Worksheet, turned in after the due date.");

        $this->get(route('admin.reports.show', ['report' => 'assessments', 'class_id' => $this->statistics->id]))
            ->assertSee('Other Class Worksheet')->assertDontSee('Loops Worksheet');
    }

    public function test_challenge_report_combines_mcq_and_coding(): void
    {
        $html = $this->authenticateAs($this->admin)->get(route('admin.reports.show', ['report' => 'challenges']))->assertOk()->getContent();

        $quiz = $this->row($html, 'Loops Quiz Challenge');
        $this->assertStringContainsString('Instructor-built', $quiz);
        $this->assertStringContainsString('65%', $quiz, 'Average of 80% and 50%.');
        $this->assertStringContainsString('50% (1 of 2 learners)', $quiz);

        $coding = $this->row($html, 'Loops Coding Challenge');
        // 5 runs: 2 passed, 3 failed; average completion time of passing runs 75s.
        $this->assertMatchesRegularExpression('#>\s*5\s*</td>\s*<td[^>]*>\s*55%\s*</td>\s*<td[^>]*>\s*2\s*</td>\s*<td[^>]*>\s*3\s*</td>\s*<td[^>]*>\s*1m 15s#', $coding);
    }

    public function test_gamification_report_shows_exp_achievements_and_missions_only(): void
    {
        $html = $this->authenticateAs($this->admin)->get(route('admin.reports.show', ['report' => 'gamification']))->assertOk()->getContent();
        $summary = $this->summary($html);

        // Coding 40 + achievement 25 + missions 140.
        $this->assertSame('205', $summary['Total EXP distributed']);
        $this->assertSame('1', $summary['Achievements earned']);
        $this->assertSame('2', $summary['Daily mission completions']);
        $this->assertSame('1', $summary['Weekly mission completions']);
        $report = strip_tags(substr($html, strpos($html, '<div class="rp">')));
        foreach (['criteria', 'badge', 'first_run', 'daily_run', 'Code Mark'] as $technical) {
            $this->assertStringNotContainsStringIgnoringCase($technical, $report);
        }
    }

    public function test_exports_carry_every_row_and_the_filters(): void
    {
        $this->authenticateAs($this->admin);
        foreach (range(1, 20) as $n) {
            $this->roleUser(User::ROLE_USER, ['name' => 'Bulk Learner '.$n]);
        }

        $csv = $this->get(route('admin.reports.export', ['report' => 'users', 'format' => 'csv', 'role' => 'learner']));
        $csv->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=datasensei-admin-users-report-', (string) $csv->headers->get('Content-Disposition'));
        $body = $csv->streamedContent();
        $this->assertStringContainsString('"DataSensei report",Users', $body);
        $this->assertStringContainsString('Filter,"Role: Learner"', $body);
        $this->assertStringContainsString('Bulk Learner 20', $body, 'All rows, not one page.');
        $this->assertStringContainsString('"Bulk Learner 1",', $body);

        $pdf = $this->get(route('admin.reports.export', ['report' => 'challenges', 'format' => 'pdf']));
        $pdf->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $pdf->getContent());
        $this->assertStringContainsString('%%EOF', $pdf->getContent());

        $this->get(route('admin.reports.export', ['report' => 'assessments', 'format' => 'print']))
            ->assertOk()
            ->assertSee('Assessments Report', false)
            ->assertDontSee('Assignments Report', false)
            ->assertSee('window.print()', false)
            ->assertSee('Loops Worksheet');

        $this->get('/admin/reports/users/export/xlsx')->assertNotFound();
        $this->get('/admin/reports/ilo-mastery')->assertNotFound();
    }

    public function test_only_admins_can_open_admin_reports(): void
    {
        foreach ([$this->ana, $this->sam] as $user) {
            $this->authenticateAs($user);
            $this->get(route('admin.reports.index'))->assertForbidden();
            $this->get(route('admin.reports.show', ['report' => 'audit']))->assertForbidden();
            $this->get(route('admin.reports.export', ['report' => 'users', 'format' => 'csv']))->assertForbidden();
        }

        auth()->logout();
        $this->flushSession();
        $this->get(route('admin.reports.show', ['report' => 'users']))->assertRedirect(route('login'));
        $this->assertTrue(Route::has('admin.reports.export'));
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /** @return array<string, string> summary label => value */
    private function summary(string $html): array
    {
        preg_match_all('#<span class="rp-tile-label">(.*?)</span>\s*<strong class="rp-tile-value">(.*?)</strong>#s', $html, $matches, PREG_SET_ORDER);

        return collect($matches)->mapWithKeys(fn ($m) => [html_entity_decode(trim($m[1])) => html_entity_decode(trim($m[2]))])->all();
    }

    /** The table row whose first cell is this text. */
    private function row(string $html, string $first): string
    {
        $this->assertSame(1, preg_match('#<tr>\s*<td[^>]*>\s*(?:<a[^>]*>)?\s*'.preg_quote(e($first), '#').'\s*(?:</a>)?\s*</td>(.*?)</tr>#s', $html, $match), 'Row not found: '.$first);

        return $match[0];
    }
}
