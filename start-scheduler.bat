@echo off
setlocal
cd /d "%~dp0"
title DataSensei Scheduler

echo DataSensei scheduler is running.
echo It handles periodic maintenance such as expired OTP cleanup.
echo Press Ctrl+C to stop it safely.
echo.

:restart
php artisan schedule:work

echo.
echo The scheduler stopped. Restarting in 3 seconds...
timeout /t 3 /nobreak >nul
goto restart
