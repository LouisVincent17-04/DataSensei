<?php

namespace App\Mail;

use App\Models\PasswordResetOtp;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PasswordResetOtpMail extends Mailable implements ShouldQueue, ShouldQueueAfterCommit, ShouldBeEncrypted
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly int $otpRecordId,
        public readonly string $recipientName,
        public readonly string $otp,
        public readonly DateTimeInterface $expiresAt,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your DataSensei password reset code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.password-reset-otp',
            with: [
                'recipientName' => $this->recipientName,
                'otp' => $this->otp,
                'expiresAt' => $this->expiresAt,
                'expiresMinutes' => (int) config('password_otp.expires_minutes', 5),
            ],
        );
    }

    public function retryUntil(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function failed(Throwable $exception): void
    {
        PasswordResetOtp::query()
            ->whereKey($this->otpRecordId)
            ->whereNull('consumed_at')
            ->update([
                'consumed_at' => now(),
                'reset_token_hash' => null,
                'reset_token_expires_at' => null,
                'updated_at' => now(),
            ]);

        Log::error('Password reset OTP email delivery failed.', [
            'otp_record_id' => $this->otpRecordId,
            'exception' => $exception::class,
        ]);
    }
}
