@echo off
setlocal
cd /d "%~dp0"
title DataSensei Defense Launcher

echo Checking DataSensei before starting the defense...
echo.
php artisan datasensei:preflight
if errorlevel 1 (
  echo.
  echo DataSensei did not start because a required preflight check failed.
  echo Fix the FAIL item shown above, then run this file again.
  pause
  exit /b 1
)

echo.
echo Starting DataSensei in four separate windows...
start "DataSensei Web" cmd /k call "%~dp0start-datasensei.bat"
start "DataSensei Default Worker" cmd /k call "%~dp0start-default-worker.bat"
start "DataSensei ML Worker" cmd /k call "%~dp0start-ml-worker.bat"
start "DataSensei Scheduler" cmd /k call "%~dp0start-scheduler.bat"

echo.
echo Open http://127.0.0.1:8000 in your browser.
echo Keep all four DataSensei windows open during the defense.
exit /b 0
