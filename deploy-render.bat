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
echo 3. Create PostgreSQL database first:
echo    - New + → PostgreSQL
echo    - Name: palsa-pos-db
echo    - Free tier
echo 4. Create Web Service:
echo    - New + → Web Service
echo    - Connect PALSA-POS repository
echo    - Runtime: Docker
echo    - Dockerfile: ./Dockerfile.render
echo 5. Add environment variables (see RENDER_DEPLOYMENT_GUIDE.md)
echo 6. Deploy!
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