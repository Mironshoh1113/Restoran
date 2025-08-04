<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $orders = Order::with(['project.restaurant', 'courier'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $restaurantIds = $user->ownedRestaurants()->pluck('id');
            $orders = Order::whereHas('project', function($query) use ($restaurantIds) {
                $query->whereIn('restaurant_id', $restaurantIds);
            })
            ->with(['project.restaurant', 'courier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        }
        
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['orderItems.menuItem.category', 'project.restaurant', 'courier']);
        
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'status' => 'required|in:new,preparing,on_way,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status,
            'delivered_at' => $request->status === 'delivered' ? now() : null
        ]);

        // Send notification to customer via Telegram
        $this->sendOrderStatusNotification($order);

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

    protected function sendOrderStatusNotification(Order $order)
    {
        // TODO: Implement Telegram notification
        // This would send a message to the customer about their order status
    }
} 