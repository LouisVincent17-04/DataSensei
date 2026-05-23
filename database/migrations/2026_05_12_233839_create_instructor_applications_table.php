<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_applications', function (Blueprint $table) {
            $table->id();

            // The instructor who is applying
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // The institution they're applying to
            $table->foreignId('institution_id')
                  ->constrained('institutions')
                  ->cascadeOnDelete();

            // The code the instructor typed in (for audit trail)
            $table->string('entered_code', 6);

            // Application lifecycle
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Who reviewed it (nullable — null until reviewed)
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            // An instructor may only have ONE pending/approved application per institution at a time
            $table->unique(['user_id', 'institution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_applications');
    }
};