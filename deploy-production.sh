#!/bin/bash

# Production Build Script
# This script ensures proper deployment for production

echo "🚀 Starting production build process..."

# 1. Clear Laravel caches
echo "📦 Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 2. Install dependencies (if needed)
echo "📦 Installing dependencies..."
npm ci --only=production

# 3. Build assets for production
echo "🔨 Building assets for production..."
npm run production

# 4. Verify manifest file exists
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Manifest file found at public/build/manifest.json"
else
    echo "❌ Error: Manifest file not found!"
    exit 1
fi

# 5. Create storage link if needed
echo "🔗 Creating storage link..."
php artisan storage:link

# 6. Clear caches again after build
echo "🧹 Final cache clear..."
php artisan cache:clear
php artisan view:clear

echo "🎉 Production build completed successfully!"
echo "🌐 Assets are ready for production deployment"
