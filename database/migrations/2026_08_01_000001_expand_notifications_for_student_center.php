<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('type', 191);
                $table->string('dedupe_key', 191)->nullable();
                $table->string('title', 191)->nullable();
                $table->text('notification_text');
                $table->text('action_url')->nullable();
                $table->longText('data')->nullable();
                $table->boolean('is_read')->default(false);
                $table->dateTime('read_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'is_read', 'created_at'], 'notif_user_read_created_idx');
                $table->index('dedupe_key', 'notif_dedupe_idx');
                $table->foreign('user_id', 'ds_ntf_usr_fk_260801')->references('id')->on('users')->onDelete('cascade');
            });

            return;
        }

        $columns = [
            'dedupe_key' => fn (Blueprint $table) => $table->string('dedupe_key', 191)->nullable()->after('type'),
            'title' => fn (Blueprint $table) => $table->string('title', 191)->nullable()->after('dedupe_key'),
            'action_url' => fn (Blueprint $table) => $table->text('action_url')->nullable()->after('notification_text'),
            'data' => fn (Blueprint $table) => $table->longText('data')->nullable()->after('action_url'),
            'read_at' => fn (Blueprint $table) => $table->dateTime('read_at')->nullable()->after('is_read'),
        ];

        foreach ($columns as $column => $definition) {
            if (! $this->columnExists('notifications', $column)) {
                Schema::table('notifications', function (Blueprint $table) use ($definition): void {
                    $definition($table);
                });
            }
        }

        if (! $this->indexExists('notifications', 'notif_user_read_created_idx')) {
            Schema::table('notifications', function (Blueprint $table): void {
                $table->index(['user_id', 'is_read', 'created_at'], 'notif_user_read_created_idx');
            });
        }

        if (! $this->indexExists('notifications', 'notif_dedupe_idx')) {
            Schema::table('notifications', function (Blueprint $table): void {
                $table->index('dedupe_key', 'notif_dedupe_idx');
            });
        }

        DB::table('notifications')
            ->whereNull('title')
            ->update([
                'title' => DB::raw("CASE
                    WHEN type LIKE 'achievement_unlocked_%' THEN 'Achievement unlocked'
                    WHEN type LIKE 'mission_completed_%' THEN 'Mission completed'
                    WHEN type LIKE 'exceptional_unlock_%' THEN 'Challenge path unlocked'
                    ELSE 'Notification'
                END"),
            ]);

        DB::table('notifications')
            ->where('is_read', true)
            ->whereNull('read_at')
            ->update(['read_at' => DB::raw('updated_at')]);

        DB::table('notifications')
            ->whereNull('action_url')
            ->where(function ($query): void {
                $query->where('type', 'like', 'achievement_unlocked_%')
                    ->orWhere('type', 'like', 'mission_completed_%');
            })
            ->update(['action_url' => '/student/achievements']);

        DB::table('notifications')
            ->whereNull('action_url')
            ->where('type', 'like', 'exceptional_unlock_%')
            ->update(['action_url' => '/challenges']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        foreach (['notif_user_read_created_idx', 'notif_dedupe_idx'] as $index) {
            if ($this->indexExists('notifications', $index)) {
                Schema::table('notifications', function (Blueprint $table) use ($index): void {
                    $table->dropIndex($index);
                });
            }
        }

        foreach (['read_at', 'data', 'action_url', 'title', 'dedupe_key'] as $column) {
            if ($this->columnExists('notifications', $column)) {
                Schema::table('notifications', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        return DB::table('information_schema.columns')
            ->whereRaw('table_schema = database()')
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->exists();
    }

    private function indexExists(string $table, string $index): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            foreach (Schema::getIndexes($table) as $existingIndex) {
                if (($existingIndex['name'] ?? null) === $index) {
                    return true;
                }
            }

            return false;
        }

        return DB::table('information_schema.statistics')
            ->whereRaw('table_schema = database()')
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
