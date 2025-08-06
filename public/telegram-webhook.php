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
    $telegramUser = null;
    if ($userData) {
        $telegramUser = TelegramUser::updateOrCreate(
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

    // Save incoming message to database
    if ($telegramUser && $text) {
        $telegramService->saveIncomingMessage($telegramUser, $text, $message['message_id'] ?? null, $message);
    }

    // Handle /start command
    if ($text === '/start') {
        $welcomeMessage = "Assalomu alaykum! 🍽️\n\n";
        $welcomeMessage .= "Restoran buyurtma botiga xush kelibsiz!\n\n";
        $welcomeMessage .= "📊 Buyurtmalaringizni ko'rish uchun \"Buyurtmalarim\" tugmasini bosing\n";
        $welcomeMessage .= "ℹ️ Yordam kerak bo'lsa \"Yordam\" tugmasini bosing";

        $keyboard = [
            [
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

        $result = $telegramService->sendMessage($chatId, $welcomeMessage, $replyKeyboard);
        
        // Save outgoing message to database
        if ($telegramUser && $result['ok']) {
            $telegramService->saveOutgoingMessage($telegramUser, $welcomeMessage, $result['result']['message_id'] ?? null, $result);
        }
    }
    // Handle other commands
    elseif ($text === '📊 Buyurtmalarim') {
        // Get orders for this user from this restaurant
        $orders = \App\Models\Order::where('telegram_chat_id', $chatId)
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($orders->isEmpty()) {
            $responseMessage = "Sizda hali buyurtmalar yo'q.";
            $result = $telegramService->sendMessage($chatId, $responseMessage);
            
            // Save outgoing message to database
            if ($telegramUser && $result['ok']) {
                $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result['result']['message_id'] ?? null, $result);
            }
        } else {
            $responseMessage = "📊 *Buyurtmalaringiz:*\n\n";
            
            foreach ($orders as $order) {
                $status = [
                    'new' => '⏳ Yangi',
                    'preparing' => '👨‍🍳 Tayyorlanmoqda',
                    'on_way' => '🚚 Yolda',
                    'delivered' => '✅ Yetkazildi',
                    'cancelled' => '❌ Bekor'
                ][$order->status] ?? 'Nomalum';

                $responseMessage .= "📦 *#{$order->order_number}*\n";
                $responseMessage .= "💰 " . number_format($order->total_price ?? 0, 0, ',', ' ') . " so'm\n";
                $responseMessage .= "📅 {$order->created_at->format('d.m.Y H:i')}\n";
                $responseMessage .= "📊 {$status}\n\n";
            }

            $result = $telegramService->sendMessage($chatId, $responseMessage);
            
            // Save outgoing message to database
            if ($telegramUser && $result['ok']) {
                $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result['result']['message_id'] ?? null, $result);
            }
        }
    }
    elseif ($text === 'ℹ️ Yordam') {
        $responseMessage = "Yordam kerakmi?\n\n";
        $responseMessage .= "📞 Qo'ng'iroq: " . ($restaurant->phone ?? 'N/A') . "\n";
        $responseMessage .= "📍 Manzil: " . ($restaurant->address ?? 'N/A') . "\n\n";
        $responseMessage .= "Yoki restoran bilan to'g'ridan-to'g'ri bog'laning.";
        
        $result = $telegramService->sendMessage($chatId, $responseMessage);
        
        // Save outgoing message to database
        if ($telegramUser && $result['ok']) {
            $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result['result']['message_id'] ?? null, $result);
        }
    }
    else {
        $responseMessage = "Kechirasiz, \"{$text}\" buyrug'i tushunilmadi. Faqat \"Buyurtmalarim\" va \"Yordam\" tugmalari mavjud.";
        $result = $telegramService->sendMessage($chatId, $responseMessage);
        
        // Save outgoing message to database
        if ($telegramUser && $result['ok']) {
            $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result['result']['message_id'] ?? null, $result);
        }
    }
}
elseif (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $data = $callbackQuery['data'];
    
    $result = $telegramService->sendMessage($chatId, "Callback: {$data}");
    
    // Save outgoing message to database if user exists
    $telegramUser = TelegramUser::where('restaurant_id', $restaurant->id)
        ->where('telegram_id', $chatId)
        ->first();
    
    if ($telegramUser && $result['ok']) {
        $telegramService->saveOutgoingMessage($telegramUser, "Callback: {$data}", $result['result']['message_id'] ?? null, $result);
    }
}

// Always return OK to Telegram
http_response_code(200);
echo 'OK'; 