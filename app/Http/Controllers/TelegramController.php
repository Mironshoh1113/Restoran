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
        Log::info('Handling text command', ['text' => $text, 'chat_id' => $chatId]);
        
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
                Log::info('Buyurtmalarim button pressed', ['chat_id' => $chatId]);
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
            case 'order':
                if ($parts[1] === 'details') {
                    $orderId = $parts[2] ?? null;
                    $this->handleOrderDetails($chatId, $orderId);
                }
                break;
            case 'back':
                if ($parts[1] === 'to' && $parts[2] === 'orders') {
                    $this->handleMyOrders($chatId);
                }
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
        Log::info('Web interface accessed', [
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all()
        ]);
        
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
                Log::error('No restaurant available for web interface');
                return response('No restaurant available', 404);
            }
            
            // Create a test user
            $user = (object) [
                'id' => 123456789,
                'first_name' => 'Test User',
                'username' => 'testuser'
            ];
            
            $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();
            
            Log::info('Web interface loaded successfully', [
                'restaurant_id' => $restaurant->id,
                'categories_count' => $categories->count()
            ]);
            
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
        try {
            // Log the incoming request for debugging
            Log::info('Order placement request received', [
                'request_data' => $request->all(),
                'headers' => $request->headers->all()
            ]);
            
            // Get restaurant from query parameter or use first available
            $restaurantId = $request->get('restaurant_id');
            
            if ($restaurantId) {
                $restaurant = Restaurant::find($restaurantId);
            } else {
                $restaurant = Restaurant::first();
            }
            
            if (!$restaurant) {
                Log::error('Restaurant not found for order placement');
                return response()->json(['error' => 'Restaurant not found'], 404);
            }
            
            Log::info('Restaurant found', ['restaurant_id' => $restaurant->id, 'restaurant_name' => $restaurant->name]);
            
            // Validate request
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'delivery_address' => 'required|string',
                'payment_method' => 'required|in:cash,card',
                'customer_name' => 'required|string',
                'customer_phone' => 'required|string',
                'telegram_chat_id' => 'nullable|numeric'
            ]);
            
            Log::info('Request validation passed', ['validated_data' => $validated]);
            
            // Calculate total
            $total = 0;
            $orderItems = [];
            
            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['id']);
                if (!$menuItem) {
                    Log::error('Menu item not found', ['item_id' => $item['id']]);
                    return response()->json(['error' => 'Menu item not found: ' . $item['id']], 404);
                }
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
            
            Log::info('Order calculation completed', [
                'total' => $total,
                'items_count' => count($orderItems)
            ]);
            
            // Prepare order data
            $orderData = [
                'restaurant_id' => $restaurant->id,
                'user_id' => null,
                'project_id' => null,
                'telegram_chat_id' => $request->telegram_chat_id ? (string) $request->telegram_chat_id : null,
                'total_amount' => $total,
                'delivery_address' => $request->delivery_address,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'items' => json_encode($orderItems),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                // Add required fields from original schema
                'order_number' => 'WEB-' . time(),
                'total_price' => $total,
                'payment_type' => $request->payment_method,
                'address' => $request->delivery_address
            ];
            
            Log::info('Order data prepared', ['order_data' => $orderData]);
            
            // Create order
            $order = Order::create($orderData);
            
            // Log successful order
            Log::info('Order placed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'restaurant_id' => $restaurant->id,
                'total' => $total,
                'customer_name' => $request->customer_name
            ]);
            
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => 'Buyurtma qabul qilindi!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Order validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Order placement error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Place order from web interface
     */
    public function placeOrder(Request $request, $token)
    {
        try {
            Log::info('Order placement with token request received', [
                'token' => $token,
                'request_data' => $request->all()
            ]);
            
            $sessionData = Cache::get("web_session_{$token}");
            
            if (!$sessionData) {
                Log::error('Session expired for order placement', ['token' => $token]);
                return response()->json(['error' => 'Session expired'], 404);
            }
            
            $restaurant = Restaurant::find($sessionData['restaurant_id']);
            $user = \App\Models\User::find($sessionData['user_id']);
            
            if (!$restaurant || !$user) {
                Log::error('Invalid session data for order placement', [
                    'session_data' => $sessionData,
                    'restaurant_found' => !!$restaurant,
                    'user_found' => !!$user
                ]);
                return response()->json(['error' => 'Invalid session'], 404);
            }
            
            Log::info('Session validation passed', [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id
            ]);
            
            // Validate request
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'delivery_address' => 'required|string',
                'payment_method' => 'required|in:cash,card',
                'customer_name' => 'required|string',
                'customer_phone' => 'required|string',
                'telegram_chat_id' => 'nullable|numeric'
            ]);
            
            Log::info('Request validation passed', ['validated_data' => $validated]);
            
            // Calculate total
            $total = 0;
            $orderItems = [];
            
            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['id']);
                if (!$menuItem) {
                    Log::error('Menu item not found', ['item_id' => $item['id']]);
                    return response()->json(['error' => 'Menu item not found: ' . $item['id']], 404);
                }
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
            
            Log::info('Order calculation completed', [
                'total' => $total,
                'items_count' => count($orderItems)
            ]);
            
            // Prepare order data
            $orderData = [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'telegram_chat_id' => $sessionData['chat_id'] ? (string) $sessionData['chat_id'] : null,
                'total_amount' => $total,
                'delivery_address' => $request->delivery_address,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'items' => json_encode($orderItems),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                // Add required fields from original schema
                'order_number' => 'WEB-' . time(),
                'total_price' => $total,
                'payment_type' => $request->payment_method,
                'address' => $request->delivery_address
            ];
            
            Log::info('Order data prepared', ['order_data' => $orderData]);
            
            // Create order
            $order = Order::create($orderData);
            
            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
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
                'order_number' => $order->order_number,
                'message' => 'Buyurtma qabul qilindi!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Order validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Validation error: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Order placement error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
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
        try {
            Log::info('Starting handleMyOrders', ['chat_id' => $chatId]);
            
            // Send initial message
            $this->telegramService->sendMessage($chatId, '📊 Buyurtmalaringiz...');
            
            // Get current bot token
            $botToken = $this->telegramService->getBotToken();
            
            if (!$botToken) {
                Log::error('Bot token not set in handleMyOrders', ['chat_id' => $chatId]);
                $this->telegramService->sendMessage($chatId, 'Kechirasiz, bot sozlanmagan.');
                return;
            }
            
            Log::info('Found bot token', ['chat_id' => $chatId, 'bot_token' => $botToken]);
            
            // Find restaurant by bot token
            $restaurant = Restaurant::where('bot_token', $botToken)->first();
            
            if (!$restaurant) {
                Log::error('Restaurant not found for bot token', [
                    'chat_id' => $chatId,
                    'bot_token' => $botToken
                ]);
                $this->telegramService->sendMessage($chatId, 'Kechirasiz, bot sozlanmagan.');
                return;
            }

            Log::info('Found restaurant', [
                'chat_id' => $chatId,
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name
            ]);

            // Get orders for this user from this restaurant
            $orders = Order::where('telegram_chat_id', $chatId)
                ->where('restaurant_id', $restaurant->id)
                ->orderBy('created_at', 'desc')
                ->limit(10) // Limit to last 10 orders
                ->get();

            Log::info('Orders query result', [
                'chat_id' => $chatId,
                'restaurant_id' => $restaurant->id,
                'orders_count' => $orders->count(),
                'orders' => $orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total_price' => $order->total_price
                    ];
                })->toArray()
            ]);

            if ($orders->isEmpty()) {
                Log::info('No orders found for user', ['chat_id' => $chatId]);
                $this->telegramService->sendMessage($chatId, 'Sizda hali buyurtmalar yo\'q.');
                return;
            }

            Log::info('Found orders, building message', ['orders_count' => $orders->count()]);

            Log::info('Found orders for user', [
                'chat_id' => $chatId,
                'restaurant_id' => $restaurant->id,
                'orders_count' => $orders->count(),
                'orders' => $orders->pluck('id', 'order_number')->toArray()
            ]);

            if ($orders->isEmpty()) {
                $this->telegramService->sendMessage($chatId, 'Sizda hali buyurtmalar yo\'q.');
                return;
            }

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
                $message .= "📊 {$status}\n";
                
                // Add order details button
                $message .= "📋 Batafsil ma'lumot uchun tugmani bosing\n\n";
            }

            // Create inline keyboard for order details
            $keyboard = [];
            foreach ($orders as $order) {
                $keyboard[] = [['text' => "📋 #{$order->order_number} batafsil", 'callback_data' => "order_details_{$order->id}"]];
            }

            $inlineKeyboard = $this->telegramService->createInlineKeyboard($keyboard);

            Log::info('Sending my orders message', [
                'chat_id' => $chatId,
                'message_length' => strlen($message),
                'orders_count' => $orders->count()
            ]);

            $result = $this->telegramService->sendMessage($chatId, $message, $inlineKeyboard);
            
            if ($result['ok']) {
                Log::info('My orders message sent successfully', [
                    'chat_id' => $chatId,
                    'message_id' => $result['result']['message_id'] ?? null
                ]);
            } else {
                Log::error('Failed to send my orders message', [
                    'chat_id' => $chatId,
                    'error' => $result['error'] ?? 'Unknown error',
                    'result' => $result
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in handleMyOrders: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->telegramService->sendMessage($chatId, 'Kechirasiz, xatolik yuz berdi.');
        }
    }

    /**
     * Handle order details
     */
    protected function handleOrderDetails($chatId, $orderId)
    {
        try {
            Log::info('Starting handleOrderDetails', ['chat_id' => $chatId, 'order_id' => $orderId]);
            
            // Get current bot token
            $botToken = $this->telegramService->getBotToken();
            
            if (!$botToken) {
                Log::error('Bot token not set in handleOrderDetails', ['chat_id' => $chatId]);
                $this->telegramService->sendMessage($chatId, 'Kechirasiz, bot sozlanmagan.');
                return;
            }
            
            // Find restaurant by bot token
            $restaurant = Restaurant::where('bot_token', $botToken)->first();
            
            if (!$restaurant) {
                Log::error('Restaurant not found for bot token', [
                    'chat_id' => $chatId,
                    'bot_token' => $botToken
                ]);
                $this->telegramService->sendMessage($chatId, 'Kechirasiz, bot sozlanmagan.');
                return;
            }

            // Get order details
            $order = Order::where('id', $orderId)
                ->where('telegram_chat_id', $chatId)
                ->where('restaurant_id', $restaurant->id)
                ->first();

            if (!$order) {
                Log::error('Order not found', [
                    'chat_id' => $chatId,
                    'order_id' => $orderId,
                    'restaurant_id' => $restaurant->id
                ]);
                $this->telegramService->sendMessage($chatId, 'Buyurtma topilmadi.');
                return;
            }

            // Get order items
            $orderItems = OrderItem::where('order_id', $order->id)
                ->with('menuItem')
                ->get();

            // Status mapping
            $status = [
                'new' => '⏳ Yangi',
                'preparing' => '👨‍🍳 Tayyorlanmoqda',
                'on_way' => '🚚 Yolda',
                'delivered' => '✅ Yetkazildi',
                'cancelled' => '❌ Bekor'
            ][$order->status] ?? 'Nomalum';

            // Build message
            $message = "📋 *Buyurtma #{$order->order_number}*\n\n";
            $message .= "🏪 Restoran: *{$restaurant->name}*\n";
            $message .= "👤 Mijoz: *{$order->customer_name}*\n";
            $message .= "📞 Telefon: *{$order->customer_phone}*\n";
            
            if ($order->delivery_address) {
                $message .= "📍 Manzil: *{$order->delivery_address}*\n";
            }
            
            $message .= "📅 Sana: *{$order->created_at->format('d.m.Y H:i')}*\n";
            $message .= "📊 Holat: *{$status}*\n";
            $message .= "💳 To'lov: *" . ($order->payment_type === 'card' ? 'Karta' : 'Naqd pul') . "*\n\n";
            
            // Add items
            if ($orderItems->isNotEmpty()) {
                $message .= "🍽️ *Buyurtma tarkibi:*\n\n";
                foreach ($orderItems as $item) {
                    $subtotal = $item->price * $item->quantity;
                    $message .= "• {$item->menuItem->name} x{$item->quantity} = " . number_format($subtotal, 0, ',', ' ') . " so'm\n";
                }
                $message .= "\n";
            }
            
            $message .= "💰 *Jami: " . number_format($order->total_price ?? 0, 0, ',', ' ') . " so'm*\n\n";
            
            // Add additional info if available
            if ($order->address) {
                $message .= "📍 *Manzil:* {$order->address}\n\n";
            }

            // Create back button
            $keyboard = [
                [['text' => '🔙 Buyurtmalarimga qaytish', 'callback_data' => 'back_to_orders']]
            ];
            $inlineKeyboard = $this->telegramService->createInlineKeyboard($keyboard);

            Log::info('Sending order details message', [
                'chat_id' => $chatId,
                'order_id' => $orderId,
                'message_length' => strlen($message)
            ]);

            $result = $this->telegramService->sendMessage($chatId, $message, $inlineKeyboard);
            
            if ($result['ok']) {
                Log::info('Order details message sent successfully', [
                    'chat_id' => $chatId,
                    'order_id' => $orderId,
                    'message_id' => $result['result']['message_id'] ?? null
                ]);
            } else {
                Log::error('Failed to send order details message', [
                    'chat_id' => $chatId,
                    'order_id' => $orderId,
                    'error' => $result['error'] ?? 'Unknown error',
                    'result' => $result
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in handleOrderDetails: ' . $e->getMessage(), [
                'chat_id' => $chatId,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->telegramService->sendMessage($chatId, 'Kechirasiz, xatolik yuz berdi.');
        }
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
        Log::info('Unknown command received', ['text' => $text, 'chat_id' => $chatId]);
        
        // Check if it's the Buyurtmalarim command
        if ($text === '📊 Buyurtmalarim') {
            Log::info('Buyurtmalarim command detected in unknown command handler', ['chat_id' => $chatId]);
            $this->handleMyOrders($chatId);
            return;
        }
        
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