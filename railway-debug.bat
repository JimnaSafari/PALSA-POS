@echo off
echo ================================
echo Railway Debug Helper
echo ================================
echo.

echo This script will help you debug Railway deployment issues.
echo.

echo 1. Check Railway Deployment Logs:
echo    - Go to https://railway.app
echo    - Click on your service
echo    - Go to "Deployments" tab
echo    - Click on latest deployment
echo    - Check "Build Logs" and "Deploy Logs"
echo.

echo 2. Required Environment Variables in Railway:
echo    APP_KEY=base64:C+kiffgn6xdlxsBUdYno57+AhYtdrqUFLSn1Zso0lcA=
echo    RAILWAY_STATIC_URL=${{RAILWAY_STATIC_URL}}
echo.

echo 3. Make sure you have:
echo    - MySQL service added to Railway project
echo    - GitHub repository connected
echo    - Environment variables set
echo.

echo 4. Test URLs (replace with your Railway URL):
echo    Health Check: https://your-app.railway.app/health
echo    Main App: https://your-app.railway.app
echo.

echo 5. Common Issues:
echo    - Database not ready (wait 2-3 minutes)
echo    - Missing environment variables
echo    - Build errors (check build logs)
echo.

set /p open_railway="Open Railway dashboard? (y/n): "
if /i "%open_railway%"=="y" (
    start https://railway.app
)

pause