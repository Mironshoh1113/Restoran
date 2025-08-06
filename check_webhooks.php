<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Services\TelegramService;

echo "=== WEBHOOK CONFIGURATION CHECK ===\n\n";

$restaurants = Restaurant::whereNotNull('bot_token')->get();

foreach ($restaurants as $restaurant) {
    echo "Restaurant: {$restaurant->name}\n";
    echo "Bot Token: {$restaurant->bot_token}\n";
    echo "Bot Username: {$restaurant->bot_username}\n";
    
    // Test bot connection
    try {
        $telegramService = new TelegramService($restaurant->bot_token);
        $botInfo = $telegramService->getMe();
        
        if ($botInfo['ok']) {
            echo "✅ Bot connection: SUCCESS\n";
            echo "Bot Name: {$botInfo['result']['first_name']}\n";
            echo "Bot Username: @{$botInfo['result']['username']}\n";
        } else {
            echo "❌ Bot connection: FAILED\n";
            echo "Error: " . ($botInfo['description'] ?? 'Unknown error') . "\n";
        }
        
        // Check webhook info
        $webhookInfo = $telegramService->getWebhookInfo();
        echo "Webhook URL: " . ($webhookInfo['result']['url'] ?? 'NOT SET') . "\n";
        echo "Webhook Active: " . ($webhookInfo['result']['url'] ? 'YES' : 'NO') . "\n";
        
        if ($webhookInfo['result']['url']) {
            echo "Last Error: " . ($webhookInfo['result']['last_error_message'] ?? 'None') . "\n";
            echo "Pending Updates: " . ($webhookInfo['result']['pending_update_count'] ?? 0) . "\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Bot connection: ERROR - {$e->getMessage()}\n";
    }
    
    echo "---\n\n";
}

echo "=== RECOMMENDED WEBHOOK URLS ===\n\n";

foreach ($restaurants as $restaurant) {
    $webhookUrl = url("/telegram/webhook/{$restaurant->bot_token}");
    echo "Restaurant: {$restaurant->name}\n";
    echo "Webhook URL: {$webhookUrl}\n";
    echo "---\n\n";
} 