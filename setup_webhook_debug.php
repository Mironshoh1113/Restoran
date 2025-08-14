<?php
/**
 * Telegram Webhook Debug and Setup Script
 * 
 * This script helps debug webhook issues and setup webhooks for Telegram bots
 */

// Get bot token from command line argument
$botToken = $argv[1] ?? null;

if (!$botToken) {
    echo "❌ Usage: php setup_webhook_debug.php <BOT_TOKEN>\n";
    echo "Example: php setup_webhook_debug.php 1234567890:ABCdefGHIjklMNOpqrsTUVwxyz\n";
    exit(1);
}

// Your domain URL
$domain = 'https://simpsons.uz'; // Updated to correct domain

// Webhook URL
$webhookUrl = $domain . '/api/telegram-webhook/' . $botToken;
$debugWebhookUrl = $domain . '/api/debug-webhook/' . $botToken;

echo "🤖 Telegram Webhook Debug Script\n";
echo "================================\n";
echo "Bot Token: " . substr($botToken, 0, 10) . "...\n";
echo "Webhook URL: $webhookUrl\n";
echo "Debug URL: $debugWebhookUrl\n\n";

// Function to make Telegram API request
function telegramRequest($botToken, $method, $data = []) {
    $url = "https://api.telegram.org/bot$botToken/$method";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// 1. Get current webhook info
echo "1️⃣ Getting current webhook info...\n";
$webhookInfo = telegramRequest($botToken, 'getWebhookInfo');

if ($webhookInfo['http_code'] === 200) {
    $info = $webhookInfo['response']['result'];
    echo "✅ Current webhook URL: " . ($info['url'] ?: 'Not set') . "\n";
    echo "✅ Pending updates: " . $info['pending_update_count'] . "\n";
    echo "✅ Last error date: " . ($info['last_error_date'] ?? 'None') . "\n";
    echo "✅ Last error message: " . ($info['last_error_message'] ?? 'None') . "\n";
} else {
    echo "❌ Failed to get webhook info\n";
    print_r($webhookInfo);
}

echo "\n";

// 2. Delete existing webhook
echo "2️⃣ Deleting existing webhook...\n";
$deleteResult = telegramRequest($botToken, 'deleteWebhook');

if ($deleteResult['http_code'] === 200 && $deleteResult['response']['ok']) {
    echo "✅ Webhook deleted successfully\n";
} else {
    echo "❌ Failed to delete webhook\n";
    print_r($deleteResult);
}

echo "\n";

// 3. Set new webhook
echo "3️⃣ Setting new webhook...\n";
$setResult = telegramRequest($botToken, 'setWebhook', [
    'url' => $webhookUrl,
    'allowed_updates' => ['message', 'callback_query']
]);

if ($setResult['http_code'] === 200 && $setResult['response']['ok']) {
    echo "✅ Webhook set successfully\n";
    echo "✅ Webhook URL: $webhookUrl\n";
} else {
    echo "❌ Failed to set webhook\n";
    print_r($setResult);
}

echo "\n";

// 4. Verify webhook
echo "4️⃣ Verifying webhook...\n";
$verifyInfo = telegramRequest($botToken, 'getWebhookInfo');

if ($verifyInfo['http_code'] === 200) {
    $info = $verifyInfo['response']['result'];
    echo "✅ Webhook URL: " . $info['url'] . "\n";
    echo "✅ Has custom certificate: " . ($info['has_custom_certificate'] ? 'Yes' : 'No') . "\n";
    echo "✅ Pending updates: " . $info['pending_update_count'] . "\n";
    
    if (isset($info['last_error_date'])) {
        echo "⚠️ Last error date: " . date('Y-m-d H:i:s', $info['last_error_date']) . "\n";
        echo "⚠️ Last error message: " . $info['last_error_message'] . "\n";
    }
} else {
    echo "❌ Failed to verify webhook\n";
}

echo "\n";

// 5. Test webhook endpoint
echo "5️⃣ Testing webhook endpoint...\n";
$testData = [
    'update_id' => 123456789,
    'message' => [
        'message_id' => 1,
        'from' => [
            'id' => 123456789,
            'is_bot' => false,
            'first_name' => 'Test',
            'username' => 'testuser'
        ],
        'chat' => [
            'id' => 123456789,
            'first_name' => 'Test',
            'username' => 'testuser',
            'type' => 'private'
        ],
        'date' => time(),
        'text' => '/start'
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $debugWebhookUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$testResponse = curl_exec($ch);
$testHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($testHttpCode === 200) {
    echo "✅ Webhook endpoint is reachable\n";
    $testResult = json_decode($testResponse, true);
    if ($testResult['success']) {
        echo "✅ Debug webhook test passed\n";
    } else {
        echo "❌ Debug webhook test failed: " . $testResult['error'] . "\n";
    }
} else {
    echo "❌ Webhook endpoint is not reachable (HTTP $testHttpCode)\n";
    echo "Response: $testResponse\n";
}

echo "\n";
echo "🎉 Webhook setup complete!\n";
echo "\n";
echo "📋 Next steps:\n";
echo "1. Test your bot by sending /start command\n";
echo "2. Check server logs for webhook requests\n";
echo "3. Visit: $domain/api/test-webhook-setup\n";
echo "4. If issues persist, check your server's SSL certificate\n";
echo "\n";
echo "🐛 Debug commands:\n";
echo "- Test webhook: curl -X POST $debugWebhookUrl -H 'Content-Type: application/json' -d '{\"test\":\"data\"}'\n";
echo "- Check logs: tail -f storage/logs/laravel.log\n";
echo "- Webhook info: curl https://api.telegram.org/bot$botToken/getWebhookInfo\n";
?> 