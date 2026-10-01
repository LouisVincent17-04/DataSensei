<?php

use App\Services\CertificateService;
use App\Support\CoreCurriculum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 12: protected Core Modules and certificates.
 *
 *   modules.module_type          'core' for the 24 Core Modules, 'custom'
 *                                for every other module (the default, so a
 *                                new module is custom)
 *   modules.module_key           permanent identity of a core module
 *                                (core-01-...); unique, NULL for custom
 *   modules.archived_at          when a custom module in use was archived
 *                                instead of deleted
 *   challenges.core_module_key   the core module a built-in challenge
 *                                belongs to (all versions); NULL otherwise
 *
 *   certificate_definitions      the predefined system certificates
 *   certificate_requirement_sets the versioned list of what each one
 *                                requires (JSON in a longText column)
 *   user_certificates            certificates issued, each with the
 *                                requirement version it was issued under
 *                                and a snapshot, so it never changes later
 *
 * The existing 24 modules and their built-in challenges are then marked
 * (App\Support\CoreCurriculum::sync) and the three core certificates are
 * defined. Nothing is removed and no progress row is touched.
 *
 * Additive only: plain columns with defaults or NULL, longText instead of a
 * JSON column, short indexed strings, no foreign keys, so it runs on MySQL
 * 5.5. Running it again changes nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if ($this->tableExists('modules')) {
            if (! $this->columnExists('modules', 'module_type')) {
                Schema::table('modules', function (Blueprint $table): void {
                    $table->string('module_type', 20)->default('custom');
                });
            }

            if (! $this->columnExists('modules', 'module_key')) {
                Schema::table('modules', function (Blueprint $table): void {
                    $table->string('module_key', 100)->nullable();
                    $table->unique('module_key', 'modules_module_key_unique');
                });
            }

            if (! $this->columnExists('modules', 'archived_at')) {
                Schema::table('modules', function (Blueprint $table): void {
                    $table->timestamp('archived_at')->nullable();
                });
            }
        }

        if ($this->tableExists('challenges') && ! $this->columnExists('challenges', 'core_module_key')) {
            Schema::table('challenges', function (Blueprint $table): void {
                $table->string('core_module_key', 100)->nullable();
                $table->index('core_module_key', 'challenges_core_module_key_index');
            });
        }

        if (! $this->tableExists('certificate_definitions')) {
            Schema::create('certificate_definitions', function (Blueprint $table): void {
                $table->id();
                $table->string('certificate_key', 64);
                $table->string('name', 189);
                $table->text('description')->nullable();
                $table->string('audience', 40)->default('public');
                $table->boolean('is_system')->default(true);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('current_version')->default(0);
                $table->timestamps();
                $table->unique('certificate_key', 'certificate_definitions_key_unique');
            });
        }

        if (! $this->tableExists('certificate_requirement_sets')) {
            Schema::create('certificate_requirement_sets', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('certificate_definition_id');
                $table->unsignedInteger('version');
                $table->longText('requirements');
                $table->string('requirements_hash', 64);
                $table->unsignedInteger('item_count')->default(0);
                $table->timestamps();
                $table->unique(['certificate_definition_id', 'version'], 'certificate_requirement_sets_version_unique');
            });
        }

        if (! $this->tableExists('user_certificates')) {
            Schema::create('user_certificates', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('certificate_definition_id');
                $table->string('certificate_key', 64);
                $table->string('certificate_number', 40);
                $table->unsignedBigInteger('requirement_set_id')->nullable();
                $table->unsignedInteger('requirement_version');
                $table->longText('snapshot');
                $table->timestamp('issued_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'certificate_definition_id'], 'user_certificates_user_definition_unique');
                $table->unique('certificate_number', 'user_certificates_number_unique');
                $table->index('certificate_key', 'user_certificates_key_index');
            });
        }

        try {
            // The table and column listing is cached per request; read it
            // again now that this migration has added to it.
            app()->forgetInstance(\App\Support\SchemaInspector::class);

            CoreCurriculum::sync();
            app(CertificateService::class)->ensureDefinitions();
        } catch (\Throwable $exception) {
            // The columns and tables are in place; marking can be repeated
            // with "php artisan datasensei:core-sync".
            Log::warning('Core module marking did not finish during the migration: '.$exception->getMessage());
        }
    }

    public function down(): void
    {
        // Additive; the columns and tables are left in place.
    }

    /**
     * Plain information_schema queries, like the earlier migrations, so they
     * also work on MySQL 5.5 (Schema::hasColumn does not there).
     */
    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
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
            'SELECT COUNT(*) AS aggregate FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
