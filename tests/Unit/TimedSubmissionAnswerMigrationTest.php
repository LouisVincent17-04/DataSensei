<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TimedSubmissionAnswerMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        foreach (['assignment_submissions', 'assessment_submissions'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->string('status')->default('in_progress');
            });
        }
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('assessment_submissions');
        Schema::dropIfExists('assignment_submissions');
        parent::tearDown();
    }

    public function test_snapshot_and_timeout_columns_are_added_and_preserved(): void
    {
        $migration = require database_path(
            'migrations/2026_08_30_000001_preserve_timed_submission_answers.php'
        );

        $migration->up();

        foreach (['assignment_submissions', 'assessment_submissions'] as $tableName) {
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_answers'));
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_version'));
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_saved_at'));
            $this->assertTrue(Schema::hasColumn($tableName, 'timed_out_at'));
        }

        $migration->down();

        foreach (['assignment_submissions', 'assessment_submissions'] as $tableName) {
            $this->assertTrue(Schema::hasColumn($tableName, 'draft_answers'));
            $this->assertTrue(Schema::hasColumn($tableName, 'timed_out_at'));
        }
    }
}
