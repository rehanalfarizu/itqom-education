#!/bin/bash

echo "=== DEPLOYMENT SCRIPT FOR HEROKU ==="

# Clear all caches
echo "1. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
echo "2. Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "3. Running migrations..."
php artisan migrate --force

# Generate optimized autoloader
echo "4. Optimizing autoloader..."
composer dump-autoload --optimize

# Check if course_description table exists and create sample data
echo "5. Checking database setup..."
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\CourseDescription;

try {
    \$count = CourseDescription::count();
    echo 'Found ' . \$count . ' courses in database' . PHP_EOL;
    
    if (\$count === 0) {
        echo 'Creating sample course...' . PHP_EOL;
        CourseDescription::create([
            'title' => 'Sample Course - Programming Fundamentals',
            'tag' => 'Programming',
            'overview' => 'Learn the basics of programming with this comprehensive course',
            'price' => 150000,
            'price_discount' => 99000,
            'instructor_name' => 'John Doe',
            'instructor_position' => 'Senior Developer',
            'video_count' => 25,
            'duration' => 180,
            'features' => json_encode(['HD Video', 'Lifetime Access', 'Certificate'])
        ]);
        echo 'Sample course created successfully!' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

echo "=== DEPLOYMENT COMPLETED ==="
echo "Application should now be ready for production!"
