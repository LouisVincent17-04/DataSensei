<?php

use App\Support\AssignmentToAssessmentMerge;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 11: the separate Assignment feature is retired.
 *
 * Every class assignment is first converted into an assessment (questions,
 * submissions, answers, grades, feedback and anti-cheat records included; see
 * App\Support\AssignmentToAssessmentMerge), the active admin assignment
 * library is preserved as the shared Question Bank pool, and only then are
 * the assignment tables removed.
 *
 * Archival form for anything that cannot be converted: the table
 * legacy_assignment_archive. A class assignment can only be unconvertible
 * when its library item is missing (possible only if rows were imported with
 * foreign-key checks off), since every old question type (mcq, fill_blank)
 * has an assessment equivalent. Such an assignment is stored there verbatim,
 * one row per record (the class_assignments row, each submission, each
 * answer), as a JSON-encoded copy of the original columns in `payload`,
 * with `reason` explaining why it was not converted. Its anti-cheat events
 * stay in anti_cheat_events with their original ids. If the archive table
 * cannot be written, the assignment tables are kept instead of dropped.
 *
 * Rerunning is safe: converted rows are remembered by their legacy ids, and
 * once the tables are gone the migration does nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Created on every install so the schema is the same everywhere.
        // Plain columns only (longText payload, no JSON type): MySQL 5.5.
        if (! Schema::hasTable('legacy_assignment_archive')) {
            Schema::create('legacy_assignment_archive', function (Blueprint $table): void {
                $table->id();
                $table->string('source_table', 64);
                $table->unsignedBigInteger('source_id');
                $table->unsignedBigInteger('class_assignment_id')->nullable()->index();
                $table->string('reason', 255);
                $table->longText('payload');
                $table->timestamp('archived_at')->nullable();
                $table->timestamps();
                $table->unique(['source_table', 'source_id'], 'legacy_assignment_archive_source_unique');
            });
        }

        if (! Schema::hasTable('class_assignments')) {
            return;
        }

        $merge = new AssignmentToAssessmentMerge();
        $merge->convert();

        if (! $merge->fullyConverted()) {
            // Something was neither converted nor archived: keep the original
            // tables rather than drop them; the next run picks it up.
            return;
        }

        // Children first, parents last, so every foreign key stays satisfied.
        Schema::dropIfExists('assignment_submission_answers');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignment_blank_answers');
        Schema::dropIfExists('assignment_question_options');
        Schema::dropIfExists('assignment_questions');
        Schema::dropIfExists('class_assignments');
        Schema::dropIfExists('assignment_library_items');
    }

    public function down(): void
    {
        // The converted records live on as assessments; the original tables
        // are not recreated.
    }
};
