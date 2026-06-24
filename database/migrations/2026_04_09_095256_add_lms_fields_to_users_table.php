<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No-op.
        // The current create_users_table migration already contains xp, streak,
        // last_activity, and bio. Keeping this file prevents duplicate column
        // errors on fresh migration while preserving migration order/history.
    }

    public function down(): void
    {
        // No-op. Do not drop profile/gamification columns that belong to users.
    }
};
