<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentionally empty. DataSensei uses the file cache store and does
        // not provision Laravel's database cache infrastructure.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No database objects are created by this migration.
    }
};
