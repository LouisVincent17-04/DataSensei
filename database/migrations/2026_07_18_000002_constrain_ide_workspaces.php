<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ide_workspaces') || ! Schema::hasTable('ide_nodes')) {
            return;
        }

        $duplicateUsers = DB::table('ide_workspaces')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        foreach ($duplicateUsers as $userId) {
            $workspaces = DB::table('ide_workspaces')
                ->where('user_id', $userId)
                ->orderBy('id')
                ->get();

            $destination = $workspaces->first();

            foreach ($workspaces->skip(1) as $source) {
                $rootNodes = DB::table('ide_nodes')
                    ->where('workspace_id', $source->id)
                    ->whereNull('parent_id')
                    ->orderBy('id')
                    ->get();

                foreach ($rootNodes as $rootNode) {
                    $candidate = (string) $rootNode->name;
                    $suffix = 1;

                    while (DB::table('ide_nodes')
                        ->where('workspace_id', $destination->id)
                        ->whereNull('parent_id')
                        ->where('name', $candidate)
                        ->exists()) {
                        $tail = " (imported {$suffix})";
                        $candidate = mb_substr((string) $rootNode->name, 0, 120 - mb_strlen($tail)) . $tail;
                        $suffix++;
                    }

                    if ($candidate !== $rootNode->name) {
                        DB::table('ide_nodes')->where('id', $rootNode->id)->update(['name' => $candidate]);
                    }
                }

                DB::table('ide_nodes')
                    ->where('workspace_id', $source->id)
                    ->update(['workspace_id' => $destination->id]);

                DB::table('ide_workspaces')->where('id', $source->id)->delete();
            }
        }

        Schema::table('ide_workspaces', function (Blueprint $table): void {
            $table->unique('user_id', 'ide_workspaces_user_unique');
        });

        Schema::table('ide_nodes', function (Blueprint $table): void {
            $table->string('language')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('ide_workspaces')) {
            Schema::table('ide_workspaces', function (Blueprint $table): void {
                $table->dropUnique('ide_workspaces_user_unique');
            });
        }

        if (Schema::hasTable('ide_nodes')) {
            DB::table('ide_nodes')
                ->whereNull('language')
                ->update(['language' => 'text']);

            Schema::table('ide_nodes', function (Blueprint $table): void {
                $table->string('language')->nullable(false)->default('python')->change();
            });
        }
    }
};
