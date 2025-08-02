<?php

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Debug script untuk memeriksa masalah
echo "=== DEBUG COURSE DESCRIPTION MODEL ===\n";

// Check if classes exist
echo "1. Checking class existence:\n";
echo "- CourseDescription exists: " . (class_exists('App\Models\CourseDescription') ? 'YES' : 'NO') . "\n";
echo "- CloudinaryService exists: " . (class_exists('App\Services\CloudinaryService') ? 'YES' : 'NO') . "\n";

// Check database connection
try {
    $pdo = DB::connection()->getPdo();
    echo "2. Database connection: OK\n";
} catch (Exception $e) {
    echo "2. Database connection: FAILED - " . $e->getMessage() . "\n";
    exit;
}

// Check if table exists
try {
    $tables = DB::select('SHOW TABLES');
    $tableNames = array_map(function($table) {
        return array_values((array)$table)[0];
    }, $tables);
    
    echo "3. Tables in database:\n";
    foreach($tableNames as $tableName) {
        echo "   - {$tableName}\n";
    }
    
    $hasCourseDesc = in_array('course_description', $tableNames);
    echo "   Course description table exists: " . ($hasCourseDesc ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "3. Error checking tables: " . $e->getMessage() . "\n";
}

// Test model instantiation
try {
    echo "4. Testing model instantiation:\n";
    $model = new App\Models\CourseDescription();
    echo "   - Model created successfully\n";
    
    $count = App\Models\CourseDescription::count();
    echo "   - Record count: {$count}\n";
    
} catch (Exception $e) {
    echo "   - Model instantiation failed: " . $e->getMessage() . "\n";
}

// Test CloudinaryService
try {
    echo "5. Testing CloudinaryService:\n";
    $service = app(App\Services\CloudinaryService::class);
    echo "   - Service created successfully\n";
    
    $storageType = $service->getStorageType();
    echo "   - Storage type: {$storageType}\n";
    
} catch (Exception $e) {
    echo "   - CloudinaryService failed: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
