<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('achievement_definitions')) {
            return;
        }

        $codes = [
            'first_mcq_pass' => 'FCP',
            'perfect_run' => 'PR',
            'fast_solver' => 'FS',
            'first_coding_pass' => 'FCA',
            'coding_clean_sweep' => 'CS',
            'coding_challenge_finisher' => 'CCF',
            'assignment_finisher' => 'AF',
            'perfect_assignment' => 'PA',
            'clean_attempt' => 'CA',
            'consistency_master' => 'CM',
            'path_newbie_complete' => 'NPC',
            'path_university_student_complete' => 'UPC',
            'path_intermediate_complete' => 'IPC',
            'path_advanced_complete' => 'APC',
            'path_professional_complete' => 'PPC',
            'coding_path_newbie_complete' => 'NCP',
            'coding_path_university_student_complete' => 'UCP',
            'coding_path_intermediate_complete' => 'ICP',
            'coding_path_advanced_complete' => 'ACP',
            'coding_path_professional_complete' => 'PCP',
        ];

        foreach ($codes as $key => $code) {
            DB::table('achievement_definitions')
                ->where('achievement_key', $key)
                ->update(['icon' => $code, 'updated_at' => now()]);
        }

        DB::table('achievement_definitions')
            ->whereNull('icon')
            ->orWhere('icon', '')
            ->update(['icon' => 'ACH', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // No rollback is needed. This only replaces decorative emoji icons with professional short codes.
    }
};
