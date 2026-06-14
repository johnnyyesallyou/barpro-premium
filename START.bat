@echo off
REM BarPro Premium Local Development Quick Start
REM Run this script to start the local environment

cd /d C:\Users\Johnn\barpro-final

echo.
echo ========================================
echo   BarPro Premium Local Environment
echo ========================================
echo.

echo [1/4] Stopping any old containers...
docker compose down 2>nul

echo [2/4] Starting new containers...
docker compose up -d

echo [3/4] Waiting for services to start...
timeout /t 5 /nobreak

echo [4/4] Checking container status...
docker compose ps

echo.
echo ========================================
echo   URLs:
echo ========================================
echo WordPress:   http://localhost:8080
echo PhpMyAdmin:  http://localhost:8081
echo.
echo Database:
echo   Host:     mysql
echo   User:     wordpress
echo   Password: wordpress
echo.
echo ========================================
echo.
echo Next steps:
echo 1. Open http://localhost:8080 in browser
echo 2. Complete WordPress installation
echo 3. Follow DEPLOYMENT_CHECKLIST.md
echo.
pause
