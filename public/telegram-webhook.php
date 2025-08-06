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

// Import classes at the top level
use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

try {
    // Bootstrap Laravel
    require_once __DIR__ . "/../vendor/autoload.php";

    $app = require_once __DIR__ . "/../bootstrap/app.php";
    $app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

    // Get the raw POST data
    $payload = file_get_contents("php://input");
    $update = json_decode($payload, true);

    // Log the incoming webhook
    Log::info("Telegram Webhook Received", [
        "payload" => $update,
        "request_uri" => $_SERVER["REQUEST_URI"] ?? "unknown",
        "method" => $_SERVER["REQUEST_METHOD"] ?? "unknown",
        "user_agent" => $_SERVER["HTTP_USER_AGENT"] ?? "unknown",
        "remote_addr" => $_SERVER["REMOTE_ADDR"] ?? "unknown"
    ]);

    if (!$update) {
        Log::error("Invalid JSON payload received", ["raw_payload" => $payload]);
        http_response_code(400);
        exit("Invalid JSON payload");
    }

    // Get bot token from URL path
    $path = $_SERVER["REQUEST_URI"] ?? "";
    $pathParts = explode("/", trim($path, "/"));
    $token = end($pathParts);

    if (!$token) {
        Log::error("No token provided in URL", ["path" => $path]);
        http_response_code(400);
        exit("No token provided");
    }

    Log::info("Processing webhook for token", ["token" => $token]);

    // Find restaurant by bot token
    $restaurant = Restaurant::where("bot_token", $token)->first();

    if (!$restaurant) {
        Log::error("Restaurant not found for bot token", ["token" => $token]);
        http_response_code(404);
        exit("Restaurant not found");
    }

    Log::info("Restaurant found", [
        "restaurant_id" => $restaurant->id,
        "restaurant_name" => $restaurant->name
    ]);

    // Check if required tables exist
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        
        // Check if telegram_users table exists
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES LIKE "telegram_users"');
        if (empty($tables)) {
            Log::error("telegram_users table does not exist");
            http_response_code(500);
            exit("Database table missing");
        }
        
        // Check if telegram_messages table exists
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES LIKE "telegram_messages"');
        if (empty($tables)) {
            Log::error("telegram_messages table does not exist");
            http_response_code(500);
            exit("Database table missing");
        }
        
    } catch (\Exception $e) {
        Log::error("Database connection error", [
            "error" => $e->getMessage()
        ]);
        http_response_code(500);
        exit("Database connection failed");
    }

    // Initialize TelegramService
    $telegramService = new TelegramService($token);

    // Handle different types of updates
    if (isset($update["message"])) {
        $message = $update["message"];
        $chatId = $message["chat"]["id"];
        $text = $message["text"] ?? "";
        $contact = $message["contact"] ?? null;
        $userData = $message["from"] ?? null;

        Log::info("Processing message", [
            "chat_id" => $chatId,
            "text" => $text,
            "user_data" => $userData
        ]);

        // Save or update telegram user
        $telegramUser = null;
        if ($userData) {
            try {
                // Check database connection first
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                
                $telegramUser = TelegramUser::updateOrCreate(
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
                        "last_activity" => now(),
                    ]
                );
                
                Log::info("Telegram user saved/updated", [
                    "telegram_id" => $userData["id"],
                    "restaurant_id" => $restaurant->id,
                    "user_id" => $telegramUser->id ?? null
                ]);
                
            } catch (\Exception $e) {
                Log::error("Error saving telegram user", [
                    "error" => $e->getMessage(),
                    "telegram_id" => $userData["id"] ?? null,
                    "restaurant_id" => $restaurant->id ?? null
                ]);
                
                // Continue without saving user if there's an error
                $telegramUser = null;
            }
        }

        // Save incoming message to database
        if ($telegramUser && $text) {
            $telegramService->saveIncomingMessage($telegramUser, $text, $message["message_id"] ?? null, $message);
        }

        // Handle /start command
        if ($text === "/start") {
            $welcomeMessage = "Assalomu alaykum! 🍽️\n\n";
            $welcomeMessage .= "Restoran buyurtma botiga xush kelibsiz!\n\n";
            $welcomeMessage .= "📋 Buyurtmalaringizni ko'rish uchun \"Buyurtmalarim\" tugmasini bosing\n";
            $welcomeMessage .= "ℹ Yordam kerak bo'lsa \"Yordam\" tugmasini bosing";

            $keyboard = [
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
                $telegramService->saveOutgoingMessage($telegramUser, $welcomeMessage, $result["result"]["message_id"] ?? null, $result);
            }
        }
        // Handle other commands
        elseif ($text === "📊 Buyurtmalarim" || $text === "Buyurtmalarim") {
            // Get orders for this user from this restaurant
            $orders = \App\Models\Order::where("telegram_chat_id", $chatId)
                ->where("restaurant_id", $restaurant->id)
                ->orderBy("created_at", "desc")
                ->limit(10)
                ->get();

            if ($orders->isEmpty()) {
                $responseMessage = "Sizda hali buyurtmalar yo'q.";
                $result = $telegramService->sendMessage($chatId, $responseMessage);
                
                // Save outgoing message to database
                if ($telegramUser && $result["ok"]) {
                    $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result["result"]["message_id"] ?? null, $result);
                }
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

                $result = $telegramService->sendMessage($chatId, $responseMessage);
                
                // Save outgoing message to database
                if ($telegramUser && $result["ok"]) {
                    $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result["result"]["message_id"] ?? null, $result);
                }
            }
        }
        elseif ($text === "ℹ Yordam" || $text === "Yordam") {
            $responseMessage = "Yordam kerakmi? 🤝\n\n";
            $responseMessage .= "📞 Qo'ng'iroq: " . ($restaurant->phone ?? "N/A") . "\n";
            $responseMessage .= "📍 Manzil: " . ($restaurant->address ?? "N/A") . "\n\n";
            $responseMessage .= "Yoki restoran bilan to'g'ridan-to'g'ri bog'laning.";
            
            $result = $telegramService->sendMessage($chatId, $responseMessage);
            
            // Save outgoing message to database
            if ($telegramUser && $result["ok"]) {
                $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result["result"]["message_id"] ?? null, $result);
            }
        }
        else {
            $responseMessage = "Kechirasiz, \"{$text}\" buyrug'i tushunilmadi. Faqat \"Buyurtmalarim\" va \"Yordam\" tugmalari mavjud.";
            $result = $telegramService->sendMessage($chatId, $responseMessage);
            
            // Save outgoing message to database
            if ($telegramUser && $result["ok"]) {
                $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result["result"]["message_id"] ?? null, $result);
            }
        }
    }
    elseif (isset($update["callback_query"])) {
        $callbackQuery = $update["callback_query"];
        $chatId = $callbackQuery["message"]["chat"]["id"];
        $data = $callbackQuery["data"];
        
        Log::info("Processing callback query", [
            "chat_id" => $chatId,
            "data" => $data
        ]);
        
        // Handle callback queries
        $parts = explode("_", $data);
        $action = $parts[0] ?? "";
        
        switch ($action) {
            case "refresh":
                if ($parts[1] === "orders") {
                    // Refresh orders
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
            $telegramUser = TelegramUser::where("restaurant_id", $restaurant->id)
                ->where("telegram_id", $chatId)
                ->first();
            
            if ($telegramUser && $result["ok"]) {
                $telegramService->saveOutgoingMessage($telegramUser, $responseMessage, $result["result"]["message_id"] ?? null, $result);
            }
        } catch (\Exception $e) {
            Log::error("Error saving callback message", [
                "error" => $e->getMessage(),
                "chat_id" => $chatId,
                "restaurant_id" => $restaurant->id ?? null
            ]);
        }
    }

    Log::info("Webhook processed successfully");
    
    // Always return OK to Telegram
    http_response_code(200);
    echo "OK";

} catch (Exception $e) {
    Log::error("Webhook error: " . $e->getMessage(), [
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTraceAsString()
    ]);
    
    // Return 200 to Telegram even on error to prevent retries
    http_response_code(200);
    echo "OK";
} 