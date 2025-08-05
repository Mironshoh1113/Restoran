<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

        return $this->makeRequest('sendMessage', $data);
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
        $url = $this->apiUrl . $this->botToken . '/' . $method;

        try {
            $response = Http::timeout($this->timeout)->post($url, $data);
            
            if (config('telegram.debug')) {
                Log::info('Telegram API Request', [
                    'method' => $method,
                    'data' => $data,
                    'response' => $response->json()
                ]);
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram API Error', [
                'method' => $method,
                'error' => $e->getMessage()
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
        $message .= "\n\nBuyurtma #{$order->order_number}";
        $message .= "\nJami: " . number_format($order->total_amount, 0, ',', ' ') . " so'm";

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
            $response = Http::timeout($this->timeout)
                ->attach('photo', file_get_contents($photo->getPathname()), $photo->getClientOriginalName())
                ->post($url);
            
            if (config('telegram.debug')) {
                Log::info('Telegram API Request', [
                    'method' => 'setProfilePhoto',
                    'response' => $response->json()
                ]);
            }

            return $response->json();
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
} 