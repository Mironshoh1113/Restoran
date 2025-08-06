<?php

/**
 * Telegram Webhook Handler
 * This file handles incoming webhooks from Telegram
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

// Get the raw POST data
$payload = file_get_contents('php://input');
$update = json_decode($payload, true);

// Log the incoming webhook
Log::info('Telegram Webhook Received', [
    'payload' => $update
]);

if (!$update) {
    http_response_code(400);
    exit('Invalid JSON payload');
}

// Get bot token from URL path
$path = $_SERVER['REQUEST_URI'] ?? '';
$pathParts = explode('/', trim($path, '/'));
$token = end($pathParts);

if (!$token) {
    http_response_code(400);
    exit('No token provided');
}

// Find restaurant by bot token
$restaurant = Restaurant::where('bot_token', $token)->first();

if (!$restaurant) {
    Log::error('Restaurant not found for bot token: ' . $token);
    http_response_code(404);
    exit('Restaurant not found');
}

// Initialize TelegramService
$telegramService = new TelegramService($token);

// Handle different types of updates
if (isset($update['message'])) {
    $message = $update['message'];
    $chatId = $message['chat']['id'];
    $text = $message['text'] ?? '';
    $contact = $message['contact'] ?? null;
    $userData = $message['from'] ?? null;

    // Save or update telegram user
    if ($userData) {
        TelegramUser::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'telegram_id' => $userData['id']
            ],
            [
                'username' => $userData['username'] ?? null,
                'first_name' => $userData['first_name'] ?? null,
                'last_name' => $userData['last_name'] ?? null,
                'language_code' => $userData['language_code'] ?? 'uz',
                'is_bot' => $userData['is_bot'] ?? false,
                'last_activity' => now(),
            ]
        );
    }

    // Handle /start command
    if ($text === '/start') {
        $welcomeMessage = "Assalomu alaykum! 🍽️\n\n";
        $welcomeMessage .= "Restoran buyurtma botiga xush kelibsiz!\n\n";
        $welcomeMessage .= "📋 Menyu ko'rish uchun \"Menyu\" tugmasini bosing\n";
        $welcomeMessage .= "🛒 Savatni ko'rish uchun \"Savat\" tugmasini bosing\n";
        $welcomeMessage .= "📞 Buyurtma qilish uchun \"Buyurtma qilish\" tugmasini bosing\n";
        $welcomeMessage .= "📊 Buyurtmalaringizni ko'rish uchun \"Buyurtmalarim\" tugmasini bosing";

        $keyboard = [
            [
                ['text' => '📋 Menyu'],
                ['text' => '🛒 Savat']
            ],
            [
                ['text' => '📞 Buyurtma qilish'],
                ['text' => '📊 Buyurtmalarim']
            ],
            [
                ['text' => 'ℹ️ Yordam']
            ]
        ];

        $replyKeyboard = [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];

        $telegramService->sendMessage($chatId, $welcomeMessage, $replyKeyboard);
    }
    // Handle other commands
    elseif ($text === '📋 Menyu') {
        // Get categories for this restaurant
        $categories = \App\Models\Category::whereHas('project', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->get();

        if ($categories->isEmpty()) {
            $telegramService->sendMessage($chatId, "Kechirasiz, hozircha menyu mavjud emas.");
        } else {
            $message = "🍽️ *Kategoriyalar:*\n\n";
            $message .= "Tanlang:\n\n";
            
            foreach ($categories as $category) {
                $message .= "• {$category->name}\n";
            }

            $telegramService->sendMessage($chatId, $message);
        }
    }
    elseif ($text === '🛒 Savat') {
        // Get cart from cache
        $cart = \Illuminate\Support\Facades\Cache::get("cart_{$chatId}", []);
        
        if (empty($cart)) {
            $telegramService->sendMessage($chatId, "Savat bo'sh. Menyudan taom tanlang.");
        } else {
            $message = "🛒 *Savat:*\n\n";
            $total = 0;
            
            foreach ($cart as $itemId => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                
                $message .= "• {$item['name']} x{$item['quantity']} = " . number_format($subtotal, 0, ',', ' ') . " so'm\n";
            }

            $message .= "\n<b>Jami: " . number_format($total, 0, ',', ' ') . " so'm</b>";

            $telegramService->sendMessage($chatId, $message);
        }
    }
    elseif ($text === '📞 Buyurtma qilish') {
        // Get cart from cache
        $cart = \Illuminate\Support\Facades\Cache::get("cart_{$chatId}", []);
        
        if (empty($cart)) {
            $telegramService->sendMessage($chatId, "Savat bo'sh. Buyurtma qilish uchun avval taom tanlang.");
        } else {
            $message = "📞 *Buyurtma qilish*\n\n";
            $message .= "Buyurtma qilish uchun quyidagi ma'lumotlarni kiriting:\n\n";
            $message .= "1️⃣ Ismingiz\n";
            $message .= "2️⃣ Telefon raqamingiz\n";
            $message .= "3️⃣ Yetkazib berish manzili\n\n";
            $message .= "Yoki web sahifani ochib buyurtma bering:";
            
            // Create web interface URL
            $webUrl = "https://simpsons.uz/web-interface";
            
            $keyboard = [
                [
                    ['text' => '🌐 Web sahifani ochish', 'web_app' => ['url' => $webUrl]]
                ]
            ];
            
            $inlineKeyboard = [
                'inline_keyboard' => $keyboard
            ];
            
            $telegramService->sendMessage($chatId, $message, $inlineKeyboard);
        }
    }
    elseif ($text === '📊 Buyurtmalarim') {
        // Get orders for this user from this restaurant
        $orders = \App\Models\Order::where('telegram_chat_id', $chatId)
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($orders->isEmpty()) {
            $telegramService->sendMessage($chatId, "Sizda hali buyurtmalar yo'q.");
        } else {
            $message = "📊 *Buyurtmalaringiz:*\n\n";
            
            foreach ($orders as $order) {
                $status = [
                    'new' => '⏳ Yangi',
                    'preparing' => '👨‍🍳 Tayyorlanmoqda',
                    'on_way' => '🚚 Yolda',
                    'delivered' => '✅ Yetkazildi',
                    'cancelled' => '❌ Bekor'
                ][$order->status] ?? 'Nomalum';

                $message .= "📦 *#{$order->order_number}*\n";
                $message .= "💰 " . number_format($order->total_price ?? 0, 0, ',', ' ') . " so'm\n";
                $message .= "📅 {$order->created_at->format('d.m.Y H:i')}\n";
                $message .= "📊 {$status}\n\n";
            }

            $telegramService->sendMessage($chatId, $message);
        }
    }
    elseif ($text === 'ℹ️ Yordam') {
        $helpMessage = "Yordam kerakmi?\n\n";
        $helpMessage .= "📞 Qo'ng'iroq: " . ($restaurant->phone ?? 'N/A') . "\n";
        $helpMessage .= "📍 Manzil: " . ($restaurant->address ?? 'N/A') . "\n\n";
        $helpMessage .= "Yoki restoran bilan to'g'ridan-to'g'ri bog'laning.";
        
        $telegramService->sendMessage($chatId, $helpMessage);
    }
    else {
        $telegramService->sendMessage($chatId, "Kechirasiz, \"{$text}\" buyrug'i tushunilmadi. Menyudan tanlang.");
    }
}
elseif (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $data = $callbackQuery['data'];
    
    $telegramService->sendMessage($chatId, "Callback: {$data}");
}

// Always return OK to Telegram
http_response_code(200);
echo 'OK'; 