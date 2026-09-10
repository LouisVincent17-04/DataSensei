<?php

namespace Tests\Unit;

use App\Mail\PasswordResetOtpMail;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class PasswordResetOtpMailTest extends TestCase
{
    public function test_password_reset_mail_exposes_the_expected_queue_and_template_contract(): void
    {
        config()->set('password_otp.expires_minutes', 7);
        $expiresAt = CarbonImmutable::parse('2026-09-09 12:07:00');
        $mail = new PasswordResetOtpMail(
            45,
            'Louis Vincent',
            '483921',
            $expiresAt
        );

        $this->assertSame('Your DataSensei password reset code', $mail->envelope()->subject);
        $this->assertSame('emails.auth.password-reset-otp', $mail->content()->view);
        $this->assertSame('Louis Vincent', $mail->content()->with['recipientName']);
        $this->assertSame('483921', $mail->content()->with['otp']);
        $this->assertSame(7, $mail->content()->with['expiresMinutes']);
        $this->assertSame($expiresAt, $mail->retryUntil());
        $this->assertSame(3, $mail->tries);
        $this->assertSame(30, $mail->timeout);
        $this->assertSame([10, 30, 60], $mail->backoff);
    }
}
