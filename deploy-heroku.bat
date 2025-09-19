@echo off
echo ================================
echo Heroku Deployment - Palsa POS
echo ================================
echo.

echo Heroku is the most reliable option for Laravel apps!
echo.

echo Prerequisites:
echo 1. Install Heroku CLI from: https://devcenter.heroku.com/articles/heroku-cli
echo 2. Create free Heroku account at: https://heroku.com
echo.

echo Deployment Steps:
echo.

echo 1. Login to Heroku:
echo    heroku login
echo.

echo 2. Create app and database:
echo    heroku create palsa-pos-system
echo    heroku addons:create heroku-postgresql:mini
echo.

echo 3. Set environment variables:
echo    heroku config:set APP_NAME="Palsa POS"
echo    heroku config:set APP_ENV=production
echo    heroku config:set APP_KEY=base64:C+kiffgn6xdlxsBUdYno57+AhYtdrqUFLSn1Zso0lcA=
echo    heroku config:set APP_DEBUG=false
echo    heroku config:set LOG_CHANNEL=errorlog
echo    heroku config:set SESSION_DRIVER=database
echo    heroku config:set CACHE_STORE=database
echo.

echo 4. Commit and deploy:
git add .
set /p commit_msg="Enter commit message (or press Enter for default): "
if "%commit_msg%"=="" set commit_msg=Deploy Palsa POS to Heroku

git commit -m "%commit_msg%"
echo    git push heroku main
echo.

echo 5. Run migrations:
echo    heroku run php artisan migrate --force
echo.

echo Your app will be live at:
echo https://palsa-pos-system.herokuapp.com
echo.

echo Benefits of Heroku:
echo - Native PHP support
echo - Git-based deployment
echo - Automatic PostgreSQL
echo - No Docker complexity
echo - Industry standard
echo - Excellent Laravel support
echo.

set /p open_heroku="Open Heroku dashboard? (y/n): "
if /i "%open_heroku%"=="y" (
    start https://dashboard.heroku.com
)

echo.
echo Manual deployment commands:
echo heroku create palsa-pos-system
echo heroku addons:create heroku-postgresql:mini
echo git push heroku main
echo heroku run php artisan migrate --force

pause