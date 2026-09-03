<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('datasets')) {
            Schema::create('datasets', function (Blueprint $table): void {
                $table->id();
                $table->string('slug', 100)->unique();
                $table->string('name', 160);
                $table->text('description')->nullable();
                $table->string('source_name', 191)->nullable();
                $table->text('source_url')->nullable();
                $table->string('license_name', 120)->nullable();
                $table->string('storage_path', 255);
                $table->unsignedInteger('row_count')->default(0);
                $table->unsignedSmallInteger('column_count')->default(0);
                $table->string('target_column', 120)->nullable();
                $table->string('problem_type', 32);
                $table->longText('feature_list')->nullable();
                $table->longText('metadata')->nullable();
                $table->string('version_label', 32)->default('v1');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['problem_type', 'is_active'], 'ds_problem_active_idx');
            });
        }

        if (! Schema::hasTable('user_datasets')) {
            Schema::create('user_datasets', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->string('uuid', 36)->unique();
                $table->string('name', 160);
                $table->string('original_filename', 191);
                $table->string('stored_filename', 191);
                $table->string('mime_type', 120);
                $table->string('storage_path', 255);
                $table->unsignedInteger('file_size')->default(0);
                $table->unsignedInteger('row_count')->default(0);
                $table->unsignedSmallInteger('column_count')->default(0);
                $table->string('target_column', 120)->nullable();
                $table->string('problem_type', 32)->nullable();
                $table->string('status', 32)->default('ready');
                $table->decimal('quality_score', 5, 2)->nullable();
                $table->longText('schema_profile')->nullable();
                $table->longText('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id', 'ud_user_fk')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_id', 'ud_class_fk')->references('id')->on('classes')->onDelete('set null');
                $table->index(['user_id', 'created_at'], 'ud_user_created_idx');
                $table->index(['class_id', 'created_at'], 'ud_class_created_idx');
                $table->index(['status', 'problem_type'], 'ud_status_problem_idx');
            });
        }

        if (! Schema::hasTable('dataset_versions')) {
            Schema::create('dataset_versions', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('dataset_id')->nullable();
                $table->unsignedBigInteger('user_dataset_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedInteger('version_number')->default(1);
                $table->string('version_label', 32)->default('v1');
                $table->string('checksum_sha256', 64);
                $table->string('storage_path', 255);
                $table->unsignedInteger('row_count')->default(0);
                $table->unsignedSmallInteger('column_count')->default(0);
                $table->longText('schema_profile')->nullable();
                $table->longText('metadata')->nullable();
                $table->timestamps();

                $table->foreign('dataset_id', 'dv_dataset_fk')->references('id')->on('datasets')->onDelete('cascade');
                $table->foreign('user_dataset_id', 'dv_user_dataset_fk')->references('id')->on('user_datasets')->onDelete('cascade');
                $table->foreign('created_by', 'dv_creator_fk')->references('id')->on('users')->onDelete('set null');
                $table->unique(['dataset_id', 'version_number'], 'dv_system_version_uq');
                $table->unique(['user_dataset_id', 'version_number'], 'dv_user_version_uq');
                $table->index('checksum_sha256', 'dv_checksum_idx');
            });
        }

        if (! Schema::hasTable('ml_models')) {
            Schema::create('ml_models', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('dataset_id')->nullable();
                $table->unsignedBigInteger('user_dataset_id')->nullable();
                $table->unsignedBigInteger('current_version_id')->nullable();
                $table->string('identity_key', 64)->unique();
                $table->string('uuid', 36)->unique();
                $table->string('name', 160);
                $table->string('pipeline_type', 16);
                $table->string('problem_type', 32);
                $table->string('algorithm_key', 64);
                $table->string('status', 32)->default('ready');
                $table->boolean('is_read_only')->default(false);
                $table->longText('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id', 'mm_user_fk')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_id', 'mm_class_fk')->references('id')->on('classes')->onDelete('set null');
                $table->foreign('dataset_id', 'mm_dataset_fk')->references('id')->on('datasets')->onDelete('set null');
                $table->foreign('user_dataset_id', 'mm_user_dataset_fk')->references('id')->on('user_datasets')->onDelete('set null');
                $table->index(['pipeline_type', 'dataset_id'], 'mm_pipeline_dataset_idx');
                $table->index(['user_id', 'created_at'], 'mm_user_created_idx');
                $table->index(['class_id', 'created_at'], 'mm_class_created_idx');
            });
        }

        if (! Schema::hasTable('training_jobs')) {
            Schema::create('training_jobs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('dataset_id')->nullable();
                $table->unsignedBigInteger('user_dataset_id')->nullable();
                $table->unsignedBigInteger('dataset_version_id')->nullable();
                $table->unsignedBigInteger('ml_model_id')->nullable();
                $table->string('uuid', 36)->unique();
                $table->string('model_name', 160);
                $table->string('problem_type', 32);
                $table->string('algorithm_key', 64);
                $table->string('status', 32)->default('queued');
                $table->unsignedTinyInteger('progress')->default(0);
                $table->string('stage', 160)->default('Waiting for the training worker');
                $table->longText('configuration');
                $table->longText('result')->nullable();
                $table->text('error_message')->nullable();
                $table->unsignedInteger('duration_ms')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id', 'tj_user_fk')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_id', 'tj_class_fk')->references('id')->on('classes')->onDelete('set null');
                $table->foreign('dataset_id', 'tj_dataset_fk')->references('id')->on('datasets')->onDelete('set null');
                $table->foreign('user_dataset_id', 'tj_user_dataset_fk')->references('id')->on('user_datasets')->onDelete('set null');
                $table->foreign('dataset_version_id', 'tj_dataset_version_fk')->references('id')->on('dataset_versions')->onDelete('set null');
                $table->foreign('ml_model_id', 'tj_model_fk')->references('id')->on('ml_models')->onDelete('set null');
                $table->index(['user_id', 'status'], 'tj_user_status_idx');
                $table->index(['class_id', 'created_at'], 'tj_class_created_idx');
                $table->index(['status', 'created_at'], 'tj_status_created_idx');
            });
        }

        if (! Schema::hasTable('model_versions')) {
            Schema::create('model_versions', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ml_model_id');
                $table->unsignedBigInteger('training_job_id')->nullable();
                $table->unsignedBigInteger('dataset_version_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedInteger('version_number');
                $table->string('version_label', 32);
                $table->string('artifact_path', 255);
                $table->string('metadata_path', 255)->nullable();
                $table->longText('metrics')->nullable();
                $table->longText('hyperparameters')->nullable();
                $table->longText('feature_names')->nullable();
                $table->string('target_column', 120)->nullable();
                $table->longText('visualizations')->nullable();
                $table->longText('explanations')->nullable();
                $table->unsignedInteger('training_time_ms')->nullable();
                $table->string('python_version', 32)->nullable();
                $table->string('sklearn_version', 32)->nullable();
                $table->string('status', 32)->default('ready');
                $table->boolean('is_active')->default(false);
                $table->timestamps();

                $table->foreign('ml_model_id', 'mv_model_fk')->references('id')->on('ml_models')->onDelete('cascade');
                $table->foreign('training_job_id', 'mv_job_fk')->references('id')->on('training_jobs')->onDelete('set null');
                $table->foreign('dataset_version_id', 'mv_dataset_version_fk')->references('id')->on('dataset_versions')->onDelete('set null');
                $table->foreign('created_by', 'mv_creator_fk')->references('id')->on('users')->onDelete('set null');
                $table->unique(['ml_model_id', 'version_number'], 'mv_model_version_uq');
                $table->index(['ml_model_id', 'is_active'], 'mv_model_active_idx');
            });
        }

        // Add the circular current-version reference only after both tables exist.
        // A deliberately short name avoids MySQL 5.5/InnoDB constraint collisions.
        if (! $this->currentVersionForeignKeyExists()) {
            try {
                Schema::table('ml_models', function (Blueprint $table): void {
                    $table->foreign('current_version_id', 'mm_current_version_fk')
                        ->references('id')->on('model_versions')->onDelete('set null');
                });
            } catch (QueryException $exception) {
                // A concurrent/partial migration may have created this exact
                // constraint after the pre-check. Only that confirmed duplicate
                // is safe to ignore; permission, type, engine, and SQL failures
                // must stop the migration.
                if (! $this->isDuplicateConstraintFailure($exception) || ! $this->currentVersionForeignKeyExists()) {
                    throw $exception;
                }
            }
        }

        if (! Schema::hasTable('benchmark_models')) {
            Schema::create('benchmark_models', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('dataset_id');
                $table->unsignedBigInteger('ml_model_id');
                $table->unsignedBigInteger('model_version_id');
                $table->string('algorithm_key', 64);
                $table->unsignedSmallInteger('benchmark_rank')->default(1);
                $table->boolean('is_primary')->default(false);
                $table->longText('metrics')->nullable();
                $table->timestamps();

                $table->foreign('dataset_id', 'bm_dataset_fk')->references('id')->on('datasets')->onDelete('cascade');
                $table->foreign('ml_model_id', 'bm_model_fk')->references('id')->on('ml_models')->onDelete('cascade');
                $table->foreign('model_version_id', 'bm_version_fk')->references('id')->on('model_versions')->onDelete('cascade');
                $table->unique(['dataset_id', 'algorithm_key'], 'bm_dataset_algorithm_uq');
                $table->index(['dataset_id', 'is_primary'], 'bm_dataset_primary_idx');
            });
        }

        if (! Schema::hasTable('prediction_logs')) {
            Schema::create('prediction_logs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('model_version_id');
                $table->unsignedBigInteger('user_id');
                $table->longText('input_values');
                $table->text('predicted_value');
                $table->longText('probabilities')->nullable();
                $table->text('explanation')->nullable();
                $table->unsignedInteger('latency_ms')->nullable();
                $table->timestamps();

                $table->foreign('model_version_id', 'pl_version_fk')->references('id')->on('model_versions')->onDelete('cascade');
                $table->foreign('user_id', 'pl_user_fk')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'created_at'], 'pl_user_created_idx');
                $table->index(['model_version_id', 'created_at'], 'pl_version_created_idx');
            });
        }

        if (! Schema::hasTable('quality_reports')) {
            Schema::create('quality_reports', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('dataset_id')->nullable();
                $table->unsignedBigInteger('user_dataset_id')->nullable();
                $table->unsignedBigInteger('dataset_version_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->decimal('quality_score', 5, 2);
                $table->longText('summary');
                $table->longText('column_analysis')->nullable();
                $table->longText('recommendations')->nullable();
                $table->timestamp('generated_at')->nullable();
                $table->timestamps();

                $table->foreign('dataset_id', 'qr_dataset_fk')->references('id')->on('datasets')->onDelete('cascade');
                $table->foreign('user_dataset_id', 'qr_user_dataset_fk')->references('id')->on('user_datasets')->onDelete('cascade');
                $table->foreign('dataset_version_id', 'qr_version_fk')->references('id')->on('dataset_versions')->onDelete('set null');
                $table->foreign('user_id', 'qr_user_fk')->references('id')->on('users')->onDelete('set null');
                $table->index(['user_dataset_id', 'created_at'], 'qr_user_dataset_created_idx');
                $table->index(['dataset_id', 'created_at'], 'qr_dataset_created_idx');
            });
        }

        if (! Schema::hasTable('algorithm_configs')) {
            Schema::create('algorithm_configs', function (Blueprint $table): void {
                $table->id();
                $table->string('algorithm_key', 64)->unique();
                $table->string('problem_type', 32);
                $table->string('label', 120);
                $table->text('description');
                $table->text('strengths')->nullable();
                $table->text('weaknesses')->nullable();
                $table->text('when_not_to_use')->nullable();
                $table->string('expected_training_time', 120)->nullable();
                $table->longText('default_parameters')->nullable();
                $table->longText('parameter_schema')->nullable();
                $table->boolean('supports_probability')->default(false);
                $table->boolean('supports_feature_importance')->default(false);
                $table->boolean('is_optional')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['problem_type', 'is_active'], 'ac_problem_active_idx');
            });
        }
    }

    public function down(): void
    {
        // Forward-only by design. up() conditionally adopts tables that may
        // pre-date this migration, so ownership cannot be proven during a
        // rollback. Dropping them here could destroy unrelated production data.
        // Restore a verified database backup to reverse this module safely.
    }

    private function currentVersionForeignKeyExists(): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        return DB::selectOne(
            <<<'SQL'
                SELECT 1 AS constraint_exists
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND CONSTRAINT_NAME = ?
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                LIMIT 1
            SQL,
            ['ml_models', 'mm_current_version_fk'],
        ) !== null;
    }

    private function isDuplicateConstraintFailure(QueryException $exception): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        $driverCode = (int) ($exception->errorInfo[1] ?? 0);
        $message = strtolower($exception->getMessage());

        return $driverCode === 1826
            || ($driverCode === 1005 && str_contains($message, 'errno: 121'))
            || str_contains($message, 'duplicate foreign key constraint name');
    }
};
