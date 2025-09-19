@echo off
echo Starting Palsa POS System with Docker...
echo.

echo Building and starting Docker containers...
docker-compose up -d --build

echo.
echo Waiting for database to be ready...
timeout /t 30 /nobreak > nul

echo.
echo Running Laravel setup commands...
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan storage:link

echo.
echo Setup complete!
echo.
echo Your Palsa POS application is now running at: http://localhost:8000
echo.
echo Default SuperAdmin Login:
echo Email: superadmin@gmail.com
echo Password: admin123
echo.
echo To stop the application, run: docker-compose down
echo To view logs, run: docker-compose logs -f
echo.
pause