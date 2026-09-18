<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Background Artisan processes
    |--------------------------------------------------------------------------
    |
    | DataSensei starts short-lived background Artisan processes for slow AI
    | reviews and, when no worker is running, for machine-learning training.
    | The PHP binary is detected automatically when the site runs through
    | "php artisan serve". Set DATASENSEI_PHP_BINARY when the site runs under
    | Apache or IIS, for example C:\xampp\php\php.exe.
    |
    */

    'enabled' => (bool) env('DATASENSEI_BACKGROUND_PROCESSES', true),

    'php_binary' => env('DATASENSEI_PHP_BINARY'),
];
