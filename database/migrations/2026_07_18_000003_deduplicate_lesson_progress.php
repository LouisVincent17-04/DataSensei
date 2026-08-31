<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lesson_user')) {
            return;
        }

        $duplicates = DB::table('lesson_user')
            ->select('user_id', 'lesson_id')
            ->groupBy('user_id', 'lesson_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $rows = DB::table('lesson_user')
                ->where('user_id', $duplicate->user_id)
                ->where('lesson_id', $duplicate->lesson_id)
                ->orderByDesc('is_completed')
                ->orderBy('id')
                ->get();

            $keep = $rows->first();
            if ($keep) {
                DB::table('lesson_user')->where('id', $keep->id)->update([
                    'is_completed' => $rows->contains(fn ($row) => (bool) $row->is_completed),
                    'updated_at' => $rows->max('updated_at') ?? now(),
                ]);
                DB::table('lesson_user')->whereIn('id', $rows->skip(1)->pluck('id'))->delete();
            }
        }

        Schema::table('lesson_user', function (Blueprint $table): void {
            $table->unique(['user_id', 'lesson_id'], 'lesson_user_user_lesson_unique');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('lesson_user')) {
            Schema::table('lesson_user', function (Blueprint $table): void {
                $table->dropUnique('lesson_user_user_lesson_unique');
            });
        }
    }
};
