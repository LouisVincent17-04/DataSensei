<?php

namespace App\Services;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class PasswordResetOtpService
{
    public function requestOtp(string $email, ?string $ipAddress, ?string $userAgent): int
    {
        $email = $this->normalizeEmail($email);
        $cooldown = max(1, (int) config('password_otp.cooldown_seconds', 60));
        $cooldownKey = $this->cooldownKey($email);

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            return max(1, RateLimiter::availableIn($cooldownKey));
        }

        RateLimiter::hit($cooldownKey, $cooldown);

        $user = $this->findActiveUser($email);

        // Perform a password-hash operation even when no account is found to
        // reduce obvious timing differences while keeping the HTTP response generic.
        if (! $user) {
            Hash::make($this->generateOtp());

            Log::info('Password reset OTP requested.', [
                'account_fingerprint' => $this->fingerprint($email),
                'request_ip_fingerprint' => $this->fingerprint((string) $ipAddress),
            ]);

            return $cooldown;
        }

        $otp = $this->generateOtp();
        $expiresAt = now()->addMinutes((int) config('password_otp.expires_minutes', 5));

        $record = DB::transaction(function () use ($user, $otp, $expiresAt, $ipAddress, $userAgent): PasswordResetOtp {
            $this->invalidateForUser($user);

            return PasswordResetOtp::create([
                'user_id' => $user->id,
                'purpose' => PasswordResetOtp::PURPOSE_PASSWORD_RESET,
                'otp_hash' => Hash::make($otp),
                'attempts' => 0,
                'max_attempts' => (int) config('password_otp.max_verification_attempts', 5),
                'expires_at' => $expiresAt,
                'sent_at' => now(),
                'request_ip_hash' => $ipAddress ? $this->fingerprint($ipAddress) : null,
                'user_agent_hash' => $userAgent ? $this->fingerprint($userAgent) : null,
            ]);
        });

        try {
            Mail::to($user->email)->queue(new PasswordResetOtpMail(
                otpRecordId: $record->id,
                recipientName: $user->name,
                otp: $otp,
                expiresAt: $expiresAt,
            ));
        } catch (Throwable $exception) {
            $record->forceFill([
                'consumed_at' => now(),
                'reset_token_hash' => null,
                'reset_token_expires_at' => null,
            ])->save();

            RateLimiter::clear($cooldownKey);

            Log::error('Unable to queue password reset OTP email.', [
                'otp_record_id' => $record->id,
                'account_fingerprint' => $this->fingerprint($email),
                'exception' => $exception::class,
            ]);

            return 0;
        }

        Log::notice('Password reset OTP generated.', [
            'otp_record_id' => $record->id,
            'user_id' => $user->id,
            'account_fingerprint' => $this->fingerprint($email),
            'expires_at' => $expiresAt->toIso8601String(),
        ]);

