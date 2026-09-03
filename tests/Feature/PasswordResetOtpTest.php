<?php

namespace Tests\Feature;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Support\PasswordOtpConfiguration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('cache.default', 'array');
        config()->set('session.driver', 'array');
        config()->set('queue.default', 'sync');
        config()->set('mail.default', 'array');
        config()->set('password_otp.cooldown_seconds', 60);
        config()->set('password_otp.max_verification_attempts', 5);

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role')->default(1);
            $table->string('status')->nullable()->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_otps', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('purpose', 32)->default('password_reset');
            $table->string('otp_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(5);
            $table->timestamp('expires_at');
            $table->timestamp('sent_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->char('reset_token_hash', 64)->nullable();
            $table->timestamp('reset_token_expires_at')->nullable();
            $table->char('request_ip_hash', 64)->nullable();
            $table->char('user_agent_hash', 64)->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('password_reset_otps');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_request_returns_generic_message_for_unknown_email(): void
    {
        Mail::fake();

        $response = $this->post(route('password.otp.send'), [
            'email' => 'missing@example.com',
        ]);

        $response->assertRedirect(route('password.otp.verify.form'));
        $response->assertSessionHas('status', 'If the email exists, a 6-digit verification code has been sent.');
        $this->assertDatabaseCount('password_reset_otps', 0);
        Mail::assertNothingQueued();
    }

    #[DataProvider('supportedOtpLengths')]
    public function test_known_user_receives_a_hashed_otp_with_the_configured_length(int $length): void
    {
        Mail::fake();
        config()->set('password_otp.length', $length);
        $user = $this->createUser();

        $this->post(route('password.otp.send'), ['email' => $user->email])
            ->assertRedirect(route('password.otp.verify.form'));

        $record = PasswordResetOtp::query()->firstOrFail();

        $this->assertNotSame(str_repeat('0', $length), $record->otp_hash);
        $this->assertTrue($record->expires_at->isFuture());

        Mail::assertQueued(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail) use ($record, $length): bool {
            return $mail->otpRecordId === $record->id
                && preg_match('/^\d{'.$length.'}$/', $mail->otp) === 1
                && Hash::check($mail->otp, $record->otp_hash);
        });
    }

    #[DataProvider('supportedOtpLengths')]
    public function test_verification_form_and_validation_use_the_same_configured_length(int $length): void
    {
        config()->set('password_otp.length', $length);

        $this->withSession(['password_reset.email' => 'test@example.com'])
            ->get(route('password.otp.verify.form'))
            ->assertOk()
            ->assertSee('maxlength="'.$length.'"', false)
            ->assertSee('placeholder="'.str_repeat('0', $length).'"', false);

        $this->withSession(['password_reset.email' => 'test@example.com'])
            ->post(route('password.otp.verify'), [
                'email' => 'test@example.com',
                'otp' => str_repeat('1', $length - 1),
            ])
            ->assertSessionHasErrors('otp');
    }

    public function test_new_otp_invalidates_previous_otp(): void
    {
        Mail::fake();
        $user = $this->createUser();

        $this->post(route('password.otp.send'), ['email' => $user->email]);
        $first = PasswordResetOtp::query()->firstOrFail();

        RateLimiter::clear('password-reset-otp:cooldown:'.hash('sha256', strtolower($user->email)));

        $this->post(route('password.otp.send'), ['email' => $user->email]);

        $this->assertNotNull($first->fresh()->consumed_at);
        $this->assertDatabaseCount('password_reset_otps', 2);
    }

    public function test_valid_otp_can_reset_password_only_once(): void
    {
        Mail::fake();
        $user = $this->createUser();
        $plainOtp = null;

        $this->post(route('password.otp.send'), ['email' => $user->email]);

        Mail::assertQueued(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail) use (&$plainOtp): bool {
            $plainOtp = $mail->otp;
            return true;
        });

        $verify = $this->withSession(['password_reset.email' => $user->email])
            ->post(route('password.otp.verify'), [
                'email' => $user->email,
                'otp' => $plainOtp,
            ]);

        $verify->assertRedirect(route('password.reset.form'));
        $verify->assertSessionHas('password_reset.token');

        $session = $verify->getSession();

        $reset = $this->withSession([
            'password_reset.verified_email' => $session->get('password_reset.verified_email'),
            'password_reset.token' => $session->get('password_reset.token'),
            'password_reset.token_expires_at' => $session->get('password_reset.token_expires_at'),
        ])->post(route('password.otp.reset'), [
            'password' => 'SecurePass!123',
            'password_confirmation' => 'SecurePass!123',
        ]);

        $reset->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('SecurePass!123', $user->fresh()->password));
        $this->assertNotNull(PasswordResetOtp::query()->firstOrFail()->consumed_at);

        $replay = $this->withSession([
            'password_reset.verified_email' => $user->email,
            'password_reset.token' => $session->get('password_reset.token'),
            'password_reset.token_expires_at' => now()->addMinutes(5)->timestamp,
        ])->post(route('password.otp.reset'), [
            'password' => 'AnotherPass!123',
            'password_confirmation' => 'AnotherPass!123',
        ]);

        $replay->assertRedirect(route('password.request'));
        $this->assertFalse(Hash::check('AnotherPass!123', $user->fresh()->password));
    }

    public function test_expired_otp_is_rejected(): void
    {
        Mail::fake();
        $user = $this->createUser();
        $plainOtp = null;

        $this->post(route('password.otp.send'), ['email' => $user->email]);

        Mail::assertQueued(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail) use (&$plainOtp): bool {
            $plainOtp = $mail->otp;
            return true;
        });

        PasswordResetOtp::query()->update(['expires_at' => now()->subMinute()]);

        $this->withSession(['password_reset.email' => $user->email])
            ->post(route('password.otp.verify'), [
                'email' => $user->email,
                'otp' => $plainOtp,
            ])
            ->assertSessionHasErrors('otp');

        $this->assertNotNull(PasswordResetOtp::query()->firstOrFail()->fresh()->consumed_at);
    }

    public function test_unsupported_otp_length_is_rejected_instead_of_being_silently_ignored(): void
    {
        config()->set('password_otp.length', 12);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('PASSWORD_OTP_LENGTH');

        PasswordOtpConfiguration::length();
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('OldPassword!123'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
    }

    public static function supportedOtpLengths(): array
    {
        return [
            'minimum supported length' => [4],
            'default length' => [6],
            'maximum supported length' => [9],
        ];
    }
}
