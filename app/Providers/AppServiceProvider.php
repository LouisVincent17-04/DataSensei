<?php

namespace App\Providers;

use App\Services\HybridMl\MlWorkerSupervisor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Queue\Events\Looping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
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
        $this->configureFileBackedRuntimeStorage();
    }

    /**
     * Keep Laravel runtime state out of database tables that are intentionally
     * not part of this project. This also protects existing installations whose
     * .env still contains the old database-backed defaults.
     */
    private function configureFileBackedRuntimeStorage(): void
    {
        if (config('session.driver') === 'database') {
            config(['session.driver' => 'file']);
        }

        if (config('cache.default') === 'database') {
            config(['cache.default' => 'file']);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MySQL 5.5's older InnoDB key limit is 767 bytes. 191 utf8mb4
        // characters (764 bytes) is safe only when the string is the whole
        // key; this schema also has composite indexes that include an 8-byte
        // foreign key. 189 leaves enough room without rewriting historical
        // migrations or truncating existing installations.
        Schema::defaultStringLength(189);

        $this->configureAuthenticationRateLimiters();
        $this->configurePasswordOtpRateLimiters();

        // Lets the training page know a machine-learning worker is alive.
        Queue::looping(function (Looping $event): void {
            if ($event->connectionName === (string) config('hybrid_ml.queue_connection', 'machine_learning')) {
                $this->app->make(MlWorkerSupervisor::class)->recordHeartbeat();
            }
        });
    }

    private function configureAuthenticationRateLimiters(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            $key = $this->emailRateLimitKey((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)
                ->by('login:'.$key)
                ->response(fn (Request $request, array $headers) => back()
                    ->withErrors(['email' => 'Too many sign-in attempts. Please wait one minute and try again.'])
                    ->withInput($request->only('email'))
                    ->withHeaders($headers));
        });

        RateLimiter::for('registration', fn (Request $request): Limit => Limit::perMinute(3)
            ->by('registration:'.$request->ip())
            ->response(fn (Request $request, array $headers) => back()
                ->withErrors(['email' => 'Too many registration attempts. Please wait one minute and try again.'])
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withHeaders($headers)));

        RateLimiter::for('python-execution', fn (Request $request): Limit => Limit::perMinute(20)
            ->by('python-execution:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('sql-execution', fn (Request $request): Limit => Limit::perMinute(60)
            ->by('sql-execution:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('code-review', fn (Request $request): Limit => Limit::perMinute(10)
            ->by('code-review:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('code-review-status', fn (Request $request): Limit => Limit::perMinute(90)
            ->by('code-review-status:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('model-development-training', fn (Request $request): Limit => Limit::perMinute(6)
            ->by('model-development-training:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('model-development-prediction', fn (Request $request): Limit => Limit::perMinute(12)
            ->by('model-development-prediction:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('ml-dataset-upload', fn (Request $request): Limit => Limit::perHour(12)
            ->by('ml-dataset-upload:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('ml-training', fn (Request $request): array => [
            Limit::perMinute(4)->by('ml-training-minute:'.($request->user()?->id ?? $request->ip())),
            Limit::perHour(30)->by('ml-training-hour:'.($request->user()?->id ?? $request->ip())),
        ]);

        RateLimiter::for('ml-prediction', fn (Request $request): Limit => Limit::perMinute(30)
            ->by('ml-prediction:'.($request->user()?->id ?? $request->ip())));

        RateLimiter::for('anti-cheat-event', fn (Request $request): Limit => Limit::perMinute(120)
            ->by('anti-cheat-event:'.($request->user()?->id ?? $request->ip())));
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
