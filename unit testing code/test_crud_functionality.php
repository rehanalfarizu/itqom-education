<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;

echo "=== TEST CRUD FUNCTIONALITY ===\n\n";

try {
    echo "1. Current Data in Database:\n";
    $courses = CourseDescription::all();

    foreach($courses as $course) {
        echo "   Course ID: {$course->id}\n";
        echo "   Title: {$course->title}\n";
        echo "   Image URL: {$course->image_url}\n";
        echo "   Price: " . number_format((float)$course->price) . "\n";
        echo "   Created: {$course->created_at}\n";
        echo "   Updated: {$course->updated_at}\n";
        echo "   ---\n";
    }

    echo "\n2. Test Image URL Generation:\n";
    foreach($courses as $course) {
        echo "   Course {$course->id}:\n";

        // Test accessor
        try {
            $imageUrl = $course->image_url;
            echo "     Raw image_url: {$course->getRawOriginal('image_url')}\n";
            echo "     Processed URL: {$imageUrl}\n";

            // Check if URL is accessible
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $status = ($httpCode >= 200 && $httpCode < 400) ? '✅' : '❌';
            echo "     URL Status: {$status} HTTP {$httpCode}\n";

        } catch(\Exception $e) {
            echo "     Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    echo "3. Test API Endpoint:\n";
    $apiUrl = 'https://itqom-platform-aa0ffce6a276.herokuapp.com/api/courses';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode == 200) {
        $data = json_decode($response, true);
        echo "   ✅ API Response OK\n";

        if(is_array($data) && count($data) > 0) {
            echo "   📊 Found " . count($data) . " courses in API\n";

            foreach($data as $index => $course) {
                echo "   Course " . ($index + 1) . ":\n";
                echo "     ID: " . ($course['id'] ?? 'N/A') . "\n";
                echo "     Title: " . ($course['title'] ?? 'N/A') . "\n";
                echo "     Image: " . ($course['image_url'] ?? 'N/A') . "\n";
                echo "     Price: " . ($course['price'] ?? 'N/A') . "\n";
                echo "\n";
            }
        } else {
            echo "   ⚠️ API returned empty or invalid data\n";
        }
    } else {
        echo "   ❌ API Error: HTTP {$httpCode}\n";
    }

} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== TEST COMPLETED ===\n";
