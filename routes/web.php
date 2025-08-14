<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\BotController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\GlobalTelegramController;
use App\Http\Controllers\TelegramController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'getStats'])->middleware(['auth', 'verified'])->name('dashboard.stats');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('restaurants', RestaurantController::class);
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::patch('orders/{order}/courier', [OrderController::class, 'assignCourier'])->name('orders.assign-courier');
        
        // Courier routes
        Route::resource('couriers', CourierController::class);
        
        // Bot routes
        Route::get('bots', [BotController::class, 'index'])->name('bots.index');
        Route::get('bots/{restaurant}', [BotController::class, 'show'])->name('bots.show');
        Route::patch('bots/{restaurant}', [BotController::class, 'update'])->name('bots.update');
        Route::post('bots/{restaurant}/test', [BotController::class, 'test'])->name('bots.test');
        Route::post('bots/{restaurant}/webhook', [BotController::class, 'setWebhook'])->name('bots.set-webhook');
        Route::delete('bots/{restaurant}/webhook', [BotController::class, 'deleteWebhook'])->name('bots.delete-webhook');
        Route::post('bots/{restaurant}/send-test', [BotController::class, 'sendTestMessage'])->name('bots.send-test');
        
        // Multi-bot management routes
        Route::get('bots/stats/all', [BotController::class, 'getAllUsersStats'])->name('bots.all-stats');
        Route::post('bots/send-multiple', [BotController::class, 'sendMessageToMultipleRestaurants'])->name('bots.send-multiple');
        Route::get('bots/users/all', [BotController::class, 'getAllUsers'])->name('bots.all-users');
        Route::post('bots/test-multiple', [BotController::class, 'testMultipleBots'])->name('bots.test-multiple');
        Route::post('bots/set-webhooks-multiple', [BotController::class, 'setMultipleWebhooks'])->name('bots.set-webhooks-multiple');
        
        // Global Telegram Users Management
        Route::prefix('global-telegram')->name('global-telegram.')->group(function () {
            Route::get('/', [GlobalTelegramController::class, 'index'])->name('index');
            Route::get('/stats', [GlobalTelegramController::class, 'getGlobalStats'])->name('stats');
            Route::get('/{globalUser}', [GlobalTelegramController::class, 'show'])->name('show');
            Route::get('/{globalUser}/stats', [GlobalTelegramController::class, 'getUserStats'])->name('user-stats');
            Route::post('/{globalUser}/send-message', [GlobalTelegramController::class, 'sendMessageToAllRestaurants'])->name('send-message');
        });
        
        // New bot management routes
        Route::post('bots/{restaurant}/update-name', [BotController::class, 'updateBotName'])->name('bots.update-name');
        Route::post('bots/{restaurant}/update-description', [BotController::class, 'updateBotDescription'])->name('bots.update-description');
        Route::post('bots/{restaurant}/update-photo', [BotController::class, 'updateBotPhoto'])->name('bots.update-photo');
        Route::get('bots/{restaurant}/commands', [BotController::class, 'getBotCommands'])->name('bots.get-commands');
        Route::post('bots/{restaurant}/commands', [BotController::class, 'setBotCommands'])->name('bots.set-commands');
        
        // Telegram users management routes
        Route::get('bots/{restaurant}/users', [BotController::class, 'users'])->name('bots.users');
        Route::post('bots/{restaurant}/users/send', [BotController::class, 'sendMessageToUsers'])->name('bots.send-to-users');
        Route::post('bots/{restaurant}/users/send-all', [BotController::class, 'sendMessageToAllUsers'])->name('bots.send-to-all-users');
        Route::get('bots/{restaurant}/users/stats', [BotController::class, 'getUsersStats'])->name('bots.users-stats');
        
        // Conversation routes
        Route::get('bots/{restaurant}/users/{telegramUser}/conversation', [BotController::class, 'conversation'])->name('bots.conversation');
        Route::post('bots/{restaurant}/users/{telegramUser}/send', [BotController::class, 'sendMessageToUser'])->name('bots.send-to-user');
        Route::get('bots/{restaurant}/users/{telegramUser}/messages', [BotController::class, 'getNewMessages'])->name('bots.get-new-messages');
        Route::post('bots/{restaurant}/users/{telegramUser}/read', [BotController::class, 'markMessagesAsRead'])->name('bots.mark-as-read');
        
        // Real-time message updates
        Route::get('bots/{restaurant}/users/{telegramUser}/messages/new', [BotController::class, 'getNewMessages'])->name('bots.get-new-messages-rt');
        
        // Project routes
        Route::get('restaurants/{restaurant}/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('restaurants/{restaurant}/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('restaurants/{restaurant}/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('restaurants/{restaurant}/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('restaurants/{restaurant}/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::patch('restaurants/{restaurant}/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('restaurants/{restaurant}/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        
        // Category routes
        Route::get('restaurants/{restaurant}/projects/{project}/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('restaurants/{restaurant}/projects/{project}/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::patch('restaurants/{restaurant}/projects/{project}/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('restaurants/{restaurant}/projects/{project}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Menu Item routes
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/create', [MenuItemController::class, 'create'])->name('menu-items.create');
        Route::post('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}', [MenuItemController::class, 'show'])->name('menu-items.show');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('menu-items.edit');
        Route::patch('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
        Route::delete('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');
    });
});

// Telegram webhook
Route::post('/telegram/webhook/{token}', [TelegramController::class, 'webhook'])->name('telegram.webhook');

// Web interface for Telegram users
Route::get('/web-interface/{token}', [TelegramController::class, 'webInterface'])->name('web.interface');
Route::get('/web-interface', [TelegramController::class, 'webInterfaceFromApp'])->name('web.interface.app');
Route::post('/web-interface/{token}/order', [TelegramController::class, 'placeOrder'])->name('web.place-order');
Route::post('/web-interface/order', [TelegramController::class, 'placeOrderWithoutToken'])->name('web.place-order-no-token');
Route::get('/web-interface/{token}/menu', [TelegramController::class, 'getMenu'])->name('web.get-menu');
Route::get('/web-interface/menu', [TelegramController::class, 'getMenuWithoutToken'])->name('web.get-menu-no-token');

// Enhanced web interface for Telegram users
Route::get('/enhanced-web-interface/{token}', [TelegramController::class, 'webInterface'])->name('enhanced.web.interface');
Route::get('/enhanced-web-interface', [TelegramController::class, 'webInterfaceFromApp'])->name('enhanced.web.interface.app');
Route::post('/enhanced-web-interface/{token}/order', [TelegramController::class, 'placeOrder'])->name('enhanced.web.place-order');
Route::post('/enhanced-web-interface/order', [TelegramController::class, 'placeOrderWithoutToken'])->name('enhanced.web.place-order-no-token');

// Telegram Web App direct access
Route::get('/web-interface/app/{botToken}', function($botToken) {
    $restaurant = \App\Models\Restaurant::where('bot_token', $botToken)->where('is_active', true)->first();
    
    if (!$restaurant) {
        return response('Restaurant not found or not active', 404);
    }
    
    // Get categories and menu items
    $categories = \App\Models\Category::where('restaurant_id', $restaurant->id)
        ->with(['menuItems' => function($query) {
            $query->where('is_active', true);
        }])
        ->get();
    
    // Create a mock user for direct access
    $user = (object) [
        'id' => 0,
        'name' => 'Telegram User',
        'phone' => null
    ];
    
    return view('web-interface.index', compact('restaurant', 'user', 'categories', 'botToken'));
})->name('web.interface.app.direct');

// Direct web interface access with bot token
Route::get('/web-interface/direct/{botToken}', function($botToken) {
    $restaurant = \App\Models\Restaurant::where('bot_token', $botToken)->first();
    
    if (!$restaurant || !$restaurant->is_active) {
        return response('Restaurant not found or not active', 404);
    }
    
    // Get categories and menu items
    $categories = \App\Models\Category::where('restaurant_id', $restaurant->id)
        ->with(['menuItems' => function($query) {
            $query->where('is_active', true);
        }])
        ->get();
    
    // Create a mock user for direct access
    $user = (object) [
        'id' => 0,
        'name' => 'Guest User',
        'phone' => null
    ];
    
    return view('web-interface.index', compact('restaurant', 'user', 'categories', 'botToken'));
})->name('web.interface.direct');

// Test endpoint for debugging
Route::get('/test-api', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working correctly',
        'timestamp' => now()
    ]);
});

