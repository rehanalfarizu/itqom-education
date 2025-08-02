@echo off
REM Production Build Script for Windows
REM This script ensures proper deployment for production

echo Starting production build process...

REM 1. Clear Laravel caches
echo Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

REM 2. Install dependencies (if needed)
echo Installing dependencies...
call npm ci --only=production

REM 3. Build assets for production
echo Building assets for production...
call npm run production

REM 4. Verify manifest file exists
if exist "public\build\manifest.json" (
    echo Manifest file found at public\build\manifest.json
) else (
    echo Error: Manifest file not found!
    exit /b 1
)

REM 5. Create storage link if needed
echo Creating storage link...
php artisan storage:link

REM 6. Clear caches again after build
echo Final cache clear...
php artisan cache:clear
php artisan view:clear

echo Production build completed successfully!
echo Assets are ready for production deployment
