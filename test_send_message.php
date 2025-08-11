<?php

require_once 'vendor/autoload.php';

use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Services\TelegramService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Telegram message sending...\n";

// Get first restaurant
$restaurant = Restaurant::first();
if (!$restaurant) {
    echo "No restaurant found\n";
    exit;
}

echo "Restaurant: " . $restaurant->name . "\n";
echo "Bot token: " . ($restaurant->bot_token ? 'Set' : 'Not set') . "\n";

if (!$restaurant->bot_token) {
    echo "Bot token not set, cannot test\n";
    exit;
}

// Get telegram users
$users = TelegramUser::where('restaurant_id', $restaurant->id)->get();
echo "Found " . $users->count() . " telegram users\n";

if ($users->count() == 0) {
    echo "No telegram users found\n";
    exit;
}

// Test telegram service
try {
    $telegramService = new TelegramService($restaurant->bot_token);
    
    // Test bot connection
    $botInfo = $telegramService->getMe();
    if ($botInfo['ok']) {
        echo "Bot connection successful: " . $botInfo['result']['first_name'] . "\n";
    } else {
        echo "Bot connection failed: " . ($botInfo['description'] ?? 'Unknown error') . "\n";
        exit;
    }
    
    // Test sending message to first user
    $firstUser = $users->first();
    echo "Testing message to user: " . $firstUser->telegram_id . "\n";
    
    $result = $telegramService->sendMessage($firstUser->telegram_id, "Test message from admin panel");
    
    if ($result['ok']) {
        echo "Message sent successfully!\n";
        echo "Message ID: " . $result['result']['message_id'] . "\n";
    } else {
        echo "Message sending failed: " . ($result['description'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Test completed\n"; 