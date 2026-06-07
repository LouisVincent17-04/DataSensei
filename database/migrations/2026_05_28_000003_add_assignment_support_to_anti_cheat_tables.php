<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No-op.
        //
        // Assignment support columns are already included directly in:
        // 2026_05_28_000002_create_anti_cheat_events_table.php
        //
        // This migration is intentionally empty to avoid Schema::hasColumn()
        // compatibility issues with older MySQL/MariaDB versions.
    }

    public function down(): void
    {
        // No-op.
    }
};