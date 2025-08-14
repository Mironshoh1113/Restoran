<?php

/**
 * Fix Telegram Webhook URLs
 * This script ensures webhooks are set correctly and prevents duplicates
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Restaurant;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

echo "🔧 Fixing Telegram Webhooks...\n\n";

try {
    // Get all restaurants with bot tokens
    $restaurants = Restaurant::whereNotNull('bot_token')->get();
    
    if ($restaurants->isEmpty()) {
        echo "❌ No restaurants with bot tokens found.\n";
        exit;
    }
    
    foreach ($restaurants as $restaurant) {
        echo "🏪 Restaurant: {$restaurant->name}\n";
        echo "🤖 Bot Token: {$restaurant->bot_token}\n";
        
        $telegramService = new TelegramService($restaurant->bot_token);
        
        // First, delete any existing webhook to clear pending updates
        echo "🧹 Deleting existing webhook...\n";
        $deleteResult = $telegramService->deleteWebhook();
        
        if ($deleteResult['ok']) {
            echo "✅ Webhook deleted successfully.\n";
        } else {
            echo "⚠️  Failed to delete webhook: " . ($deleteResult['error'] ?? 'Unknown error') . "\n";
        }
        
        // Wait a moment for Telegram to process
        sleep(2);
        
        // Set the correct webhook URL
        $webhookUrl = url("/telegram-webhook/{$restaurant->bot_token}");
        echo "🔗 Setting webhook to: {$webhookUrl}\n";
        
        $setResult = $telegramService->setWebhook($webhookUrl);
        
        if ($setResult['ok']) {
            echo "✅ Webhook set successfully!\n";
            
            // Verify webhook is set correctly
            $webhookInfo = $telegramService->getWebhookInfo();
            if ($webhookInfo['ok'] && $webhookInfo['result']['url'] === $webhookUrl) {
                echo "✅ Webhook verified successfully!\n";
                echo "📊 Pending Updates: " . ($webhookInfo['result']['pending_update_count'] ?? 0) . "\n";
            } else {
                echo "⚠️  Webhook verification failed!\n";
            }
        } else {
            echo "❌ Failed to set webhook: " . ($setResult['error'] ?? 'Unknown error') . "\n";
        }
        
        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
    
    echo "✅ Webhook fixing completed.\n";
    echo "\n💡 Tips to prevent duplicate messages:\n";
    echo "1. Make sure only one webhook URL is set per bot\n";
    echo "2. Check that webhook URLs are unique for each restaurant\n";
    echo "3. Monitor logs for duplicate message warnings\n";
    echo "4. Use the check_webhooks.php script to monitor webhook health\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    Log::error('Webhook fixing error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} 