<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievement_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('achievement_key', 100)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('ACH');
            $table->string('badge_color', 50)->default('blue');
            $table->unsignedInteger('xp_reward')->default(0);
            $table->string('criteria_type', 80)->default('manual');
            $table->unsignedInteger('criteria_value')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('achievement_definition_id')->constrained('achievement_definitions')->cascadeOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->string('trigger_source', 80)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('progress_value', 8, 2)->default(0);
            $table->longText('details')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'achievement_definition_id'], 'user_achievement_unique');
            $table->index(['trigger_source', 'source_id']);
            $table->index('unlocked_at');
        });

        Schema::create('mission_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('mission_key', 100)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('period_type', ['daily', 'weekly'])->default('daily');
            $table->string('target_type', 80);
            $table->unsignedInteger('target_count')->default(1);
            $table->unsignedInteger('xp_reward')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['period_type', 'is_active']);
        });

        Schema::create('student_mission_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mission_definition_id')->constrained('mission_definitions')->cascadeOnDelete();
            $table->date('period_start');
            $table->unsignedInteger('progress_count')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('xp_awarded')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'mission_definition_id', 'period_start'], 'student_mission_period_unique');
            $table->index(['user_id', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_mission_progress');
        Schema::dropIfExists('mission_definitions');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievement_definitions');
    }
};
