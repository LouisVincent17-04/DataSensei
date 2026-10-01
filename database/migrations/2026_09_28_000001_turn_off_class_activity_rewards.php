<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 5, task 1: class work gives no XP.
 *
 * Achievements and missions that counted class work (assignments,
 * assessments, the University Student level) are switched off, so no XP
 * reward can come from them. The rows are kept, only is_active changes; an
 * admin can still see and edit them. Running it again changes nothing.
 * Plain UPDATE statements, so it runs on MySQL 5.5.
 */
return new class extends Migration
{
    private const CLASS_CRITERIA = [
        'assignment_submissions',
        'assignment_perfect',
        'clean_assignment',
        'assessment_submissions',
        'assignment_submit',
    ];

    /**
     * Keys of the older keyed class-work achievements (GamificationSeeder):
     * assignments, and finishing the University Student level, which needs
     * a class.
     */
    private const CLASS_ACHIEVEMENT_KEYS = [
        'assignment_finisher',
        'perfect_assignment',
        'clean_attempt',
        'path_university_student_complete',
        'coding_path_university_student_complete',
    ];

    private const CLASS_MISSION_TYPES = [
        'assignment_submissions',
        'assessment_submissions',
        'submit_assignment',
    ];

    public function up(): void
    {
        if ($this->tableExists('achievement_definitions')) {
            DB::table('achievement_definitions')
                ->where(function ($query): void {
                    $query->whereIn('criteria_type', self::CLASS_CRITERIA)
                        ->orWhereIn('achievement_key', self::CLASS_ACHIEVEMENT_KEYS);
                })
                ->where('is_active', true)
                ->update(['is_active' => false, 'updated_at' => now()]);
        }

        if ($this->tableExists('mission_definitions')) {
            DB::table('mission_definitions')
                ->whereIn('target_type', self::CLASS_MISSION_TYPES)
                ->where('is_active', true)
                ->update(['is_active' => false, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // The rewards stay off: class work gives no XP.
    }

    /**
     * A plain information_schema query, like the earlier migrations, so it
     * also works on MySQL 5.5.
     */
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
};
