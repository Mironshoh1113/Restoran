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

        // Handle web app data
        if (isset($message['web_app_data'])) {
            $this->handleWebAppData($chatId, $message['web_app_data']);
            return;
        }

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
            // User is registered, send welcome message with web interface
            $this->sendWelcomeWithWebInterface($chatId, $restaurant, $user);
        }
    }

    /**
     * Send welcome message with web interface
     */
    protected function sendWelcomeWithWebInterface($chatId, $restaurant, $user)
    {
        // Generate unique session token for web interface
        $sessionToken = Str::random(32);
        
        // Store session data in cache
        Cache::put("web_session_{$sessionToken}", [
            'chat_id' => $chatId,
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'created_at' => now()
        ], 3600); // 1 hour

        // Create web interface URL
        $webUrl = url("/web-interface/{$sessionToken}");
        
        $message = "🍽 *{$restaurant->name}*\n\n";
        $message .= "Xush kelibsiz! Restoran menyusini ko'rish uchun quyidagi tugmani bosing:\n\n";
        $message .= "📱 *Web sahifani ochish* - menyu va buyurtma berish uchun";
        
        $keyboard = [
            [
                ['text' => '🍽 Menyuni ko\'rish', 'web_app' => ['url' => $webUrl]]
            ],
            [
                ['text' => '📞 Aloqa', 'callback_data' => 'contact'],
                ['text' => '📍 Manzil', 'callback_data' => 'location']
            ]
        ];
        
        $this->telegramService->sendMessageWithKeyboard($chatId, $message, $keyboard);
    }

    /**
     * Handle web app data from Telegram
     */
    protected function handleWebAppData($chatId, $webAppData)
    {
        $data = json_decode($webAppData, true);
        
        if (!$data) {
            $this->telegramService->sendMessage($chatId, 'Xatolik yuz berdi. Qaytadan urinib ko\'ring.');
            return;
        }
        
        // Handle different web app actions
        switch ($data['action'] ?? '') {
            case 'order_placed':
                $this->handleOrderPlaced($chatId, $data);
                break;
            case 'menu_viewed':
                $this->handleMenuViewed($chatId, $data);
                break;
            default:
                $this->telegramService->sendMessage($chatId, 'Buyurtma qabul qilindi! Tez orada siz bilan bog\'lanamiz.');
        }
    }

    /**
     * Handle order placed from web interface
     */
    protected function handleOrderPlaced($chatId, $data)
    {
        $orderData = $data['order'] ?? [];
        $restaurant = Restaurant::where('bot_token', $this->telegramService->getBotToken())->first();
        
        if (!$restaurant) {
            $this->telegramService->sendMessage($chatId, 'Xatolik yuz berdi.');
            return;
        }
        
        // Create order
        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $data['user_id'] ?? null,
            'telegram_chat_id' => $chatId,
            'total_amount' => $orderData['total'] ?? 0,
            'delivery_address' => $orderData['address'] ?? '',
            'payment_method' => $orderData['payment_method'] ?? 'cash',
            'status' => 'pending',
            'items' => json_encode($orderData['items'] ?? []),
            'customer_name' => $orderData['customer_name'] ?? '',
            'customer_phone' => $orderData['customer_phone'] ?? ''
        ]);
        
        // Send confirmation to user
        $message = "✅ *Buyurtma qabul qilindi!*\n\n";
        $message .= "📋 Buyurtma raqami: *#{$order->id}*\n";
        $message .= "💰 Jami: *{$order->total_amount} so'm*\n";
        $message .= "📍 Manzil: *{$order->delivery_address}*\n";
        $message .= "💳 To'lov: *" . ($order->payment_method === 'card' ? 'Karta' : 'Naqd pul') . "*\n\n";
        $message .= "Tez orada siz bilan bog'lanamiz!";
        
        $this->telegramService->sendMessage($chatId, $message);
        
        // Notify admin about new order
        $this->notifyAdminAboutOrder($order);
    }

    /**
     * Handle menu viewed from web interface
     */
    protected function handleMenuViewed($chatId, $data)
    {
        $this->telegramService->sendMessage($chatId, '🍽 Menyu ko\'rildi. Buyurtma berish uchun menyuni oching.');
    }

    /**
     * Web interface for Telegram users (from Web App)
     */
    public function webInterfaceFromApp(Request $request)
    {
        // Get init data from Telegram Web App
        $initData = $request->get('_tgInitData') ?? $request->get('tgInitData');
        
        // For testing purposes, allow access without init data
        if (!$initData) {
            // Try to get restaurant from query parameter or use first available
            $restaurantId = $request->get('restaurant_id');
            
            if ($restaurantId) {
                $restaurant = Restaurant::find($restaurantId);
            } else {
                // Use first available restaurant for testing
                $restaurant = Restaurant::first();
            }
            
            if (!$restaurant) {
                return response('No restaurant available', 404);
            }
            
            // Create a test user
            $user = (object) [
                'id' => 123456789,
                'first_name' => 'Test User',
                'username' => 'testuser'
            ];
            
            $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();
            
            return view('web-interface.index', compact('restaurant', 'user', 'categories'));
        }
        
        // Parse init data to get user info
        $data = [];
        parse_str($initData, $data);
        
        $user = $data['user'] ?? null;
        $chatId = $data['chat_instance'] ?? null;
        
        if (!$user || !$chatId) {
            return response('Invalid user data', 403);
        }
        
        // Find restaurant by bot token (from init data)
        $botToken = $this->extractBotTokenFromInitData($initData);
        
        if (!$botToken) {
            return response('Bot token not found', 403);
        }
        
        $restaurant = Restaurant::where('bot_token', $botToken)->first();
        
        if (!$restaurant) {
            return response('Restaurant not found', 404);
        }
        
        // Get or create user
        $dbUser = \App\Models\User::where('telegram_chat_id', $chatId)->first();
        
        if (!$dbUser) {
            // Create user from Telegram data
            $userData = json_decode($user, true);
            $dbUser = \App\Models\User::create([
                'name' => $userData['first_name'] ?? 'User',
                'email' => 'telegram_' . $chatId . '@example.com',
                'telegram_chat_id' => $chatId,
                'password' => bcrypt(Str::random(16))
            ]);
        }
        
        // Get categories and menu items
        $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();
        
        return view('web-interface.index', compact('restaurant', 'user', 'categories'));
    }

    /**
     * Extract bot token from Telegram init data
     */
    protected function extractBotTokenFromInitData($initData)
    {
        // This is a simplified version. In production, you should verify the hash
        $data = [];
        parse_str($initData, $data);
        
        // Try to get bot token from various sources
        $botToken = $data['bot_token'] ?? null;
        
        if (!$botToken) {
            // Try to find bot token from restaurant that matches the user
            $user = $data['user'] ?? null;
            if ($user) {
                $userData = json_decode($user, true);
                $chatId = $userData['id'] ?? null;
                
                if ($chatId) {
                    // Find restaurant by checking which bot this user is chatting with
                    // This is a simplified approach
                    $restaurant = Restaurant::where('admin_telegram_chat_id', $chatId)->first();
                    if ($restaurant) {
                        return $restaurant->bot_token;
                    }
                }
            }
        }
        
        return $botToken;
    }

    /**
     * Web interface for Telegram users
     */
    public function webInterface($token)
    {
        // Get session data from cache
        $sessionData = Cache::get("web_session_{$token}");
        
        if (!$sessionData) {
            return response('Session expired', 404);
        }
        
        $restaurant = Restaurant::find($sessionData['restaurant_id']);
        $user = \App\Models\User::find($sessionData['user_id']);
        
        if (!$restaurant || !$user) {
            return response('Invalid session', 404);
        }
        
        // Get categories and menu items
        $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();
        
        return view('web-interface.index', compact('restaurant', 'user', 'categories', 'token'));
    }

    /**
     * Get menu data for web interface
     */
    public function getMenu($token)
    {
        $sessionData = Cache::get("web_session_{$token}");
        
        if (!$sessionData) {
            return response()->json(['error' => 'Session expired'], 404);
        }
        
        $restaurant = Restaurant::find($sessionData['restaurant_id']);
        
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }
        
        $categories = Category::where('restaurant_id', $restaurant->id)
            ->with(['menuItems' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();
        
        return response()->json([
            'restaurant' => $restaurant,
            'categories' => $categories
        ]);
    }

    /**
     * Place order from web interface without token
     */
    public function placeOrderWithoutToken(Request $request)
    {
        // Get restaurant from query parameter or use first available
        $restaurantId = $request->get('restaurant_id');
        
        if ($restaurantId) {
            $restaurant = Restaurant::find($restaurantId);
        } else {
            $restaurant = Restaurant::first();
        }
        
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }
        
        // Validate request
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string',
            'payment_method' => 'required|in:cash,card',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string'
        ]);
        
        // Calculate total
        $total = 0;
        $orderItems = [];
        
        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['id']);
            $subtotal = $menuItem->price * $item['quantity'];
            $total += $subtotal;
            
            $orderItems[] = [
                'menu_item_id' => $item['id'],
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal
            ];
        }
        
        // Create order
        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => null,
            'telegram_chat_id' => null,
            'total_amount' => $total,
            'delivery_address' => $request->delivery_address,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'items' => json_encode($orderItems),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone
        ]);
        
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'message' => 'Buyurtma qabul qilindi!'
        ]);
    }

    /**
     * Place order from web interface
     */
    public function placeOrder(Request $request, $token)
    {
        $sessionData = Cache::get("web_session_{$token}");
        
        if (!$sessionData) {
            return response()->json(['error' => 'Session expired'], 404);
        }
        
        $restaurant = Restaurant::find($sessionData['restaurant_id']);
        $user = \App\Models\User::find($sessionData['user_id']);
        
        if (!$restaurant || !$user) {
            return response()->json(['error' => 'Invalid session'], 404);
        }
        
        // Validate request
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string',
            'payment_method' => 'required|in:cash,card',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string'
        ]);
        
        // Calculate total
        $total = 0;
        $orderItems = [];
        
        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['id']);
            $subtotal = $menuItem->price * $item['quantity'];
            $total += $subtotal;
            
            $orderItems[] = [
                'menu_item_id' => $item['id'],
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal
            ];
        }
        
        // Create order
        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'telegram_chat_id' => $sessionData['chat_id'],
            'total_amount' => $total,
            'delivery_address' => $request->delivery_address,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'items' => json_encode($orderItems),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone
        ]);
        
        // Send confirmation to Telegram
        $this->handleOrderPlaced($sessionData['chat_id'], [
            'order' => [
                'id' => $order->id,
                'total' => $total,
                'address' => $request->delivery_address,
                'payment_method' => $request->payment_method,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'items' => $orderItems
            ],
            'user_id' => $user->id
        ]);
        
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'message' => 'Buyurtma qabul qilindi!'
        ]);
    }

    /**
     * Notify admin about new order
     */
    protected function notifyAdminAboutOrder($order)
    {
        $restaurant = $order->restaurant;
        
        $message = "🆕 *Yangi buyurtma!*\n\n";
        $message .= "📋 Buyurtma raqami: *#{$order->id}*\n";
        $message .= "🏪 Restoran: *{$restaurant->name}*\n";
        $message .= "👤 Mijoz: *{$order->customer_name}*\n";
        $message .= "📞 Telefon: *{$order->customer_phone}*\n";
        $message .= "📍 Manzil: *{$order->delivery_address}*\n";
        $message .= "💰 Jami: *{$order->total_amount} so'm*\n";
        $message .= "💳 To'lov: *" . ($order->payment_method === 'card' ? 'Karta' : 'Naqd pul') . "*\n\n";
        
        // Send to admin if admin has telegram chat id
        if ($restaurant->admin_telegram_chat_id) {
            $this->telegramService->sendMessage($restaurant->admin_telegram_chat_id, $message);
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