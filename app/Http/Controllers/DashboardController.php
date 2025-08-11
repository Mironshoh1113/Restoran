<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Courier;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Project;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user's restaurants
        $restaurants = $user->ownedRestaurants()->get();
        $restaurantIds = $restaurants->pluck('id');
        
        // Get statistics
        $stats = $this->getDashboardStats($user, $restaurantIds);
        
        // Get recent orders
        $recentOrders = $this->getRecentOrders($restaurantIds);
        
        // Get activity feed
        $activityFeed = $this->getActivityFeed($restaurantIds);
        
        // Get monthly trends
        $monthlyTrends = $this->getMonthlyTrends($restaurantIds);
        
        return view('dashboard', compact('stats', 'recentOrders', 'activityFeed', 'monthlyTrends'));
    }
    
    private function getDashboardStats($user, $restaurantIds)
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Get user's restaurants for this calculation
        $userRestaurants = $user->ownedRestaurants()->get();
        
        // Current counts
        $currentRestaurants = $userRestaurants->count();
        $currentOrders = Order::whereHas('project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->count();
        $currentCouriers = Courier::whereIn('restaurant_id', $restaurantIds)->count();
        $currentMenuItems = MenuItem::whereHas('category.project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->count();
        
        // Last month counts for comparison
        $lastMonthOrders = Order::whereHas('project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])->count();
        
        $lastMonthRestaurants = $userRestaurants->where('created_at', '<=', $lastMonth->endOfMonth())->count();
        $lastMonthCouriers = Courier::whereIn('restaurant_id', $restaurantIds)
            ->where('created_at', '<=', $lastMonth->endOfMonth())->count();
        $lastMonthMenuItems = MenuItem::whereHas('category.project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->where('created_at', '<=', $lastMonth->endOfMonth())->count();
        
        // Calculate percentage changes
        $orderChange = $lastMonthOrders > 0 ? (($currentOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;
        $restaurantChange = $lastMonthRestaurants > 0 ? (($currentRestaurants - $lastMonthRestaurants) / $lastMonthRestaurants) * 100 : 0;
        $courierChange = $lastMonthCouriers > 0 ? (($currentCouriers - $lastMonthCouriers) / $lastMonthCouriers) * 100 : 0;
        $menuItemChange = $lastMonthMenuItems > 0 ? (($currentMenuItems - $lastMonthMenuItems) / $lastMonthMenuItems) * 100 : 0;
        
        return [
            'restaurants' => [
                'count' => $currentRestaurants,
                'change' => round($restaurantChange, 1),
                'change_type' => $restaurantChange >= 0 ? 'increase' : 'decrease'
            ],
            'orders' => [
                'count' => $currentOrders,
                'change' => round($orderChange, 1),
                'change_type' => $orderChange >= 0 ? 'increase' : 'decrease'
            ],
            'couriers' => [
                'count' => $currentCouriers,
                'change' => round($courierChange, 1),
                'change_type' => $courierChange >= 0 ? 'increase' : 'decrease'
            ],
            'menu_items' => [
                'count' => $currentMenuItems,
                'change' => round($menuItemChange, 1),
                'change_type' => $menuItemChange >= 0 ? 'increase' : 'decrease'
            ]
        ];
    }
    
    private function getRecentOrders($restaurantIds)
    {
        return Order::whereHas('project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })
        ->with(['project.restaurant', 'courier'])
        ->latest()
        ->limit(5)
        ->get();
    }
    
    private function getActivityFeed($restaurantIds)
    {
        $activities = collect();
        
        // Recent orders
        $recentOrders = Order::whereHas('project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->with(['project.restaurant'])->latest()->limit(3)->get();
        
        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'icon' => 'shopping-cart',
                'icon_color' => 'green',
                'title' => 'Yangi buyurtma qabul qilindi',
                'subtitle' => "#{$order->order_number} - {$order->project->restaurant->name}",
                'time' => $order->created_at->diffForHumans(),
                'timestamp' => $order->created_at
            ]);
        }
        
        // Recent courier assignments
        $recentCourierAssignments = Order::whereHas('project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->whereNotNull('courier_id')->with(['project.restaurant', 'courier'])->latest()->limit(2)->get();
        
        foreach ($recentCourierAssignments as $order) {
            $activities->push([
                'type' => 'courier',
                'icon' => 'truck',
                'icon_color' => 'blue',
                'title' => 'Buyurtma kuryerga tayinlandi',
                'subtitle' => "#{$order->order_number} - {$order->courier->name}",
                'time' => $order->updated_at->diffForHumans(),
                'timestamp' => $order->updated_at
            ]);
        }
        
        // Recent menu items
        $recentMenuItems = MenuItem::whereHas('category.project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->with(['category.project.restaurant'])->latest()->limit(2)->get();
        
        foreach ($recentMenuItems as $menuItem) {
            $activities->push([
                'type' => 'menu_item',
                'icon' => 'utensils',
                'icon_color' => 'purple',
                'title' => 'Yangi taom qo\'shildi',
                'subtitle' => "{$menuItem->name} - {$menuItem->category->project->restaurant->name}",
                'time' => $menuItem->created_at->diffForHumans(),
                'timestamp' => $menuItem->created_at
            ]);
        }
        
        // Sort by timestamp and take top 5
        return $activities->sortByDesc('timestamp')->take(5);
    }
    
    private function getMonthlyTrends($restaurantIds)
    {
        $months = collect();
        $now = Carbon::now();
        
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $orderCount = Order::whereHas('project', function($query) use ($restaurantIds) {
                $query->whereIn('restaurant_id', $restaurantIds);
            })->whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            $totalRevenue = Order::whereHas('project', function($query) use ($restaurantIds) {
                $query->whereIn('restaurant_id', $restaurantIds);
            })->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_price');
            
            $months->push([
                'month' => $month->format('M Y'),
                'orders' => $orderCount,
                'revenue' => $totalRevenue,
                'date' => $month
            ]);
        }
        
        return $months;
    }
    
    public function getStats()
    {
        $user = Auth::user();
        $restaurants = $user->ownedRestaurants()->get();
        $restaurantIds = $restaurants->pluck('id');
        
        $stats = $this->getDashboardStats($user, $restaurantIds);
        $monthlyTrends = $this->getMonthlyTrends($restaurantIds);
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'trends' => $monthlyTrends
        ]);
    }
} 