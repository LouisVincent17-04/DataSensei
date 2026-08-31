<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['student_performance_snapshots', 'student_performance_clusters'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('class_id')
                ->whereNotIn('class_id', DB::table('classes')->select('id'))
                ->update(['class_id' => null]);

            $duplicates = DB::table($table)
                ->whereNotNull('class_id')
                ->select('student_id', 'class_id')
                ->groupBy('student_id', 'class_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicates as $duplicate) {
                $ids = DB::table($table)
                    ->where('student_id', $duplicate->student_id)
                    ->where('class_id', $duplicate->class_id)
                    ->orderByDesc('id')
                    ->pluck('id');

                DB::table($table)->whereIn('id', $ids->skip(1))->delete();
            }

            // MySQL composite unique indexes allow repeated NULL values, so
            // learner-wide snapshots need an explicit cleanup too. Application
            // writes are serialized with a user-row lock after this migration.
            $globalDuplicates = DB::table($table)
                ->whereNull('class_id')
                ->select('student_id')
                ->groupBy('student_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('student_id');

            foreach ($globalDuplicates as $studentId) {
                $ids = DB::table($table)
                    ->where('student_id', $studentId)
                    ->whereNull('class_id')
                    ->orderByDesc('id')
                    ->pluck('id');

                DB::table($table)->whereIn('id', $ids->skip(1))->delete();
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->unique(
                    ['student_id', 'class_id'],
                    $table === 'student_performance_snapshots'
                        ? 'performance_snapshot_student_class_uq'
                        : 'performance_cluster_student_class_uq'
                );

                $blueprint->foreign('class_id', $table === 'student_performance_snapshots'
                    ? 'performance_snapshot_class_fk'
                    : 'performance_cluster_class_fk')
                    ->references('id')
                    ->on('classes')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['student_performance_snapshots', 'student_performance_clusters'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropForeign($table === 'student_performance_snapshots'
                    ? 'performance_snapshot_class_fk'
                    : 'performance_cluster_class_fk');
                $blueprint->dropUnique($table === 'student_performance_snapshots'
                    ? 'performance_snapshot_student_class_uq'
                    : 'performance_cluster_student_class_uq');
            });
        }
    }
};
