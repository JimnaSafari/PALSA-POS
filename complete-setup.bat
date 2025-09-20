@echo off
echo ========================================
echo   PALSA POS - PRODUCTION SETUP
echo ========================================
echo.

echo Step 1: Installing additional PHP dependencies...
docker-compose exec app composer require laravel/sanctum barryvdh/laravel-dompdf intervention/image

echo.
echo Step 2: Publishing Sanctum configuration...
docker-compose exec app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo.
echo Step 3: Running additional migrations...
docker-compose exec app php artisan migrate

echo.
echo Step 4: Creating storage symlinks...
docker-compose exec app php artisan storage:link

echo.
echo Step 5: Clearing and caching configurations...
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo.
echo Step 6: Optimizing application...
docker-compose exec app php artisan optimize

echo.
echo Step 7: Seeding default data...
docker-compose exec app php artisan db:seed --class=ProductionSeeder

echo.
echo Step 8: Running tests...
docker-compose exec app php artisan test

echo.
echo ========================================
echo   SETUP COMPLETE!
echo ========================================
echo.
echo Your Palsa POS system is now production-ready!
echo.
echo 🌐 Web Application: http://localhost:8000
echo 🔧 Admin Panel: http://localhost:8000/admin
echo 📱 API Endpoint: http://localhost:8000/api
echo 📊 Health Check: http://localhost:8000/api/health
echo.
echo Default Login:
echo Email: superadmin@gmail.com
echo Password: admin123
echo.
echo 📋 Next Steps:
echo 1. Update .env with production values
echo 2. Set up SSL certificates
echo 3. Configure backup system
echo 4. Set up monitoring
echo 5. Configure M-Pesa integration
echo.
echo For production deployment, use:
echo docker-compose -f docker-compose.production.yml up -d
echo.
pause