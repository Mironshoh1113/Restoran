<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Services\TelegramService;

echo "=== COMPREHENSIVE BOT SETUP ===\n\n";

// Function to validate bot token format
function isValidBotToken($token) {
    // Telegram bot tokens follow pattern: number:letters
    return preg_match('/^\d+:[A-Za-z0-9_-]+$/', $token);
}

// Function to test bot token
function testBotToken($token) {
    try {
        $telegramService = new TelegramService($token);
        $botInfo = $telegramService->getMe();
        return $botInfo['ok'] ? $botInfo['result'] : false;
    } catch (\Exception $e) {
        return false;
    }
}

$restaurants = Restaurant::all();

echo "Found " . $restaurants->count() . " restaurants\n\n";

foreach ($restaurants as $restaurant) {
    echo "=== Restaurant: {$restaurant->name} ===\n";
    echo "ID: {$restaurant->id}\n";
    echo "Current Bot Token: " . ($restaurant->bot_token ?: 'NOT SET') . "\n";
    
    // Check if token is valid format
    if ($restaurant->bot_token && !isValidBotToken($restaurant->bot_token)) {
        echo "❌ Bot token format is invalid\n";
        echo "   Expected format: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz\n";
        echo "   Current token: {$restaurant->bot_token}\n\n";
        
        echo "To fix this:\n";
        echo "1. Create a new bot at @BotFather on Telegram\n";
        echo "2. Get the bot token\n";
        echo "3. Update the restaurant bot settings\n\n";
        continue;
    }
    
    // Test bot token if it exists
    if ($restaurant->bot_token) {
        $botInfo = testBotToken($restaurant->bot_token);
        
        if ($botInfo) {
            echo "✅ Bot token is valid\n";
            echo "Bot Name: {$botInfo['first_name']}\n";
            echo "Bot Username: @{$botInfo['username']}\n";
            
            // Set up webhook
            $telegramService = new TelegramService($restaurant->bot_token);
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
                    echo "⚠️  Failed to set bot commands\n";
                }
                
                // Update restaurant with bot info
                $restaurant->update([
                    'bot_name' => $botInfo['first_name'],
                    'bot_username' => $botInfo['username']
                ]);
                
                echo "✅ Restaurant updated with bot info\n";
                
            } else {
                echo "❌ Failed to set webhook: " . ($webhookResult['description'] ?? 'Unknown error') . "\n";
            }
            
        } else {
            echo "❌ Bot token is invalid or bot doesn't exist\n";
            echo "   Please check the token with @BotFather\n\n";
        }
    } else {
        echo "❌ No bot token set\n";
        echo "   Please set a bot token in the admin panel\n\n";
    }
    
    echo "---\n\n";
}

echo "=== SETUP INSTRUCTIONS ===\n\n";

echo "To fix all bots:\n\n";

echo "1. CREATE BOTS:\n";
echo "   - Go to @BotFather on Telegram\n";
echo "   - Send /newbot command\n";
echo "   - Follow instructions to create bots\n";
echo "   - Save the bot tokens\n\n";

echo "2. UPDATE RESTAURANTS:\n";
echo "   - Go to admin panel\n";
echo "   - Navigate to each restaurant's bot settings\n";
echo "   - Enter the correct bot token\n";
echo "   - Save the settings\n\n";

echo "3. TEST BOTS:\n";
echo "   - Send /start to each bot\n";
echo "   - Check if they respond\n";
echo "   - Test menu functionality\n\n";

echo "4. WEBHOOK URLS FOR EACH BOT:\n";
foreach ($restaurants as $restaurant) {
    if ($restaurant->bot_token && isValidBotToken($restaurant->bot_token)) {
        $webhookUrl = url("/telegram/webhook/{$restaurant->bot_token}");
        echo "   {$restaurant->name}: {$webhookUrl}\n";
    }
}

echo "\n5. VERIFY WEBHOOKS:\n";
echo "   - Each bot should have its webhook URL set\n";
echo "   - Test by sending a message to the bot\n";
echo "   - Check logs for webhook activity\n\n"; 