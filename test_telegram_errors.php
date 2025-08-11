<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Services\TelegramService;

echo "=== TELEGRAM XATOLIKLARINI TEKSHIRISH ===\n\n";

$restaurants = Restaurant::all();

foreach ($restaurants as $restaurant) {
    echo "=== Restoran: {$restaurant->name} ===\n";
    echo "ID: {$restaurant->id}\n";
    echo "Bot Token: " . ($restaurant->bot_token ?: 'NOT SET') . "\n";
    
    if (!$restaurant->bot_token) {
        echo "❌ Bot token o'rnatilmagan\n";
        echo "---\n\n";
        continue;
    }
    
    // Test bot connection
    try {
        $telegramService = new TelegramService($restaurant->bot_token);
        $botInfo = $telegramService->getMe();
        
        if ($botInfo['ok']) {
            echo "✅ Bot connection successful\n";
            echo "Bot Name: {$botInfo['result']['first_name']}\n";
            echo "Bot Username: @{$botInfo['result']['username']}\n";
            
            // Test sending a message
            $testResult = $telegramService->sendMessage(
                123456789, // Test chat ID
                "🔧 Test xabar - {$restaurant->name} boti ishlayapti!"
            );
            
            if ($testResult['ok']) {
                echo "✅ Test xabar yuborildi\n";
            } else {
                echo "❌ Test xabar yuborilmadi: " . ($testResult['description'] ?? 'Unknown error') . "\n";
            }
            
        } else {
            echo "❌ Bot connection failed: " . ($botInfo['description'] ?? 'Unknown error') . "\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Error: {$e->getMessage()}\n";
    }
    
    echo "---\n\n";
}

echo "=== XATOLIKLAR TUSHUNISHI ===\n\n";

echo "Telegramdan botga yozgan xabarlar xatoliklari quyidagi sabablarga ko'ra yuz bera oladi:\n\n";

echo "1. BOT TOKEN MUAMMOSI:\n";
echo "   - Bot token noto'g'ri yoki mavjud emas\n";
echo "   - Bot token format noto'g'ri\n";
echo "   - Bot token o'chirilgan yoki bloklangan\n\n";

echo "2. WEBHOOK MUAMMOSI:\n";
echo "   - Webhook URL noto'g'ri\n";
echo "   - Webhook o'rnatilmagan\n";
echo "   - Server xatoligi\n\n";

echo "3. NETWORK MUAMMOSI:\n";
echo "   - Internet aloqasi yo'q\n";
echo "   - Telegram API ga ulanish muammosi\n";
echo "   - Timeout xatoligi\n\n";

echo "4. DATABASE MUAMMOSI:\n";
echo "   - Database ulanish muammosi\n";
echo "   - Jadval mavjud emas\n";
echo "   - Ma'lumotlar saqlash muammosi\n\n";

echo "5. KOD MUAMMOSI:\n";
echo "   - TelegramController da xatolik\n";
echo "   - TelegramService da xatolik\n";
echo "   - Logging muammosi\n\n";

echo "=== YECHIM ===\n\n";

echo "1. BOT TOKENNI TEKSHIRING:\n";
echo "   - @BotFather da bot tokenini tekshiring\n";
echo "   - Yangi bot yarating kerak bo'lsa\n";
echo "   - Admin panelda to'g'ri kiriting\n\n";

echo "2. WEBHOOKNI O'RNATING:\n";
echo "   - Webhook URL ni to'g'ri o'rnating\n";
echo "   - SSL sertifikatini tekshiring\n";
echo "   - Server loglarini tekshiring\n\n";

echo "3. LOGLARNI TEKSHIRING:\n";
echo "   - Laravel log fayllarini tekshiring\n";
echo "   - Telegram API javoblarini tekshiring\n";
echo "   - Database xatoliklarini tekshiring\n\n";

echo "4. TEST QILING:\n";
echo "   - Botga /start yuboring\n";
echo "   - Xabar yuborib test qiling\n";
echo "   - Admin panelda tekshiring\n\n";

echo "=== LOG FAYLLARINI TEKSHIRISH ===\n\n";

echo "Laravel log fayllarini tekshirish:\n";
echo "tail -f storage/logs/laravel.log\n\n";

echo "Telegram API loglari:\n";
echo "TelegramService da Log::info va Log::error qo'shilgan\n\n";

echo "=== KEYINGI QADAMLAR ===\n\n";

echo "1. Log fayllarini tekshiring\n";
echo "2. Bot tokenlarni @BotFather da tekshiring\n";
echo "3. Webhook URL larni o'rnating\n";
echo "4. Test qiling\n\n"; 