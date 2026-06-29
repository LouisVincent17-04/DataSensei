<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $this->configurePasswordOtpRateLimiters();
    }

    private function configurePasswordOtpRateLimiters(): void
    {
        RateLimiter::for('password-otp-request', function (Request $request): array {
            $emailKey = $this->emailRateLimitKey((string) $request->input('email'));

            return [
                Limit::perMinute((int) config('password_otp.rate_limits.request_per_minute', 3))
                    ->by('password-otp-request:ip:'.$request->ip())
                    ->response(fn (Request $request, array $headers) => back()
                        ->withErrors(['rate_limit' => 'Too many code requests. Please wait before trying again.'])
                        ->withInput($request->only('email'))
                        ->withHeaders($headers)),
                Limit::perHour((int) config('password_otp.rate_limits.request_per_hour', 10))
                    ->by('password-otp-request:email:'.$emailKey)
                    ->response(fn (Request $request, array $headers) => back()
                        ->withErrors(['rate_limit' => 'Too many code requests. Please try again later.'])
                        ->withInput($request->only('email'))
                        ->withHeaders($headers)),
            ];
        });

        RateLimiter::for('password-otp-verify', function (Request $request): array {
            $emailKey = $this->emailRateLimitKey((string) $request->input('email'));

            return [
                Limit::perMinute((int) config('password_otp.rate_limits.verify_per_minute', 10))
                    ->by('password-otp-verify:ip:'.$request->ip())
                    ->response(fn (Request $request, array $headers) => back()
                        ->withErrors(['rate_limit' => 'Too many verification attempts. Please wait before trying again.'])
                        ->withInput($request->only('email'))
                        ->withHeaders($headers)),
                Limit::perMinute((int) config('password_otp.max_verification_attempts', 5))
                    ->by('password-otp-verify:email:'.$emailKey)
                    ->response(fn (Request $request, array $headers) => back()
                        ->withErrors(['rate_limit' => 'Too many verification attempts. Request a new code.'])
                        ->withInput($request->only('email'))
                        ->withHeaders($headers)),
            ];
        });

        RateLimiter::for('password-otp-reset', function (Request $request): Limit {
            $email = (string) $request->session()->get('password_reset.verified_email', '');

            return Limit::perMinute((int) config('password_otp.rate_limits.reset_per_minute', 5))
                ->by('password-otp-reset:'.hash('sha256', strtolower(trim($email)).'|'.$request->ip()))
                ->response(fn (Request $request, array $headers) => back()
                    ->withErrors(['rate_limit' => 'Too many password reset attempts. Please wait before trying again.'])
                    ->withHeaders($headers));
        });
    }

    private function emailRateLimitKey(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }
}
