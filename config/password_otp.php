<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Password Reset OTP Settings
    |--------------------------------------------------------------------------
    */

    'length' => (int) env('PASSWORD_OTP_LENGTH', 6),
    'expires_minutes' => (int) env('PASSWORD_OTP_EXPIRES_MINUTES', 5),
    'cooldown_seconds' => (int) env('PASSWORD_OTP_COOLDOWN_SECONDS', 60),
    'max_verification_attempts' => (int) env('PASSWORD_OTP_MAX_VERIFY_ATTEMPTS', 5),
    'reset_authorization_minutes' => (int) env('PASSWORD_OTP_RESET_WINDOW_MINUTES', 10),
    'delete_after_hours' => (int) env('PASSWORD_OTP_DELETE_AFTER_HOURS', 24),

    'rate_limits' => [
        'request_per_minute' => (int) env('PASSWORD_OTP_REQUESTS_PER_MINUTE', 3),
        'request_per_hour' => (int) env('PASSWORD_OTP_REQUESTS_PER_HOUR', 10),
        'verify_per_minute' => (int) env('PASSWORD_OTP_VERIFY_PER_MINUTE', 10),
        'reset_per_minute' => (int) env('PASSWORD_OTP_RESET_PER_MINUTE', 5),
    ],

    'password_min_length' => (int) env('PASSWORD_RESET_MIN_LENGTH', 8),
    'check_compromised_passwords' => (bool) env('PASSWORD_RESET_CHECK_COMPROMISED', false),
];
