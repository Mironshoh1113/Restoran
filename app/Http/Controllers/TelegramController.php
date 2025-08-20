<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TelegramController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle webhook from Telegram - Production Ready
     */
    public function webhook(Request $request, $token)
    {
        // Always return 200 OK to Telegram to prevent retries
        try {
            // Handle GET requests (for webhook verification)
            if ($request->isMethod('GET')) {
                return $this->handleGetRequest($token);
            }

            // Handle POST requests (actual webhook data)
            return $this->handlePostRequest($request, $token);

        } catch (\Exception $e) {
            // Catch-all for any unexpected errors
            Log::critical('Critical error in webhook handler', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ]);
            return response('OK', 200);
        }
    }

    /**
     * Handle GET requests for webhook verification
     */
    private function handleGetRequest($token)
    {
        try {
            // Validate token format
            if (!$this->isValidToken($token)) {
                Log::warning('Invalid token format in GET request');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token format'
                ], 200);
            }
        
        // Find restaurant by bot token
            $restaurant = Restaurant::where('bot_token', $token)->first();
        
        if (!$restaurant) {
                Log::error('Restaurant not found for bot token in GET request');
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found'
                ], 200);
            }

            Log::info('Webhook GET request verified', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook endpoint is working',
                'restaurant' => $restaurant->name,
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in webhook GET request', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Internal error'
            ], 200);
        }
    }

    /**
     * Handle POST requests with webhook data
     */
    private function handlePostRequest($request, $token)
    {
        try {
            // Log incoming request details
            Log::info('Webhook POST request received', [
                'token' => substr($token, 0, 10) . '...',
                'content_type' => $request->header('Content-Type'),
                'user_agent' => $request->header('User-Agent'),
                'request_size' => strlen($request->getContent()),
                'raw_content' => $request->getContent()
            ]);

            // Basic validation first
            if (!$token || strlen($token) < 10) {
                Log::warning('Invalid or empty token in POST request');
                return response('OK', 200);
            }

            // Validate token format
            if (!$this->isValidToken($token)) {
                Log::warning('Invalid token format in POST request', [
                    'token' => substr($token, 0, 10) . '...'
                ]);
                return response('OK', 200);
            }
        
        // Find restaurant by bot token
            $restaurant = Restaurant::where('bot_token', $token)->first();
        
        if (!$restaurant) {
                Log::error('Restaurant not found for bot token in POST request', [
                    'token' => substr($token, 0, 10) . '...',
                    'available_tokens' => Restaurant::whereNotNull('bot_token')->pluck('bot_token')->map(function($t) {
                        return substr($t, 0, 10) . '...';
                    })->toArray()
                ]);
                return response('OK', 200);
            }

            Log::info('Restaurant found for webhook', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'token' => substr($token, 0, 10) . '...'
            ]);

            // Get request data safely
            $requestData = $request->all();
            
            // Log request data structure
            Log::info('Webhook request data structure', [
                'restaurant_id' => $restaurant->id,
                'data_keys' => array_keys($requestData),
                'has_message' => isset($requestData['message']),
                'has_callback_query' => isset($requestData['callback_query']),
                'update_id' => $requestData['update_id'] ?? null
            ]);

            // Basic validation of request structure
            if (empty($requestData)) {
                Log::warning('Empty request data received', [
                'restaurant_id' => $restaurant->id
            ]);
                return response('OK', 200);
            }

            // Process the update safely
            $this->processWebhookUpdate($restaurant, $requestData, $token);

            return response('OK', 200);
            
        } catch (\Exception $e) {
            Log::error('Error in webhook POST request', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'token' => isset($token) ? substr($token, 0, 10) . '...' : 'unknown'
            ]);
            return response('OK', 200);
        }
    }

    /**
     * Validate bot token format
     */
    private function isValidToken($token)
    {
        return preg_match('/^\d+:[A-Za-z0-9_-]+$/', $token);
    }

    /**
     * Process webhook update safely
     */
    private function processWebhookUpdate($restaurant, $requestData, $token)
    {
        try {
            // Handle different types of updates
            if (isset($requestData['message'])) {
                $this->handleMessage($restaurant, $requestData['message']);
            } elseif (isset($requestData['callback_query'])) {
                $this->handleCallbackQuery($restaurant, $requestData['callback_query']);
            } elseif (isset($requestData['edited_message'])) {
                Log::info('Edited message received', ['restaurant_id' => $restaurant->id]);
            } else {
                Log::info('Unhandled update type', [
            'restaurant_id' => $restaurant->id,
                    'update_keys' => array_keys($requestData)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error processing webhook update', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Handle regular message
     */
    private function handleMessage($restaurant, $message)
    {
        try {
            Log::info('Processing message - START', [
                'restaurant_id' => $restaurant->id,
                'message_structure' => array_keys($message),
                'has_from' => isset($message['from']),
                'has_chat' => isset($message['chat']),
                'from_id' => $message['from']['id'] ?? 'missing',
                'chat_id' => $message['chat']['id'] ?? 'missing'
            ]);

            if (!isset($message['from']['id']) || !isset($message['chat']['id'])) {
                Log::warning('Invalid message structure', [
                    'restaurant_id' => $restaurant->id,
                    'message' => $message
                ]);
                return;
            }

            $userId = $message['from']['id'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';

            Log::info('Message details extracted', [
                'restaurant_id' => $restaurant->id,
                'user_id' => $userId,
                'chat_id' => $chatId,
                'text' => $text,
                'text_length' => strlen($text)
            ]);

            // Create or update telegram user
            try {
                $telegramUser = TelegramUser::updateOrCreate(
                    [
                        'telegram_id' => $chatId,
                        'restaurant_id' => $restaurant->id
                    ],
                    [
                        'first_name' => $message['from']['first_name'] ?? '',
                        'last_name' => $message['from']['last_name'] ?? '',
                        'username' => $message['from']['username'] ?? '',
                        'language_code' => $message['from']['language_code'] ?? 'uz',
                        'is_active' => true,
                        'last_activity' => now()
                    ]
                );

                Log::info('Telegram user created/updated', [
                'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $telegramUser->id,
                    'chat_id' => $chatId
            ]);
            
        } catch (\Exception $e) {
                Log::error('Error creating/updating telegram user', [
                    'restaurant_id' => $restaurant->id,
                    'chat_id' => $chatId,
                    'error' => $e->getMessage()
                ]);
                return;
            }

            // Store the message
            try {
                TelegramMessage::create([
                    'telegram_user_id' => $telegramUser->id,
                    'restaurant_id' => $restaurant->id,
                    'message_id' => $message['message_id'] ?? null,
                    'direction' => 'incoming',
                    'message_text' => $text,
                    'message_data' => $message,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);

                Log::info('Message stored in database', [
                'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $telegramUser->id,
                    'text' => $text
                ]);

            } catch (\Exception $e) {
                Log::error('Error storing message', [
                    'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $telegramUser->id,
                    'error' => $e->getMessage()
                ]);
                // Continue processing even if message storage fails
            }

            // Process commands
            if (strpos($text, '/') === 0) {
                Log::info('Processing as command', [
                    'restaurant_id' => $restaurant->id,
                    'command' => $text,
                    'chat_id' => $chatId
                ]);
                $this->handleCommand($restaurant, $telegramUser, $text);
            } else {
				// Text buttons handling
				if (in_array($text, ['📋 Menyu','Menyu'])) {
					$this->sendMenuMessage($restaurant, $telegramUser);
					return;
				}
				if (in_array($text, ['📊 Buyurtmalarim','Buyurtmalarim'])) {
					$this->sendRecentOrders($restaurant, $telegramUser);
					return;
				}
				if (in_array($text, ['ℹ Yordam','Yordam','Yordam'])) {
					$this->sendHelpMessage($restaurant, $telegramUser);
					return;
				}
				
				// Show main menu keyboard (Menyu opens Web App directly)
				$webAppUrl = url('/enhanced-web-interface?bot_token=' . $restaurant->bot_token);
				$buttonText = $restaurant->web_app_button_text ?: "Menyuni ko'rish";
				$keyboard = [
					'keyboard' => [
						[[ 'text' => '📋 ' . $buttonText, 'web_app' => ['url' => $webAppUrl] ]],
						[[ 'text' => '📊 Buyurtmalarim' ]],
						[[ 'text' => 'ℹ Yordam' ]],
					],
					'resize_keyboard' => true,
					'one_time_keyboard' => false,
				];
				$this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, "Kerakli bo'limni tanlang:", $keyboard);
            }

            Log::info('Message processing completed', [
                'restaurant_id' => $restaurant->id,
                'chat_id' => $chatId,
                'text' => $text
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error handling message', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'restaurant_id' => $restaurant->id,
                'message' => $message ?? 'null'
            ]);
        }
    }

    /**
     * Handle bot commands
     */
    private function handleCommand($restaurant, $telegramUser, $command)
    {
        try {
            $command = strtolower(trim($command));

            switch ($command) {
                case '/start':
                    $this->sendWelcomeMessage($restaurant, $telegramUser);
                    break;
                case '/menu':
                    $this->sendMenuMessage($restaurant, $telegramUser);
                    break;
                case '/help':
                    $this->sendHelpMessage($restaurant, $telegramUser);
                    break;
                default:
                    $this->sendUnknownCommandMessage($restaurant, $telegramUser);
            }

        } catch (\Exception $e) {
            Log::error('Error handling command', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Send welcome message
     */
    private function sendWelcomeMessage($restaurant, $telegramUser)
    {
        try {
            $message = "🎉 Xush kelibsiz!\n\n";
            $message .= "🏪 {$restaurant->name}\n";
            $message .= "📱 Menyu tugmasini bosing !\n\n";
            
            $webAppUrl = url('/enhanced-web-interface?bot_token=' . $restaurant->bot_token);
            $buttonText = $restaurant->web_app_button_text ?: "Menyuni ko'rish";
            
            $replyKeyboard = [
                'keyboard' => [
                    [
                        [
                            'text' => '📋 ' . $buttonText,
                            'web_app' => ['url' => $webAppUrl]
                        ]
                    ],
                    [
                        ['text' => '📊 Buyurtmalarim']
                    ],
                    [
                        ['text' => 'ℹ Yordam']
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ];

            $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, $message, $replyKeyboard);
            
        } catch (\Exception $e) {
            Log::error('Error sending welcome message', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Send menu message
     */
    private function sendMenuMessage($restaurant, $telegramUser)
    {
        try {
            $message = "📋 <b>{$restaurant->name}</b> menyusi:\n\n";
            
            $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();
            
            foreach ($categories as $category) {
                $message .= "🍽 <b>{$category->name}</b>\n";
                foreach ($category->menuItems as $item) {
                    $price = number_format($item->price, 0, '.', ' ');
                    $message .= "• {$item->name} - {$price} so'm\n";
                }
                $message .= "\n";
            }
            
            $webAppUrl = url('/enhanced-web-interface?bot_token=' . $restaurant->bot_token);
            $buttonText = $restaurant->web_app_button_text ?: "Menyuni ko'rish";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        [
                            'text' => $buttonText,
                            'web_app' => ['url' => $webAppUrl]
                        ]
                    ]
                ]
            ];

            $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, $message, $keyboard);
            
        } catch (\Exception $e) {
            Log::error('Error sending menu message', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Send help message
     */
    private function sendHelpMessage($restaurant, $telegramUser)
    {
        try {
            $info = [];
            $info[] = "❓ <b>Yordam</b>";
            if ($restaurant->description) {
                $info[] = "📝 {$restaurant->description}";
            }
            if ($restaurant->phone) {
                $info[] = "📞 Tel: {$restaurant->phone}";
            }
            if ($restaurant->address) {
                $info[] = "📍 Manzil: {$restaurant->address}";
            }
            if ($restaurant->working_hours) {
                $info[] = "⏰ Ish vaqti: {$restaurant->working_hours}";
            }
            $info[] = "";
            
            $message = implode("\n", $info);
            
            $webAppUrl = url('/enhanced-web-interface?bot_token=' . $restaurant->bot_token);
            $buttonText = $restaurant->web_app_button_text ?: "Menyuni ko'rish";
            $keyboard = [
                'inline_keyboard' => [
                    [[ 'text' => $buttonText, 'web_app' => ['url' => $webAppUrl] ]]
                ]
            ];
            
            // If logo exists, try to send photo with caption; otherwise send text
            if (!empty($restaurant->logo)) {
                $photoUrl = asset('storage/' . $restaurant->logo);
                $this->sendTelegramPhoto($restaurant->bot_token, $telegramUser->telegram_id, $photoUrl, $message, $keyboard);
            } else {
                $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, $message, $keyboard);
            }

        } catch (\Exception $e) {
            Log::error('Error sending help message', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Send unknown command message
     */
    private function sendUnknownCommandMessage($restaurant, $telegramUser)
    {
        try {
            $message = "❓ Noma'lum buyruq.\n\n";
            $message .= "Mavjud buyruqlar:\n";
            $message .= "/start - Botni ishga tushirish\n";
            $message .= "/menu - Menyuni ko'rish\n";
            $message .= "/help - Yordam\n";

            $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, $message);

        } catch (\Exception $e) {
            Log::error('Error sending unknown command message', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Send default response for regular messages
     */
    private function sendDefaultResponse($restaurant, $telegramUser)
    {
        try {
            $message = "Kerakli bo'limni tanlang:";
            
            $webAppUrl = url('/enhanced-web-interface?bot_token=' . $restaurant->bot_token);
            $buttonText = $restaurant->web_app_button_text ?: "Menyuni ko'rish";
            
            $replyKeyboard = [
                'keyboard' => [
                    [[ 'text' => '📋 ' . $buttonText, 'web_app' => ['url' => $webAppUrl] ]],
                    [[ 'text' => '📊 Buyurtmalarim' ]],
                    [[ 'text' => 'ℹ Yordam' ]],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ];

            $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, $message, $replyKeyboard);

        } catch (\Exception $e) {
            Log::error('Error sending default response', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Send recent orders summary (last 5) for this user and restaurant
     */
    private function sendRecentOrders($restaurant, $telegramUser)
    {
        try {
            $orders = \App\Models\Order::where('restaurant_id', $restaurant->id)
                ->where(function($q) use ($telegramUser, $restaurant) {
                    $q->where('telegram_chat_id', (string) $telegramUser->telegram_id);
                    if (!empty($telegramUser->phone_number)) {
                        $q->orWhere('customer_phone', $telegramUser->phone_number);
                    }
                    $q->orWhere(function($qq) use ($restaurant, $telegramUser) {
                        $qq->where('bot_token', $restaurant->bot_token)
                           ->where('telegram_chat_id', (string) $telegramUser->telegram_id);
                    });
                })
                ->with(['orderItems.menuItem'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Fallback: match by phone number if chat_id not present in older orders
            if ($orders->isEmpty() && !empty($telegramUser->phone_number)) {
                $orders = \App\Models\Order::where('restaurant_id', $restaurant->id)
                    ->where('customer_phone', $telegramUser->phone_number)
                    ->with(['orderItems.menuItem'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }

            // Fallback 2: recent orders in this restaurant without chat id (last 24h)
            $usedFallback2 = false;
            if ($orders->isEmpty()) {
                $orders = \App\Models\Order::where('restaurant_id', $restaurant->id)
                    ->whereNull('telegram_chat_id')
                    ->where('created_at', '>=', now()->subDay())
                    ->with(['orderItems.menuItem'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                $usedFallback2 = $orders->isNotEmpty();
            }

            if ($orders->isEmpty()) {
                $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, "Sizda hali buyurtmalar yo'q.");
                return;
            }

            $message = "📊 Oxirgi buyurtmalaringiz:\n\n";
            foreach ($orders as $order) {
                $orderNo = $order->order_number ?: ('#' . $order->id);
                $total = number_format((float)($order->total_price ?? $order->total_amount ?? 0), 0, ',', ' ');
                $date = optional($order->created_at)->timezone(config('app.timezone', 'Asia/Tashkent'))->format('d.m.Y H:i');
                $statusMap = [
                    'pending' => '⏳ Kutilmoqda',
                    'processing' => '👨‍🍳 Tayyorlanmoqda',
                    'prepared' => '👨‍🍳 Tayyorlanmoqda',
                    'new' => '🆕 Yangi',
                    'preparing' => '👨‍🍳 Tayyorlanmoqda',
                    'on_way' => '🚚 Yolda',
                    'delivered' => '✅ Yetkazildi',
                    'cancelled' => '❌ Bekor'
                ];
                $status = $statusMap[$order->status] ?? '❓ Noma’lum';
                $message .= "📦 {$orderNo} — {$status}\n💰 {$total} so'm\n📅 {$date}\n";

                // Build items list (prefer relation; fallback to JSON column)
                $lines = [];
                if ($order->relationLoaded('orderItems') && $order->orderItems && $order->orderItems->count() > 0) {
                    foreach ($order->orderItems as $oi) {
                        $name = $oi->menuItem->name ?? 'Taom';
                        $qty = (int) ($oi->quantity ?? 1);
                        $lineTotal = (float) ($oi->subtotal ?? ($oi->price * $qty));
                        $lines[] = "• {$name} x {$qty} — " . number_format($lineTotal, 0, ',', ' ') . " so'm";
                    }
                } elseif (is_array($order->items)) {
                    foreach ($order->items as $it) {
                        $name = $it['name'] ?? 'Taom';
                        $qty = (int) ($it['quantity'] ?? 1);
                        $price = (float) ($it['price'] ?? 0);
                        $lineTotal = (float) ($it['total'] ?? ($qty * $price));
                        $lines[] = "• {$name} x {$qty} — " . number_format($lineTotal, 0, ',', ' ') . " so'm";
                    }
                }

                if (!empty($lines)) {
                    $maxItemsToShow = 5;
                    $shown = array_slice($lines, 0, $maxItemsToShow);
                    $message .= "🧾 Buyurtmangiz:\n" . implode("\n", $shown);
                    if (count($lines) > $maxItemsToShow) {
                        $message .= "\n… va boshqalar";
                    }
                    $message .= "\n";
                }

                $message .= "\n"; // space between orders
            }

            $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, $message);
        } catch (\Exception $e) {
            Log::error('Error sending recent orders', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id,
                'user_id' => $telegramUser->id
            ]);
            $this->sendTelegramMessage($restaurant->bot_token, $telegramUser->telegram_id, "Xatolik yuz berdi. Iltimos, qayta urinib ko'ring.");
        }
    }

    /**
     * Handle callback query
     */
    private function handleCallbackQuery($restaurant, $callbackQuery)
    {
        try {
            // Acknowledge the callback query
            $this->answerCallbackQuery($restaurant->bot_token, $callbackQuery['id']);
            
            // Process the callback data
            $data = $callbackQuery['data'] ?? '';
            $chatId = $callbackQuery['from']['id'] ?? null;
            
            if (!$chatId) return;

            // Find telegram user
            $telegramUser = TelegramUser::where('telegram_id', $chatId)
                 ->where('restaurant_id', $restaurant->id)
                ->first();
            
            if (!$telegramUser) {
                Log::warning('Telegram user not found for callback query', [
                'restaurant_id' => $restaurant->id,
                    'chat_id' => $chatId
            ]);
            return;
        }

            switch ($data) {
                case 'show_menu':
                    $this->sendMenuMessage($restaurant, $telegramUser);
                    break;
                case 'show_help':
                    $this->sendHelpMessage($restaurant, $telegramUser);
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Error handling callback query', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id
            ]);
        }
    }

    /**
     * Answer callback query
     */
    private function answerCallbackQuery($botToken, $callbackQueryId)
    {
        try {
            $url = "https://api.telegram.org/bot{$botToken}/answerCallbackQuery";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['callback_query_id' => $callbackQueryId]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            curl_exec($ch);
            curl_close($ch);

        } catch (\Exception $e) {
            Log::error('Error answering callback query', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send telegram message safely
     */
    private function sendTelegramMessage($botToken, $chatId, $message, $keyboard = null)
    {
        try {
            Log::info('Attempting to send Telegram message', [
                'bot_token' => substr($botToken, 0, 10) . '...',
                    'chat_id' => $chatId,
                'message_length' => strlen($message),
                'has_keyboard' => $keyboard !== null,
                'message_preview' => substr($message, 0, 100)
            ]);

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            
            $data = [
                    'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ];
            
            if ($keyboard) {
                $data['reply_markup'] = json_encode($keyboard);
                Log::info('Keyboard added to message', [
                    'keyboard_structure' => array_keys($keyboard),
                    'chat_id' => $chatId
                ]);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: TelegramBot/1.0'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            Log::info('Telegram API response', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response_length' => strlen($response),
                'response' => $response,
                'chat_id' => $chatId
            ]);

            if ($httpCode !== 200) {
                Log::warning('Failed to send Telegram message - HTTP error', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'curl_error' => $curlError,
                    'chat_id' => $chatId,
                    'bot_token' => substr($botToken, 0, 10) . '...'
                ]);
                return false;
            }

            // Parse response
            $responseData = json_decode($response, true);
            if (!$responseData || !$responseData['ok']) {
                Log::warning('Failed to send Telegram message - API error', [
                    'response' => $response,
                    'chat_id' => $chatId,
                    'error_code' => $responseData['error_code'] ?? 'unknown',
                    'description' => $responseData['description'] ?? 'unknown'
                ]);
                return false;
            }

            Log::info('Telegram message sent successfully', [
                'chat_id' => $chatId,
                'message_id' => $responseData['result']['message_id'] ?? 'unknown',
                'bot_token' => substr($botToken, 0, 10) . '...'
            ]);

            // Try to store outgoing message if we can map chatId -> telegram_user
            try {
                $restaurant = Restaurant::where('bot_token', $botToken)->first();
                if ($restaurant) {
                    $telegramUser = TelegramUser::where('restaurant_id', $restaurant->id)
                        ->where('telegram_id', $chatId)
                        ->first();
                    if ($telegramUser) {
                        TelegramMessage::create([
                            'restaurant_id' => $restaurant->id,
                            'telegram_user_id' => $telegramUser->id,
                            'message_id' => $responseData['result']['message_id'] ?? null,
                            'direction' => 'outgoing',
                            'message_text' => $message,
                            'message_data' => $responseData,
                            'message_type' => 'text',
                            'is_read' => true,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to record outgoing message', [
                    'error' => $e->getMessage(),
                    'chat_id' => $chatId
                ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending Telegram message', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'chat_id' => $chatId,
                'bot_token' => substr($botToken ?? '', 0, 10) . '...'
            ]);
            return false;
        }
    }

    /**
     * Send telegram photo safely
     */
    private function sendTelegramPhoto($botToken, $chatId, $photoUrl, $caption = null, $keyboard = null)
    {
        try {
            $payload = [
                'chat_id' => $chatId,
                'photo' => $photoUrl,
            ];
            if ($caption) { $payload['caption'] = $caption; $payload['parse_mode'] = 'HTML'; }
            if ($keyboard) { $payload['reply_markup'] = json_encode($keyboard); }
            
            $apiUrl = config('telegram.api_url') . $botToken . '/sendPhoto';
            $response = \Illuminate\Support\Facades\Http::post($apiUrl, $payload);
            Log::info('sendPhoto response', ['status' => $response->status(), 'body' => $response->json()]);
        } catch (\Exception $e) {
            Log::warning('sendTelegramPhoto failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Serve web interface for Telegram Web App
     */
    public function webInterface(Request $request, $token = null)
    {
        try {
            Log::info('Web interface request received', [
                'token' => $token ? substr($token, 0, 10) . '...' : 'null',
                'query_params' => $request->query(),
                'user_agent' => $request->header('User-Agent')
            ]);

            $botToken = $token ?? $request->query('bot_token');
            
            if (!$botToken) {
                Log::warning('Bot token not provided for web interface');
                return response('Bot token not provided', 400);
            }
            
            Log::info('Looking for restaurant with bot token', [
                'token' => substr($botToken, 0, 10) . '...'
            ]);

            $restaurant = Restaurant::where('bot_token', $botToken)->first();
            
            if (!$restaurant) {
                Log::error('Restaurant not found for web interface', [
                    'token' => substr($botToken, 0, 10) . '...',
                    'available_tokens' => Restaurant::whereNotNull('bot_token')->pluck('bot_token')->map(function($t) {
                        return substr($t, 0, 10) . '...';
                    })->toArray()
                ]);
                return response('Restaurant not found', 404);
            }

            Log::info('Restaurant found for web interface', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'token' => substr($botToken, 0, 10) . '...'
            ]);

            Log::info('Loading categories and menu items');
            $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();

            Log::info('Categories loaded', [
                'categories_count' => $categories->count(),
                'total_menu_items' => $categories->sum(function($cat) {
                    return $cat->menuItems->count();
                })
            ]);

            Log::info('Rendering enhanced web interface view');
            return view('web-interface.enhanced', compact('restaurant', 'categories', 'botToken'));

        } catch (\Exception $e) {
            Log::error('Error serving web interface', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'trace' => $e->getTraceAsString(),
                'token' => $token ?? 'null'
            ]);
            return response('Internal Server Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Serve web interface from app
     */
    public function webInterfaceFromApp(Request $request)
    {
        return $this->webInterface($request);
    }

    /**
     * Place order from web interface
     */
    public function placeOrder(Request $request, $token)
    {
        try {
            $restaurant = Restaurant::where('bot_token', $token)->first();
        
        if (!$restaurant) {
                return response()->json(['success' => false, 'error' => 'Restaurant not found'], 404);
        }

            // Validate request data
            $data = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:255',
                'customer_address' => 'nullable|string|max:1000',
                'customer_notes' => 'nullable|string|max:1000'
            ]);

            // Calculate total and create order
            $totalPrice = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                $menuItem = MenuItem::find($item['id']);
                if ($menuItem && $menuItem->restaurant_id == $restaurant->id) {
                    $itemTotal = $menuItem->price * $item['quantity'];
                    $totalPrice += $itemTotal;
                    $orderItems[] = [
                        'menu_item_id' => $menuItem->id,
                        'quantity' => $item['quantity'],
                        'price' => $menuItem->price,
                        'total' => $itemTotal
                    ];
    }
            }

            if (empty($orderItems)) {
                return response()->json(['success' => false, 'error' => 'No valid items found'], 400);
    }

            // Create order
            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'customer_name' => $data['customer_name'] ?? 'Telegram User',
                'customer_phone' => $data['customer_phone'] ?? 'N/A',
                'delivery_address' => $data['customer_address'] ?? 'Telegram Order',
                'notes' => $data['customer_notes'] ?? '',
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => 'cash',
                'bot_token' => $token
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            Log::info('Order placed successfully', [
                'order_id' => $order->id,
                    'restaurant_id' => $restaurant->id,
                'total_price' => $totalPrice
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Buyurtma muvaffaqiyatli qabul qilindi!',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error placing order', [
                'error' => $e->getMessage(),
                'token' => $token ?? 'null'
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Buyurtma berishda xatolik yuz berdi'
            ], 500);
        }
    }

    /**
     * Place order without token (from app)
     */
    public function placeOrderWithoutToken(Request $request)
    {
        $botToken = $request->input('bot_token');
        
        if (!$botToken) {
            return response()->json(['success' => false, 'error' => 'Bot token not provided'], 400);
        }

        return $this->placeOrder($request, $botToken);
    }

    /**
     * Get recent orders for a Telegram user (by chat id or phone fallback)
     */
    public function getRecentOrdersForWeb(Request $request)
    {
        $botToken = $request->query('bot_token');
        $restaurant = Restaurant::where('bot_token', $botToken)->first();
        if (!$restaurant) {
            return response()->json(['success' => false, 'error' => 'Restaurant not found'], 404);
        }

        $chatId = $request->query('telegram_chat_id');
        $phone = $request->query('phone');

        $query = Order::where('restaurant_id', $restaurant->id);
        if ($chatId) {
            $query->where('telegram_chat_id', (string)$chatId);
        } elseif ($phone) {
            $query->where('customer_phone', $phone);
        } else {
            return response()->json(['success' => true, 'orders' => []]);
        }

        $orders = $query->orderByDesc('created_at')->limit(10)->get(['id','order_number','status','total_price','created_at']);
        return response()->json(['success' => true, 'orders' => $orders]);
    }
} 