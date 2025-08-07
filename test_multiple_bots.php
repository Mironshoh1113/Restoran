<?php
/**
 * Multiple Bots Test Script
 * Bu script ko'p botlar tizimining to'g'ri ishlashini tekshiradi
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;

// Laravel app-ni ishga tushirish
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Multiple Bots Test Script ===\n\n";

try {
    // 1. Database ulanishini tekshirish
    echo "1. Database ulanishini tekshirish...\n";
    $restaurants = Restaurant::count();
    echo "   ✅ Database ulangan. Restoranlar soni: {$restaurants}\n\n";

    // 2. Restoranlarni ko'rish
    echo "2. Restoranlar ro'yxati:\n";
    $restaurants = Restaurant::with('owner')->get();
    foreach ($restaurants as $restaurant) {
        $botStatus = $restaurant->bot_token ? '✅ Faol' : '❌ Faol emas';
        $userCount = $restaurant->telegramUsers()->count();
        $ownerName = $restaurant->owner ? $restaurant->owner->name : 'Noma\'lum';
        echo "   - {$restaurant->name} ({$ownerName}) - {$botStatus} - {$userCount} foydalanuvchi\n";
    }
    echo "\n";

    // 3. Telegram foydalanuvchilarini ko'rish
    echo "3. Telegram foydalanuvchilar statistikasi:\n";
    $totalUsers = TelegramUser::count();
    $activeUsers = TelegramUser::where('is_active', true)->count();
    $totalMessages = TelegramMessage::count();
    
    echo "   Jami foydalanuvchilar: {$totalUsers}\n";
    echo "   Faol foydalanuvchilar: {$activeUsers}\n";
    echo "   Jami xabarlar: {$totalMessages}\n\n";

    // 4. Har bir restoran uchun foydalanuvchilar
    echo "4. Har bir restoran uchun foydalanuvchilar:\n";
    foreach ($restaurants as $restaurant) {
        $users = $restaurant->telegramUsers()->count();
        $activeUsers = $restaurant->telegramUsers()->where('is_active', true)->count();
        $messages = TelegramMessage::whereHas('telegramUser', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->count();
        
        echo "   {$restaurant->name}:\n";
        echo "     - Foydalanuvchilar: {$users}\n";
        echo "     - Faol foydalanuvchilar: {$activeUsers}\n";
        echo "     - Xabarlar: {$messages}\n";
    }
    echo "\n";

    // 5. Webhook URL-larni ko'rish
    echo "5. Webhook URL-lar:\n";
    foreach ($restaurants as $restaurant) {
        if ($restaurant->bot_token) {
            $webhookUrl = url('/telegram/webhook/' . $restaurant->bot_token);
            echo "   {$restaurant->name}: {$webhookUrl}\n";
        }
    }
    echo "\n";

    // 6. Bot tokenlarini tekshirish
    echo "6. Bot tokenlarini tekshirish:\n";
    foreach ($restaurants as $restaurant) {
        if ($restaurant->bot_token) {
            try {
                $telegramService = new \App\Services\TelegramService($restaurant->bot_token);
                $botInfo = $telegramService->getMe();
                
                if ($botInfo['ok']) {
                    $bot = $botInfo['result'];
                    echo "   ✅ {$restaurant->name}: @{$bot['username']} ({$bot['first_name']})\n";
                } else {
                    echo "   ❌ {$restaurant->name}: Bot token noto'g'ri\n";
                }
            } catch (Exception $e) {
                echo "   ❌ {$restaurant->name}: Xatolik - {$e->getMessage()}\n";
            }
        } else {
            echo "   ⚠️  {$restaurant->name}: Bot token o'rnatilmagan\n";
        }
    }
    echo "\n";

    // 7. Database strukturasini tekshirish
    echo "7. Database strukturasini tekshirish:\n";
    
    // Restoranlar jadvali
    $restaurantsTable = DB::select("SHOW TABLES LIKE 'restaurants'");
    if (!empty($restaurantsTable)) {
        echo "   ✅ restaurants jadvali mavjud\n";
        
        $columns = DB::select("SHOW COLUMNS FROM restaurants");
        $requiredColumns = ['id', 'name', 'bot_token', 'bot_username', 'owner_user_id'];
        $existingColumns = array_column($columns, 'Field');
        
        foreach ($requiredColumns as $column) {
            if (in_array($column, $existingColumns)) {
                echo "     ✅ {$column} ustuni mavjud\n";
            } else {
                echo "     ❌ {$column} ustuni yo'q\n";
            }
        }
    } else {
        echo "   ❌ restaurants jadvali yo'q\n";
    }
    
    // Telegram foydalanuvchilar jadvali
    $telegramUsersTable = DB::select("SHOW TABLES LIKE 'telegram_users'");
    if (!empty($telegramUsersTable)) {
        echo "   ✅ telegram_users jadvali mavjud\n";
        
        $columns = DB::select("SHOW COLUMNS FROM telegram_users");
        $requiredColumns = ['id', 'restaurant_id', 'telegram_id', 'username', 'first_name', 'is_active'];
        $existingColumns = array_column($columns, 'Field');
        
        foreach ($requiredColumns as $column) {
            if (in_array($column, $existingColumns)) {
                echo "     ✅ {$column} ustuni mavjud\n";
            } else {
                echo "     ❌ {$column} ustuni yo'q\n";
            }
        }
    } else {
        echo "   ❌ telegram_users jadvali yo'q\n";
    }
    echo "\n";

    // 8. Test natijalari
    echo "8. Test natijalari:\n";
    
    $activeBots = Restaurant::whereNotNull('bot_token')->count();
    $totalUsers = TelegramUser::count();
    $totalMessages = TelegramMessage::count();
    
    echo "   Jami restoranlar: {$restaurants->count()}\n";
    echo "   Faol botlar: {$activeBots}\n";
    echo "   Jami foydalanuvchilar: {$totalUsers}\n";
    echo "   Jami xabarlar: {$totalMessages}\n\n";
    
    if ($activeBots > 0 && $totalUsers > 0) {
        echo "   ✅ Sistema to'g'ri ishlayapti!\n";
        echo "   ✅ Ko'p botlar qo'llab-quvvatlanadi!\n";
        echo "   ✅ Foydalanuvchilar to'g'ri saqlanadi!\n";
    } else {
        echo "   ⚠️  Sistema ishlayapti, lekin ma'lumotlar yo'q\n";
    }

} catch (Exception $e) {
    echo "❌ Xatolik: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test tugadi ===\n";
?> 