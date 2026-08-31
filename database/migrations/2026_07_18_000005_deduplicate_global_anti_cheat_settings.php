<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('anti_cheat_settings')) {
            return;
        }

        // MySQL permits multiple NULL values in a composite unique index, so
        // historical global settings (class_id = NULL) can contain duplicates.
        // Keep the newest record in each instructor/type scope.
        $duplicates = DB::table('anti_cheat_settings')
            ->whereNull('class_id')
            ->select(
                'instructor_id',
                'assessment_type',
                DB::raw('MAX(id) AS keep_id'),
                DB::raw('COUNT(*) AS duplicate_count'),
            )
            ->groupBy('instructor_id', 'assessment_type')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('anti_cheat_settings')
                ->where('instructor_id', $duplicate->instructor_id)
                ->where('assessment_type', $duplicate->assessment_type)
                ->whereNull('class_id')
                ->where('id', '<>', $duplicate->keep_id)
                ->delete();
        }
    }

    public function down(): void
    {
        // Duplicate settings intentionally cannot be reconstructed.
    }
};
