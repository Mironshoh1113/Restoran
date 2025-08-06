<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;

echo "=== TEST TELEGRAM FOYDALANUVCHILAR YARATISH ===\n\n";

$restaurants = Restaurant::all();

foreach ($restaurants as $restaurant) {
    echo "=== Restoran: {$restaurant->name} ===\n";
    echo "ID: {$restaurant->id}\n";
    
    // Create test users for this restaurant
    $testUsers = [
        [
            'telegram_id' => 123456789,
            'username' => 'test_user_1',
            'first_name' => 'Test',
            'last_name' => 'User 1',
            'language_code' => 'uz',
            'is_bot' => false,
            'is_active' => true,
            'last_activity' => now(),
        ],
        [
            'telegram_id' => 987654321,
            'username' => 'test_user_2',
            'first_name' => 'Test',
            'last_name' => 'User 2',
            'language_code' => 'uz',
            'is_bot' => false,
            'is_active' => true,
            'last_activity' => now()->subHours(2),
        ],
        [
            'telegram_id' => 555666777,
            'username' => 'test_user_3',
            'first_name' => 'Test',
            'last_name' => 'User 3',
            'language_code' => 'uz',
            'is_bot' => false,
            'is_active' => true,
            'last_activity' => now()->subDays(1),
        ],
    ];
    
    foreach ($testUsers as $index => $userData) {
        $userData['restaurant_id'] = $restaurant->id;
        
        // Check if user already exists
        $existingUser = TelegramUser::where('telegram_id', $userData['telegram_id'])
            ->where('restaurant_id', $restaurant->id)
            ->first();
        
        if ($existingUser) {
            echo "⚠️  Foydalanuvchi mavjud: {$existingUser->full_name}\n";
            continue;
        }
        
        // Create new user
        $user = TelegramUser::create($userData);
        echo "✅ Foydalanuvchi yaratildi: {$user->full_name}\n";
        
        // Create some test messages
        $messages = [
            [
                'direction' => 'incoming',
                'message_text' => '/start',
                'message_type' => 'text',
                'is_read' => false,
            ],
            [
                'direction' => 'outgoing',
                'message_text' => "Xush kelibsiz! {$restaurant->name} ga xush kelibsiz!",
                'message_type' => 'text',
                'is_read' => false,
            ],
            [
                'direction' => 'incoming',
                'message_text' => 'Menyuni ko\'rish',
                'message_type' => 'text',
                'is_read' => false,
            ],
            [
                'direction' => 'outgoing',
                'message_text' => 'Menyu kategoriyalarini tanlang:',
                'message_type' => 'text',
                'is_read' => false,
            ],
        ];
        
        foreach ($messages as $messageData) {
            $messageData['restaurant_id'] = $restaurant->id;
            $messageData['telegram_user_id'] = $user->id;
            $messageData['message_id'] = rand(1000, 9999);
            
            TelegramMessage::create($messageData);
        }
        
        echo "   📝 4 ta test xabar yaratildi\n";
    }
    
    // Count users and messages
    $userCount = TelegramUser::where('restaurant_id', $restaurant->id)->count();
    $messageCount = TelegramMessage::where('restaurant_id', $restaurant->id)->count();
    
    echo "\n📊 {$restaurant->name} uchun:\n";
    echo "   Foydalanuvchilar: {$userCount}\n";
    echo "   Xabarlar: {$messageCount}\n";
    
    echo "\n---\n\n";
}

echo "=== TEST FOYDALANUVCHILAR YARATILDI ===\n\n";

echo "Endi admin panelda telegram foydalanuvchilar ko'rinadi!\n\n";

echo "Tekshirish uchun:\n";
echo "1. Admin panelga kiring\n";
echo "2. Bots bo'limiga boring\n";
echo "3. Har bir restoran uchun 'Users' tugmasini bosing\n";
echo "4. Foydalanuvchilar ro'yxatini ko'ring\n\n";

echo "Test foydalanuvchilar:\n";
echo "- Test User 1 (@test_user_1)\n";
echo "- Test User 2 (@test_user_2)\n";
echo "- Test User 3 (@test_user_3)\n\n";

echo "Har bir foydalanuvchi uchun 4 ta test xabar yaratildi.\n";
echo "Endi admin panelda conversation va xabar yuborish funksiyalari ishlaydi.\n\n";

echo "=== KEYINGI QADAMLAR ===\n\n";

echo "1. Admin panelda foydalanuvchilarni tekshiring\n";
echo "2. Xabar yuborish funksiyasini test qiling\n";
echo "3. Conversation qismini tekshiring\n";
echo "4. Haqiqiy bot tokenlarini o'rnating\n";
echo "5. Haqiqiy foydalanuvchilar bilan test qiling\n\n"; 