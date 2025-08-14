<?php

// Simple webhook test script
$webhookUrl = 'https://simpsons.uz/api/telegram-webhook/8414997692:AAF7AZSs_PP8F9PgxJj9lf5025warHYF19A';

echo "🔍 Testing webhook URL: $webhookUrl\n\n";

// Test GET request
echo "1. Testing GET request...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Webhook Test Script');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
if ($error) {
    echo "   Error: $error\n";
} else {
    echo "   Response: $response\n";
}

echo "\n";

// Test POST request with sample data
echo "2. Testing POST request with sample data...\n";
$testData = [
    'update_id' => 123456789,
    'message' => [
        'message_id' => 1,
        'from' => [
            'id' => 123456789,
            'is_bot' => false,
            'first_name' => 'Test User'
        ],
        'chat' => [
            'id' => 123456789,
            'type' => 'private'
        ],
        'date' => time(),
        'text' => '/start'
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhookUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: Telegram-Bot-API/1.0'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
if ($error) {
    echo "   Error: $error\n";
} else {
    echo "   Response: $response\n";
}

echo "\n✅ Test completed!\n"; 