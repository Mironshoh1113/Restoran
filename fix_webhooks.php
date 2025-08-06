<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Services\TelegramService;

echo "=== FIXING WEBHOOK CONFIGURATIONS ===\n\n";

$restaurants = Restaurant::whereNotNull('bot_token')->get();

foreach ($restaurants as $restaurant) {
    echo "Processing Restaurant: {$restaurant->name}\n";
    echo "Bot Token: {$restaurant->bot_token}\n";
    
    // Skip test tokens
    if (strpos($restaurant->bot_token, 'test_') === 0) {
        echo "⚠️  Skipping test token\n";
        echo "---\n\n";
        continue;
    }
    
    try {
        $telegramService = new TelegramService($restaurant->bot_token);
        
        // Test bot connection first
        $botInfo = $telegramService->getMe();
        
        if (!$botInfo['ok']) {
            echo "❌ Bot token is invalid: " . ($botInfo['description'] ?? 'Unknown error') . "\n";
            echo "---\n\n";
            continue;
        }
        
        echo "✅ Bot connection successful\n";
        echo "Bot Name: {$botInfo['result']['first_name']}\n";
        echo "Bot Username: @{$botInfo['result']['username']}\n";
        
        // Set webhook URL
        $webhookUrl = url("/telegram/webhook/{$restaurant->bot_token}");
        echo "Setting webhook URL: {$webhookUrl}\n";
        
        $webhookResult = $telegramService->setWebhook($webhookUrl);
        
        if ($webhookResult['ok']) {
            echo "✅ Webhook set successfully\n";
            
            // Set bot commands
            $commands = [
                ['command' => 'start', 'description' => 'Botni ishga tushirish'],
                ['command' => 'menu', 'description' => 'Menyuni ko\'rish'],
                ['command' => 'cart', 'description' => 'Savatni ko\'rish'],
                ['command' => 'help', 'description' => 'Yordam']
            ];
            
            $commandsResult = $telegramService->setMyCommands($commands);
            
            if ($commandsResult['ok']) {
                echo "✅ Bot commands set successfully\n";
            } else {
                echo "⚠️  Failed to set bot commands: " . ($commandsResult['description'] ?? 'Unknown error') . "\n";
            }
            
        } else {
            echo "❌ Failed to set webhook: " . ($webhookResult['description'] ?? 'Unknown error') . "\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Error: {$e->getMessage()}\n";
    }
    
    echo "---\n\n";
}

echo "=== SUMMARY ===\n\n";
echo "To make all bots work:\n";
echo "1. Ensure all bot tokens are valid\n";
echo "2. Set webhook URLs for each bot\n";
echo "3. Configure bot commands\n";
echo "4. Test the webhook endpoints\n\n";

echo "For each restaurant, you need to:\n";
echo "- Go to admin panel\n";
echo "- Navigate to Bot settings\n";
echo "- Set the correct webhook URL\n";
echo "- Test the bot connection\n"; 