<?php

echo "=== CLOUDINARY INTEGRATION TEST SUITE ===\n";
echo "Running comprehensive tests...\n\n";

$testFiles = [
    'unit_test_cloudinary.php' => 'CloudinaryService Unit Tests',
    'unit_test_database.php' => 'Database Integration Tests',
    'unit_test_frontend.php' => 'Frontend Integration Tests',
    'unit_test_complete.php' => 'Complete Integration Tests'
];

$allTestResults = [];
$startTime = microtime(true);

foreach ($testFiles as $file => $description) {
    echo str_repeat("=", 80) . "\n";
    echo "🧪 RUNNING: $description\n";
    echo str_repeat("=", 80) . "\n";

    $testStartTime = microtime(true);

    if (file_exists($file)) {
        ob_start();
        include $file;
        $output = ob_get_clean();

        $testEndTime = microtime(true);
        $testDuration = round(($testEndTime - $testStartTime), 2);

        echo $output;
        echo "\n⏱️ Test completed in {$testDuration} seconds\n\n";

        // Analyze output for pass/fail
        $passCount = substr_count($output, '✅');
        $failCount = substr_count($output, '❌');
        $warnCount = substr_count($output, '⚠️');

        $allTestResults[$description] = [
            'duration' => $testDuration,
            'pass' => $passCount,
            'fail' => $failCount,
            'warn' => $warnCount
        ];
    } else {
        echo "❌ Test file not found: $file\n\n";
        $allTestResults[$description] = [
            'duration' => 0,
            'pass' => 0,
            'fail' => 1,
            'warn' => 0
        ];
    }
}

$endTime = microtime(true);
$totalDuration = round(($endTime - $startTime), 2);

// Final Report
echo str_repeat("=", 80) . "\n";
echo "📋 FINAL TEST REPORT\n";
echo str_repeat("=", 80) . "\n";

$totalPass = 0;
$totalFail = 0;
$totalWarn = 0;

foreach ($allTestResults as $testName => $results) {
    $totalPass += $results['pass'];
    $totalFail += $results['fail'];
    $totalWarn += $results['warn'];

    echo "🧪 $testName:\n";
    echo "   ✅ Passed: {$results['pass']}\n";
    echo "   ❌ Failed: {$results['fail']}\n";
    echo "   ⚠️ Warnings: {$results['warn']}\n";
    echo "   ⏱️ Duration: {$results['duration']}s\n";
    echo "\n";
}

echo str_repeat("-", 60) . "\n";
echo "📊 OVERALL SUMMARY:\n";
echo "   Total Tests Run: " . ($totalPass + $totalFail) . "\n";
echo "   ✅ Total Passed: $totalPass\n";
echo "   ❌ Total Failed: $totalFail\n";
echo "   ⚠️ Total Warnings: $totalWarn\n";
echo "   ⏱️ Total Duration: {$totalDuration}s\n";

$successRate = $totalPass + $totalFail > 0 ? round(($totalPass / ($totalPass + $totalFail)) * 100, 1) : 0;
echo "   📈 Success Rate: {$successRate}%\n";

// Final Verdict
echo "\n🎯 FINAL VERDICT:\n";
if ($totalFail === 0 && $successRate >= 95) {
    echo "🎉 EXCELLENT! All systems are working perfectly.\n";
    echo "✅ Your CloudinaryService integration is production-ready!\n";
    echo "\n🚀 RECOMMENDATIONS:\n";
    echo "   • Deploy to production with confidence\n";
    echo "   • Monitor CloudinaryService logs in production\n";
    echo "   • Set up automated testing pipeline\n";
} elseif ($successRate >= 80) {
    echo "✅ GOOD! Most functionality is working correctly.\n";
    echo "🔧 Minor issues detected - consider addressing warnings.\n";
    echo "\n🛠️ RECOMMENDATIONS:\n";
    echo "   • Address failed tests if critical\n";
    echo "   • Monitor warning areas closely\n";
    echo "   • Deploy with caution\n";
} elseif ($successRate >= 60) {
    echo "⚠️ PARTIAL SUCCESS! Core functionality works but issues exist.\n";
    echo "🔧 Several areas need attention before production.\n";
    echo "\n🛠️ RECOMMENDATIONS:\n";
    echo "   • Fix critical failed tests\n";
    echo "   • Re-run tests after fixes\n";
    echo "   • Consider staging environment testing\n";
} else {
    echo "❌ CRITICAL ISSUES DETECTED! Major problems found.\n";
    echo "🚨 Do not deploy to production until issues are resolved.\n";
    echo "\n🛠️ RECOMMENDATIONS:\n";
    echo "   • Review CloudinaryService implementation\n";
    echo "   • Check environment configuration\n";
    echo "   • Fix database integration issues\n";
    echo "   • Re-run complete test suite\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Test suite completed at " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 80) . "\n";
