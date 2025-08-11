<?php

/**
 * Telegram Webhook Handler
 * This file handles incoming webhooks from Telegram
 * 
 * URL Format: /telegram-webhook/{token}
 * Example: https://yourdomain.com/telegram-webhook/1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
 */

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    // Bootstrap Laravel properly
    require_once __DIR__ . "/../vendor/autoload.php";
    
    $app = require_once __DIR__ . "/../bootstrap/app.php";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Get the raw POST data
    $payload = file_get_contents("php://input");
    $update = json_decode($payload, true);
    
    // Log the incoming webhook
    \Illuminate\Support\Facades\Log::info("Telegram Webhook Received", [
        "payload" => $update,
        "request_uri" => $_SERVER["REQUEST_URI"] ?? "unknown",
        "method" => $_SERVER["REQUEST_METHOD"] ?? "unknown",
        "user_agent" => $_SERVER["HTTP_USER_AGENT"] ?? "unknown",
        "remote_addr" => $_SERVER["REMOTE_ADDR"] ?? "unknown"
    ]);
    
    if (!$update) {
        \Illuminate\Support\Facades\Log::error("Invalid JSON payload received", ["raw_payload" => $payload]);
        http_response_code(400);
        exit("Invalid JSON payload");
    }
    
    // Get bot token from URL path
    $path = $_SERVER["REQUEST_URI"] ?? "";
    $pathParts = explode("/", trim($path, "/"));
    $token = end($pathParts);
    
    if (!$token) {
        \Illuminate\Support\Facades\Log::error("No token provided in URL", ["path" => $path]);
        http_response_code(400);
        exit("No token provided");
    }
    
    // Validate token format
    if (!preg_match('/^\d+:[A-Za-z0-9_-]+$/', $token)) {
        \Illuminate\Support\Facades\Log::error("Invalid token format", ["token" => $token]);
        http_response_code(400);
        exit("Invalid token format");
    }
    
    \Illuminate\Support\Facades\Log::info("Processing webhook for token", ["token" => $token]);
    
    // Find restaurant by bot token
    $restaurant = \App\Models\Restaurant::where("bot_token", $token)->first();
    
    if (!$restaurant) {
        \Illuminate\Support\Facades\Log::error("Restaurant not found for bot token", ["token" => $token]);
        http_response_code(404);
        exit("Restaurant not found");
    }
    
    if (!$restaurant->is_active) {
        \Illuminate\Support\Facades\Log::error("Restaurant is not active", ["restaurant_id" => $restaurant->id]);
        http_response_code(400);
        exit("Restaurant is not active");
    }
    
    \Illuminate\Support\Facades\Log::info("Restaurant found", [
        "restaurant_id" => $restaurant->id,
        "restaurant_name" => $restaurant->name,
        "bot_username" => $restaurant->bot_username
    ]);
    
    // Initialize TelegramService with the specific bot token
    $telegramService = new \App\Services\TelegramService($token);
    
    // Handle different types of updates
    if (isset($update["message"])) {
        $message = $update["message"];
        $chatId = $message["chat"]["id"];
        $text = $message["text"] ?? "";
        $contact = $message["contact"] ?? null;
        $userData = $message["from"] ?? null;
        
        \Illuminate\Support\Facades\Log::info("Processing message", [
            "chat_id" => $chatId,
            "text" => $text,
            "user_data" => $userData,
            "restaurant_id" => $restaurant->id,
            "restaurant_name" => $restaurant->name
        ]);
        
        // Save or update telegram user for this specific restaurant
        $telegramUser = null;
        if ($userData) {
            try {
                // First, save or update global telegram user
                $globalUser = \App\Models\GlobalTelegramUser::updateOrCreate(
                    ['telegram_id' => $userData["id"]],
                    [
                        'username' => $userData["username"] ?? null,
                        'first_name' => $userData["first_name"] ?? null,
                        'last_name' => $userData["last_name"] ?? null,
                        'language_code' => $userData["language_code"] ?? 'uz',
                        'is_bot' => $userData["is_bot"] ?? false,
                        'last_activity' => now(),
                    ]
                );
                
                // Then, save or update restaurant-specific user
                $telegramUser = \App\Models\TelegramUser::updateOrCreate(
                    [
                        "restaurant_id" => $restaurant->id,
                        "telegram_id" => $userData["id"]
                    ],
                    [
                        "username" => $userData["username"] ?? null,
                        "first_name" => $userData["first_name"] ?? null,
                        "last_name" => $userData["last_name"] ?? null,
                        "language_code" => $userData["language_code"] ?? "uz",
                        "is_bot" => $userData["is_bot"] ?? false,
                        "is_active" => true,
                        "last_activity" => now(),
                    ]
                );
                
                \Illuminate\Support\Facades\Log::info("Telegram user saved/updated", [
                    "global_user_id" => $globalUser->id,
                    "telegram_id" => $userData["id"],
                    "restaurant_id" => $restaurant->id,
                    "user_id" => $telegramUser->id ?? null,
                    "username" => $userData["username"] ?? null
                ]);
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error saving telegram user", [
                    "error" => $e->getMessage(),
                    "telegram_id" => $userData["id"] ?? null,
                    "restaurant_id" => $restaurant->id ?? null
                ]);
                
                // Continue without saving user if there's an error
                $telegramUser = null;
            }
        }
        
        // Save incoming message to database for this restaurant
        if ($telegramUser && $text) {
            try {
                \App\Models\TelegramMessage::create([
                    'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $telegramUser->id,
                    'message_id' => $message["message_id"] ?? null,
                    'direction' => 'incoming',
                    'message_text' => $text,
                    'message_data' => $message,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);
                
                \Illuminate\Support\Facades\Log::info("Incoming message saved", [
                    "message_id" => $message["message_id"] ?? null,
                    "restaurant_id" => $restaurant->id,
                    "telegram_user_id" => $telegramUser->id
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error saving incoming message", [
                    "error" => $e->getMessage(),
                    "restaurant_id" => $restaurant->id,
                    "telegram_user_id" => $telegramUser->id ?? null
                ]);
            }
        }
        
        // Handle /start command
        if ($text === "/start") {
            $welcomeMessage = "Assalomu alaykum! 🍽️\n\n";
            $welcomeMessage .= "**{$restaurant->name}** buyurtma botiga xush kelibsiz!\n\n";
            $welcomeMessage .= "🍽️ Menyuni ko'rish uchun \"Menyu\" tugmasini bosing\n";
            $welcomeMessage .= "📊 Buyurtmalaringizni ko'rish uchun \"Buyurtmalarim\" tugmasini bosing\n";
            $welcomeMessage .= "ℹ Yordam kerak bo'lsa \"Yordam\" tugmasini bosing";
            
            $keyboard = [
                [
                    ["text" => "🍽️ Menyu"]
                ],
                [
                    ["text" => "📊 Buyurtmalarim"]
                ],
                [
                    ["text" => "ℹ Yordam"]
                ]
            ];
            
            $replyKeyboard = [
                "keyboard" => $keyboard,
                "resize_keyboard" => true,
                "one_time_keyboard" => false
            ];
            
            $result = $telegramService->sendMessage($chatId, $welcomeMessage, $replyKeyboard);
            
            // Save outgoing message to database
            if ($telegramUser && $result["ok"]) {
                try {
                    \App\Models\TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $telegramUser->id,
                        'message_id' => $result["result"]["message_id"] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $welcomeMessage,
                        'message_data' => $result["result"] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error saving outgoing message", [
                        "error" => $e->getMessage(),
                        "restaurant_id" => $restaurant->id,
                        "telegram_user_id" => $telegramUser->id ?? null
                    ]);
                }
            }
        }
        // Handle menu command
        elseif ($text === "🍽️ Menyu" || $text === "Menyu") {
            $webAppUrl = url("/web-interface?bot_token={$token}");
            
            $menuMessage = "🍽️ *Menyu*\n\n";
            $menuMessage .= "Menyuni ko'rish va buyurtma qilish uchun quyidagi tugmani bosing:";
            
            $inlineKeyboard = [
                [
                    [
                        "text" => "🍽️ Menyuni ochish",
                        "web_app" => ["url" => $webAppUrl]
                    ]
                ]
            ];
            
            $result = $telegramService->sendMessage($chatId, $menuMessage, ["inline_keyboard" => $inlineKeyboard]);
            
            // Save outgoing message to database
            if ($telegramUser && $result["ok"]) {
                try {
                    \App\Models\TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $telegramUser->id,
                        'message_id' => $result["result"]["message_id"] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $menuMessage,
                        'message_data' => $result["result"] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error saving outgoing message", [
                        "error" => $e->getMessage(),
                        "restaurant_id" => $restaurant->id,
                        "telegram_user_id" => $telegramUser->id ?? null
                    ]);
                }
            }
        }
        // Handle orders command
        elseif ($text === "📊 Buyurtmalarim" || $text === "Buyurtmalarim") {
            // Get orders for this user from this specific restaurant
            $orders = \App\Models\Order::where("telegram_chat_id", $chatId)
                ->where("restaurant_id", $restaurant->id)
                ->orderBy("created_at", "desc")
                ->limit(10)
                ->get();
            
            if ($orders->isEmpty()) {
                $responseMessage = "Sizda hali buyurtmalar yo'q.";
            } else {
                $responseMessage = "📊 *Buyurtmalaringiz:*\n\n";
                
                foreach ($orders as $order) {
                    $status = [
                        "new" => "⏳ Yangi",
                        "preparing" => "👨‍🍳 Tayyorlanmoqda",
                        "on_way" => "🚚 Yolda",
                        "delivered" => "✅ Yetkazildi",
                        "cancelled" => "❌ Bekor"
                    ][$order->status] ?? "❓ Nomalum";
                    
                    $responseMessage .= "📦 *#{$order->order_number}* {$status}\n";
                    $responseMessage .= "💰 " . number_format($order->total_price ?? 0, 0, ",", " ") . " so'm\n";
                    $responseMessage .= "📅 " . $order->created_at->format("d.m.Y H:i") . "\n\n";
                }
            }
            
            $result = $telegramService->sendMessage($chatId, $responseMessage);
            
            // Save outgoing message to database
            if ($telegramUser && $result["ok"]) {
                try {
                    \App\Models\TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $telegramUser->id,
                        'message_id' => $result["result"]["message_id"] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $responseMessage,
                        'message_data' => $result["result"] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error saving outgoing message", [
                        "error" => $e->getMessage(),
                        "restaurant_id" => $restaurant->id,
                        "telegram_user_id" => $telegramUser->id ?? null
                    ]);
                }
            }
        }
        // Handle help command
        elseif ($text === "ℹ Yordam" || $text === "Yordam") {
            $responseMessage = "Yordam kerakmi? 🤝\n\n";
            $responseMessage .= "📞 Qo'ng'iroq: " . ($restaurant->phone ?? "N/A") . "\n";
            $responseMessage .= "📍 Manzil: " . ($restaurant->address ?? "N/A") . "\n\n";
            $responseMessage .= "Yoki restoran bilan to'g'ridan-to'g'ri bog'laning.";
            
            $result = $telegramService->sendMessage($chatId, $responseMessage);
            
            // Save outgoing message to database
            if ($telegramUser && $result["ok"]) {
                try {
                    \App\Models\TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $telegramUser->id,
                        'message_id' => $result["result"]["message_id"] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $responseMessage,
                        'message_data' => $result["result"] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error saving outgoing message", [
                        "error" => $e->getMessage(),
                        "restaurant_id" => $restaurant->id,
                        "telegram_user_id" => $telegramUser->id ?? null
                    ]);
                }
            }
        }
        // Handle unknown commands
        else {
            $responseMessage = "Kechirasiz, \"{$text}\" buyrug'i tushunilmadi.\n\n";
            $responseMessage .= "Mavjud buyruqlar:\n";
            $responseMessage .= "🍽️ Menyu - Menyuni ko'rish\n";
            $responseMessage .= "📊 Buyurtmalarim - Buyurtmalaringizni ko'rish\n";
            $responseMessage .= "ℹ Yordam - Yordam olish";
            
            $result = $telegramService->sendMessage($chatId, $responseMessage);
            
            // Save outgoing message to database
            if ($telegramUser && $result["ok"]) {
                try {
                    \App\Models\TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $telegramUser->id,
                        'message_id' => $result["result"]["message_id"] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $responseMessage,
                        'message_data' => $result["result"] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error saving outgoing message", [
                        "error" => $e->getMessage(),
                        "restaurant_id" => $restaurant->id,
                        "telegram_user_id" => $telegramUser->id ?? null
                    ]);
                }
            }
        }
    }
    elseif (isset($update["callback_query"])) {
        $callbackQuery = $update["callback_query"];
        $chatId = $callbackQuery["message"]["chat"]["id"];
        $data = $callbackQuery["data"];
        
        \Illuminate\Support\Facades\Log::info("Processing callback query", [
            "chat_id" => $chatId,
            "data" => $data,
            "restaurant_id" => $restaurant->id
        ]);
        
        // Handle callback queries
        $parts = explode("_", $data);
        $action = $parts[0] ?? "";
        
        switch ($action) {
            case "refresh":
                if ($parts[1] === "orders") {
                    // Refresh orders for this specific restaurant
                    $orders = \App\Models\Order::where("telegram_chat_id", $chatId)
                        ->where("restaurant_id", $restaurant->id)
                        ->orderBy("created_at", "desc")
                        ->limit(5)
                        ->get();
                    
                    if ($orders->isEmpty()) {
                        $responseMessage = "Sizda hali buyurtmalar yo'q.";
                    } else {
                        $responseMessage = "📊 *So'nggi buyurtmalar:*\n\n";
                        foreach ($orders as $order) {
                            $status = [
                                "new" => "⏳ Yangi",
                                "preparing" => "👨‍🍳 Tayyorlanmoqda",
                                "on_way" => "🚚 Yolda",
                                "delivered" => "✅ Yetkazildi",
                                "cancelled" => "❌ Bekor"
                            ][$order->status] ?? "❓ Nomalum";
                            
                            $responseMessage .= "📦 *#{$order->order_number}* {$status}\n";
                            $responseMessage .= "💰 " . number_format($order->total_price ?? 0, 0, ",", " ") . " so'm\n";
                            $responseMessage .= "📅 " . $order->created_at->format("d.m.Y H:i") . "\n\n";
                        }
                    }
                } else {
                    $responseMessage = "Callback: {$data}";
                }
                break;
                
            case "contact":
                $responseMessage = "📞 Admin bilan bog'lanish uchun: " . ($restaurant->phone ?? "N/A");
                break;
                
            default:
                $responseMessage = "Callback: {$data}";
        }
        
        $result = $telegramService->sendMessage($chatId, $responseMessage);
        
        // Save outgoing message to database if user exists
        try {
            $telegramUser = \App\Models\TelegramUser::where("restaurant_id", $restaurant->id)
                ->where("telegram_id", $chatId)
                ->first();
            
            if ($telegramUser && $result["ok"]) {
                \App\Models\TelegramMessage::create([
                    'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $telegramUser->id,
                    'message_id' => $result["result"]["message_id"] ?? null,
                    'direction' => 'outgoing',
                    'message_text' => $responseMessage,
                    'message_data' => $result["result"] ?? null,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error saving callback message", [
                "error" => $e->getMessage(),
                "chat_id" => $chatId,
                "restaurant_id" => $restaurant->id ?? null
            ]);
        }
    }
    
    \Illuminate\Support\Facades\Log::info("Webhook processed successfully", [
        "restaurant_id" => $restaurant->id,
        "restaurant_name" => $restaurant->name
    ]);
    
    // Always return OK to Telegram
    http_response_code(200);
    echo "OK";
    
} catch (Exception $e) {
    \Illuminate\Support\Facades\Log::error("Webhook error: " . $e->getMessage(), [
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTraceAsString()
    ]);
    
    // Return 200 to Telegram even on error to prevent retries
    http_response_code(200);
    echo "OK";
} 