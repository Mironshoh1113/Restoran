<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;

echo "=== TELEGRAM FOYDALANUVCHILAR MUAMMOSINI HAL QILISH ===\n\n";

$restaurants = Restaurant::all();

foreach ($restaurants as $restaurant) {
    echo "=== Restoran: {$restaurant->name} ===\n";
    echo "ID: {$restaurant->id}\n";
    echo "Bot Token: " . ($restaurant->bot_token ?: 'NOT SET') . "\n";
    
    // Count telegram users for this restaurant
    $userCount = TelegramUser::where('restaurant_id', $restaurant->id)->count();
    echo "Telegram foydalanuvchilar: {$userCount}\n";
    
    // Count messages for this restaurant
    $messageCount = TelegramMessage::where('restaurant_id', $restaurant->id)->count();
    echo "Xabarlar: {$messageCount}\n";
    
    // Show all telegram users for this restaurant
    $users = TelegramUser::where('restaurant_id', $restaurant->id)->get();
    
    if ($users->count() > 0) {
        echo "\nFoydalanuvchilar ro'yxati:\n";
        foreach ($users as $user) {
            echo "- ID: {$user->id}, Telegram ID: {$user->telegram_id}, Nomi: {$user->full_name}, Username: " . ($user->username ? '@' . $user->username : 'Yo\'q') . "\n";
        }
    } else {
        echo "\n❌ Bu restoran uchun telegram foydalanuvchilar yo'q\n";
        
        // Check if there are any telegram users without restaurant_id
        $orphanedUsers = TelegramUser::whereNull('restaurant_id')->get();
        if ($orphanedUsers->count() > 0) {
            echo "\n⚠️  Restaurant ID siz foydalanuvchilar topildi:\n";
            foreach ($orphanedUsers as $user) {
                echo "- ID: {$user->id}, Telegram ID: {$user->telegram_id}, Nomi: {$user->full_name}\n";
            }
            
            // Ask if we should assign them to this restaurant
            echo "\nBu foydalanuvchilarni {$restaurant->name} ga tayinlashni xohlaysizmi? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            fclose($handle);
            
            if (trim(strtolower($line)) === 'y') {
                foreach ($orphanedUsers as $user) {
                    $user->update(['restaurant_id' => $restaurant->id]);
                    echo "✅ {$user->full_name} {$restaurant->name} ga tayinlandi\n";
                }
            }
        }
    }
    
    // Check if there are any messages without proper restaurant_id
    $orphanedMessages = TelegramMessage::whereNull('restaurant_id')->get();
    if ($orphanedMessages->count() > 0) {
        echo "\n⚠️  Restaurant ID siz xabarlar topildi: {$orphanedMessages->count()} ta\n";
        
        // Try to assign messages to restaurant based on telegram_user_id
        foreach ($orphanedMessages as $message) {
            if ($message->telegram_user_id) {
                $user = TelegramUser::find($message->telegram_user_id);
                if ($user && $user->restaurant_id) {
                    $message->update(['restaurant_id' => $user->restaurant_id]);
                    echo "✅ Xabar {$message->id} {$user->restaurant->name} ga tayinlandi\n";
                }
            }
        }
    }
    
    echo "\n---\n\n";
}

echo "=== MUAMMO TUSHUNILDI ===\n\n";

echo "Muammo: Admin panelda telegram foydalanuvchilar ko'rinmayapti\n\n";

echo "Sababi:\n";
echo "1. TelegramUser jadvalida ma'lumotlar yo'q\n";
echo "2. restaurant_id to'g'ri o'rnatilmagan\n";
echo "3. Bot orqali foydalanuvchilar ro'yxatdan o'tmagan\n";
echo "4. TelegramController da foydalanuvchilar to'g'ri saqlanmayapti\n\n";

echo "YECHIM:\n";
echo "1. Bot orqali foydalanuvchilar ro'yxatdan o'tishini ta'minlash\n";
echo "2. TelegramController da saveTelegramUser funksiyasini to'g'rilash\n";
echo "3. Mavjud foydalanuvchilarni to'g'ri restaurant_id ga tayinlash\n";
echo "4. Test foydalanuvchilar yaratish\n\n";

echo "=== TAVSIYALAR ===\n\n";

echo "1. BOT ORQALI FOYDALANUVCHI YARATISH:\n";
echo "   - Botga /start yuboring\n";
echo "   - Kontaktni yuboring\n";
echo "   - Foydalanuvchi avtomatik saqlanadi\n\n";

echo "2. TEST FOYDALANUVCHI YARATISH:\n";
echo "   - Database ga to'g'ridan-to'g'ri ma'lumot qo'shish\n";
echo "   - TelegramController ni test qilish\n\n";

echo "3. MAVJUD FOYDALANUVCHILARNI TAYINLASH:\n";
echo "   - Restaurant ID siz foydalanuvchilarni to'g'ri restoranlarga tayinlash\n\n";

echo "4. TELEGRAMCONTROLLER NI TO'GRILASH:\n";
echo "   - saveTelegramUser funksiyasini tekshirish\n";
echo "   - Xatoliklarni bartaraf etish\n\n"; 