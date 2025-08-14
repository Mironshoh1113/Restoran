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



// Telegram webhook - API route (both GET and POST)
Route::match(['get', 'post'], '/telegram-webhook/{token}', [TelegramController::class, 'webhook'])->name('api.telegram.webhook');




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



 