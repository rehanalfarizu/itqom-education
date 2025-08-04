<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Quick script to check Cloudinary configuration
echo "=== Cloudinary Configuration Check ===\n";

// Check environment variables
$cloudName = env('CLOUDINARY_CLOUD_NAME');
$apiKey = env('CLOUDINARY_API_KEY');
$apiSecret = env('CLOUDINARY_API_SECRET');

echo "Cloud Name: " . ($cloudName ? 'SET' : 'MISSING') . "\n";
echo "API Key: " . ($apiKey ? 'SET' : 'MISSING') . "\n";
echo "API Secret: " . ($apiSecret ? 'SET' : 'MISSING') . "\n";

// Check config values
echo "\nConfig values:\n";
echo "Cloud Name: " . (config('cloudinary.cloud.cloud_name') ?: 'NOT SET') . "\n";
echo "API Key: " . (config('cloudinary.cloud.api_key') ?: 'NOT SET') . "\n";
echo "API Secret: " . (config('cloudinary.cloud.api_secret') ? 'SET' : 'NOT SET') . "\n";

// Check if Cloudinary package is installed
try {
    $cloudinary = new \Cloudinary\Cloudinary();
    echo "\nCloudinary package: INSTALLED\n";
} catch (Exception $e) {
    echo "\nCloudinary package: NOT INSTALLED or ERROR\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Instructions ===\n";
echo "On Heroku, run these commands to set environment variables:\n";
echo "heroku config:set CLOUDINARY_CLOUD_NAME=your_cloud_name\n";
echo "heroku config:set CLOUDINARY_API_KEY=your_api_key\n";
echo "heroku config:set CLOUDINARY_API_SECRET=your_api_secret\n";
