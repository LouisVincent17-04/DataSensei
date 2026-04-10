<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Gamification stats
            $table->integer('xp')->default(0);
            $table->integer('streak')->default(0);
            $table->date('last_activity')->nullable(); // Used to calculate if they keep their streak
            
            // Profile details
            $table->text('bio')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'streak', 'last_activity', 'bio']);
        });
    }

};
