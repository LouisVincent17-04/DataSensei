<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Admin-editable public modules, lessons, challenges and coding challenges,
 * plus instructor-built challenges handed to a class.
 *
 *   modules.has_coding_exercises   whether a new module fans out a coding
 *                                  challenge as well as an MCQ one
 *   lessons.blocks                 the block editor's source (JSON in a
 *                                  longText); lessons.content stays the
 *                                  rendered HTML the learning room shows
 *   challenges.module_id           the module a fanned-out challenge came from
 *   challenges.created_by          the instructor who built it, NULL for
 *                                  platform content
 *   challenges.visibility          platform | instructor
 *   challenge_questions.image_path a picture shown with the question
 *   coding_questions.title         a short name for the problem
 *   coding_questions.reference_solution
 *                                  a known-good answer the admin can run
 *                                  against the test cases before publishing
 *   class_challenge_assignments    which class was given which challenge
 *
 * Additive only: plain nullable columns or columns with defaults, no
 * JSON/generated columns, no foreign keys and no dependence on column order,
 * so it runs on MySQL 5.5 and on populated tables without touching any row.
 */
return new class extends Migration
{
    public function up(): void
    {
        if ($this->tableExists('modules') && ! $this->columnExists('modules', 'has_coding_exercises')) {
            Schema::table('modules', function (Blueprint $table): void {
                $table->boolean('has_coding_exercises')->default(false);
            });
        }

        if ($this->tableExists('lessons') && ! $this->columnExists('lessons', 'blocks')) {
            Schema::table('lessons', function (Blueprint $table): void {
                $table->longText('blocks')->nullable();
            });
        }

        if ($this->tableExists('challenges')) {
            if (! $this->columnExists('challenges', 'module_id')) {
                Schema::table('challenges', function (Blueprint $table): void {
                    $table->unsignedBigInteger('module_id')->nullable();
                });
            }

            if (! $this->columnExists('challenges', 'created_by')) {
                Schema::table('challenges', function (Blueprint $table): void {
                    $table->unsignedBigInteger('created_by')->nullable();
                });
            }

            if (! $this->columnExists('challenges', 'visibility')) {
                Schema::table('challenges', function (Blueprint $table): void {
                    $table->string('visibility', 20)->default('platform');
                });
            }
        }

        if ($this->tableExists('challenge_questions') && ! $this->columnExists('challenge_questions', 'image_path')) {
            Schema::table('challenge_questions', function (Blueprint $table): void {
                $table->string('image_path', 255)->nullable();
            });
        }

        if ($this->tableExists('coding_questions')) {
            if (! $this->columnExists('coding_questions', 'title')) {
                Schema::table('coding_questions', function (Blueprint $table): void {
                    $table->string('title', 189)->nullable();
                });
            }

            if (! $this->columnExists('coding_questions', 'reference_solution')) {
                Schema::table('coding_questions', function (Blueprint $table): void {
                    $table->longText('reference_solution')->nullable();
                });
            }
        }

        if (! $this->tableExists('class_challenge_assignments')) {
            Schema::create('class_challenge_assignments', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('challenge_id');
                $table->unsignedBigInteger('assigned_by')->nullable();
                $table->string('title', 189)->nullable();
                $table->text('instructions')->nullable();
                $table->dateTime('available_at')->nullable();
                $table->dateTime('due_at')->nullable();
                // draft | published | closed
                $table->string('status', 20)->default('draft');
                $table->timestamps();

                $table->unique(['class_id', 'challenge_id'], 'class_challenge_assignments_unique');
                $table->index(['challenge_id', 'status'], 'class_challenge_assignments_challenge_idx');
            });
        }
    }

    public function down(): void
    {
        if ($this->tableExists('class_challenge_assignments')) {
            Schema::drop('class_challenge_assignments');
        }

        foreach ([
            ['coding_questions', 'reference_solution'],
            ['coding_questions', 'title'],
            ['challenge_questions', 'image_path'],
            ['challenges', 'visibility'],
            ['challenges', 'created_by'],
            ['challenges', 'module_id'],
            ['lessons', 'blocks'],
            ['modules', 'has_coding_exercises'],
        ] as [$tableName, $column]) {
            if ($this->tableExists($tableName) && $this->columnExists($tableName, $column)) {
                Schema::table($tableName, function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /**
     * Schema::hasColumn() and Schema::hasTable() read information_schema
     * columns (generation_expression) that MySQL 5.5 does not have, so they
     * fail there with "Unknown column". These plain queries work on every
     * MySQL/MariaDB version, like the helpers of the earlier migrations.
     */
    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?',
            [$table]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
