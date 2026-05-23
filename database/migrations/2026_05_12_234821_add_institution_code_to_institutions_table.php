<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add as nullable first (no unique yet — backfill needs to run first)
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('institution_code', 6)->nullable()->after('slug');
        });

        // Step 2: Back-fill existing rows
        \DB::table('institutions')->get()->each(function ($institution) {
            do {
                $code = strtoupper(Str::random(6));
            } while (\DB::table('institutions')->where('institution_code', $code)->exists());

            \DB::table('institutions')
                ->where('id', $institution->id)
                ->update(['institution_code' => $code]);
        });

        // Step 3: Apply unique index via raw SQL (avoids ->change() which needs MySQL 5.7+)
        \DB::statement('ALTER TABLE institutions MODIFY institution_code VARCHAR(6) NOT NULL');
        \DB::statement('ALTER TABLE institutions ADD UNIQUE INDEX institutions_institution_code_unique (institution_code)');
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropUnique('institutions_institution_code_unique');
            $table->dropColumn('institution_code');
        });
    }
};