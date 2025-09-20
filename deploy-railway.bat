@echo off
echo ================================
echo Railway Deployment - Palsa POS
echo ================================
echo.

echo 1. Checking git status...
git status

echo.
echo 2. Adding all changes...
git add .

echo.
echo 3. Committing changes...
set /p commit_msg="Enter commit message (or press Enter for default): "
if "%commit_msg%"=="" set commit_msg=Deploy Palsa POS to Railway

git commit -m "%commit_msg%"

echo.
echo 4. Pushing to GitHub...
git push origin main

echo.
echo ================================
echo Railway Deployment Instructions:
echo ================================
echo.
echo 1. Go to https://railway.app
echo 2. Sign in with GitHub
echo 3. Click "New Project"
echo 4. Select "Deploy from GitHub repo"
echo 5. Choose your PALSA-POS repository
echo 6. Add MySQL database service
echo 7. Set environment variables:
echo    - APP_KEY=base64:C+kiffgn6xdlxsBUdYno57+AhYtdrqUFLSn1Zso0lcA=
echo    - RAILWAY_STATIC_URL=${{RAILWAY_STATIC_URL}}
echo 8. Deploy!
echo.
echo Your complete POS system will be available at:
echo https://your-app-name.railway.app
echo.
echo This includes:
echo - Frontend (Blade templates)
echo - Backend API
echo - Admin dashboard
echo - POS interface
echo - M-Pesa integration
echo.

set /p open_railway="Open Railway dashboard? (y/n): "
if /i "%open_railway%"=="y" (
    start https://railway.app
)

pause