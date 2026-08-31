<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challenges', function (Blueprint $table): void {
            $table->string('content_code', 64)->nullable()->after('challenge_category_id');
            $table->unsignedInteger('version_no')->default(1)->after('is_coding_challenge');
            $table->string('version_name', 100)->default('Version 1')->after('version_no');
            $table->string('version_code', 64)->nullable()->after('version_name');
            $table->boolean('is_active')->default(true)->after('version_code');
        });

        DB::table('challenges')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->each(function ($challenge): void {
                DB::table('challenges')
                    ->where('id', $challenge->id)
                    ->update([
                        'content_code' => sprintf('CH-%06d', $challenge->id),
                        'version_code' => 'V1',
                    ]);
            });

        Schema::table('challenges', function (Blueprint $table): void {
            $table->unique(['content_code', 'version_code'], 'challenges_content_version_unique');
            $table->index(['is_coding_challenge', 'is_active'], 'challenges_type_active_index');
            $table->index(['challenge_category_id', 'is_active'], 'challenges_category_active_index');
        });

        Schema::table('challenge_questions', function (Blueprint $table): void {
            $table->unsignedInteger('order_index')->default(0)->after('question_text');
            $table->index(['challenge_id', 'order_index'], 'challenge_questions_order_index');
        });

        Schema::table('challenge_options', function (Blueprint $table): void {
            $table->unsignedInteger('order_index')->default(0)->after('is_correct');
            $table->index(['challenge_question_id', 'order_index'], 'challenge_options_order_index');
        });

        $this->backfillOrderIndexes();
    }

    public function down(): void
    {
        Schema::table('challenge_options', function (Blueprint $table): void {
            $table->dropIndex('challenge_options_order_index');
            $table->dropColumn('order_index');
        });

        Schema::table('challenge_questions', function (Blueprint $table): void {
            $table->dropIndex('challenge_questions_order_index');
            $table->dropColumn('order_index');
        });

        Schema::table('challenges', function (Blueprint $table): void {
            $table->dropUnique('challenges_content_version_unique');
            $table->dropIndex('challenges_type_active_index');
            $table->dropIndex('challenges_category_active_index');
            $table->dropColumn([
                'content_code',
                'version_no',
                'version_name',
                'version_code',
                'is_active',
            ]);
        });
    }

    private function backfillOrderIndexes(): void
    {
        // Existing seeders insert questions and options in presentation order.
        // Using the primary key as the initial order preserves that sequence
        // without issuing thousands of row-by-row updates on large seed sets.
        DB::table('challenge_questions')->update([
            'order_index' => DB::raw('id'),
        ]);

        DB::table('challenge_options')->update([
            'order_index' => DB::raw('id'),
        ]);
    }
};
