<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentionally empty. DataSensei uses the synchronous queue driver
        // and does not provision Laravel's database queue infrastructure.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No database objects are created by this migration.
    }
};
