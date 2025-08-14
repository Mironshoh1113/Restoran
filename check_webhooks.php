<?php

/**
 * Check and Clean Up Telegram Webhooks
 * This script helps identify and fix duplicate webhook issues
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Restaurant;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

echo "🔍 Checking Telegram Webhooks...\n\n";

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
        
        // Get current webhook info
        $webhookInfo = $telegramService->getWebhookInfo();
        
        if (!$webhookInfo['ok']) {
            echo "❌ Failed to get webhook info: " . ($webhookInfo['error'] ?? 'Unknown error') . "\n";
            continue;
        }
        
        $result = $webhookInfo['result'];
        
        if ($result['url']) {
            echo "🔗 Current Webhook URL: {$result['url']}\n";
            echo "📊 Pending Updates: " . ($result['pending_update_count'] ?? 0) . "\n";
            echo "❌ Failed Updates: " . ($result['last_error_message'] ?? 'None') . "\n";
            
            // Check if there are pending updates that might cause duplicate messages
            if (($result['pending_update_count'] ?? 0) > 10) {
                echo "⚠️  High number of pending updates detected!\n";
                echo "   This might cause duplicate message issues.\n";
                
                // Ask if user wants to clear pending updates
                echo "\nDo you want to clear pending updates? (y/n): ";
                $handle = fopen("php://stdin", "r");
                $response = trim(fgets($handle));
                fclose($handle);
                
                if (strtolower($response) === 'y') {
                    echo "🧹 Clearing pending updates...\n";
                    
                    // Delete webhook and set it again to clear pending updates
                    $deleteResult = $telegramService->deleteWebhook();
                    if ($deleteResult['ok']) {
                        echo "✅ Webhook deleted successfully.\n";
                        
                        // Set webhook again
                        $webhookUrl = url("/telegram-webhook/{$restaurant->bot_token}");
                        $setResult = $telegramService->setWebhook($webhookUrl);
                        
                        if ($setResult['ok']) {
                            echo "✅ Webhook set successfully: {$webhookUrl}\n";
                        } else {
                            echo "❌ Failed to set webhook: " . ($setResult['error'] ?? 'Unknown error') . "\n";
                        }
                    } else {
                        echo "❌ Failed to delete webhook: " . ($deleteResult['error'] ?? 'Unknown error') . "\n";
                    }
                }
            }
        } else {
            echo "❌ No webhook URL set.\n";
            
            // Ask if user wants to set webhook
            echo "\nDo you want to set webhook? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $response = trim(fgets($handle));
            fclose($handle);
            
            if (strtolower($response) === 'y') {
                $webhookUrl = url("/telegram-webhook/{$restaurant->bot_token}");
                $setResult = $telegramService->setWebhook($webhookUrl);
                
                if ($setResult['ok']) {
                    echo "✅ Webhook set successfully: {$webhookUrl}\n";
                } else {
                    echo "❌ Failed to set webhook: " . ($setResult['error'] ?? 'Unknown error') . "\n";
                }
            }
        }
        
        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
    
    echo "✅ Webhook check completed.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    Log::error('Webhook check error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} 