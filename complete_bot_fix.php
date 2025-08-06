<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Services\TelegramService;

echo "=== TO'LIQ BOT MUAMMOSINI HAL QILISH ===\n\n";

// Function to validate bot token format
function isValidBotToken($token) {
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

echo "Jami restoranlar: " . $restaurants->count() . "\n\n";

foreach ($restaurants as $restaurant) {
    echo "=== Restoran: {$restaurant->name} ===\n";
    echo "ID: {$restaurant->id}\n";
    echo "Bot Token: " . ($restaurant->bot_token ?: 'NOT SET') . "\n";
    
    // Check if token is valid format
    if ($restaurant->bot_token && !isValidBotToken($restaurant->bot_token)) {
        echo "❌ Bot token format noto'g'ri\n";
        echo "   Kutilgan format: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz\n";
        echo "   Hozirgi token: {$restaurant->bot_token}\n\n";
        
        echo "YECHIM:\n";
        echo "1. @BotFather ga boring\n";
        echo "2. /newbot buyrug'ini yuboring\n";
        echo "3. Bot nomini kiriting: {$restaurant->name} Bot\n";
        echo "4. Bot username ni kiriting: " . strtolower(str_replace(' ', '_', $restaurant->name)) . "_bot\n";
        echo "5. Bot tokenini oling va admin panelda kiriting\n\n";
        continue;
    }
    
    // Test bot token if it exists
    if ($restaurant->bot_token) {
        $botInfo = testBotToken($restaurant->bot_token);
        
        if ($botInfo) {
            echo "✅ Bot token to'g'ri\n";
            echo "Bot Name: {$botInfo['first_name']}\n";
            echo "Bot Username: @{$botInfo['username']}\n";
            
            // Set up webhook
            $telegramService = new TelegramService($restaurant->bot_token);
            $webhookUrl = url("/telegram/webhook/{$restaurant->bot_token}");
            
            echo "Webhook URL: {$webhookUrl}\n";
            
            $webhookResult = $telegramService->setWebhook($webhookUrl);
            
            if ($webhookResult['ok']) {
                echo "✅ Webhook o'rnatildi\n";
                
                // Set bot commands
                $commands = [
                    ['command' => 'start', 'description' => 'Botni ishga tushirish'],
                    ['command' => 'menu', 'description' => 'Menyuni ko\'rish'],
                    ['command' => 'cart', 'description' => 'Savatni ko\'rish'],
                    ['command' => 'help', 'description' => 'Yordam']
                ];
                
                $commandsResult = $telegramService->setMyCommands($commands);
                
                if ($commandsResult['ok']) {
                    echo "✅ Bot buyruqlari o'rnatildi\n";
                } else {
                    echo "⚠️  Bot buyruqlari o'rnatilmadi\n";
                }
                
                // Update restaurant with bot info
                $restaurant->update([
                    'bot_name' => $botInfo['first_name'],
                    'bot_username' => $botInfo['username']
                ]);
                
                echo "✅ Restoran ma'lumotlari yangilandi\n";
                
            } else {
                echo "❌ Webhook o'rnatilmadi: " . ($webhookResult['description'] ?? 'Unknown error') . "\n";
            }
            
            // Count telegram users and messages
            $userCount = TelegramUser::where('restaurant_id', $restaurant->id)->count();
            $messageCount = TelegramMessage::where('restaurant_id', $restaurant->id)->count();
            
            echo "Telegram foydalanuvchilar: {$userCount}\n";
            echo "Xabarlar: {$messageCount}\n";
            
            // Test admin message if admin_telegram_chat_id is set
            if ($restaurant->admin_telegram_chat_id) {
                echo "Admin xabar test qilinmoqda...\n";
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
            
        } else {
            echo "❌ Bot token noto'g'ri yoki bot mavjud emas\n";
            echo "   @BotFather da tekshiring\n\n";
        }
    } else {
        echo "❌ Bot token o'rnatilmagan\n";
        echo "   Admin panelda bot tokenini kiriting\n\n";
    }
    
    echo "---\n\n";
}

echo "=== XABARLASHISH TIZIMI MUAMMOSI ===\n\n";

echo "Muammo: Admin panelda faqat 1-chi restoran boti ishlayapti\n\n";

echo "Sababi:\n";
echo "1. Har bir restoran uchun alohida bot token kerak\n";
echo "2. Bot tokenlar noto'g'ri yoki mavjud emas\n";
echo "3. Webhook URL lar o'rnatilmagan\n";
echo "4. Admin panelda restaurant_id to'g'ri ishlatilmayapti\n\n";

echo "YECHIM:\n";
echo "1. Har bir restoran uchun alohida bot yarating\n";
echo "2. Bot tokenlarni admin panelda to'g'ri kiriting\n";
echo "3. Webhook URL larni o'rnating\n";
echo "4. Admin panelda conversation qismini test qiling\n\n";

echo "=== TAVSIYALAR ===\n\n";

echo "1. HAR BIR RESTORAN UCHUN BOT YARATING:\n";
echo "   - @BotFather ga boring\n";
echo "   - /newbot buyrug'ini yuboring\n";
echo "   - Bot nomini va username ni kiriting\n";
echo "   - Bot tokenini saqlang\n\n";

echo "2. ADMIN PANELDA SOZLANG:\n";
echo "   - Har bir restoran uchun bot tokenini kiriting\n";
echo "   - Webhook URL ni o'rnating\n";
echo "   - Bot nomini va tavsifini to'ldiring\n\n";

echo "3. TEST QILING:\n";
echo "   - Har bir botga /start yuboring\n";
echo "   - Bot javob berishini tekshiring\n";
echo "   - Admin panelda conversation qismini test qiling\n\n";

echo "4. WEBHOOK URL LAR:\n";
foreach ($restaurants as $restaurant) {
    if ($restaurant->bot_token && isValidBotToken($restaurant->bot_token)) {
        $webhookUrl = url("/telegram/webhook/{$restaurant->bot_token}");
        echo "   {$restaurant->name}: {$webhookUrl}\n";
    }
}

echo "\n5. VERIFY WEBHOOKS:\n";
echo "   - Har bir bot uchun webhook URL o'rnatilganini tekshiring\n";
echo "   - Botga xabar yuborib test qiling\n";
echo "   - Log fayllarini tekshiring\n\n";

echo "=== AVTOMATIK SOZLASH ===\n\n";

echo "Agar barcha bot tokenlar to'g'ri bo'lsa, quyidagi script orqali avtomatik sozlash mumkin:\n";
echo "php setup_all_bots.php\n\n";

echo "=== XATOLIKLAR ===\n\n";

echo "Agar bot ishlamasa:\n";
echo "1. Bot tokenini @BotFather da tekshiring\n";
echo "2. Webhook URL ni tekshiring\n";
echo "3. Server loglarini tekshiring\n";
echo "4. Bot tokenini admin panelda qayta kiriting\n\n";

echo "=== YANGI RESTORANLAR ===\n\n";

echo "Yangi restoran qo'shganingizda:\n";
echo "1. Yangi bot yarating\n";
echo "2. Bot tokenini admin panelda sozlang\n";
echo "3. Webhook ni o'rnating\n";
echo "4. Test qiling\n\n"; 