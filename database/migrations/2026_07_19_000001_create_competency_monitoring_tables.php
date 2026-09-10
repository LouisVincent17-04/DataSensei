<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * This migration is intentionally resumable. On older MySQL versions,
         * the original migration could create the competencies table and then
         * fail when it reached the unsupported native JSON column. Checking
         * each table separately allows `php artisan migrate` to continue safely
         * without dropping data created during the first attempt.
         */
        if (! Schema::hasTable('competencies')) {
            Schema::create('competencies', function (Blueprint $table): void {
                $table->id();
                $table->string('key', 80)->unique();
                $table->string('name', 120);
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'sort_order'], 'competencies_active_sort_idx');
            });
        }

        if (! Schema::hasTable('student_competency_snapshots')) {
            Schema::create('student_competency_snapshots', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('student_id');
                $table->foreignId('class_id');
                $table->foreignId('competency_id');
                $table->decimal('percentage', 6, 2)->default(0);
                $table->unsignedInteger('evidence_count')->default(0);
                $table->string('level', 40)->default('Not Assessed');

                /*
                 * MySQL versions before 5.7.8 do not support the JSON column
                 * type. Laravel's `array` model cast still serializes and
                 * deserializes valid JSON correctly when stored as LONGTEXT.
                 */
                $table->longText('source_breakdown')->nullable();
                $table->timestamp('last_evidence_at')->nullable();
                $table->timestamp('calculated_at')->nullable();
                $table->timestamps();

                $table->unique(
                    ['student_id', 'class_id', 'competency_id'],
                    'student_class_competency_snapshot_uq'
                );
                $table->index(['class_id', 'competency_id', 'percentage'], 'competency_class_score_idx');
                $table->index(['student_id', 'class_id', 'level'], 'competency_student_level_idx');

                $table->foreign('student_id', 'scs_student_fk')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
                $table->foreign('class_id', 'scs_class_fk')
                    ->references('id')
                    ->on('classes')
                    ->cascadeOnDelete();
                $table->foreign('competency_id', 'scs_competency_fk')
                    ->references('id')
                    ->on('competencies')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('student_competency_trends')) {
            Schema::create('student_competency_trends', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('student_id');
                $table->foreignId('class_id');
                $table->foreignId('competency_id');
                $table->decimal('percentage', 6, 2)->default(0);
                $table->unsignedInteger('evidence_count')->default(0);
                $table->date('recorded_on');
                $table->timestamps();

                $table->unique(
                    ['student_id', 'class_id', 'competency_id', 'recorded_on'],
                    'student_class_competency_trend_uq'
                );
                $table->index(['class_id', 'competency_id', 'recorded_on'], 'competency_class_trend_idx');
                $table->index(['student_id', 'class_id', 'recorded_on'], 'competency_student_trend_idx');

                $table->foreign('student_id', 'sct_student_fk')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
                $table->foreign('class_id', 'sct_class_fk')
                    ->references('id')
                    ->on('classes')
                    ->cascadeOnDelete();
                $table->foreign('competency_id', 'sct_competency_fk')
                    ->references('id')
                    ->on('competencies')
                    ->cascadeOnDelete();
            });
        }

        $now = now();
        $competencies = [
            [
                'key' => 'python_programming',
                'name' => 'Python Programming',
                'description' => 'Python syntax, algorithms, debugging, data structures, and executable problem solving.',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'key' => 'statistics',
                'name' => 'Statistics',
                'description' => 'Descriptive and inferential statistics, probability, correlation, regression, and experimental analysis.',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'key' => 'sql',
                'name' => 'SQL',
                'description' => 'Relational databases, SQL querying, joins, aggregation, and database management.',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'key' => 'data_visualization',
                'name' => 'Data Visualization',
                'description' => 'Charts, plots, dashboards, and clear visual communication of analytical findings.',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'key' => 'data_analysis',
                'name' => 'Data Analysis',
                'description' => 'Interpreting datasets, selecting analytical methods, and translating results into conclusions.',
                'sort_order' => 50,
                'is_active' => true,
            ],
            [
                'key' => 'eda',
                'name' => 'Exploratory Data Analysis (EDA)',
                'description' => 'Dataset exploration, cleaning, descriptive summaries, patterns, relationships, and anomaly discovery.',
                'sort_order' => 60,
                'is_active' => true,
            ],
            [
                'key' => 'machine_learning',
                'name' => 'Machine Learning',
                'description' => 'Supervised and unsupervised learning, model evaluation, optimization, and intelligent prediction.',
                'sort_order' => 70,
                'is_active' => true,
            ],
        ];

        foreach ($competencies as $competency) {
            $existingCreatedAt = DB::table('competencies')
                ->where('key', $competency['key'])
                ->value('created_at');

            DB::table('competencies')->updateOrInsert(
                ['key' => $competency['key']],
                array_merge($competency, [
                    'updated_at' => $now,
                    'created_at' => $existingCreatedAt ?? $now,
                ])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_competency_trends');
        Schema::dropIfExists('student_competency_snapshots');
        Schema::dropIfExists('competencies');
    }
};
