<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

echo "=== Fix CloudinaryService Array to String Conversion ===\n\n";

// Path to CloudinaryService.php
$cloudinaryServicePath = app_path('Services/CloudinaryService.php');

// Check if file exists
if (!File::exists($cloudinaryServicePath)) {
    echo "Error: CloudinaryService.php not found at {$cloudinaryServicePath}\n";
    exit(1);
}

// Read the file content
$fileContent = File::get($cloudinaryServicePath);

// Find the problematic code pattern
$searchPattern = <<<'CODE'
                    try {
                        // Menggunakan Cloudinary SDK untuk menghasilkan URL yang valid
                        $options = [];
                        if (isset($transformations['width'])) $options['width'] = $transformations['width'];
                        if (isset($transformations['height'])) $options['height'] = $transformations['height'];
                        if (isset($transformations['crop'])) $options['crop'] = $transformations['crop'];
                        if (isset($transformations['quality'])) $options['quality'] = $transformations['quality'];
                        if (isset($transformations['fetch_format'])) $options['format'] = $transformations['fetch_format'];
                        
                        return $this->cloudinary->image($publicId)->toUrl($options);
CODE;

// Define replacement code
$replacementPattern = <<<'CODE'
                    try {
                        // Menggunakan Cloudinary SDK untuk menghasilkan URL yang valid
                        $options = [];
                        if (isset($transformations['width'])) $options['width'] = $transformations['width'];
                        if (isset($transformations['height'])) $options['height'] = $transformations['height'];
                        if (isset($transformations['crop'])) $options['crop'] = $transformations['crop'];
                        if (isset($transformations['quality'])) $options['quality'] = $transformations['quality'];
                        if (isset($transformations['fetch_format'])) $options['format'] = $transformations['fetch_format'];
                        
                        // Wrap options in transformation array to prevent Array to String conversion
                        return $this->cloudinary->image($publicId)->toUrl(['transformation' => $options]);
CODE;

// If the exact pattern isn't found, try a more flexible search
if (!str_contains($fileContent, $searchPattern)) {
    echo "Could not find exact pattern in the file. Trying a more generic approach...\n";
    
    // Define a more generic pattern using regex
    if (preg_match('/(\s+try\s*\{\s*\/\/\s*Menggunakan Cloudinary SDK.*?)return\s+\$this->cloudinary->image\(\$publicId\)->toUrl\(\$options\);/s', $fileContent, $matches)) {
        $searchPattern = $matches[0];
        $replacementPattern = $matches[1] . "return \$this->cloudinary->image(\$publicId)->toUrl(['transformation' => \$options]);";
    } else {
        echo "Could not find code to replace.\n";
        exit(1);
    }
}

// Replace the code
$newContent = str_replace($searchPattern, $replacementPattern, $fileContent);

// Check if any replacement was made
if ($newContent === $fileContent) {
    echo "No changes made. Could not find the pattern to replace.\n";
    
    // Try to locate the method
    if (preg_match('/public function getOptimizedUrl\(.*?\)\s*:\s*string.*?\{.*?\}/s', $fileContent, $matches)) {
        echo "Found getOptimizedUrl method, but could not find the exact code to replace.\n";
        echo "Please manually update the method to fix the Array to String conversion issue.\n";
    } else {
        echo "Could not locate getOptimizedUrl method. Please manually check the file.\n";
    }
    
    exit(1);
}

// Backup the original file
$backupPath = $cloudinaryServicePath . '.backup.' . time();
File::put($backupPath, $fileContent);
echo "Created backup at {$backupPath}\n";

// Save the modified file
File::put($cloudinaryServicePath, $newContent);
echo "Updated CloudinaryService.php successfully!\n";

echo "\n=== Fix Complete ===\n";
echo "The CloudinaryService.php file has been updated to fix the Array to String conversion error.\n";
echo "To apply these changes to your Heroku environment:\n";
echo "1. Commit the changes: git commit -am 'Fix Array to String conversion in CloudinaryService'\n";
echo "2. Push to Heroku: git push heroku main\n\n";

echo "You may need to clear the application cache after deploying:\n";
echo "heroku run php artisan cache:clear\n";
echo "heroku run php artisan config:clear\n\n";

echo "Done!\n";