// Test order placement endpoint
Route::post('/test-order', function(\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('Test order request', $request->all());
    
    $restaurant = \App\Models\Restaurant::first();
    if (!$restaurant) {
        return response()->json(['error' => 'No restaurant found'], 404);
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Test order endpoint working',
        'restaurant' => $restaurant->name,
        'request_data' => $request->all()
    ]);
});

// Test order creation endpoint
Route::post('/test-create-order', function(\Illuminate\Http\Request $request) {
    try {
        \Illuminate\Support\Facades\Log::info('Test order creation request', $request->all());
        
        $restaurant = \App\Models\Restaurant::first();
        if (!$restaurant) {
            return response()->json(['error' => 'No restaurant found'], 404);
        }
        
        // Simulate order creation
        $orderData = [
            'restaurant_id' => $restaurant->id,
            'items' => $request->get('items', []),
            'customer_name' => $request->get('customer_name', 'Test Customer'),
            'customer_phone' => $request->get('customer_phone', '123456789'),
            'delivery_address' => $request->get('delivery_address', 'Test Address'),
            'payment_method' => $request->get('payment_method', 'cash'),
            'total_amount' => 10000,
            'status' => 'new'
        ];
        
        \Illuminate\Support\Facades\Log::info('Test order data prepared', $orderData);
        
        return response()->json([
            'success' => true,
            'message' => 'Test order creation successful',
            'order_data' => $orderData,
            'restaurant' => $restaurant->name
        ]);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Test order creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => 'Test order creation failed: ' . $e->getMessage()
        ], 500);
    }
});

