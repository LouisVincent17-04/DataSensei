<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anti_cheat_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->cascadeOnDelete();
            $table->enum('assessment_type', ['assignment'])->default('assignment');

            $table->boolean('enabled')->default(true);

            // Navigation / focus controls
            $table->boolean('allow_tab_switch')->default(false);
            $table->unsignedInteger('max_tab_switches')->default(2);
            $table->boolean('block_on_tab_limit')->default(true);

            // Browser / environment controls
            $table->boolean('require_fullscreen')->default(false);
            $table->boolean('detect_dual_monitor')->default(true);
            $table->boolean('block_dual_monitor')->default(false);

            // Clipboard / source controls
            $table->boolean('allow_copy')->default(true);
            $table->boolean('allow_paste')->default(false);
            $table->boolean('block_external_paste')->default(true);
            $table->boolean('allow_right_click')->default(false);
            $table->boolean('allow_devtools_shortcuts')->default(false);

            // Response behavior
            $table->boolean('show_warnings')->default(true);
            $table->boolean('auto_submit_mcq_on_violation')->default(false);
            $table->boolean('lock_screen_on_violation')->default(true);

            $table->timestamps();

            $table->unique(['instructor_id', 'class_id', 'assessment_type'], 'anti_cheat_setting_unique');
            $table->index(['class_id', 'assessment_type'], 'anti_cheat_setting_class_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anti_cheat_settings');
    }
};
