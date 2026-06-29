
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');

            $table->string('purpose', 32)
                ->default('password_reset');

            /*
             * The OTP itself must never be stored in plain text.
             * This column stores only its secure hash.
             */
            $table->string('otp_hash', 191);

            $table->unsignedTinyInteger('attempts')
                ->default(0);

            $table->unsignedTinyInteger('max_attempts')
                ->default(5);

            /*
             * DATETIME is used instead of TIMESTAMP for compatibility
             * with MySQL 5.5, which has restrictive TIMESTAMP defaults.
             */
            $table->dateTime('expires_at');
            $table->dateTime('sent_at');

            $table->dateTime('verified_at')
                ->nullable();

            $table->dateTime('consumed_at')
                ->nullable();

            /*
             * Stores only a SHA-256 hash of the temporary reset token.
             */
            $table->char('reset_token_hash', 64)
                ->nullable();

            $table->dateTime('reset_token_expires_at')
                ->nullable();

            /*
             * Privacy-preserving hashes used for security logging
             * and rate-limit investigation.
             */
            $table->char('request_ip_hash', 64)
                ->nullable();

            $table->char('user_agent_hash', 64)
                ->nullable();

            /*
             * Do not use $table->timestamps() here because it creates
             * TIMESTAMP columns that may fail on older MySQL versions.
             */
            $table->dateTime('created_at')
                ->nullable();

            $table->dateTime('updated_at')
                ->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(
                ['user_id', 'purpose'],
                'password_reset_otps_user_purpose_index'
            );

            $table->index(
                'expires_at',
                'password_reset_otps_expires_at_index'
            );

            $table->index(
                'sent_at',
                'password_reset_otps_sent_at_index'
            );

            $table->index(
                'consumed_at',
                'password_reset_otps_consumed_at_index'
            );

            $table->unique(
                'reset_token_hash',
                'password_reset_otps_token_hash_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};
