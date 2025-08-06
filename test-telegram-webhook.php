<?php

/**
 * Telegram Webhook Test Script
 * Bu script webhook ishlayotganini tekshirish uchun ishlatiladi
 */

// Test sozlamalari
$testConfig = [
    'webhook_url' => 'https://simpsons.uz/telegram-webhook/8169180454:AAGgWk8LV1--nxZIDxLo0MSjxTzV0968EcQ',
    'bot_token' => '8169180454:AAGgWk8LV1--nxZIDxLo0MSjxTzV0968EcQ',
    'chat_id' => '123456789' // O'zingizning chat ID nizni kiriting
];

// Test payload yaratish
function createTestPayload($text = '/start') {
    return [
        'update_id' => time(),
        'message' => [
            'message_id' => 1,
            'from' => [
                'id' => 123456789,
                'first_name' => 'Test',
                'username' => 'testuser',
                'language_code' => 'uz'
            ],
            'chat' => [
                'id' => 123456789,
                'type' => 'private'
            ],
            'date' => time(),
            'text' => $text
        ]
    ];
}

// Webhook test qilish
function testWebhook($url, $payload) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: TelegramBot/1.0'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

// Bot test qilish
function testBot($botToken, $chatId, $message) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $payload = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => json_decode($response, true),
        'error' => $error
    ];
}

// Webhook holatini tekshirish
function checkWebhookInfo($botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => json_decode($response, true),
        'error' => $error
    ];
}

// Test natijalarini ko'rsatish
function displayResult($testName, $result) {
    echo "=== {$testName} ===\n";
    echo "HTTP Code: {$result['http_code']}\n";
    
    if ($result['error']) {
        echo "Error: {$result['error']}\n";
    } else {
        echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    }
    echo "\n";
}

// Asosiy test
echo "Telegram Webhook Test Script\n";
echo "============================\n\n";

// 1. Webhook holatini tekshirish
echo "1. Webhook holatini tekshirish...\n";
$webhookInfo = checkWebhookInfo($testConfig['bot_token']);
displayResult('Webhook Info', $webhookInfo);

// 2. Bot test xabarini yuborish
echo "2. Bot test xabarini yuborish...\n";
$botTest = testBot($testConfig['bot_token'], $testConfig['chat_id'], '🧪 Test xabar - ' . date('Y-m-d H:i:s'));
displayResult('Bot Test', $botTest);

// 3. Webhook test qilish
echo "3. Webhook test qilish...\n";

// /start buyrug'i test
$startPayload = createTestPayload('/start');
$startTest = testWebhook($testConfig['webhook_url'], $startPayload);
displayResult('Webhook /start Test', $startTest);

// Buyurtmalarim test
$ordersPayload = createTestPayload('📊 Buyurtmalarim');
$ordersTest = testWebhook($testConfig['webhook_url'], $ordersPayload);
displayResult('Webhook Buyurtmalarim Test', $ordersTest);

// Yordam test
$helpPayload = createTestPayload('ℹ Yordam');
$helpTest = testWebhook($testConfig['webhook_url'], $helpPayload);
displayResult('Webhook Yordam Test', $helpTest);

echo "Test yakunlandi!\n";
echo "Natijalarni tekshiring va kerak bo'lsa sozlamalarni yangilang.\n"; 