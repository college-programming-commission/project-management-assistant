@echo off
echo Clearing Laravel cache...
docker exec project-management-app php artisan config:clear
docker exec project-management-app php artisan cache:clear
docker exec project-management-app php artisan view:clear
docker exec project-management-app php artisan route:clear
echo.
echo Restarting app container...
docker-compose restart app reverb
echo.
echo Done! ENV changes applied.
pause
