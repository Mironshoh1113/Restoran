<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\TelegramUser;
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
			$orders = Order::with(['restaurant', 'courier', 'orderItems.menuItem'])
				->orderBy('created_at', 'desc')
				->paginate(15);
		} else {
			$restaurantIds = $user->ownedRestaurants()->pluck('id');
			$orders = Order::whereIn('restaurant_id', $restaurantIds)
				->with(['restaurant', 'courier', 'orderItems.menuItem'])
				->orderBy('created_at', 'desc')
				->paginate(15);
		}
		
		return view('admin.orders.index', compact('orders'));
	}

	public function show(Order $order)
	{
		$this->authorize('view', $order);
		
		// Load relationships
		$order->load(['restaurant', 'courier', 'orderItems.menuItem.category']);
		
		// If order has items JSON (from web interface), decode it
		if ($order->items) {
			// Check if items is already an array or needs to be decoded
			if (is_array($order->items)) {
				$order->decoded_items = $order->items;
			} else {
				$order->decoded_items = json_decode($order->items, true);
			}
		}
		
		return view('admin.orders.show', compact('order'));
	}

	public function updateStatus(Request $request, Order $order)
	{
		$this->authorize('update', $order);
		
		$request->validate([
			'status' => 'required|in:new,preparing,on_way,delivered,cancelled'
		]);

		$oldStatus = $order->status;
		
		$order->update([
			'status' => $request->status,
			'delivered_at' => $request->status === 'delivered' ? now() : null
		]);

		// Send notification to customer via Telegram
		$this->sendOrderStatusNotification($order, $oldStatus);

		// If AJAX/JSON request, return JSON
		if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
			return response()->json([
				'success' => true,
				'order_id' => $order->id,
				'new_status' => $order->status,
				'delivered_at' => $order->delivered_at?->toISOString(),
			]);
		}

		return redirect()->back()->with('success', 'Buyurtma holati yangilandi.');
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

	public function updatePayment(Request $request, Order $order)
	{
		$this->authorize('update', $order);

		$request->validate([
			'payment_method' => 'required|string|in:cash,card,click,payme',
			'is_paid' => 'required|boolean',
		]);

		$order->update([
			'payment_method' => $request->payment_method,
			'is_paid' => (bool) $request->is_paid,
		]);

		if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
			return response()->json([
				'success' => true,
				'order_id' => $order->id,
				'payment_method' => $order->payment_method,
				'is_paid' => (bool) $order->is_paid,
			]);
		}

		return redirect()->back()->with('success', "To'lov ma'lumotlari yangilandi.");
	}

	protected function sendOrderStatusNotification(Order $order, $oldStatus)
	{
		Log::info('Starting order status notification', [
			'order_id' => $order->id,
			'telegram_chat_id' => $order->telegram_chat_id,
			'old_status' => $oldStatus,
			'new_status' => $order->status,
			'customer_name' => $order->customer_name,
			'restaurant_id' => $order->restaurant_id
		]);

		// Ensure restaurant relation
		if (!$order->relationLoaded('restaurant')) {
			$order->load('restaurant');
		}
		if (!$order->restaurant) {
			Log::error('Order has no restaurant', ['order_id' => $order->id]);
			return;
		}

		// Backfill telegram_chat_id from TelegramUser by phone if missing
		if (empty($order->telegram_chat_id) && !empty($order->customer_phone)) {
			try {
				$digits = preg_replace('/\D+/', '', (string) $order->customer_phone);
				$like = substr($digits, -9); // last 9 digits
				$candidate = TelegramUser::where('restaurant_id', $order->restaurant_id)
					->where(function($q) use ($digits, $like) {
						$q->where('phone_number', 'like', "%$digits%");
						if ($like) { $q->orWhere('phone_number', 'like', "%$like%"); }
					})
					->orderByDesc('last_activity')
					->first();
				if ($candidate) {
					$order->telegram_chat_id = (string) $candidate->telegram_id;
					$order->save();
					Log::info('Backfilled telegram_chat_id from TelegramUser', [
						'order_id' => $order->id,
						'chat_id' => $order->telegram_chat_id
					]);
				}
			} catch (\Exception $e) {
				Log::warning('Failed to backfill telegram_chat_id', [
					'order_id' => $order->id,
					'error' => $e->getMessage()
				]);
			}
		}

		if ($order->telegram_chat_id) {
			$this->sendTelegramNotification($order, $oldStatus);
		} else {
			Log::info('Order has no telegram_chat_id - cannot send notification', ['order_id' => $order->id]);
		}
	}

	protected function sendTelegramNotification($order, $oldStatus)
	{
		if (!$order->restaurant->bot_token) {
			Log::error('Restaurant has no bot token', [
				'order_id' => $order->id,
				'restaurant_id' => $order->restaurant->id,
				'restaurant_name' => $order->restaurant->name
			]);
			return;
		}

		try {
			Log::info('Creating TelegramService with bot token', [
				'restaurant_id' => $order->restaurant->id,
				'bot_token' => $order->restaurant->bot_token,
				'chat_id' => $order->telegram_chat_id
			]);

			$telegramService = new TelegramService($order->restaurant->bot_token);

			$statusMessages = [
				'new' => '🆕 Yangi buyurtma qabul qilindi',
				'preparing' => '👨‍🍳 Buyurtma tayyorlanmoqda',
				'on_way' => '🚚 Buyurtma yolda',
				'delivered' => '✅ Buyurtma yetkazildi',
				'cancelled' => '❌ Buyurtma bekor qilindi'
			];

			$message = "📋 <b>Buyurtma #{$order->order_number}</b>\n\n";
			$message .= "👤 Mijoz: <b>{$order->customer_name}</b>\n";
			$message .= "📞 Telefon: <b>{$order->customer_phone}</b>\n";
			if ($order->delivery_address) {
				$message .= "📍 Manzil: <b>{$order->delivery_address}</b>\n";
			}
			$message .= "💰 Jami: <b>" . number_format($order->total_amount ?? $order->total_price ?? 0, 0, ',', ' ') . " so'm</b>\n\n";
			$message .= "🔄 <b>Holat o'zgartirildi:</b>\n";
			$message .= "<code>{$oldStatus}</code> → <code>{$order->status}</code>\n\n";
			$message .= "📝 <b>Yangilangan holat:</b> " . ($statusMessages[$order->status] ?? $order->status);

			Log::info('Sending Telegram message', [
				'chat_id' => $order->telegram_chat_id,
				'message_length' => strlen($message),
				'bot_token' => $order->restaurant->bot_token
			]);

			// Prefer HTML formatting (we used <b>, <code>)
			$result = $telegramService->sendMessage($order->telegram_chat_id, $message, null, 'HTML');
			if (!$result['ok']) {
				// Fallback to plain text
				$plain = trim(strip_tags($message));
				$result = $telegramService->sendMessage($order->telegram_chat_id, $plain, null, null);
			}
			
			if ($result['ok']) {
				Log::info('Order status notification sent successfully via Telegram', [
					'order_id' => $order->id,
					'chat_id' => $order->telegram_chat_id,
					'status' => $order->status,
					'message_id' => $result['result']['message_id'] ?? null
				]);
			} else {
				Log::error('Failed to send order status notification via Telegram', [
					'order_id' => $order->id,
					'chat_id' => $order->telegram_chat_id,
					'error' => $result['error'] ?? ($result['description'] ?? 'Unknown error'),
					'result' => $result
				]);
			}
		} catch (\Exception $e) {
			Log::error('Telegram notification exception', [
				'order_id' => $order->id,
				'chat_id' => $order->telegram_chat_id,
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
		}
	}
} 