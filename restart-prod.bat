@echo off
echo ==========================================
echo   Restarting Production Containers
echo ==========================================
echo.

echo [1/3] Reading .env configuration...
type .env | findstr /R "^APP_ENV= ^APP_DEBUG="
echo.

echo [2/3] Stopping containers...
docker-compose -f docker-compose.prod.yml down
echo.

echo [3/3] Starting containers...
docker-compose -f docker-compose.prod.yml up -d
echo.

echo Waiting for containers to be ready...
timeout /t 10 /nobreak > nul
echo.

echo ==========================================
echo   Container Status:
echo ==========================================
docker ps --filter "name=project-management"
echo.

echo ==========================================
echo   Logs:
echo ==========================================
docker logs project-management-app --tail 30
echo.

echo Done!
pause