// Debug restaurants and bot tokens
Route::get('/debug-restaurants', function() {
    $restaurants = \App\Models\Restaurant::select('id', 'name', 'bot_token', 'is_active')
        ->get()
        ->map(function($restaurant) {
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'bot_token' => $restaurant->bot_token ? substr($restaurant->bot_token, 0, 20) . '...' : 'NULL',
                'bot_token_length' => $restaurant->bot_token ? strlen($restaurant->bot_token) : 0,
                'is_active' => $restaurant->is_active,
                'web_interface_url' => url("/web-interface/direct/{$restaurant->bot_token}")
            ];
        });
    
    return response()->json([
        'success' => true,
        'restaurants' => $restaurants,
        'total_restaurants' => $restaurants->count(),
        'active_restaurants' => $restaurants->where('is_active', true)->count(),
        'restaurants_with_bot_tokens' => $restaurants->where('bot_token', '!=', null)->count()
    ]);
});

// Test web interface with different bot tokens
Route::get('/test-web-interface', function (Request $request) {
    $restaurants = Restaurant::where('is_active', true)->get();
    
    $results = [];
    foreach ($restaurants as $restaurant) {
        if ($restaurant->bot_token) {
            $results[] = [
                'restaurant_name' => $restaurant->name,
                'bot_token' => $restaurant->bot_token,
                'web_interface_url' => url("/web-interface?bot_token={$restaurant->bot_token}"),
                'enhanced_web_interface_url' => url("/enhanced-web-interface?bot_token={$restaurant->bot_token}")
            ];
        }
    }
    
    return response()->json([
        'message' => 'Web interface test results',
        'restaurants' => $results
    ]);
})->name('test.web.interface');

// Debug endpoint for web interface
Route::get('/debug-web-interface', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'Web interface debug endpoint',
        'request_data' => [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'query_params' => $request->query(),
            'restaurants_count' => \App\Models\Restaurant::count(),
            'categories_count' => \App\Models\Category::count(),
            'menu_items_count' => \App\Models\MenuItem::count()
        ],
        'timestamp' => now()
    ]);
});

// Test order creation endpoint
Route::post('/test-order', function (Request $request) {
    try {
        $restaurant = \App\Models\Restaurant::first();
        if (!$restaurant) {
            return response()->json(['error' => 'No restaurant found'], 404);
        }
        
        $order = \App\Models\Order::create([
            'order_number' => 'TEST-' . time(),
            'restaurant_id' => $restaurant->id,
            'user_id' => null,
            'project_id' => null,
            'status' => 'new',
            'total_price' => 1000,
            'payment_type' => 'cash',
            'address' => 'Test address',
            'customer_name' => 'Test Customer',
            'customer_phone' => '123456789',
            'total_amount' => 1000,
            'delivery_address' => 'Test address',
            'payment_method' => 'cash',
            'items' => [] // Store as empty array directly
        ]);
        
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'message' => 'Test order created successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Test order creation failed: ' . $e->getMessage()
        ], 500);
    }
});