        return $cooldown;
    }

    public function verifyOtp(string $email, string $otp): ?string
    {
        $email = $this->normalizeEmail($email);
        $user = $this->findActiveUser($email);

        if (! $user) {
            Hash::make($otp);

            Log::warning('Password reset OTP verification failed.', [
                'account_fingerprint' => $this->fingerprint($email),
            ]);

            return null;
        }

        $resetToken = Str::random(64);
        $resetTokenHash = hash('sha256', $resetToken);
        $resetTokenExpiresAt = now()->addMinutes((int) config('password_otp.reset_authorization_minutes', 10));

        $verified = DB::transaction(function () use ($user, $otp, $resetTokenHash, $resetTokenExpiresAt): bool {
            /** @var PasswordResetOtp|null $record */
            $record = PasswordResetOtp::query()
                ->where('user_id', $user->id)
                ->where('purpose', PasswordResetOtp::PURPOSE_PASSWORD_RESET)
                ->whereNull('consumed_at')
                ->whereNull('verified_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $record) {
                return false;
            }

            if ($record->expires_at->isPast() || $record->attempts >= $record->max_attempts) {
                $record->forceFill([
                    'consumed_at' => now(),
                    'reset_token_hash' => null,
                    'reset_token_expires_at' => null,
                ])->save();

                return false;
            }

            $nextAttempt = $record->attempts + 1;

            if (! Hash::check($otp, $record->otp_hash)) {
                $payload = ['attempts' => $nextAttempt];

                if ($nextAttempt >= $record->max_attempts) {
                    $payload['consumed_at'] = now();
                }

                $record->forceFill($payload)->save();

                return false;
            }

            $record->forceFill([
                'attempts' => $nextAttempt,
                'verified_at' => now(),
                'reset_token_hash' => $resetTokenHash,
                'reset_token_expires_at' => $resetTokenExpiresAt,
            ])->save();

            return true;
        });

        if (! $verified) {
            Log::warning('Password reset OTP verification failed.', [
                'user_id' => $user->id,
                'account_fingerprint' => $this->fingerprint($email),
            ]);

            return null;
        }

        Log::notice('Password reset OTP verified.', [
            'user_id' => $user->id,
            'account_fingerprint' => $this->fingerprint($email),
        ]);

        return $resetToken;
    }

    public function resetPassword(string $email, string $resetToken, string $newPassword): bool
    {
        $email = $this->normalizeEmail($email);
        $user = $this->findActiveUser($email);

        if (! $user || $resetToken === '') {
            return false;
        }

        $tokenHash = hash('sha256', $resetToken);

        $reset = DB::transaction(function () use ($user, $tokenHash, $newPassword): bool {
            /** @var PasswordResetOtp|null $record */
            $record = PasswordResetOtp::query()
                ->where('user_id', $user->id)
                ->where('purpose', PasswordResetOtp::PURPOSE_PASSWORD_RESET)
                ->where('reset_token_hash', $tokenHash)
                ->whereNotNull('verified_at')
                ->whereNull('consumed_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $record || ! $record->reset_token_expires_at || $record->reset_token_expires_at->isPast()) {
                if ($record) {
                    $record->forceFill([
                        'consumed_at' => now(),
                        'reset_token_hash' => null,
                        'reset_token_expires_at' => null,
                    ])->save();
                }

                return false;
            }

            $user->forceFill([
                'password' => Hash::make($newPassword),
                'remember_token' => Str::random(60),
            ])->save();

            $this->invalidateForUser($user);

            return true;
        });

        if ($reset) {
            event(new PasswordReset($user));
            $this->invalidateDatabaseSessions($user);
        }

        Log::log($reset ? 'notice' : 'warning', $reset
            ? 'Password reset completed.'
            : 'Password reset authorization rejected.', [
                'user_id' => $user->id,
                'account_fingerprint' => $this->fingerprint($email),
            ]);

        return $reset;
    }

    public function invalidateForUser(User $user): void
    {
        PasswordResetOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->update([
                'consumed_at' => now(),
                'reset_token_hash' => null,
                'reset_token_expires_at' => null,
                'updated_at' => now(),
            ]);
    }

    public function cooldownRemaining(string $email): int
    {
        return max(0, RateLimiter::availableIn($this->cooldownKey($this->normalizeEmail($email))));
    }

    public function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function findActiveUser(string $email): ?User
    {
        return User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where(function ($query): void {
                $query->whereNull('status')->orWhere('status', 'active');
            })
            ->first();
    }

    private function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }


    private function invalidateDatabaseSessions(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        try {
            $connection = config('session.connection');
            $table = (string) config('session.table', 'sessions');

            DB::connection($connection)
                ->table($table)
                ->where('user_id', $user->id)
                ->delete();
        } catch (Throwable $exception) {
            Log::warning('Password was reset, but existing database sessions could not be revoked.', [
                'user_id' => $user->id,
                'exception' => $exception::class,
            ]);
        }
    }

    private function cooldownKey(string $email): string
    {
        return 'password-reset-otp:cooldown:'.hash('sha256', $email);
    }

    private function fingerprint(string $value): string
    {
        return hash_hmac('sha256', $value, (string) config('app.key'));
    }
}
