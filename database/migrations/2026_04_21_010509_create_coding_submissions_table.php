<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coding_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('coding_question_id')->constrained('coding_questions')->cascadeOnDelete();
            $table->text('code');
            $table->string('language', 20)->default('python');
            $table->enum('status', ['pending', 'running', 'passed', 'failed', 'error'])->default('pending');
            $table->integer('tests_passed')->default(0);
            $table->integer('tests_total')->default(0);
            $table->integer('xp_earned')->default(0);
            $table->integer('time_taken_seconds')->default(0);
            $table->longText('test_results')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coding_submissions');
    }
};