// Test order placement endpoint
Route::post('/test-order-placement', function (Request $request) {
    try {
        $restaurant = \App\Models\Restaurant::first();
        if (!$restaurant) {
            return response()->json(['error' => 'No restaurant found'], 404);
        }
        
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
        
        // Calculate total
        $total = 0;
        $orderItems = [];
        
        foreach ($request->items as $item) {
            $menuItem = \App\Models\MenuItem::find($item['id']);
            if (!$menuItem) {
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
        
        // Create order
        $order = \App\Models\Order::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => null,
            'project_id' => null,
            'telegram_chat_id' => $request->telegram_chat_id ? (string) $request->telegram_chat_id : null,
            'total_amount' => $total,
            'delivery_address' => $request->delivery_address,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'items' => $orderItems, // Store as array directly
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'order_number' => 'TEST-' . time(),
            'total_price' => $total,
            'payment_type' => $request->payment_method,
            'address' => $request->delivery_address
        ]);
        
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'message' => 'Test order placement successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Test order placement failed: ' . $e->getMessage()
        ], 500);
    }
});

// Git Webhook Route for Auto Deployment
Route::post('/webhook', function () {
    // Set error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Log file
    $logFile = storage_path('logs/webhook.log');

    // Function to log messages
    function logMessage($message) {
        global $logFile;
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    // Get the raw POST data
    $payload = file_get_contents('php://input');
    $headers = getallheaders();

    // Verify the request is from Git (optional but recommended)
    $signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, env('WEBHOOK_SECRET', 'YOUR_WEBHOOK_SECRET'));

    if (!hash_equals($expectedSignature, $signature)) {
        logMessage('Invalid signature');
        http_response_code(403);
        exit('Invalid signature');
    }

    // Parse the JSON payload
    $data = json_decode($payload, true);

    if (!$data) {
        logMessage('Invalid JSON payload');
        http_response_code(400);
        exit('Invalid JSON payload');
    }

    // Check if this is a push to the main branch
    $ref = $data['ref'] ?? '';
    $branch = str_replace('refs/heads/', '', $ref);

    if ($branch !== 'main' && $branch !== 'master') {
        logMessage("Ignoring push to branch: $branch");
        http_response_code(200);
        exit('Ignoring non-main branch');
    }

    // Log the deployment trigger
    logMessage("Deployment triggered for branch: $branch");

    // Execute the deployment script
    $deployScript = base_path('deploy.sh');
    $output = [];
    $returnCode = 0;

    if (file_exists($deployScript)) {
        exec("bash $deployScript 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            logMessage('Deployment completed successfully');
            http_response_code(200);
            echo 'Deployment completed successfully';
        } else {
            logMessage('Deployment failed: ' . implode("\n", $output));
            http_response_code(500);
            echo 'Deployment failed';
        }
    } else {
        logMessage('Deployment script not found: ' . $deployScript);
        http_response_code(500);
        echo 'Deployment script not found';
    }
})->name('webhook');

// Test route to check PHP upload limits
Route::get('/test-upload-limits', function() {
    return response()->json([
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
        'file_uploads' => ini_get('file_uploads'),
        'upload_tmp_dir' => ini_get('upload_tmp_dir'),
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'timestamp' => now()->toISOString(),
    ]);
})->name('test.upload.limits');

// Telegram Web App settings route
Route::get('/admin/restaurants/{restaurant}/web-app-settings', [App\Http\Controllers\Admin\RestaurantController::class, 'webAppSettings'])
    ->name('admin.restaurants.web-app-settings')
    ->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';

// Test enhanced web interface directly
Route::get('/test-enhanced-web-interface', function (Request $request) {
    $restaurant = Restaurant::where('is_active', true)->first();
    
    if (!$restaurant) {
        return response()->json(['error' => 'No active restaurant found'], 404);
    }
    
    $categories = Category::where('restaurant_id', $restaurant->id)
        ->with(['menuItems' => function($query) {
            $query->where('is_active', true);
        }])
        ->get();
    
    return view('web-interface.enhanced', compact('restaurant', 'categories'));
})->name('test.enhanced.web.interface');
