<?php

// Simple web interface test
require_once 'vendor/autoload.php';

echo "🔍 Testing Web Interface Components\n";
echo "==================================\n\n";

// Test 1: Check if view file exists
$viewFile = 'resources/views/web-interface/enhanced.blade.php';
if (file_exists($viewFile)) {
    echo "✅ View file exists: $viewFile\n";
    $fileSize = filesize($viewFile);
    echo "   File size: " . number_format($fileSize) . " bytes\n";
} else {
    echo "❌ View file missing: $viewFile\n";
    exit(1);
}

// Test 2: Check for PHP syntax errors in view file
echo "\n2. Checking view file for syntax errors...\n";
$command = "php -l \"$viewFile\"";
$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ View file syntax is valid\n";
} else {
    echo "❌ View file has syntax errors:\n";
    foreach ($output as $line) {
        echo "   $line\n";
    }
}

// Test 3: Check for common issues in view file
echo "\n3. Checking for common issues...\n";
$content = file_get_contents($viewFile);

// Check for undefined variables
$issues = [];
if (strpos($content, '$restaurant->') !== false) {
    echo "✅ Uses \$restaurant variable\n";
}
if (strpos($content, '$categories') !== false) {
    echo "✅ Uses \$categories variable\n";
}
if (strpos($content, '$botToken') !== false) {
    echo "✅ Uses \$botToken variable\n";
}

// Check for potential issues
if (strpos($content, '{{') === false) {
    $issues[] = "No Blade syntax found";
}

if (strpos($content, '@') === false) {
    $issues[] = "No Blade directives found";
}

if (!empty($issues)) {
    echo "⚠️  Potential issues found:\n";
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
} else {
    echo "✅ No obvious issues found\n";
}

// Test 4: Test with minimal data
echo "\n4. Testing with minimal data structure...\n";

$testRestaurant = (object) [
    'id' => 1,
    'name' => 'Test Restaurant',
    'bot_token' => 'test_token',
    'primary_color' => '#667eea',
    'secondary_color' => '#764ba2',
    'accent_color' => '#ff6b35',
    'text_color' => '#2c3e50',
    'bg_color' => '#f8f9fa',
    'card_bg' => '#ffffff',
    'border_radius' => '16px',
    'shadow' => '0 8px 32px rgba(0,0,0,0.1)',
    'logo' => null,
    'phone' => '+998901234567',
    'address' => 'Test Address'
];

$testCategories = [];
$testBotToken = 'test_token_123';

// Try to render a small part of the view
echo "✅ Test data structure created\n";

echo "\n5. Manual URL test suggestions:\n";
echo "   - Test URL: http://localhost:8000/test-enhanced-web-interface\n";
echo "   - With bot token: http://localhost:8000/enhanced-web-interface?bot_token=YOUR_BOT_TOKEN\n";
echo "   - Direct test: http://localhost:8000/enhanced-web-interface/YOUR_BOT_TOKEN\n";

echo "\n✅ Web interface component test completed!\n";
echo "\nNext steps:\n";
echo "1. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "2. Try accessing the URL in browser\n";
echo "3. Check database for restaurant with bot_token\n"; 