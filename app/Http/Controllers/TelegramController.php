<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Project;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle webhook from Telegram
     */
    public function webhook(Request $request, $token)
    {
        $update = $request->all();
        Log::info('Telegram Webhook', $update);

        // Find restaurant by bot token
        $restaurant = Restaurant::where('bot_token', $token)->first();
        
        if (!$restaurant) {
            Log::error('Restaurant not found for bot token: ' . $token);
            return response('OK');
        }

        // Set bot token for this request
        $this->telegramService->setBotToken($token);

        // Handle different types of updates
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }

        return response('OK');
    }

    /**
     * Handle incoming messages
     */
    protected function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $contact = $message['contact'] ?? null;

        // Save or update telegram user
        $telegramUser = $this->saveTelegramUser($message['from']);

        // Save incoming message
        if ($telegramUser && $text) {
            $this->saveIncomingMessage($telegramUser, $text, $message['message_id'] ?? null, $message);
        }

        // Handle contact sharing
        if ($contact) {
            $this->handleContact($chatId, $contact);
            return;
        }

        // Handle text commands
        switch ($text) {
            case '/start':
                $this->handleStart($chatId);
                break;
            case '📋 Menyu':
                $this->handleMenu($chatId);
                break;
            case '🛒 Savat':
                $this->handleCart($chatId);
                break;
            case '📞 Buyurtma qilish':
                $this->handleOrder($chatId);
                break;
            case '📊 Buyurtmalarim':
                $this->handleMyOrders($chatId);
                break;
            case 'ℹ️ Yordam':
                $this->handleHelp($chatId);
                break;
            default:
                $this->handleUnknownCommand($chatId, $text);
        }
    }

    /**
     * Handle callback queries (inline keyboard buttons)
     */
    protected function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];
        $messageId = $callbackQuery['message']['message_id'];

        // Parse callback data
        $parts = explode('_', $data);
        $action = $parts[0] ?? '';

        switch ($action) {
            case 'category':
                $categoryId = $parts[1] ?? null;
                $this->handleCategorySelection($chatId, $categoryId);
                break;
            case 'add':
                $itemId = $parts[2] ?? null;
                $this->handleAddToCart($chatId, $itemId);
                break;
            case 'remove':
                $itemId = $parts[2] ?? null;
                $this->handleRemoveFromCart($chatId, $itemId);
                break;
            case 'pay':
                $orderId = $parts[2] ?? null;
                $paymentType = $parts[1] ?? 'cash';
                $this->handlePayment($chatId, $orderId, $paymentType);
                break;
            case 'cancel':
                $orderId = $parts[2] ?? null;
                $this->handleCancelOrder($chatId, $orderId);
                break;
        }
    }

    /**
     * Handle /start command
     */
    protected function handleStart($chatId)
    {
        // Get current bot token
        $botToken = $this->telegramService->getBotToken();
        
        // Find restaurant by bot token
        $restaurant = Restaurant::where('bot_token', $botToken)->first();
        
        if (!$restaurant) {
            $this->telegramService->sendMessage($chatId, 'Kechirasiz, bot sozlanmagan.');
            return;
        }

        // Check if user is already registered
        $user = \App\Models\User::where('telegram_chat_id', $chatId)->first();
        
        if (!$user) {
            // User is not registered, request contact
            $this->requestContact($chatId);
        } else {
            // User is registered, send welcome message
            $this->telegramService->sendWelcomeMessage($chatId, $restaurant);
        }
    }

    /**
     * Handle menu request
     */
    protected function handleMenu($chatId)
    {
        $restaurant = $this->getRestaurantByChatId($chatId);
        if (!$restaurant) {
            $this->sendRestaurantNotFound($chatId);
            return;
        }

        $categories = Category::whereHas('project', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->get();

        $this->telegramService->sendMenuCategories($chatId, $categories);
    }

    /**
     * Handle category selection
     */
    protected function handleCategorySelection($chatId, $categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            $this->telegramService->sendMessage($chatId, 'Kategoriya topilmadi.');
            return;
        }

        $items = MenuItem::where('category_id', $categoryId)->get();
        $this->telegramService->sendMenuItems($chatId, $items);
    }

    /**
     * Handle add to cart
     */
    protected function handleAddToCart($chatId, $itemId)
    {
        $item = MenuItem::find($itemId);
        if (!$item) {
            $this->telegramService->sendMessage($chatId, 'Taom topilmadi.');
            return;
        }

        // Get or create cart for this user
        $cart = Cache::get("cart_{$chatId}", []);
        
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity']++;
        } else {
            $cart[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1
            ];
        }

        Cache::put("cart_{$chatId}", $cart, 3600); // 1 hour

        $this->telegramService->sendMessage($chatId, "✅ {$item->name} savatga qo'shildi!");
    }

    /**
     * Handle cart view
     */
    protected function handleCart($chatId)
    {
        $cart = Cache::get("cart_{$chatId}", []);
        
        if (empty($cart)) {
            $this->telegramService->sendMessage($chatId, config('telegram.messages.cart_empty'));
            return;
        }

        $message = "🛒 Savat:\n\n";
        $total = 0;
        $keyboard = [];

        foreach ($cart as $itemId => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
            
            $message .= "• {$item['name']} x{$item['quantity']} = " . number_format($subtotal, 0, ',', ' ') . " so'm\n";
            
            $keyboard[] = [
                ['text' => "➖ {$item['name']}", 'callback_data' => "remove_item_{$itemId}"],
                ['text' => "➕ {$item['name']}", 'callback_data' => "add_item_{$itemId}"]
            ];
        }

        $message .= "\n<b>Jami: " . number_format($total, 0, ',', ' ') . " so'm</b>";

        if (!empty($keyboard)) {
            $keyboard[] = [['text' => '📞 Buyurtma qilish', 'callback_data' => 'checkout']];
            $inlineKeyboard = $this->telegramService->createInlineKeyboard($keyboard);
            $this->telegramService->sendMessage($chatId, $message, $inlineKeyboard);
        }
    }

    /**
     * Handle order checkout
     */
    protected function handleOrder($chatId)
    {
        $cart = Cache::get("cart_{$chatId}", []);
        
        if (empty($cart)) {
            $this->telegramService->sendMessage($chatId, config('telegram.messages.cart_empty'));
            return;
        }

        // Check if user has shared contact
        $userContact = Cache::get("user_contact_{$chatId}");
        if (!$userContact) {
            $this->requestContact($chatId);
            return;
        }

        // Create order
        $restaurant = $this->getRestaurantByChatId($chatId);
        if (!$restaurant) {
            $this->sendRestaurantNotFound($chatId);
            return;
        }

        $project = Project::where('restaurant_id', $restaurant->id)->first();
        if (!$project) {
            $this->telegramService->sendMessage($chatId, 'Proyekt topilmadi.');
            return;
        }

        $order = Order::create([
            'project_id' => $project->id,
            'customer_name' => $userContact['name'] ?? 'N/A',
            'customer_phone' => $userContact['phone'] ?? 'N/A',
            'customer_telegram_id' => $chatId,
            'delivery_address' => 'Telegram orqali buyurtma',
            'payment_type' => 'cash',
            'total_amount' => array_sum(array_map(function($item) {
                return $item['price'] * $item['quantity'];
            }, $cart)),
            'status' => 'new'
        ]);

        // Create order items
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        // Clear cart
        Cache::forget("cart_{$chatId}");

        // Send success message
        $message = str_replace(
            ['{order_number}', '{total_amount}'],
            [$order->order_number, number_format($order->total_amount, 0, ',', ' ')],
            config('telegram.messages.order_success')
        );

        $this->telegramService->sendMessage($chatId, $message);

        // Send notification to restaurant
        $this->telegramService->sendOrderStatusNotification($order);
    }

    /**
     * Handle my orders
     */
    protected function handleMyOrders($chatId)
    {
        $orders = Order::where('customer_telegram_id', $chatId)
            ->with(['project.restaurant'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            $this->telegramService->sendMessage($chatId, 'Sizda hali buyurtmalar yo\'q.');
            return;
        }

        $message = "📊 Buyurtmalaringiz:\n\n";
        
        foreach ($orders as $order) {
            $status = [
                'new' => '🆕 Yangi',
                'preparing' => '👨‍🍳 Tayyorlanmoqda',
                'on_way' => '🚚 Yolda',
                'delivered' => '✅ Yetkazildi',
                'cancelled' => '❌ Bekor'
            ][$order->status] ?? 'Nomalum';

            $message .= "📦 #{$order->order_number}\n";
            $message .= "📍 {$order->project->restaurant->name}\n";
            $message .= "💰 " . number_format($order->total_amount, 0, ',', ' ') . " so'm\n";
            $message .= "📅 {$order->created_at->format('d.m.Y H:i')}\n";
            $message .= "📊 {$status}\n\n";
        }

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Handle help
     */
    protected function handleHelp($chatId)
    {
        $restaurant = $this->getRestaurantByChatId($chatId);
        if (!$restaurant) {
            $this->sendRestaurantNotFound($chatId);
            return;
        }

        $message = str_replace(
            ['{phone}', '{address}'],
            [$restaurant->phone ?? 'N/A', $restaurant->address ?? 'N/A'],
            config('telegram.messages.help')
        );

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Handle contact sharing
     */
    protected function handleContact($chatId, $contact)
    {
        // Get current bot token
        $botToken = $this->telegramService->getBotToken();
        
        // Find restaurant by bot token
        $restaurant = Restaurant::where('bot_token', $botToken)->first();
        
        if (!$restaurant) {
            $this->telegramService->sendMessage($chatId, 'Kechirasiz, bot sozlanmagan.');
            return;
        }

        // Create or update user
        $user = \App\Models\User::updateOrCreate(
            ['telegram_chat_id' => $chatId],
            [
                'name' => $contact['first_name'] . ' ' . ($contact['last_name'] ?? ''),
                'phone' => $contact['phone_number'],
                'email' => 'telegram_' . $chatId . '@example.com', // Temporary email
                'password' => bcrypt(Str::random(16)), // Random password
                'role' => 'user',
                'restaurant_id' => $restaurant->id
            ]
        );

        // Send welcome message with main menu
        $this->telegramService->sendWelcomeMessage($chatId, $restaurant);
    }

    /**
     * Request contact from user
     */
    protected function requestContact($chatId)
    {
        $keyboard = [
            [
                [
                    'text' => '📱 Raqamni yuborish',
                    'request_contact' => true
                ]
            ]
        ];

        $replyKeyboard = $this->telegramService->createReplyKeyboard($keyboard, true, true);
        $this->telegramService->sendMessage($chatId, 'Buyurtma qilish uchun avval raqamingizni yuboring:', $replyKeyboard);
    }

    /**
     * Handle unknown commands
     */
    protected function handleUnknownCommand($chatId, $text)
    {
        $this->telegramService->sendMessage($chatId, "Kechirasiz, \"{$text}\" buyrug'i tushunilmadi. Menyudan tanlang.");
    }

    /**
     * Get restaurant by chat ID (you might need to adjust this logic)
     */
    protected function getRestaurantByChatId($chatId)
    {
        // Get current bot token
        $botToken = $this->telegramService->getBotToken();
        
        // This is a simplified version. You might want to store user-restaurant mapping
        return Restaurant::where('bot_token', $botToken)->first();
    }

    /**
     * Handle remove from cart
     */
    protected function handleRemoveFromCart($chatId, $itemId)
    {
        $item = MenuItem::find($itemId);
        if (!$item) {
            $this->telegramService->sendMessage($chatId, 'Taom topilmadi.');
            return;
        }

        // Get cart for this user
        $cart = Cache::get("cart_{$chatId}", []);
        
        if (isset($cart[$itemId])) {
            if ($cart[$itemId]['quantity'] > 1) {
                $cart[$itemId]['quantity']--;
            } else {
                unset($cart[$itemId]);
            }
        }

        Cache::put("cart_{$chatId}", $cart, 3600); // 1 hour

        $this->telegramService->sendMessage($chatId, "➖ {$item->name} savatdan olindi!");
    }

    /**
     * Handle payment
     */
    protected function handlePayment($chatId, $orderId, $paymentType)
    {
        $order = Order::find($orderId);
        if (!$order) {
            $this->telegramService->sendMessage($chatId, 'Buyurtma topilmadi.');
            return;
        }

        // Update order payment type
        $order->update(['payment_type' => $paymentType]);

        $this->telegramService->sendMessage($chatId, "To'lov turi: " . ($paymentType === 'card' ? 'Karta' : 'Naqd pul'));
    }

    /**
     * Handle cancel order
     */
    protected function handleCancelOrder($chatId, $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            $this->telegramService->sendMessage($chatId, 'Buyurtma topilmadi.');
            return;
        }

        // Update order status
        $order->update(['status' => 'cancelled']);

        $this->telegramService->sendMessage($chatId, "Buyurtma bekor qilindi.");
    }

    /**
     * Send restaurant not found message
     */
    protected function sendRestaurantNotFound($chatId)
    {
        $this->telegramService->sendMessage($chatId, 'Kechirasiz, restoran topilmadi.');
    }

    /**
     * Save or update telegram user
     */
    protected function saveTelegramUser($userData)
    {
        // Get current bot token
        $botToken = $this->telegramService->getBotToken();
        
        // Find restaurant by bot token
        $restaurant = Restaurant::where('bot_token', $botToken)->first();
        
        if (!$restaurant) {
            return;
        }

        // Save or update telegram user
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
     * Save incoming message
     */
    protected function saveIncomingMessage($telegramUser, $messageText, $messageId = null, $messageData = null)
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
} 