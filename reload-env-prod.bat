@echo off
echo ==========================================
echo   Reloading ENV for Production
echo ==========================================
echo.

echo [1/4] Clearing Laravel cache...
docker exec project-management-app php artisan config:clear
docker exec project-management-app php artisan cache:clear
docker exec project-management-app php artisan view:clear
docker exec project-management-app php artisan route:clear
echo.

echo [2/4] Checking current ENV values...
docker exec project-management-app php artisan tinker --execute="echo 'APP_ENV: ' . config('app.env') . PHP_EOL; echo 'APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false');"
echo.

echo [3/4] Restarting app and reverb containers...
docker-compose -f docker-compose.prod.yml restart app reverb
echo.

echo [4/4] Waiting for services to be ready...
timeout /t 5 /nobreak > nul
echo.

echo ==========================================
echo   Status:
echo ==========================================
docker ps --filter "name=project-management-app" --filter "name=project-management-reverb"
echo.

echo Done! ENV reloaded.
pause
