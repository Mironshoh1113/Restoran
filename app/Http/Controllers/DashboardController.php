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
        try {
            $user = Auth::user();
            
            // Get user's restaurants
			$restaurants = $user->ownedRestaurants()->with('subscriptions.plan')->get();
            $restaurantIds = $restaurants->pluck('id');
			
			// Subscription alerts
			$subscriptionAlerts = [];
			foreach ($restaurants as $restaurant) {
				$sub = $restaurant->activeSubscription();
				if (!$sub || !$sub->isActive()) {
					$subscriptionAlerts[] = "{$restaurant->name} uchun tarif faol emas yoki muddati tugagan";
				}
			}
            
            // Get statistics
            $stats = $this->getDashboardStats($user, $restaurantIds);
            
            // Get recent orders
            $recentOrders = $this->getRecentOrders($restaurantIds);
            
            // Get activity feed
            $activityFeed = $this->getActivityFeed($restaurantIds);
            
            // Get monthly trends
            $monthlyTrends = $this->getMonthlyTrends($restaurantIds);
            
			return view('dashboard', compact('stats', 'recentOrders', 'activityFeed', 'monthlyTrends', 'subscriptionAlerts'));
        } catch (\Exception $e) {
            // Return dashboard with empty data if there's an error
            $stats = [
                'restaurants' => ['count' => 0, 'change' => 0, 'change_type' => 'increase'],
                'orders' => ['count' => 0, 'change' => 0, 'change_type' => 'increase'],
                'couriers' => ['count' => 0, 'change' => 0, 'change_type' => 'increase'],
                'menu_items' => ['count' => 0, 'change' => 0, 'change_type' => 'increase']
            ];
            
            $recentOrders = collect();
            $activityFeed = collect();
            $monthlyTrends = collect();
			$subscriptionAlerts = [];
            
			return view('dashboard', compact('stats', 'recentOrders', 'activityFeed', 'monthlyTrends', 'subscriptionAlerts'));
        }
    }
    
    private function getDashboardStats($user, $restaurantIds)
    {
        try {
            $now = Carbon::now();
            $lastMonth = Carbon::now()->subMonth();
            
            // Get user's restaurants for this calculation
            $userRestaurants = $user->ownedRestaurants()->get();
            
                    // Current counts with error handling
        $currentRestaurants = $userRestaurants->count();
        $currentOrders = Order::whereIn('restaurant_id', $restaurantIds)->count();
        $currentCouriers = Courier::whereIn('restaurant_id', $restaurantIds)->count();
        $currentMenuItems = MenuItem::whereHas('category.project', function($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->count();
        
        // Last month counts for comparison with error handling
        $lastMonthOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])->count();
            
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
        } catch (\Exception $e) {
            // Return default stats if there's an error
            return [
                'restaurants' => ['count' => 0, 'change' => 0, 'change_type' => 'increase'],
                'orders' => ['count' => 0, 'change' => 0, 'change_type' => 'increase'],
                'couriers' => ['count' => 0, 'change' => 0, 'change_type' => 'increase'],
                'menu_items' => ['count' => 0, 'change' => 0, 'change_type' => 'increase']
            ];
        }
    }
    
    private function getRecentOrders($restaurantIds)
    {
        try {
            return Order::whereIn('restaurant_id', $restaurantIds)
                ->with(['restaurant', 'courier'])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect(); // Return empty collection if there's an error
        }
    }
    
    private function getActivityFeed($restaurantIds)
    {
        try {
            $activities = collect();
            
            // Recent orders
            $recentOrders = Order::whereIn('restaurant_id', $restaurantIds)
                ->with(['restaurant'])->latest()->limit(3)->get();
            
            foreach ($recentOrders as $order) {
                try {
                    $activities->push([
                        'type' => 'order',
                        'icon' => 'shopping-cart',
                        'icon_color' => 'green',
                        'title' => 'Yangi buyurtma qabul qilindi',
                        'subtitle' => "#{$order->order_number} - " . ($order->restaurant->name ?? 'Restoran'),
                        'time' => $order->created_at->diffForHumans(),
                        'timestamp' => $order->created_at
                    ]);
                } catch (\Exception $e) {
                    // Skip this order if there's an error
                    continue;
                }
            }
            
            // Recent courier assignments
            $recentCourierAssignments = Order::whereIn('restaurant_id', $restaurantIds)
                ->whereNotNull('courier_id')->with(['restaurant', 'courier'])->latest()->limit(2)->get();
            
            foreach ($recentCourierAssignments as $order) {
                try {
                    $activities->push([
                        'type' => 'courier',
                        'icon' => 'truck',
                        'icon_color' => 'blue',
                        'title' => 'Buyurtma kuryerga tayinlandi',
                        'subtitle' => "#{$order->order_number} - " . ($order->courier->name ?? 'Kuryer'),
                        'time' => $order->updated_at->diffForHumans(),
                        'timestamp' => $order->updated_at
                    ]);
                } catch (\Exception $e) {
                    // Skip this order if there's an error
                    continue;
                }
            }
            
            // Recent menu items
            $recentMenuItems = MenuItem::whereHas('category.project', function($query) use ($restaurantIds) {
                $query->whereIn('restaurant_id', $restaurantIds);
            })->with(['category.project.restaurant'])->latest()->limit(2)->get();
            
            foreach ($recentMenuItems as $menuItem) {
                try {
                    $activities->push([
                        'type' => 'menu_item',
                        'icon' => 'utensils',
                        'icon_color' => 'purple',
                        'title' => 'Yangi taom qo\'shildi',
                        'subtitle' => "{$menuItem->name} - " . ($menuItem->category->project->restaurant->name ?? 'Restoran'),
                        'time' => $menuItem->created_at->diffForHumans(),
                        'timestamp' => $menuItem->created_at
                    ]);
                } catch (\Exception $e) {
                    // Skip this menu item if there's an error
                    continue;
                }
            }
            
            // Sort by timestamp and take top 5
            return $activities->sortByDesc('timestamp')->take(5);
        } catch (\Exception $e) {
            return collect(); // Return empty collection if there's an error
        }
    }
    
    private function getMonthlyTrends($restaurantIds)
    {
        try {
            $months = collect();
            $now = Carbon::now();
            
            for ($i = 5; $i >= 0; $i--) {
                try {
                    $month = $now->copy()->subMonths($i);
                    $monthStart = $month->copy()->startOfMonth();
                    $monthEnd = $month->copy()->endOfMonth();
                    
                    $orderCount = Order::whereIn('restaurant_id', $restaurantIds)
                        ->whereBetween('created_at', [$monthStart, $monthEnd])->count();
                    
                    $totalRevenue = Order::whereIn('restaurant_id', $restaurantIds)
                        ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_price');
                    
                    $months->push([
                        'month' => $month->format('M Y'),
                        'orders' => $orderCount,
                        'revenue' => $totalRevenue,
                        'date' => $month
                    ]);
                } catch (\Exception $e) {
                    // Skip this month if there's an error
                    continue;
                }
            }
            
            return $months;
        } catch (\Exception $e) {
            return collect(); // Return empty collection if there's an error
        }
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