<?php

namespace Tests\Feature\Regression;

use App\Services\Reports\ClassProgress;
use App\Support\Reports\PerformanceBands;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * DataSensei Updates 12, part 3: Class Analytics and At-Risk are one
 * instructor area. A Low / Moderate / High bar graph (thresholds from
 * config/class_analytics.php, agreed defaults 70 and 90) and the at-risk
 * students with their reasons are on the same page.
 *
 * Fixture: Sam's assessment average is 83.3% (Moderate), Lia's 55% (Low),
 * Tom has nothing graded (Not yet graded).
 */
class Updates12ClassAnalyticsAtRiskTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();
    }

    public function test_the_overview_shows_the_performance_graph_and_the_at_risk_students_together(): void
    {
        $html = $this->authenticateAs($this->ana)
            ->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id]))
            ->assertOk()->getContent();

        $graph = substr($html, (int) strpos($html, 'id="performance"'), 2500);
        $this->assertStringContainsString('Class performance', $graph);
        $this->assertStringContainsString('Low (Below 70%)', $graph);
        $this->assertStringContainsString('Moderate (70% to under 90%)', $graph);
        $this->assertStringContainsString('High (90% or more)', $graph);
        // Lia Low, Sam Moderate, nobody High; Tom has no graded work.
        $this->assertMatchesRegularExpression('#Low \(Below 70%\).*?1 student, 50%#s', $graph);
        $this->assertMatchesRegularExpression('#Moderate \(70% to under 90%\).*?1 student, 50%#s', $graph);
        $this->assertMatchesRegularExpression('#High \(90% or more\).*?0 students, 0%#s', $graph);
        $this->assertStringContainsString('1 student has no graded assessment yet', $graph);
        $this->assertStringContainsString('rp-fill-bad', $graph);

        // The at-risk list is in the same area, with the evidence.
        $this->assertGreaterThan(strpos($html, 'id="performance"'), strpos($html, 'id="attention"'));
        $atRisk = substr($html, (int) strpos($html, 'id="attention"'));
        $this->assertStringContainsString('At-risk students', $atRisk);
        $this->assertStringContainsString('Assessment average 55% (Low performance group)', $atRisk);
        $this->assertStringContainsString('Low performance', $atRisk);
        $this->assertStringContainsString('Repeated failures on 1 coding problem', $atRisk);
        $this->assertStringContainsString('Missing 3 assessments', $atRisk);
        $this->assertStringContainsString('Low performance group: assessment average below 70%', $atRisk);
        $this->assertStringNotContainsString('Sam Student', $atRisk);
    }

    public function test_thresholds_come_from_configuration(): void
    {
        config(['class_analytics.performance.low_below' => 50, 'class_analytics.performance.high_from' => 80]);

        $sam = app(ClassProgress::class)->forClass($this->dataScience)['perStudent'][$this->sam->id]['summary'];
        $lia = app(ClassProgress::class)->forClass($this->dataScience)['perStudent'][$this->lia->id]['summary'];
        $this->assertSame(PerformanceBands::HIGH, $sam['performance_group']);
        $this->assertSame(PerformanceBands::MODERATE, $lia['performance_group']);
        $this->assertArrayNotHasKey('assessments', $lia['attention']);

        $html = $this->authenticateAs($this->ana)
            ->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id]))->getContent();
        $this->assertStringContainsString('High (80% or more)', $html);
        $this->assertStringContainsString('assessment average below 50%', $html);

        // Values that make no sense fall back to the agreed 70 / 90.
        config(['class_analytics.performance.low_below' => 95, 'class_analytics.performance.high_from' => 90]);
        $this->assertSame(70.0, PerformanceBands::lowBelow());
        $this->assertSame(90.0, PerformanceBands::highFrom());
        config(['class_analytics.performance.low_below' => 'abc']);
        $this->assertSame(PerformanceBands::LOW, PerformanceBands::groupOf(69.9));
        $this->assertSame(PerformanceBands::MODERATE, PerformanceBands::groupOf(70.0));
        $this->assertSame(PerformanceBands::HIGH, PerformanceBands::groupOf(90.0));
        $this->assertSame(PerformanceBands::NOT_GRADED, PerformanceBands::groupOf(null));

        // At-risk numbers too.
        config(['class_analytics.at_risk.missing_assessments' => 4]);
        $tom = app(ClassProgress::class)->forClass($this->dataScience)['perStudent'][$this->tom->id]['summary'];
        $this->assertArrayNotHasKey('missing', $tom['attention']);
    }

    public function test_repeated_failed_challenge_attempts_flag_a_student(): void
    {
        foreach ([2, 3] as $no) {
            DB::table('challenge_attempts')->insert([
                'user_id' => $this->lia->id, 'challenge_id' => $this->ids['c1'], 'attempt_no' => $no, 'mode' => 'practice', 'status' => 'submitted',
                'started_at' => now(), 'submitted_at' => now(), 'time_taken_seconds' => 60, 'score' => 4, 'total_questions' => 10, 'xp_awarded' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $lia = app(ClassProgress::class)->forClass($this->dataScience)['perStudent'][$this->lia->id]['summary'];
        $this->assertSame('3 or more failed attempts on 1 challenge or assessment not yet passed', $lia['attention']['attempts']);
    }

    public function test_the_students_tab_filters_by_performance_group(): void
    {
        $this->authenticateAs($this->ana);
        $low = $this->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id, 'tab' => 'students', 'status' => 'low']))->assertOk()->getContent();
        $table = substr($low, (int) strpos($low, '<table'));
        $this->assertStringContainsString('Lia Student', $table);
        $this->assertStringNotContainsString('Sam Student', $table);
        $this->assertStringContainsString('Performance', $table);

        $notGraded = $this->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id, 'tab' => 'students', 'status' => 'not_graded']))->getContent();
        $table = substr($notGraded, (int) strpos($notGraded, '<table'));
        $this->assertStringContainsString('Tom Student', $table);
        $this->assertStringNotContainsString('Lia Student', $table);
    }

    public function test_the_old_at_risk_page_opens_the_merged_area(): void
    {
        $this->authenticateAs($this->ana);

        $this->get(route('instructor.risk.index', ['class_id' => $this->dataScience->id]))
            ->assertRedirect(route('instructor.analytics.index', ['class_id' => $this->dataScience->id]).'#attention');
        // Someone else's class id is dropped, not opened.
        $this->get(route('instructor.risk.index', ['class_id' => $this->statistics->id]))
            ->assertRedirect(route('instructor.analytics.index').'#attention');

        $page = $this->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id]))->getContent();
        $this->assertStringContainsString('Class Analytics &amp; At-Risk', $page);
        $this->assertStringNotContainsString('At-Risk Alerts', $page);
        $this->assertStringNotContainsString(route('instructor.risk.index'), $page);
        $this->assertFileDoesNotExist(resource_path('views/instructor/risk/index.blade.php'));
    }

    public function test_the_dashboard_uses_the_same_live_at_risk_rules(): void
    {
        $html = $this->authenticateAs($this->ana)->get(route('instructor.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('At-risk learners', $html);
        $card = substr($html, (int) strpos($html, 'At-risk learners'), 3000);
        $this->assertStringContainsString('Lia Student', $card);
        $this->assertStringContainsString('Low performance', $card);
        $this->assertStringNotContainsString('Sam Student', $card);
        $this->assertStringNotContainsString('latest performance snapshots', $html);
        $this->assertStringContainsString('Low / Moderate / High', $html);
    }
}
