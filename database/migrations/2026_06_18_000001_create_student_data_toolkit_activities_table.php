<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_data_toolkit_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('dataset_key', 120);
            $table->string('activity_type', 60);
            $table->longText('selected_columns')->nullable();
            $table->longText('result_summary')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'dataset_key'], 'student_data_toolkit_user_dataset_idx');
            $table->index(['user_id', 'activity_type'], 'student_data_toolkit_user_activity_idx');
            $table->index('created_at', 'student_data_toolkit_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_data_toolkit_activities');
    }
};
