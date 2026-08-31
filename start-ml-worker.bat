@echo off
setlocal
cd /d "%~dp0"
title DataSensei Machine Learning Worker

echo =====================================================
echo DataSensei Machine Learning queue worker is running.
echo Keep this window open while models are being trained.
echo Press Ctrl+C to stop the worker safely.
echo =====================================================
echo.

:restart
php artisan queue:work machine_learning --queue=machine-learning --sleep=1 --tries=2 --timeout=900 --max-time=3600

echo.
echo The machine learning worker stopped. Restarting in 3 seconds...
timeout /t 3 /nobreak >nul
goto restart
