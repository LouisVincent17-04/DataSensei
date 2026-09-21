<?php

use App\Models\PasswordResetOtp;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    $cutoff = now()->subHours((int) config('password_otp.delete_after_hours', 24));

    PasswordResetOtp::query()
        ->where(function ($query) use ($cutoff): void {
            $query->where('expires_at', '<', $cutoff)
                ->orWhere('consumed_at', '<', $cutoff);
        })
        ->delete();
})->dailyAt('02:15')->name('prune-password-reset-otps')->withoutOverlapping();

Schedule::command('ml:prune-temporary-files')
    ->hourly()
    ->name('prune-ml-temporary-files')
    ->withoutOverlapping();

// Keeps the AI reviewer model loaded (and reloads it after Ollama restarts).
Schedule::command('code-review:warm --timeout=120')
    ->everyFiveMinutes()
    ->name('warm-code-review-model')
    ->withoutOverlapping()
    ->runInBackground();

// Keeps the warm Python sandbox pool full and removes abandoned containers.
Schedule::command('python-sandbox:pool maintain')
    ->everyMinute()
    ->name('maintain-python-sandbox-pool')
    ->withoutOverlapping()
    ->runInBackground();
