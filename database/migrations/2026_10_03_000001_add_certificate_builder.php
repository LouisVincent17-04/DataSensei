<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 13: the instructor Certificate Builder, admin
 * certificate settings, revocation and reissue, and public verification.
 *
 *   certificate_definitions   gains the class Certificate of Completion an
 *                             instructor configures: owner, class (the course;
 *                             the certificate is for all the class's required
 *                             work, never one module), one of the five
 *                             layouts, statement, signatory title, status
 *                             (draft / active / inactive) and a content
 *                             version with the version last previewed. The
 *                             three system certificates keep is_system = 1.
 *   user_certificates         gains status (active / revoked), when, by whom
 *                             and why it was revoked, the certificate it
 *                             replaces when reissued, the class, the
 *                             instructor who issued it, and active_slot. One
 *                             learner can hold only one active copy of a
 *                             certificate: the unique index is now on
 *                             (user, certificate, active_slot), where a
 *                             revoked copy has active_slot NULL. MySQL allows
 *                             any number of NULLs in a unique index, so
 *                             revoked copies are kept as history.
 *   certificate_settings      issuer name and line, global signatory and the
 *                             layout of the system certificates (key/value)
 *   certificate_layouts       which of the five predefined layouts admins
 *                             have enabled; the layouts themselves are code
 *
 * Additive: plain columns with defaults or NULL, a longText-free key/value
 * table, no JSON, no foreign keys, so it runs on MySQL 5.5. The one index
 * replaced is swapped only after the new one exists. Running it again
 * changes nothing.
 */
return new class extends Migration
{
    private const LAYOUTS = ['academic_classic', 'institutional', 'minimal_professional', 'formal_border', 'modern_academic'];

    public function up(): void
    {
        if ($this->tableExists('certificate_definitions')) {
            $columns = [
                'owner_user_id' => fn (Blueprint $t) => $t->unsignedBigInteger('owner_user_id')->nullable(),
                'class_id' => fn (Blueprint $t) => $t->unsignedBigInteger('class_id')->nullable(),
                'layout_key' => fn (Blueprint $t) => $t->string('layout_key', 40)->nullable(),
                'statement' => fn (Blueprint $t) => $t->text('statement')->nullable(),
                'signatory_title' => fn (Blueprint $t) => $t->string('signatory_title', 120)->nullable(),
                'status' => fn (Blueprint $t) => $t->string('status', 20)->default('active'),
                'config_version' => fn (Blueprint $t) => $t->unsignedInteger('config_version')->default(1),
                'previewed_version' => fn (Blueprint $t) => $t->unsignedInteger('previewed_version')->default(0),
                'activated_at' => fn (Blueprint $t) => $t->timestamp('activated_at')->nullable(),
            ];
            foreach ($columns as $column => $define) {
                if (! $this->columnExists('certificate_definitions', $column)) {
                    Schema::table('certificate_definitions', fn (Blueprint $blueprint) => $define($blueprint));
                }
            }
            if (! $this->indexExists('certificate_definitions', 'certificate_definitions_owner_index')) {
                Schema::table('certificate_definitions', function (Blueprint $table): void {
                    $table->index(['owner_user_id', 'class_id'], 'certificate_definitions_owner_index');
                });
            }

            // System certificates: status follows is_active.
            DB::table('certificate_definitions')->where('is_system', true)->where('is_active', false)->update(['status' => 'inactive']);
        }

        if ($this->tableExists('user_certificates')) {
            $columns = [
                'status' => fn (Blueprint $t) => $t->string('status', 20)->default('active'),
                'active_slot' => fn (Blueprint $t) => $t->unsignedTinyInteger('active_slot')->nullable()->default(1),
                'revoked_at' => fn (Blueprint $t) => $t->timestamp('revoked_at')->nullable(),
                'revoked_by' => fn (Blueprint $t) => $t->unsignedBigInteger('revoked_by')->nullable(),
                'revoke_reason' => fn (Blueprint $t) => $t->string('revoke_reason', 500)->nullable(),
                'reissued_from_id' => fn (Blueprint $t) => $t->unsignedBigInteger('reissued_from_id')->nullable(),
                'class_id' => fn (Blueprint $t) => $t->unsignedBigInteger('class_id')->nullable(),
                'issued_by' => fn (Blueprint $t) => $t->unsignedBigInteger('issued_by')->nullable(),
            ];
            foreach ($columns as $column => $define) {
                if (! $this->columnExists('user_certificates', $column)) {
                    Schema::table('user_certificates', fn (Blueprint $blueprint) => $define($blueprint));
                }
            }
            DB::table('user_certificates')->whereNull('active_slot')->where('status', 'active')->update(['active_slot' => 1]);

            if (! $this->indexExists('user_certificates', 'user_certificates_active_unique')) {
                Schema::table('user_certificates', function (Blueprint $table): void {
                    $table->unique(['user_id', 'certificate_definition_id', 'active_slot'], 'user_certificates_active_unique');
                });
            }
            if ($this->indexExists('user_certificates', 'user_certificates_user_definition_unique')) {
                Schema::table('user_certificates', function (Blueprint $table): void {
                    $table->dropUnique('user_certificates_user_definition_unique');
                });
            }
            if (! $this->indexExists('user_certificates', 'user_certificates_user_index')) {
                Schema::table('user_certificates', function (Blueprint $table): void {
                    $table->index('user_id', 'user_certificates_user_index');
                });
            }
        }

        if (! $this->tableExists('certificate_settings')) {
            Schema::create('certificate_settings', function (Blueprint $table): void {
                $table->id();
                $table->string('setting_key', 64);
                $table->text('setting_value')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->unique('setting_key', 'certificate_settings_key_unique');
            });
        }

        if (! $this->tableExists('certificate_layouts')) {
            Schema::create('certificate_layouts', function (Blueprint $table): void {
                $table->id();
                $table->string('layout_key', 40);
                $table->boolean('is_enabled')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique('layout_key', 'certificate_layouts_key_unique');
            });
        }

        foreach (self::LAYOUTS as $order => $key) {
            if (! DB::table('certificate_layouts')->where('layout_key', $key)->exists()) {
                DB::table('certificate_layouts')->insert([
                    'layout_key' => $key,
                    'is_enabled' => true,
                    'sort_order' => $order + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // The table and column listing is cached per request, and an earlier
        // migration in the same run may have read it: read it again now that
        // this migration has added to it.
        app()->forgetInstance(\App\Support\SchemaInspector::class);
    }

    public function down(): void
    {
        // Additive; certificates and their history are kept.
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

    private function indexExists(string $table, string $index): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasIndex($table, $index);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $index]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
