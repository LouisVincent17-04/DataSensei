<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When a user retakes a challenge we do NOT delete old submissions —
     * that would corrupt XP history and analytics. Instead we mark them
     * as voided = true so all game-logic queries (alreadyPassed, previousBest,
     * priorSubmissions displayed in the UI) skip them.
     *
     * XP already awarded to the user is intentionally kept (they earned it).
     */
    public function up(): void
    {
        Schema::table('coding_submissions', function (Blueprint $table) {
            $table->boolean('voided')->default(false)->after('error_message');

            // Composite index so WHERE voided = 0 is fast in all hot queries
            $table->index(['user_id', 'coding_question_id', 'voided'], 'cs_user_question_voided');
        });
    }

    public function down(): void
    {
        Schema::table('coding_submissions', function (Blueprint $table) {
            $table->dropIndex('cs_user_question_voided');
            $table->dropColumn('voided');
        });
    }
};