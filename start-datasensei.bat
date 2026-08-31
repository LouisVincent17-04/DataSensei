@echo off
setlocal
cd /d "%~dp0"
title DataSensei Laravel Server

:restart
cls
echo ==============================================
echo DataSensei is running at http://127.0.0.1:8000
echo Keep this window open while using the system.
echo Press Ctrl+C and confirm to stop the server.
echo ==============================================
echo.

php artisan serve --host=127.0.0.1 --port=8000

echo.
echo The Laravel server stopped unexpectedly.
echo Restarting in 3 seconds...
timeout /t 3 /nobreak >nul
goto restart
