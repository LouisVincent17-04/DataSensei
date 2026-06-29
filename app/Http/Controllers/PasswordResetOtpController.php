<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResetPasswordWithOtpRequest;
use App\Http\Requests\Auth\SendPasswordOtpRequest;
use App\Http\Requests\Auth\VerifyPasswordOtpRequest;
use App\Services\PasswordResetOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(
        SendPasswordOtpRequest $request,
        PasswordResetOtpService $service,
    ): RedirectResponse {
        $email = $service->normalizeEmail($request->validated('email'));
        $cooldown = $service->requestOtp($email, $request->ip(), $request->userAgent());

        $request->session()->put([
            'password_reset.email' => $email,
            'password_reset.cooldown_until' => now()->addSeconds(max(0, $cooldown))->timestamp,
        ]);

        return redirect()
            ->route('password.otp.verify.form')
            ->with('status', 'If the email exists, a six-digit verification code has been sent.');
    }

    public function showVerifyForm(Request $request, PasswordResetOtpService $service): RedirectResponse|View
    {
        $email = (string) $request->session()->get('password_reset.email', '');

        if ($email === '') {
            return redirect()->route('password.request');
        }

        return view('auth.verify-password-otp', [
            'email' => $email,
            'maskedEmail' => $this->maskEmail($email),
            'cooldownRemaining' => $service->cooldownRemaining($email),
        ]);
    }

    public function verifyOtp(
        VerifyPasswordOtpRequest $request,
        PasswordResetOtpService $service,
    ): RedirectResponse {
        $email = $service->normalizeEmail($request->validated('email'));
        $sessionEmail = $service->normalizeEmail((string) $request->session()->get('password_reset.email', ''));

        if ($sessionEmail === '' || ! hash_equals($sessionEmail, $email)) {
            return redirect()
                ->route('password.request')
                ->withErrors(['otp' => 'The password reset session is invalid. Request a new code.']);
        }

        $resetToken = $service->verifyOtp($email, $request->validated('otp'));

        if (! $resetToken) {
            return back()
                ->withErrors(['otp' => 'The verification code is invalid, expired, or has reached its attempt limit.'])
                ->withInput($request->safe()->only('email'));
        }

        $request->session()->regenerate();
        $request->session()->forget(['password_reset.cooldown_until']);
        $request->session()->put([
            'password_reset.verified_email' => $email,
            'password_reset.token' => $resetToken,
            'password_reset.token_expires_at' => now()
                ->addMinutes((int) config('password_otp.reset_authorization_minutes', 10))
                ->timestamp,
        ]);

        return redirect()
            ->route('password.reset.form')
            ->with('status', 'Code verified. Create a new password to finish resetting your account.');
    }

    public function showResetForm(Request $request): RedirectResponse|View
    {
        if (! $this->hasValidResetSession($request)) {
            $this->clearResetSession($request);

            return redirect()
                ->route('password.request')
                ->withErrors(['password' => 'Your password reset authorization has expired. Request a new code.']);
        }

        return view('auth.reset-password-otp');
    }

    public function resetPassword(
        ResetPasswordWithOtpRequest $request,
        PasswordResetOtpService $service,
    ): RedirectResponse {
        if (! $this->hasValidResetSession($request)) {
            $this->clearResetSession($request);

            return redirect()
                ->route('password.request')
                ->withErrors(['password' => 'Your password reset authorization has expired. Request a new code.']);
        }

        $email = (string) $request->session()->get('password_reset.verified_email');
        $token = (string) $request->session()->get('password_reset.token');

        if (! $service->resetPassword($email, $token, $request->validated('password'))) {
            $this->clearResetSession($request);

            return redirect()
                ->route('password.request')
                ->withErrors(['password' => 'The password reset authorization is invalid or expired. Request a new code.']);
        }

        $this->clearResetSession($request);
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been reset successfully. You may now sign in.');
    }

    private function hasValidResetSession(Request $request): bool
    {
        $email = (string) $request->session()->get('password_reset.verified_email', '');
        $token = (string) $request->session()->get('password_reset.token', '');
        $expiresAt = (int) $request->session()->get('password_reset.token_expires_at', 0);

        return $email !== '' && $token !== '' && $expiresAt > now()->timestamp;
    }

    private function clearResetSession(Request $request): void
    {
        $request->session()->forget([
            'password_reset.email',
            'password_reset.cooldown_until',
            'password_reset.verified_email',
            'password_reset.token',
            'password_reset.token_expires_at',
        ]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));
        $maskedLocal = $visible.str_repeat('•', max(3, mb_strlen($local) - mb_strlen($visible)));

        return $domain === '' ? $maskedLocal : $maskedLocal.'@'.$domain;
    }
}
