<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\TelegramController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Deploy webhook
Route::post('/deploy', function () {
    try {
        // Get the current directory
        $currentDir = base_path();
        
        // Git pull
        $gitPull = shell_exec("cd {$currentDir} && git pull origin main 2>&1");
        
        // Composer install
        $composerInstall = shell_exec("cd {$currentDir} && composer install --no-dev --optimize-autoloader 2>&1");
        
        // Cache config
        $configCache = shell_exec("cd {$currentDir} && php artisan config:cache 2>&1");
        
        return response()->json([
            'success' => true,
            'message' => 'Deployment completed successfully',
            'git_pull' => $gitPull,
            'composer_install' => $composerInstall,
            'config_cache' => $configCache
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}); 

// Debug route for testing orders API
Route::post('/test-orders', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Test API is working',
        'received_data' => $request->all(),
        'timestamp' => now()->toISOString()
    ]);
});

// Debug route for full order testing
Route::post('/debug-orders', function (Request $request) {
    try {
        \Log::info('Debug order request received', $request->all());
        
        // Check if restaurant exists
        $restaurant = \App\Models\Restaurant::find($request->restaurant_id);
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'error' => 'Restaurant not found',
                'restaurant_id' => $request->restaurant_id
            ], 404);
        }
        
        // Check if restaurant is active
        if (!$restaurant->is_active) {
            return response()->json([
                'success' => false,
                'error' => 'Restaurant is not active',
                'restaurant' => $restaurant->only(['id', 'name', 'is_active'])
            ], 400);
        }
        
        // Check bot token
        if ($restaurant->bot_token !== $request->bot_token) {
            return response()->json([
                'success' => false,
                'error' => 'Bot token mismatch',
                'expected' => substr($restaurant->bot_token, 0, 10) . '...',
                'received' => substr($request->bot_token, 0, 10) . '...'
            ], 400);
        }
        
        // Check menu items
        $itemIds = collect($request->items)->pluck('menu_item_id');
        $menuItems = \App\Models\MenuItem::whereIn('id', $itemIds)->get();
        
        if ($menuItems->count() !== $itemIds->count()) {
            return response()->json([
                'success' => false,
                'error' => 'Some menu items not found',
                'requested_ids' => $itemIds,
                'found_ids' => $menuItems->pluck('id')
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'All checks passed - ready to create order',
            'restaurant' => $restaurant->only(['id', 'name', 'is_active']),
            'menu_items' => $menuItems->pluck('name', 'id'),
            'total_items' => count($request->items),
            'total_amount' => $request->total_amount
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Debug order error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Telegram Web App API routes
Route::post('/orders', function (Request $request) {
    try {
        $data = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'telegram_chat_id' => 'nullable|integer',
            'bot_token' => 'required|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:1000',
            'customer_notes' => 'nullable|string|max:1000'
        ]);

        // Verify bot token
        $restaurant = \App\Models\Restaurant::where('id', $data['restaurant_id'])
            ->where('bot_token', $data['bot_token'])
            ->where('is_active', true)
            ->first();

        if (!$restaurant) {
            return response()->json(['success' => false, 'error' => 'Invalid restaurant or bot token'], 400);
        }

        // Create order
        $order = \App\Models\Order::create([
            'restaurant_id' => $data['restaurant_id'],
            'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
            'customer_name' => $data['customer_name'] ?? 'Telegram User',
            'customer_phone' => $data['customer_phone'] ?? 'N/A',
            'delivery_address' => $data['customer_address'] ?? 'Telegram Order',
            'notes' => $data['customer_notes'] ?? '',
            'total_price' => $data['total_amount'],
            'status' => 'new',
            'payment_method' => 'telegram',
            'telegram_chat_id' => $data['telegram_chat_id'],
            'bot_token' => $data['bot_token']
        ]);

        // Create order items
        foreach ($data['items'] as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['quantity'] * $item['price']
            ]);
        }

        // Log the order creation
        \Log::info('Order created from Telegram Web App', [
            'order_id' => $order->id,
            'restaurant_id' => $data['restaurant_id'],
            'telegram_chat_id' => $data['telegram_chat_id'],
            'total_amount' => $data['total_amount'],
            'customer_name' => $data['customer_name'] ?? 'N/A',
            'customer_phone' => $data['customer_phone'] ?? 'N/A'
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'message' => 'Order created successfully'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error creating order from Telegram Web App', [
            'errors' => $e->errors(),
            'data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Validation failed',
            'details' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Error creating order from Telegram Web App', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Failed to create order: ' . $e->getMessage()
        ], 500);
    }
}); 

// Telegram webhook route - accept both GET and POST
Route::match(['get', 'post'], '/telegram-webhook/{token}', [TelegramController::class, 'webhook']);

// Debug webhook route
Route::post('/debug-webhook/{token}', function (Request $request, $token) {
    try {
        \Log::info('Debug webhook called', [
            'token' => $token,
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);
        
        // Find restaurant by bot token
        $restaurant = \App\Models\Restaurant::where('bot_token', $token)->first();
        
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'error' => 'Restaurant not found for token',
                'token' => substr($token, 0, 10) . '...'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Webhook debug successful',
            'restaurant' => $restaurant->only(['id', 'name', 'is_active']),
            'token_match' => true,
            'received_data' => $request->all(),
            'timestamp' => now()->toISOString()
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Debug webhook error', [
            'token' => $token,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Test webhook setup
Route::get('/test-webhook-setup', function () {
    $restaurants = \App\Models\Restaurant::where('is_active', true)
        ->whereNotNull('bot_token')
        ->get(['id', 'name', 'bot_token']);
    
    $webhookUrls = [];
    foreach ($restaurants as $restaurant) {
        $webhookUrls[] = [
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'webhook_url' => url("/api/telegram-webhook/{$restaurant->bot_token}"),
            'debug_webhook_url' => url("/api/debug-webhook/{$restaurant->bot_token}"),
            'bot_token' => substr($restaurant->bot_token, 0, 10) . '...'
        ];
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Webhook URLs for all active restaurants',
        'webhooks' => $webhookUrls,
        'total_restaurants' => count($webhookUrls)
    ]);
}); 