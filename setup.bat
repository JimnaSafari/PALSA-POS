@echo off
echo Setting up Palsa POS System for the first time...
echo.

echo Step 1: Building Docker containers (this may take a few minutes)...
docker-compose build

echo.
echo Step 2: Starting services...
docker-compose up -d

echo.
echo Step 3: Waiting for database to initialize...
timeout /t 45 /nobreak > nul

echo.
echo Step 4: Installing PHP dependencies...
docker-compose exec app composer install --optimize-autoloader

echo.
echo Step 5: Setting up Laravel...
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan storage:link

echo.
echo Step 6: Setting up database (using existing mypos.sql)...
echo Database should be automatically imported from mypos.sql file

echo.
echo Setup complete!
echo.
echo Your Palsa POS application is now running at: http://localhost:8000
echo.
echo Default SuperAdmin Login:
echo Email: superadmin@gmail.com
echo Password: admin123
echo.
echo To stop the application: docker-compose down
echo To restart the application: docker-compose up -d
echo To view logs: docker-compose logs -f
echo.
pause