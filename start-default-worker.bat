@echo off
setlocal
cd /d "%~dp0"
title DataSensei Default Queue Worker

echo DataSensei default queue worker is running.
echo Keep this window open for mail and other background jobs.
echo Press Ctrl+C to stop it safely.
echo.

:restart
php artisan queue:work database --queue=default --sleep=1 --tries=3 --timeout=120 --max-time=3600

echo.
echo The default worker stopped. Restarting in 3 seconds...
timeout /t 3 /nobreak >nul
goto restart
