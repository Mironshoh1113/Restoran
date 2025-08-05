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
        $telegramService->sendMessage($chatId, "🍽️ Menyu ko'rsatiladi...");
    }
    elseif ($text === '🛒 Savat') {
        $telegramService->sendMessage($chatId, "🛒 Savat ko'rsatiladi...");
    }
    elseif ($text === '📞 Buyurtma qilish') {
        $telegramService->sendMessage($chatId, "📞 Buyurtma qilish...");
    }
    elseif ($text === '📊 Buyurtmalarim') {
        $telegramService->sendMessage($chatId, "📊 Buyurtmalaringiz...");
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