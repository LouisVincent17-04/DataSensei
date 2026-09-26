<?php

namespace Tests\Feature\Regression;

use App\Services\SuperAdminAnalyticsService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Superadmin audit: Platform Analytics gates every number behind a table and
 * column existence check. Those checks ran "SHOW TABLES LIKE ?" and
 * "SHOW COLUMNS FROM `t` LIKE ?", which MySQL cannot prepare. The resulting
 * syntax error was swallowed, every table was reported missing, and the whole
 * page plus all of its CSV exports read zero on a populated database.
 *
 * The defect only appears on MySQL/MariaDB, so this runs against a real one.
 * Skipped unless DS_MYSQL_TEST_DB names an already-migrated MySQL/MariaDB
 * database (optional: DS_MYSQL_TEST_HOST, DS_MYSQL_TEST_PORT,
 * DS_MYSQL_TEST_USER, DS_MYSQL_TEST_PASSWORD; defaults 127.0.0.1/3306/ds/ds).
 */
class AuditAdminPlatformAnalyticsMysqlTest extends TestCase
{
    /** @var array<int, int> */
    private array $userIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $database = getenv('DS_MYSQL_TEST_DB') ?: '';
        if ($database === '') {
            $this->markTestSkipped('Set DS_MYSQL_TEST_DB to a migrated MySQL/MariaDB database to run the analytics schema test.');
        }

        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.host', getenv('DS_MYSQL_TEST_HOST') ?: '127.0.0.1');
        config()->set('database.connections.mysql.port', getenv('DS_MYSQL_TEST_PORT') ?: '3306');
        config()->set('database.connections.mysql.database', $database);
        config()->set('database.connections.mysql.username', getenv('DS_MYSQL_TEST_USER') ?: 'ds');
        config()->set('database.connections.mysql.password', getenv('DS_MYSQL_TEST_PASSWORD') ?: 'ds');
        DB::purge('mysql');
    }

    protected function tearDown(): void
    {
        if ($this->userIds !== []) {
            DB::table('users')->whereIn('id', $this->userIds)->delete();
        }

        parent::tearDown();
    }

    private function seedLearners(int $count): void
    {
        for ($index = 0; $index < $count; $index++) {
            $this->userIds[] = (int) DB::table('users')->insertGetId([
                'name' => 'Analytics Audit Learner '.$index,
                'email' => 'analytics-audit-'.$index.'-'.uniqid().'@audit.test',
                'password' => 'not-a-real-hash',
                'role' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_existence_checks_answer_truthfully_on_mysql(): void
    {
        $service = new SuperAdminAnalyticsService();
        $reflection = new \ReflectionClass($service);
        $tableExists = $reflection->getMethod('tableExists');
        $columnExists = $reflection->getMethod('columnExists');

        $this->assertTrue($tableExists->invoke($service, 'users'));
        $this->assertFalse($tableExists->invoke($service, 'a_table_that_does_not_exist'));
        $this->assertTrue($columnExists->invoke($service, 'users', 'role'));
        $this->assertFalse($columnExists->invoke($service, 'users', 'a_column_that_does_not_exist'));
    }

    public function test_summary_and_role_distribution_report_real_rows(): void
    {
        $existingLearners = (int) DB::table('users')->where('role', 1)->count();
        $this->seedLearners(3);

        $analytics = (new SuperAdminAnalyticsService())->build();
        $summary = collect($analytics['summary'])->keyBy('label');

        $this->assertSame($existingLearners + 3, $summary['Total Students']['value']);
        $this->assertGreaterThan(0, $summary['Active Accounts']['value']);
        $this->assertNotEmpty($analytics['roleDistribution']);
    }

    /**
     * The at-risk list filtered on "assignment_avg", a SELECT alias, inside
     * HAVING. MySQL rejects that under ONLY_FULL_GROUP_BY with
     *
     *   1463 Non-grouping field 'assignment_avg' is used in HAVING clause
     *
     * and the whole Platform Analytics page died with a 500. The two sibling
     * conditions in the same HAVING were already written as aggregates, which
     * is why only this one failed. ONLY_FULL_GROUP_BY is set explicitly here
     * because it is the condition that triggers the defect, and a server
     * configured without it hides the bug entirely.
     */
    public function test_at_risk_students_survive_only_full_group_by(): void
    {
        $originalMode = DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode;

        DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES'");

        try {
            $this->seedLearners(3);

            $service = new SuperAdminAnalyticsService();
            $reflection = new \ReflectionClass($service);
            $atRisk = $reflection->getMethod('atRiskStudents');

            $range = [
                'from' => now()->subDays(30)->startOfDay()->toDateTimeString(),
                'to' => now()->endOfDay()->toDateTimeString(),
            ];

            DB::flushQueryLog();
            DB::enableQueryLog();
            $rows = $atRisk->invoke($service, $range);
            $executed = DB::getQueryLog();
            DB::disableQueryLog();

            $this->assertIsArray($rows);

            // MariaDB accepts the alias even under ONLY_FULL_GROUP_BY, so
            // executing the query is not on its own proof. The SQL itself is
            // inspected: MySQL is the strict one and this is what it rejected.
            $having = '';
            foreach ($executed as $entry) {
                if (str_contains($entry['query'], 'assignment_avg')) {
                    $having = substr($entry['query'], (int) strpos($entry['query'], 'having'));
                    break;
                }
            }

            $this->assertNotSame('', $having, 'The at-risk query was not captured.');
            $this->assertStringNotContainsString(
                'assignment_avg',
                $having,
                'HAVING must repeat the aggregate, not reference the SELECT alias: MySQL rejects that with error 1463.'
            );
            $this->assertStringContainsString('AVG(CASE WHEN s.graded_at', $having);

            // Freshly seeded learners have no XP and no activity, so the list
            // must actually contain them: a query that silently returned
            // nothing would pass a "no exception" check on its own.
            $this->assertNotEmpty($rows, 'The at-risk list found no one despite three inactive learners.');

            $emails = array_column($rows, 'email');
            $seeded = array_filter($emails, fn ($email) => str_contains((string) $email, '@audit.test'));
            $this->assertNotEmpty($seeded, 'The seeded inactive learners are missing from the at-risk list.');

            // And the whole page, which is what actually 500'd. build() reaches
            // atRiskStudents() twice: once for the student panel and once for
            // the insights count.
            $analytics = $service->build();
            $this->assertArrayHasKey('atRisk', $analytics['students']);
            $this->assertNotEmpty($analytics['students']['atRisk']);
            $this->assertNotEmpty($analytics['insights']);
        } finally {
            DB::statement('SET SESSION sql_mode = ?', [$originalMode]);
        }
    }

    public function test_summary_export_rows_carry_the_real_counts(): void
    {
        $this->seedLearners(2);

        $service = new SuperAdminAnalyticsService();
        $rows = $service->exportRows('summary', $service->build());

        $students = collect($rows)->first(fn (array $row) => ($row[0] ?? null) === 'Total Students');

        $this->assertNotNull($students, 'The summary export lost its Total Students row.');
        $this->assertGreaterThanOrEqual(2, (int) $students[1]);
    }
}
