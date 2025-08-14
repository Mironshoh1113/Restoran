<?php

// Real-time webhook debugging script
echo "🔍 Webhook Debug - Real Time\n";
echo "============================\n\n";

$botTokens = [
    '8024961324:AAEaUivDfaNC4JobEuboYoZyzECL8dBgt7k', // New bot from user
    '8414997692:AAF7AZSs_PP8F9PgxJj9lf5025warHYF19A'  // Original bot
];

foreach ($botTokens as $botToken) {
    echo "🤖 Testing Bot: " . substr($botToken, 0, 10) . "...\n";
    echo "================================\n";
    
    // 1. Check webhook info
    echo "1. Checking webhook info...\n";
    $webhookUrl = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && $data['ok']) {
            $result = $data['result'];
            echo "   ✅ Webhook URL: " . ($result['url'] ?? 'Not set') . "\n";
            echo "   📊 Pending updates: " . ($result['pending_update_count'] ?? 0) . "\n";
            if (isset($result['last_error_message'])) {
                echo "   ❌ Last error: " . $result['last_error_message'] . "\n";
                echo "   📅 Error date: " . date('Y-m-d H:i:s', $result['last_error_date']) . "\n";
            } else {
                echo "   ✅ No errors\n";
            }
        } else {
            echo "   ❌ Failed to get webhook info\n";
        }
    } else {
        echo "   ❌ HTTP Error: $httpCode\n";
    }
    
    // 2. Test our webhook endpoint
    echo "\n2. Testing our webhook endpoint...\n";
    $ourWebhookUrl = "https://simpsons.uz/api/telegram-webhook/{$botToken}";
    
    // Test GET request
    echo "   GET request: ";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ourWebhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "✅ Working\n";
        } else {
            echo "❌ Response: $response\n";
        }
    } else {
        echo "❌ HTTP $httpCode\n";
    }
    
    // Test POST request with sample /start message
    echo "   POST request (simulated /start): ";
    $testMessage = [
        'update_id' => rand(1000000, 9999999),
        'message' => [
            'message_id' => rand(1, 1000),
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
    curl_setopt($ch, CURLOPT_URL, $ourWebhookUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testMessage));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: TelegramBot (like TwitterBot)'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && trim($response) === 'OK') {
        echo "✅ Working (returned OK)\n";
    } else {
        echo "❌ HTTP $httpCode, Response: $response\n";
    }
    
    // 3. Check if bot can send messages
    echo "\n3. Testing bot send message capability...\n";
    $testChatId = 123456789; // Test chat ID
    $sendUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $messageData = [
        'chat_id' => $testChatId,
        'text' => 'Test message from debug script',
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sendUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($messageData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLOPT_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && $data['ok']) {
            echo "   ✅ Bot can send messages\n";
        } else {
            echo "   ❌ Send failed: " . ($data['description'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "   ❌ HTTP Error: $httpCode\n";
    }
    
    // 4. Check database for this bot
    echo "\n4. Checking database for this bot...\n";
    try {
        // This would need to be run in Laravel context
        echo "   ℹ️  Run this manually: Restaurant::where('bot_token', '$botToken')->first()\n";
    } catch (Exception $e) {
        echo "   ❌ Database check failed\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

echo "🔧 Manual debugging steps:\n";
echo "1. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "2. Send /start to bot and watch logs\n";
echo "3. Check database: php artisan tinker -> Restaurant::where('bot_token', 'TOKEN')->first()\n";
echo "4. Test webhook manually: curl -X POST 'webhook_url' -d 'test_data'\n";
echo "\n✅ Debug completed!\n"; 