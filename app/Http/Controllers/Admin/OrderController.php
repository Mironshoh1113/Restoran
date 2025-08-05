<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $orders = Order::with(['restaurant', 'courier'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $restaurantIds = $user->ownedRestaurants()->pluck('id');
            $orders = Order::whereIn('restaurant_id', $restaurantIds)
                ->with(['restaurant', 'courier'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['restaurant', 'courier']);
        
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'status' => 'required|in:pending,preparing,on_way,delivered,cancelled'
        ]);

        $oldStatus = $order->status;
        
        $order->update([
            'status' => $request->status,
            'delivered_at' => $request->status === 'delivered' ? now() : null
        ]);

        // Send notification to customer via Telegram
        $this->sendOrderStatusNotification($order, $oldStatus);

        return redirect()->back()
            ->with('success', 'Buyurtma holati yangilandi.');
    }

    public function assignCourier(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'courier_id' => 'required|exists:couriers,id'
        ]);

        $order->update(['courier_id' => $request->courier_id]);

        return redirect()->back()
            ->with('success', 'Kuryer tayinlandi.');
    }

    protected function sendOrderStatusNotification(Order $order, $oldStatus)
    {
        if (!$order->telegram_chat_id) {
            Log::info('Order has no telegram_chat_id', ['order_id' => $order->id]);
            return;
        }

        if (!$order->restaurant) {
            Log::error('Order has no restaurant', ['order_id' => $order->id]);
            return;
        }

        if (!$order->restaurant->bot_token) {
            Log::error('Restaurant has no bot token', [
                'order_id' => $order->id,
                'restaurant_id' => $order->restaurant->id
            ]);
            return;
        }

        try {
            $telegramService = new TelegramService();
            $telegramService->setBotToken($order->restaurant->bot_token);

            $statusMessages = [
                'pending' => '⏳ Buyurtma qabul qilindi',
                'preparing' => '👨‍🍳 Buyurtma tayyorlanmoqda',
                'on_way' => '🚚 Buyurtma yo\'lda',
                'delivered' => '✅ Buyurtma yetkazildi',
                'cancelled' => '❌ Buyurtma bekor qilindi'
            ];

            $message = "📋 *Buyurtma #{$order->order_number}*\n\n";
            $message .= "🏪 Restoran: *{$order->restaurant->name}*\n";
            $message .= "👤 Mijoz: *{$order->customer_name}*\n";
            $message .= "📞 Telefon: *{$order->customer_phone}*\n";
            if ($order->delivery_address) {
                $message .= "📍 Manzil: *{$order->delivery_address}*\n";
            }
            $message .= "💰 Jami: *" . number_format($order->total_amount ?? $order->total_price ?? 0, 0, ',', ' ') . " so'm*\n\n";
            $message .= "🔄 *Holat o'zgartirildi:*\n";
            $message .= "`{$oldStatus}` → `{$order->status}`\n\n";
            $message .= "📝 *Yangilangan holat:* " . ($statusMessages[$order->status] ?? $order->status);

            $result = $telegramService->sendMessage($order->telegram_chat_id, $message);
            
            if ($result['ok']) {
                Log::info('Order status notification sent successfully', [
                    'order_id' => $order->id,
                    'chat_id' => $order->telegram_chat_id,
                    'status' => $order->status
                ]);
            } else {
                Log::error('Failed to send order status notification', [
                    'order_id' => $order->id,
                    'chat_id' => $order->telegram_chat_id,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Telegram notification error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'chat_id' => $order->telegram_chat_id,
                'exception' => $e
            ]);
        }
    }
} 