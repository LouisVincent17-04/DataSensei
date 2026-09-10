<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    public function up(): void
    {
        $this->repairTableOfSpecifications();
        $this->repairTableOfSpecificationRows();
        $this->repairAssessments();
        $this->repairAssessmentQuestions();
        $this->repairAssessmentSubmissions();
    }

    private function repairTableOfSpecifications(): void
    {
        if (! $this->tableExists('table_of_specifications')) {
            return;
        }

        if (! $this->columnExists('table_of_specifications', 'total_items')) {
            Schema::table('table_of_specifications', function (Blueprint $table): void {
                $table->unsignedInteger('total_items')->default(0)->after('module_no');
            });
        }

        if (! $this->tableExists('table_of_specification_rows')) {
            return;
        }

        DB::table('table_of_specification_rows')
            ->select('table_of_specification_id', DB::raw('SUM(item_count) AS assigned_items'))
            ->groupBy('table_of_specification_id')
            ->orderBy('table_of_specification_id')
            ->get()
            ->each(function ($row): void {
                DB::table('table_of_specifications')
                    ->where('id', $row->table_of_specification_id)
                    ->where('total_items', 0)
                    ->update(['total_items' => (int) $row->assigned_items]);
            });
    }

    private function repairTableOfSpecificationRows(): void
    {
        if (! $this->tableExists('table_of_specification_rows')) {
            return;
        }

        if (! $this->columnExists('table_of_specification_rows', 'subtopic_title')) {
            Schema::table('table_of_specification_rows', function (Blueprint $table): void {
                $table->string('subtopic_title', 191)->nullable()->after('topic_title');
            });
        }

        if (! $this->columnExists('table_of_specification_rows', 'learning_objective')) {
            Schema::table('table_of_specification_rows', function (Blueprint $table): void {
                $table->text('learning_objective')->nullable()->after('subtopic_title');
            });
        }

        if (! $this->columnExists('table_of_specification_rows', 'default_points')) {
            Schema::table('table_of_specification_rows', function (Blueprint $table): void {
                $table->unsignedInteger('default_points')->default(1)->after('item_count');
            });
        }
    }

    private function repairAssessments(): void
    {
        if (! $this->tableExists('assessments')) {
            return;
        }

        if (! $this->columnExists('assessments', 'draft_last_item')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->unsignedInteger('draft_last_item')->nullable()->after('status');
            });
        }

        if (! $this->columnExists('assessments', 'draft_saved_at')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->timestamp('draft_saved_at')->nullable()->after('draft_last_item');
            });
        }
    }

    private function repairAssessmentQuestions(): void
    {
        if (! $this->tableExists('assessment_questions')
            || $this->columnExists('assessment_questions', 'authoring_touched')) {
            return;
        }

        Schema::table('assessment_questions', function (Blueprint $table): void {
            $table->boolean('authoring_touched')->default(false)->after('is_required');
        });

        DB::table('assessment_questions')
            ->where(function ($query): void {
                $query->where('question_type', '<>', 'unconfigured')
                    ->orWhere('is_required', false)
                    ->orWhere(function ($nested): void {
                        $nested->whereNotNull('question_text')->where('question_text', '<>', '');
                    })
                    ->orWhere(function ($nested): void {
                        $nested->whereNotNull('image_path')->where('image_path', '<>', '');
                    })
                    ->orWhere(function ($nested): void {
                        $nested->whereNotNull('correct_answer')->where('correct_answer', '<>', '');
                    })
                    ->orWhere(function ($nested): void {
                        $nested->whereNotNull('answer_explanation')->where('answer_explanation', '<>', '');
                    })
                    ->orWhere(function ($nested): void {
                        $nested->whereNotNull('rubric_text')->where('rubric_text', '<>', '');
                    });
            })
            ->update(['authoring_touched' => true]);

        if ($this->tableExists('table_of_specification_rows')
            && $this->columnExists('table_of_specification_rows', 'default_points')) {
            DB::table('assessment_questions as question')
                ->leftJoin(
                    'table_of_specification_rows as tos_row',
                    'tos_row.id',
                    '=',
                    'question.table_of_specification_row_id'
                )
                ->where('question.authoring_touched', false)
                ->where(function ($query): void {
                    $query->where(function ($matched): void {
                        $matched->whereNotNull('tos_row.id')
                            ->whereColumn('question.points', '<>', 'tos_row.default_points');
                    })->orWhere(function ($unmatched): void {
                        $unmatched->whereNull('tos_row.id')
                            ->where('question.points', '<>', 1);
                    });
                })
                ->select('question.id')
                ->orderBy('question.id')
                ->chunkById(500, function ($questions): void {
                    DB::table('assessment_questions')
                        ->whereIn('id', $questions->pluck('id'))
                        ->update(['authoring_touched' => true]);
                }, 'question.id', 'id');
        }

        if ($this->tableExists('assessment_question_options')) {
            DB::table('assessment_questions')
                ->whereExists(function ($query): void {
                    $query->select(DB::raw(1))
                        ->from('assessment_question_options')
                        ->whereColumn(
                            'assessment_question_options.assessment_question_id',
                            'assessment_questions.id'
                        );
                })
                ->update(['authoring_touched' => true]);
        }
    }

    private function repairAssessmentSubmissions(): void
    {
        if (! $this->tableExists('assessment_submissions')) {
            return;
        }

        $columns = [
            'draft_answers' => function (Blueprint $table): void {
                $table->longText('draft_answers')->nullable();
            },
            'draft_version' => function (Blueprint $table): void {
                $table->unsignedBigInteger('draft_version')->default(0);
            },
            'draft_saved_at' => function (Blueprint $table): void {
                $table->dateTime('draft_saved_at')->nullable();
            },
            'timed_out_at' => function (Blueprint $table): void {
                $table->dateTime('timed_out_at')->nullable();
            },
        ];

        foreach ($columns as $column => $definition) {
            if ($this->columnExists('assessment_submissions', $column)) {
                continue;
            }

            Schema::table('assessment_submissions', $definition);
        }
    }

    public function down(): void
    {
        // Forward-only repair: rollback must not remove quiz drafts, answers,
        // timeout evidence, or authoring-state data from an existing install.
    }
};
