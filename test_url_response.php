<?php

echo "=== URL RESPONSE TEST ===\n\n";

$appUrl = 'https://itqom-platform-aa0ffce6a276.herokuapp.com';

$testUrls = [
    '/' => 'Home page',
    '/admin' => 'Admin panel',
    '/api/courses' => 'API courses endpoint'
];

foreach($testUrls as $path => $description) {
    echo "Testing {$description}: {$appUrl}{$path}\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $appUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Test Bot)');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if($error) {
        echo "   ❌ cURL Error: {$error}\n";
    } else {
        $status = ($httpCode >= 200 && $httpCode < 400) ? '✅' : '❌';
        echo "   {$status} HTTP {$httpCode}\n";

        if($httpCode >= 200 && $httpCode < 400) {
            $responseLength = strlen($response);
            echo "   📄 Response length: {$responseLength} characters\n";

            if(strpos($response, 'Laravel') !== false) {
                echo "   ✅ Laravel detected in response\n";
            }
            if(strpos($response, 'error') !== false || strpos($response, 'Error') !== false) {
                echo "   ⚠️ Possible error in response\n";
            }
        }
    }
    echo "\n";
}

echo "=== URL TEST COMPLETED ===\n";
