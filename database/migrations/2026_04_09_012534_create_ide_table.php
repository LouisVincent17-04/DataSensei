<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ide_workspaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('ide_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('ide_workspaces')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('ide_nodes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['file', 'folder']);
            $table->string('name');
            $table->longText('content')->nullable();   // null for folders
            $table->string('language')->default('python');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['workspace_id', 'parent_id']);
            $table->index(['user_id', 'type']);
        });

        Schema::create('ide_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('ide_nodes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->longText('output')->nullable();
            $table->longText('error')->nullable();
            $table->integer('exit_code')->default(0);
            $table->integer('execution_time_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ide_execution_logs');
        Schema::dropIfExists('ide_nodes');
        Schema::dropIfExists('ide_workspaces');
    }
};