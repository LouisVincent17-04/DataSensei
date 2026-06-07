<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anti_cheat_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('class_assignment_id')->nullable();
            $table->unsignedBigInteger('assignment_submission_id')->nullable();
            $table->unsignedBigInteger('assignment_question_id')->nullable();

            $table->enum('assessment_type', ['assignment'])->default('assignment');

            $table->string('event_type', 80);
            $table->string('severity', 30)->default('warning');
            $table->string('attempt_session_id', 120)->nullable();

            // Use longText instead of json for older MySQL/MariaDB compatibility.
            // Eloquent can still cast this as array/json in the model.
            $table->longText('details')->nullable();

            $table->timestamp('occurred_at')->nullable();

            $table->timestamps();

            $table->index('class_id');
            $table->index('class_assignment_id');
            $table->index('assignment_submission_id');
            $table->index('assignment_question_id');
            $table->index('assessment_type');
            $table->index('event_type');
            $table->index('severity');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anti_cheat_events');
    }
};