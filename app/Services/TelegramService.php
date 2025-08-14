<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Added Cache facade

class TelegramService
{
    protected $apiUrl;
    protected $botToken;
    protected $timeout;

    public function __construct($botToken = null)
    {
        $this->apiUrl = config('telegram.api_url');
        $this->botToken = $botToken ?? config('telegram.default_bot');
        $this->timeout = config('telegram.timeout');
    }

    /**
     * Set bot token
     */
    public function setBotToken($token)
    {
        $this->botToken = $token;
        return $this;
    }

    /**
     * Get bot token
     */
    public function getBotToken()
    {
        return $this->botToken;
    }

    /**
     * Send message to user
     */
    public function sendMessage($chatId, $text, $keyboard = null, $parseMode = 'HTML')
    {
        try {
            if (!$this->botToken) {
                throw new \InvalidArgumentException('Bot token not set');
            }

            // Validate chat ID
            if (!is_numeric($chatId) || $chatId <= 0) {
                throw new \InvalidArgumentException('Invalid chat ID');
            }

            // Validate text length (Telegram limit is 4096 characters)
            if (strlen($text) > 4096) {
                throw new \InvalidArgumentException('Message text too long (max 4096 characters)');
            }

            // Rate limiting: prevent sending more than 1 message per second to the same chat
            $rateLimitKey = "telegram_rate_limit_{$chatId}";
            if (Cache::get($rateLimitKey, 0) > 0) {
                Log::warning('Rate limit exceeded for chat', [
                    'chat_id' => $chatId,
                    'bot_token' => $this->botToken
                ]);
                return ['ok' => false, 'error' => 'Rate limit exceeded'];
            }
            
            // Set rate limit for 1 second
            Cache::put($rateLimitKey, 1, 1);

            // Check for duplicate messages (same text to same chat within last 5 seconds)
            $duplicateKey = "telegram_duplicate_{$chatId}_" . md5($text);
            if (Cache::get($duplicateKey, false)) {
                Log::warning('Duplicate message detected, skipping', [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'bot_token' => $this->botToken
                ]);
                return ['ok' => false, 'error' => 'Duplicate message detected'];
            }
            
            // Set duplicate check for 5 seconds
            Cache::put($duplicateKey, true, 5);

            $data = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => $parseMode,
            ];

            if ($keyboard) {
                $data['reply_markup'] = json_encode($keyboard);
            }

            $result = $this->makeRequest('sendMessage', $data);
            
            if (!$result['ok']) {
                Log::error('Telegram sendMessage failed', [
                    'chat_id' => $chatId,
                    'error' => $result['error'] ?? 'Unknown error',
                    'bot_token' => $this->botToken
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Telegram sendMessage error', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send photo with caption
     */
    public function sendPhoto($chatId, $photo, $caption = null, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'photo' => $photo,
        ];

        if ($caption) {
            $data['caption'] = $caption;
        }

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

        return $this->makeRequest('sendPhoto', $data);
    }

    /**
     * Send invoice for payment
     */
    public function sendInvoice($chatId, $title, $description, $payload, $providerToken, $currency, $prices)
    {
        $data = [
            'chat_id' => $chatId,
            'title' => $title,
            'description' => $description,
            'payload' => $payload,
            'provider_token' => $providerToken,
            'currency' => $currency,
            'prices' => json_encode($prices),
        ];

        return $this->makeRequest('sendInvoice', $data);
    }

    /**
     * Set webhook
     */
    public function setWebhook($url, $certificate = null)
    {
        $data = ['url' => $url];

        if ($certificate) {
            $data['certificate'] = $certificate;
        }

        return $this->makeRequest('setWebhook', $data);
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        return $this->makeRequest('deleteWebhook');
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        return $this->makeRequest('getWebhookInfo');
    }

    /**
     * Set bot commands
     */
    public function setMyCommands($commands)
    {
        $data = ['commands' => json_encode($commands)];
        return $this->makeRequest('setMyCommands', $data);
    }

    /**
     * Get bot info
     */
    public function getMe()
    {
        return $this->makeRequest('getMe');
    }

    /**
     * Make HTTP request to Telegram API
     */
    protected function makeRequest($method, $data = [])
    {
        if (!$this->botToken) {
            Log::error('Bot token not set for Telegram API request', ['method' => $method]);
            return ['ok' => false, 'error' => 'Bot token not set'];
        }

        $url = $this->apiUrl . $this->botToken . '/' . $method;

        try {
            Log::info('Making Telegram API request', [
                'method' => $method,
                'url' => $url,
                'bot_token' => $this->botToken,
                'data' => $data
            ]);

            $response = Http::timeout($this->timeout)->post($url, $data);
            $responseData = $response->json();
            
            Log::info('Telegram API Response', [
                'method' => $method,
                'status_code' => $response->status(),
                'response' => $responseData
            ]);

            if (!$response->successful()) {
                Log::error('Telegram API HTTP error', [
                    'method' => $method,
                    'status_code' => $response->status(),
                    'response' => $responseData
                ]);
                return ['ok' => false, 'error' => 'HTTP ' . $response->status()];
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Telegram API Exception', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create inline keyboard
     */
    public function createInlineKeyboard($buttons)
    {
        return [
            'inline_keyboard' => $buttons
        ];
    }

    /**
     * Create reply keyboard
     */
    public function createReplyKeyboard($buttons, $resize = true, $oneTime = false)
    {
        return [
            'keyboard' => $buttons,
            'resize_keyboard' => $resize,
            'one_time_keyboard' => $oneTime
        ];
    }

    /**
     * Create main menu keyboard
     */
    public function createMainMenu()
    {
        return $this->createReplyKeyboard([
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
        ]);
    }

    /**
     * Create payment keyboard
     */
    public function createPaymentKeyboard($orderId)
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '💳 Karta orqali', 'callback_data' => 'pay_card_' . $orderId],
                ['text' => '💵 Naqd pul', 'callback_data' => 'pay_cash_' . $orderId]
            ],
            [
                ['text' => '❌ Bekor qilish', 'callback_data' => 'cancel_order_' . $orderId]
            ]
        ]);
    }

    /**
     * Send order status notification
     */
    public function sendOrderStatusNotification(Order $order)
    {
        $restaurant = $order->project->restaurant;
        
        if (!$restaurant->bot_token) {
            return false;
        }

        $this->botToken = $restaurant->bot_token;
        
        $statusMessages = [
            'new' => '🆕 Yangi buyurtma qabul qilindi',
            'preparing' => '👨‍🍳 Buyurtmangiz tayyorlanmoqda',
            'on_way' => '🚚 Buyurtmangiz yolda',
            'delivered' => '✅ Buyurtmangiz yetkazildi',
            'cancelled' => '❌ Buyurtma bekor qilindi'
        ];

        $message = $statusMessages[$order->status] ?? 'Buyurtma holati yangilandi';
        $message .= "\n\n📦 Buyurtma #{$order->order_number}";
        $message .= "\n💰 Jami: " . number_format($order->total_amount, 0, ',', ' ') . " so'm";
        $message .= "\n📅 Sana: " . $order->created_at->format('d.m.Y H:i');

        return $this->sendMessage($order->customer_telegram_id, $message);
    }

    /**
     * Send welcome message
     */
    public function sendWelcomeMessage($chatId, Restaurant $restaurant)
    {
        $message = config('telegram.messages.welcome');
        $keyboard = $this->createMainMenu();

        return $this->sendMessage($chatId, $message, $keyboard);
    }

    /**
     * Send menu categories
     */
    public function sendMenuCategories($chatId, $categories)
    {
        if ($categories->isEmpty()) {
            $message = config('telegram.messages.menu_not_found');
            return $this->sendMessage($chatId, $message);
        }

        $keyboard = [];
        foreach ($categories as $category) {
            $keyboard[] = [['text' => $category->name, 'callback_data' => 'category_' . $category->id]];
        }

        $inlineKeyboard = $this->createInlineKeyboard($keyboard);
        $message = "🍽️ Kategoriyalar:\n\nTanlang:";

        return $this->sendMessage($chatId, $message, $inlineKeyboard);
    }

    /**
     * Send menu items
     */
    public function sendMenuItems($chatId, $items)
    {
        if ($items->isEmpty()) {
            return $this->sendMessage($chatId, "Bu kategoriyada hali taomlar yo'q.");
        }

        $message = "🍽️ Taomlar:\n\n";
        $keyboard = [];

        foreach ($items as $item) {
            $message .= "• {$item->name} - " . number_format($item->price, 0, ',', ' ') . " so'm\n";
            $keyboard[] = [
                ['text' => "➕ {$item->name}", 'callback_data' => 'add_item_' . $item->id]
            ];
        }

        $inlineKeyboard = $this->createInlineKeyboard($keyboard);
        return $this->sendMessage($chatId, $message, $inlineKeyboard);
    }

    /**
     * Set bot name
     */
    public function setMyName($name)
    {
        $data = ['name' => $name];
        return $this->makeRequest('setMyName', $data);
    }

    /**
     * Set bot description
     */
    public function setMyDescription($description)
    {
        $data = ['description' => $description];
        return $this->makeRequest('setMyDescription', $data);
    }

    /**
     * Set bot short description
     */
    public function setMyShortDescription($shortDescription)
    {
        $data = ['short_description' => $shortDescription];
        return $this->makeRequest('setMyShortDescription', $data);
    }

    /**
     * Set profile photo
     */
    public function setProfilePhoto($photo)
    {
        $url = $this->apiUrl . $this->botToken . '/setProfilePhoto';
        
        try {
            // Get file contents and create a temporary file
            $fileContents = file_get_contents($photo->getPathname());
            $tempFile = tempnam(sys_get_temp_dir(), 'telegram_photo_');
            file_put_contents($tempFile, $fileContents);
            
            // Use cURL for file upload
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            
            // Create multipart form data
            $postData = [
                'photo' => new \CURLFile($tempFile, $photo->getMimeType(), $photo->getClientOriginalName())
            ];
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Clean up temporary file
            unlink($tempFile);
            
            $result = json_decode($response, true);
            
            if (config('telegram.debug')) {
                Log::info('Telegram API Request', [
                    'method' => 'setProfilePhoto',
                    'response' => $result
                ]);
            }

            return $result ?: ['ok' => false, 'error' => 'Invalid response'];
        } catch (\Exception $e) {
            Log::error('Telegram API Error', [
                'method' => 'setProfilePhoto',
                'error' => $e->getMessage()
            ]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get bot commands
     */
    public function getMyCommands()
    {
        return $this->makeRequest('getMyCommands');
    }

    /**
     * Save or update telegram user
     */
    public function saveTelegramUser($userData, Restaurant $restaurant)
    {
        $telegramUser = \App\Models\TelegramUser::updateOrCreate(
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

        return $telegramUser;
    }

    /**
     * Get telegram users for restaurant with proper filtering
     */
    public function getTelegramUsers(Restaurant $restaurant, $limit = 50)
    {
        return $restaurant->telegramUsers()
            ->where('is_active', true)
            ->orderBy('last_activity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Send message to all users of a restaurant
     */
    public function sendMessageToAllUsers(Restaurant $restaurant, $message, $keyboard = null)
    {
        if (!$restaurant->bot_token) {
            return ['success' => false, 'message' => 'Bot token o\'rnatilmagan'];
        }

        // Create new TelegramService instance with restaurant's bot token
        $telegramService = new TelegramService($restaurant->bot_token);
        
        // Get all active users for this restaurant
        $users = \App\Models\TelegramUser::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->get();
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($users as $user) {
            $result = $telegramService->sendMessage($user->telegram_id, $message, $keyboard);
            
            if ($result['ok']) {
                $successCount++;
                
                // Save outgoing message to database
                \App\Models\TelegramMessage::create([
                    'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $user->id,
                    'message_id' => $result['result']['message_id'] ?? null,
                    'direction' => 'outgoing',
                    'message_text' => $message,
                    'message_data' => $result['result'] ?? null,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);
            } else {
                $errorCount++;
                $errors[] = "User {$user->telegram_id}: " . ($result['description'] ?? 'Unknown error');
            }
        }
        
        return [
            'success' => true,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'total_users' => $users->count(),
            'errors' => $errors
        ];
    }

    /**
     * Send message to specific users of a restaurant
     */
    public function sendMessageToUsers($userIds, $message, $keyboard = null)
    {
        // Get restaurant from first user
        $firstUser = \App\Models\TelegramUser::whereIn('telegram_id', $userIds)->first();
        if (!$firstUser) {
            return ['success' => false, 'message' => 'Foydalanuvchilar topilmadi'];
        }
        
        $restaurant = \App\Models\Restaurant::find($firstUser->restaurant_id);
        if (!$restaurant || !$restaurant->bot_token) {
            return ['success' => false, 'message' => 'Bot token o\'rnatilmagan'];
        }

        // Create new TelegramService instance with restaurant's bot token
        $telegramService = new TelegramService($restaurant->bot_token);
        
        // Get users for this specific restaurant
        $users = \App\Models\TelegramUser::where('restaurant_id', $restaurant->id)
            ->whereIn('telegram_id', $userIds)
            ->where('is_active', true)
            ->get();
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($users as $user) {
            $result = $telegramService->sendMessage($user->telegram_id, $message, $keyboard);
            
            if ($result['ok']) {
                $successCount++;
                
                // Save outgoing message to database
                \App\Models\TelegramMessage::create([
                    'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $user->id,
                    'message_id' => $result['result']['message_id'] ?? null,
                    'direction' => 'outgoing',
                    'message_text' => $message,
                    'message_data' => $result['result'] ?? null,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);
            } else {
                $errorCount++;
                $errors[] = "User {$user->telegram_id}: " . ($result['description'] ?? 'Unknown error');
            }
        }
        
        return [
            'success' => true,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'total_users' => $users->count(),
            'errors' => $errors
        ];
    }

    /**
     * Save incoming message with proper restaurant association
     */
    public function saveIncomingMessage($telegramUser, $messageText, $messageId = null, $messageData = null)
    {
        return \App\Models\TelegramMessage::create([
            'restaurant_id' => $telegramUser->restaurant_id,
            'telegram_user_id' => $telegramUser->id,
            'message_id' => $messageId,
            'direction' => 'incoming',
            'message_text' => $messageText,
            'message_data' => $messageData,
            'message_type' => 'text',
            'is_read' => false,
        ]);
    }

    /**
     * Save outgoing message with proper restaurant association
     */
    public function saveOutgoingMessage($telegramUser, $messageText, $messageId = null, $messageData = null)
    {
        return \App\Models\TelegramMessage::create([
            'restaurant_id' => $telegramUser->restaurant_id,
            'telegram_user_id' => $telegramUser->id,
            'message_id' => $messageId,
            'direction' => 'outgoing',
            'message_text' => $messageText,
            'message_data' => $messageData,
            'message_type' => 'text',
            'is_read' => false,
        ]);
    }

    /**
     * Get conversation for specific user and restaurant
     */
    public function getConversation($telegramUser, $limit = 50)
    {
        return \App\Models\TelegramMessage::where('restaurant_id', $telegramUser->restaurant_id)
            ->where('telegram_user_id', $telegramUser->id)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Send message with inline keyboard
     */
    public function sendMessageWithKeyboard($chatId, $message, $keyboard)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ];
        
        return $this->makeRequest('sendMessage', $data);
    }

    /**
     * Send message with web app button
     */
    public function sendMessageWithWebApp($chatId, $message, $webAppUrl, $buttonText = 'Menyuni ko\'rish')
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => $buttonText,
                            'web_app' => ['url' => $webAppUrl]
                        ]
                    ]
                ]
            ])
        ];
        
        return $this->makeRequest('sendMessage', $data);
    }

    /**
     * Handle web app data
     */
    public function handleWebAppData($chatId, $webAppData)
    {
        // This method will be called when user interacts with web app
        $data = json_decode($webAppData, true);
        
        if (!$data) {
            $this->sendMessage($chatId, 'Xatolik yuz berdi. Qaytadan urinib ko\'ring.');
            return;
        }
        
        // Handle different web app actions
        switch ($data['action'] ?? '') {
            case 'order_placed':
                $this->sendMessage($chatId, '✅ Buyurtma qabul qilindi! Tez orada siz bilan bog\'lanamiz.');
                break;
            case 'menu_viewed':
                $this->sendMessage($chatId, '🍽 Menyu ko\'rildi. Buyurtma berish uchun menyuni oching.');
                break;
            default:
                $this->sendMessage($chatId, 'Buyurtma qabul qilindi! Tez orada siz bilan bog\'lanamiz.');
        }
    }
} 