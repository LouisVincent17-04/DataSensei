<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 7, task 2: no University/Organization-only rewards.
 *
 * The public DataSensei Modules no longer have a part reserved for
 * University/Organization learners, so no achievement is left for one. The
 * only University-only achievements are the older "University Path Clear"
 * and "University Coding Path Clear" (finishing the University Student
 * challenge level, which needs a class). They were switched off by the
 * 2026_09_28_000001 migration because class work gives no XP.
 *
 * Each is removed when no student has earned it. One that was already earned
 * is kept, switched off, so the learner's record stays (the same rule admins
 * follow on the Gamification page). The University Student level itself and
 * every class feature are untouched. Running it again changes nothing. Plain
 * SELECT/UPDATE/DELETE statements, so it runs on MySQL 5.5.
 */
return new class extends Migration
{
    private const UNIVERSITY_ONLY_KEYS = [
        'path_university_student_complete',
        'coding_path_university_student_complete',
    ];

    public function up(): void
    {
        if (! $this->tableExists('achievement_definitions')) {
            return;
        }

        $definitions = DB::table('achievement_definitions')
            ->whereIn('achievement_key', self::UNIVERSITY_ONLY_KEYS)
            ->get(['id', 'is_active']);

        $hasUnlocks = $this->tableExists('user_achievements');

        foreach ($definitions as $definition) {
            $earned = $hasUnlocks
                && DB::table('user_achievements')->where('achievement_definition_id', $definition->id)->exists();

            if (! $earned) {
                DB::table('achievement_definitions')->where('id', $definition->id)->delete();

                continue;
            }

            if ((bool) $definition->is_active) {
                DB::table('achievement_definitions')
                    ->where('id', $definition->id)
                    ->update(['is_active' => false, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Removed achievements are not recreated.
    }

    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
            [$table]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
