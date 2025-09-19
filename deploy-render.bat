@echo off
echo ================================
echo Render Deployment - Palsa POS
echo ================================
echo.

echo Render is often better than Railway for Laravel apps!
echo.

echo 1. Committing changes to GitHub...
git add .
set /p commit_msg="Enter commit message (or press Enter for default): "
if "%commit_msg%"=="" set commit_msg=Add Render deployment configuration

git commit -m "%commit_msg%"
git push origin main

echo.
echo ================================
echo Render Deployment Instructions:
echo ================================
echo.
echo 1. Go to https://render.com
echo 2. Sign up/Login with GitHub
echo 3. Click "New +" then "Blueprint"
echo 4. Select your PALSA-POS repository
echo 5. Render will detect render.yaml automatically
echo 6. Click "Apply" to deploy
echo.
echo Alternative Manual Setup:
echo 1. New + → Web Service
echo 2. Connect PALSA-POS repository
echo 3. Environment: PHP
echo 4. Build Command: composer install --no-dev --optimize-autoloader
echo 5. Start Command: php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
echo 6. Add environment variables (see RENDER_DEPLOYMENT_GUIDE.md)
echo 7. Create MySQL database service
echo 8. Deploy!
echo.
echo Your app will be available at:
echo https://your-app-name.onrender.com
echo.
echo Benefits of Render over Railway:
echo - Better Laravel support
echo - More reliable deployments
echo - Clearer error messages
echo - Easier database setup
echo - Better performance
echo.

set /p open_render="Open Render dashboard? (y/n): "
if /i "%open_render%"=="y" (
    start https://render.com
)

pause