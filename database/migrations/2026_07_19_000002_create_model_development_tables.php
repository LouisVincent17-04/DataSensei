<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_development_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('dataset_key', 120);
            $table->string('task_type', 24);
            $table->string('algorithm', 64);
            $table->longText('feature_columns');
            $table->string('target_column', 120)->nullable();
            $table->decimal('test_size', 4, 2)->nullable();
            $table->unsignedSmallInteger('random_seed')->default(42);
            $table->longText('preprocessing')->nullable();
            $table->longText('parameters')->nullable();
            $table->string('status', 24)->default('processing');
            $table->longText('metrics')->nullable();
            $table->longText('visualization_data')->nullable();
            $table->longText('training_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'model_runs_user_created_idx');
            $table->index(['class_id', 'task_type'], 'model_runs_class_task_idx');
            $table->index(['dataset_key', 'algorithm'], 'model_runs_dataset_algorithm_idx');
            $table->index('status', 'model_runs_status_idx');
        });

        Schema::create('model_predictions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('model_development_run_id')
                ->constrained('model_development_runs')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('input_values');
            $table->text('predicted_value');
            $table->longText('probabilities')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->index(['model_development_run_id', 'created_at'], 'model_predictions_run_created_idx');
            $table->index(['user_id', 'created_at'], 'model_predictions_user_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_predictions');
        Schema::dropIfExists('model_development_runs');
    }
};
