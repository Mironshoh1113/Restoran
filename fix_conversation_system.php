<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Services\TelegramService;

echo "=== XABARLASHISH TIZIMINI TAXRIRLASH ===\n\n";

$restaurants = Restaurant::all();

foreach ($restaurants as $restaurant) {
    echo "=== Restoran: {$restaurant->name} ===\n";
    echo "ID: {$restaurant->id}\n";
    echo "Bot Token: " . ($restaurant->bot_token ?: 'NOT SET') . "\n";
    
    // Check if restaurant has valid bot token
    if (!$restaurant->bot_token || strpos($restaurant->bot_token, 'test_') === 0) {
        echo "❌ Bot token noto'g'ri yoki mavjud emas\n";
        echo "---\n\n";
        continue;
    }
    
    // Test bot connection
    try {
        $telegramService = new TelegramService($restaurant->bot_token);
        $botInfo = $telegramService->getMe();
        
        if (!$botInfo['ok']) {
            echo "❌ Bot token noto'g'ri: " . ($botInfo['description'] ?? 'Unknown error') . "\n";
            echo "---\n\n";
            continue;
        }
        
        echo "✅ Bot connection successful\n";
        echo "Bot Name: {$botInfo['result']['first_name']}\n";
        echo "Bot Username: @{$botInfo['result']['username']}\n";
        
        // Count telegram users for this restaurant
        $userCount = TelegramUser::where('restaurant_id', $restaurant->id)->count();
        echo "Telegram users: {$userCount}\n";
        
        // Count messages for this restaurant
        $messageCount = TelegramMessage::where('restaurant_id', $restaurant->id)->count();
        echo "Messages: {$messageCount}\n";
        
        // Test sending a message to admin if admin_telegram_chat_id is set
        if ($restaurant->admin_telegram_chat_id) {
            echo "Testing admin message...\n";
            $testResult = $telegramService->sendMessage(
                $restaurant->admin_telegram_chat_id, 
                "🔧 Test xabar - {$restaurant->name} boti ishlayapti!"
            );
            
            if ($testResult['ok']) {
                echo "✅ Admin xabar yuborildi\n";
            } else {
                echo "❌ Admin xabar yuborilmadi: " . ($testResult['description'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "⚠️  Admin telegram chat ID o'rnatilmagan\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Error: {$e->getMessage()}\n";
    }
    
    echo "---\n\n";
}

echo "=== XABARLASHISH TIZIMI MUAMMOSI ===\n\n";

echo "Muammo: Admin panelda faqat 1-chi restoran boti ishlayapti\n\n";

echo "Sababi:\n";
echo "1. Har bir restoran uchun alohida bot token kerak\n";
echo "2. TelegramService da bot token to'g'ri o'rnatilmayapti\n";
echo "3. Admin panelda restaurant_id to'g'ri ishlatilmayapti\n\n";

echo "Yechim:\n";
echo "1. Har bir restoran uchun to'g'ri bot token o'rnatish\n";
echo "2. TelegramService da restaurant_id asosida bot token o'rnatish\n";
echo "3. Admin panelda conversation qismini to'g'rilash\n\n";

echo "=== TAVSIYALAR ===\n\n";

echo "1. Har bir restoran uchun alohida bot yarating\n";
echo "2. Bot tokenlarni admin panelda to'g'ri kiriting\n";
echo "3. Webhook URL larni o'rnating\n";
echo "4. Admin panelda conversation qismini test qiling\n\n";

echo "4. Admin panelda conversation qismini test qiling\n\n"; 