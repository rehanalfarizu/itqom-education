@echo off
echo === BUILDING ASSETS FOR PRODUCTION ===

echo 1. Installing dependencies...
call npm ci --only=production

echo 2. Building assets with Vite...
call npm run production

echo 3. Clearing Laravel caches...
call php artisan config:clear
call php artisan cache:clear
call php artisan view:clear

echo 4. Creating storage link...
call php artisan storage:link

echo 5. Optimizing for production...
call php artisan config:cache
call php artisan view:cache

echo === BUILD COMPLETED ===
echo Ready to commit and push to Heroku!
echo Run: git add . && git commit -m "Build assets for production" && git push heroku main

pause
