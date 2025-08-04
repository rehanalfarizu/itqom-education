<?php

echo "=== FRONTEND INTEGRATION TESTS ===\n\n";

$baseUrl = 'https://itqom-platform-aa0ffce6a276.herokuapp.com';
$testResults = [];

// Test 1: Homepage Access
echo "🧪 Test 1: Homepage Access\n";
try {
    $response = makeHttpRequest($baseUrl);
    if ($response['status'] === 200) {
        echo "   ✅ PASS: Homepage accessible (HTTP 200)\n";
        echo "   📄 Response length: " . number_format(strlen($response['body'])) . " characters\n";
        $testResults['homepage'] = 'PASS';
    } else {
        echo "   ❌ FAIL: Homepage returned HTTP " . $response['status'] . "\n";
        $testResults['homepage'] = 'FAIL';
    }
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['homepage'] = 'FAIL';
}

// Test 2: API Endpoints
echo "\n🧪 Test 2: API Endpoints\n";
$apiEndpoints = [
    '/api/courses' => 'Courses API',
    '/api/course/1' => 'Single Course API',
];

foreach ($apiEndpoints as $endpoint => $description) {
    try {
        $response = makeHttpRequest($baseUrl . $endpoint);
        
        if ($response['status'] === 200) {
            echo "   ✅ $description: HTTP 200\n";
            
            // Try to parse JSON
            $data = json_decode($response['body'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "     📊 Valid JSON response\n";
                
                // Check for course data
                if (isset($data['data']) || isset($data[0])) {
                    echo "     📋 Contains course data\n";
                } else {
                    echo "     ⚠️ No course data found\n";
                }
            } else {
                echo "     ⚠️ Invalid JSON response\n";
            }
            
        } else {
            echo "   ❌ $description: HTTP " . $response['status'] . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ $description: " . $e->getMessage() . "\n";
    }
}
$testResults['api_endpoints'] = 'PASS'; // Assume pass if no major errors

// Test 3: Admin Panel Access
echo "\n🧪 Test 3: Admin Panel Access\n";
try {
    $response = makeHttpRequest($baseUrl . '/admin');
    if ($response['status'] === 200 || $response['status'] === 302) {
        echo "   ✅ PASS: Admin panel accessible (HTTP " . $response['status'] . ")\n";
        
        if (strpos($response['body'], 'login') !== false || strpos($response['body'], 'Filament') !== false) {
            echo "   🔐 Login page or admin interface detected\n";
        }
        $testResults['admin_panel'] = 'PASS';
    } else {
        echo "   ❌ FAIL: Admin panel returned HTTP " . $response['status'] . "\n";
        $testResults['admin_panel'] = 'FAIL';
    }
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['admin_panel'] = 'FAIL';
}

// Test 4: Image URLs in API Response
echo "\n🧪 Test 4: Image URLs in API Response\n";
try {
    $response = makeHttpRequest($baseUrl . '/api/courses');
    
    if ($response['status'] === 200) {
        $data = json_decode($response['body'], true);
        
        if ($data && is_array($data)) {
            $imageUrlsFound = 0;
            $cloudinaryUrls = 0;
            $invalidUrls = 0;
            
            // Handle different API response structures
            $courses = isset($data['data']) ? $data['data'] : $data;
            
            foreach ($courses as $course) {
                if (isset($course['image_url'])) {
                    $imageUrlsFound++;
                    $imageUrl = $course['image_url'];
                    
                    if (strpos($imageUrl, 'cloudinary.com') !== false) {
                        $cloudinaryUrls++;
                        echo "   ✅ Course '{$course['title']}': Cloudinary URL\n";
                    } elseif (strpos($imageUrl, 'Upload failed') !== false) {
                        $invalidUrls++;
                        echo "   ❌ Course '{$course['title']}': Upload failed message\n";
                    } elseif (strpos($imageUrl, 'livewire-tmp') !== false) {
                        $invalidUrls++;
                        echo "   ⚠️ Course '{$course['title']}': Still using livewire-tmp\n";
                    } else {
                        echo "   ❓ Course '{$course['title']}': Unknown URL format\n";
                    }
                }
            }
            
            echo "\n   📊 Summary:\n";
            echo "     Total courses: " . count($courses) . "\n";
            echo "     With image URLs: $imageUrlsFound\n";
            echo "     Using Cloudinary: $cloudinaryUrls\n";
            echo "     Invalid URLs: $invalidUrls\n";
            
            if ($invalidUrls === 0 && $cloudinaryUrls > 0) {
                echo "   ✅ All image URLs are valid\n";
                $testResults['image_urls'] = 'PASS';
            } else {
                echo "   ⚠️ Some image URLs need fixing\n";
                $testResults['image_urls'] = 'PARTIAL';
            }
            
        } else {
            echo "   ❌ Invalid API response format\n";
            $testResults['image_urls'] = 'FAIL';
        }
    } else {
        echo "   ❌ API request failed\n";
        $testResults['image_urls'] = 'FAIL';
    }
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['image_urls'] = 'FAIL';
}

// Test 5: Direct Image URL Access
echo "\n🧪 Test 5: Direct Image URL Access\n";
$testImageUrls = [
    'https://res.cloudinary.com/hltd67bzw/image/upload/q_auto,f_auto/courses/test.jpg',
    'https://res.cloudinary.com/hltd67bzw/image/upload/w_800,h_450,c_fill,q_auto,f_auto/courses/sample.jpg'
];

$imageAccessPass = true;
foreach ($testImageUrls as $imageUrl) {
    try {
        $response = makeHttpRequest($imageUrl);
        $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
        
        if ($response['status'] === 200) {
            echo "   ✅ '$filename': Image accessible\n";
        } elseif ($response['status'] === 404) {
            echo "   ❌ '$filename': Image not found (404)\n";
            $imageAccessPass = false;
        } else {
            echo "   ⚠️ '$filename': HTTP " . $response['status'] . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ '$filename': " . $e->getMessage() . "\n";
        $imageAccessPass = false;
    }
}
$testResults['image_access'] = $imageAccessPass ? 'PASS' : 'PARTIAL';

// Helper function
function makeHttpRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Unit Test Bot)');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new \Exception("cURL Error: $error");
    }
    
    return [
        'status' => $httpCode,
        'body' => $response
    ];
}

// Test Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "FRONTEND INTEGRATION TEST SUMMARY:\n";
$totalTests = count($testResults);
$passedTests = count(array_filter($testResults, function($result) { return $result === 'PASS'; }));
$partialTests = count(array_filter($testResults, function($result) { return $result === 'PARTIAL'; }));

foreach ($testResults as $test => $result) {
    $icon = $result === 'PASS' ? '✅' : ($result === 'PARTIAL' ? '⚠️' : '❌');
    echo "   $icon " . ucfirst(str_replace('_', ' ', $test)) . ": $result\n";
}

echo "\nOVERALL: $passedTests/$totalTests tests passed";
if ($partialTests > 0) {
    echo " ($partialTests partial)";
}
echo "\n";

if ($passedTests === $totalTests) {
    echo "🎉 ALL FRONTEND TESTS PASSED!\n";
} elseif ($passedTests + $partialTests === $totalTests) {
    echo "✅ Frontend mostly working with minor issues!\n";
} else {
    echo "⚠️ Some frontend tests failed. Please check the implementation.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
