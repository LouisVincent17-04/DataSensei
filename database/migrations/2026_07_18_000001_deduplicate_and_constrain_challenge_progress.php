<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('challenge_user')) {
            return;
        }

        $duplicates = DB::table('challenge_user')
            ->select('user_id', 'challenge_id')
            ->groupBy('user_id', 'challenge_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('user_id')
            ->get();

        foreach ($duplicates as $duplicate) {
            $rows = DB::table('challenge_user')
                ->where('user_id', $duplicate->user_id)
                ->where('challenge_id', $duplicate->challenge_id)
                ->orderByDesc('score')
                ->orderBy('time_taken_seconds')
                ->orderByDesc('xp_awarded')
                ->orderBy('id')
                ->get();

            if ($rows->count() > 1) {
                DB::table('challenge_user')
                    ->whereIn('id', $rows->skip(1)->pluck('id'))
                    ->delete();
            }
        }

        Schema::table('challenge_user', function (Blueprint $table): void {
            $table->unique(['user_id', 'challenge_id'], 'challenge_user_user_challenge_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('challenge_user')) {
            return;
        }

        Schema::table('challenge_user', function (Blueprint $table): void {
            $table->dropUnique('challenge_user_user_challenge_unique');
        });
    }
};
