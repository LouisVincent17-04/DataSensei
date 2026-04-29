<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coding_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('challenges')->cascadeOnDelete();
            $table->text('problem_description');
            $table->string('language', 20)->default('python');
            $table->text('starter_code')->nullable();
            $table->integer('order_index')->default(0);
            $table->integer('time_limit_seconds')->default(1800); // 30 min default
            $table->integer('base_xp')->default(100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coding_questions');
    }
};