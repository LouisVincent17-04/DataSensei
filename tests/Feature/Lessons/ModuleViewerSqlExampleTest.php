<?php

namespace Tests\Feature\Lessons;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The module library labels every code sample "Python Example" and used to
 * send it to the Python compiler. 540 samples (the two database modules) are
 * SQL and always ended in "SyntaxError: invalid syntax". They now go to the
 * SQL sandbox together with the practice tables they query.
 */
class ModuleViewerSqlExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_sql_examples_with_their_practice_tables_run_in_the_sql_sandbox(): void
    {
        $view = file_get_contents(resource_path('views/student/shared/module_netacad_viewer.blade.php'));
        $this->assertSame(1, preg_match('/const SQL_EXAMPLE_TABLES = \{(.*?)\n\s*\};/s', $view, $match));
        preg_match_all('/(\w+): `(.*?)`/s', $match[1], $tables, PREG_SET_ORDER);
        $setup = array_column($tables, 2, 1);
        $this->assertSame(['student_scores', 'fact_sales'], array_keys($setup));

        $examples = [
            'student_scores' => "SELECT department, AVG(score) AS average_score\nFROM student_scores\nGROUP BY department\nHAVING AVG(score) >= 80\nORDER BY average_score DESC;\n-- Topic focus: Database Concepts",
            'fact_sales' => "SELECT d.month_name, SUM(f.sales_amount) AS total_sales\nFROM fact_sales f\nJOIN dim_date d ON f.date_key = d.date_key\nGROUP BY d.month_name\nORDER BY MIN(d.month_number);",
        ];

        $student = $this->roleUser();

        foreach ($examples as $table => $query) {
            $response = $this->authenticateAs($student)->postJson(route('sql-sandbox.execute'), [
                'query' => "-- Practice data for this example\n".str_replace("\r", '', $setup[$table])."\n\n".$query,
            ]);

            $response->assertOk();
            $this->assertStringNotContainsStringIgnoringCase('error', json_encode($response->json('error') ?? ''));
            $this->assertStringContainsString($table === 'fact_sales' ? 'January' : 'Data Science', $response->getContent());
        }
    }

    public function test_every_viewer_detects_sql_and_keeps_python_on_the_python_compiler(): void
    {
        foreach (glob(resource_path('views/*/shared/module_*viewer.blade.php')) as $path) {
            $view = file_get_contents($path);
            $this->assertStringContainsString('function isSqlExample(code)', $view, $path);
            $this->assertStringContainsString("sessionStorage.setItem('datasensei_pending_sql_code'", $view, $path);
            $this->assertStringContainsString("sessionStorage.setItem('datasensei_pending_code', code || '')", $view, $path);
        }
    }
}
