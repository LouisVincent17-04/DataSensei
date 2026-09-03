<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SESSION_UUID_INDEX = 'anti_cheat_event_session_uuid_uq';

    private const FOCUS_LOOKUP_INDEX = 'anti_cheat_event_focus_lookup_idx';

    public function up(): void
    {
        if (! $this->tableExists('anti_cheat_events')) {
            return;
        }

        if (! $this->columnExists('anti_cheat_events', 'event_uuid')) {
            Schema::table('anti_cheat_events', function (Blueprint $table): void {
                // Existing events remain valid; every newly accepted browser
                // event receives a UUID in AntiCheatEventController.
                $table->string('event_uuid', 36)->nullable();
            });
        }

        if (! $this->indexExists('anti_cheat_events', self::SESSION_UUID_INDEX)) {
            Schema::table('anti_cheat_events', function (Blueprint $table): void {
                $table->unique(
                    ['attempt_session_id', 'event_uuid'],
                    self::SESSION_UUID_INDEX
                );
            });
        }

        if (! $this->indexExists('anti_cheat_events', self::FOCUS_LOOKUP_INDEX)) {
            Schema::table('anti_cheat_events', function (Blueprint $table): void {
                $table->index(
                    ['assignment_submission_id', 'attempt_session_id', 'occurred_at'],
                    self::FOCUS_LOOKUP_INDEX
                );
            });
        }
    }

    public function down(): void
    {
        // Forward-only by design: event UUIDs are integrity evidence and must
        // not be destroyed by a rollback after this migration has been used.
    }

    private function indexExists(string $table, string $indexName): bool
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $result = DB::selectOne(
                'SELECT COUNT(*) AS aggregate
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND INDEX_NAME = ?',
                [$table, $indexName]
            );

            return (int) ($result->aggregate ?? 0) > 0;
        }

        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? null) === $indexName) {
                return true;
            }
        }

        return false;
    }

    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?',
            [$table]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
