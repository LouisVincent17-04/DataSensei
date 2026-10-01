<?php

namespace Tests\Feature\Regression;

use App\Support\SchemaInspector;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * DataSensei Updates 12 (fix): a database whose module_library_progress
 * table existed before DataSensei Updates 8 kept its own columns (the
 * Updates 8 migration only creates the table when it is missing). The
 * instructor dashboard, now reading Class Analytics, failed there with
 * "Unknown column 'opened_at' in 'field list'".
 *
 * The pages now read a missing column as empty, and the migration
 * 2026_10_02_000002 adds the missing columns and fills them from the old
 * rows.
 */
class Updates12LegacyProgressTableTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();

        // The table as an older database has it: no opened_at,
        // last_opened_at, completed_at or class_id.
        Schema::drop('module_library_progress');
        Schema::create('module_library_progress', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('module_library_item_id');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
        DB::table('module_library_progress')->insert([
            ['user_id' => $this->sam->id, 'module_library_item_id' => $this->ids['m1'], 'is_completed' => true, 'created_at' => now()->subDays(5), 'updated_at' => now()->subDays(2)],
            ['user_id' => $this->lia->id, 'module_library_item_id' => $this->ids['m1'], 'is_completed' => false, 'created_at' => now()->subDays(4), 'updated_at' => now()->subDays(4)],
        ]);
        app()->forgetInstance(SchemaInspector::class);
    }

    public function test_the_instructor_pages_open_before_the_repair_migration_runs(): void
    {
        $this->authenticateAs($this->ana);

        $this->get(route('instructor.dashboard'))->assertOk()->assertSee('At-risk learners');
        $this->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id]))->assertOk()->assertSee('Class performance');
        $this->get(route('instructor.analytics.index', ['class_id' => $this->dataScience->id, 'tab' => 'modules']))->assertOk();
        $this->get(route('instructor.reports.show', ['report' => 'modules']))->assertOk();
        $this->get(route('instructor.gradebook.index', ['class_id' => $this->dataScience->id]))->assertOk();
    }

    public function test_the_repair_migration_adds_and_fills_the_missing_columns(): void
    {
        $migration = require database_path('migrations/2026_10_02_000002_repair_reporting_table_columns.php');
        $migration->up();

        foreach (['class_id', 'opened_at', 'last_opened_at', 'completed_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('module_library_progress', $column), $column);
        }
        $this->assertTrue(Schema::hasIndex('module_library_progress', 'module_library_progress_user_module_unique'));

        $sam = DB::table('module_library_progress')->where('user_id', $this->sam->id)->first();
        $lia = DB::table('module_library_progress')->where('user_id', $this->lia->id)->first();
        $this->assertSame((string) $sam->created_at, (string) $sam->opened_at);
        $this->assertSame((string) $sam->updated_at, (string) $sam->last_opened_at);
        $this->assertSame((string) $sam->updated_at, (string) $sam->completed_at);
        $this->assertNotNull($lia->opened_at);
        $this->assertNull($lia->completed_at);

        // Running it again changes nothing.
        $before = DB::table('module_library_progress')->orderBy('id')->get()->toArray();
        $migration->up();
        $this->assertEquals($before, DB::table('module_library_progress')->orderBy('id')->get()->toArray());

        // Class Analytics now sees Sam's completed class module.
        app()->forgetInstance(SchemaInspector::class);
        $snapshot = app(\App\Services\Reports\ClassProgress::class)->forClass($this->dataScience);
        $this->assertSame('completed', $snapshot['perStudent'][$this->sam->id]['modules'][$this->ids['m1']]['state']);
        $this->assertSame('started', $snapshot['perStudent'][$this->lia->id]['modules'][$this->ids['m1']]['state']);
    }

    public function test_a_correct_table_is_left_exactly_as_it_is(): void
    {
        Schema::drop('module_library_progress');
        $migration = require database_path('migrations/2026_10_02_000002_repair_reporting_table_columns.php');
        $migration->up(); // creates the table the way Updates 8 defines it
        DB::table('module_library_progress')->insert(['user_id' => $this->sam->id, 'module_library_item_id' => $this->ids['m1'], 'opened_at' => null, 'created_at' => now(), 'updated_at' => now()]);

        $migration->up();

        $this->assertNull(DB::table('module_library_progress')->value('opened_at'));
    }
}
