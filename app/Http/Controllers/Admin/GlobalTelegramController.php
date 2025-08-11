<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalTelegramUser;
use App\Models\Restaurant;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GlobalTelegramController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show global telegram users across all restaurants
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Restaurant::class);
        
        $query = GlobalTelegramUser::with(['restaurantUsers.restaurant'])
            ->withCount(['restaurantUsers', 'allMessages']);

        // Search by username, first_name, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by activity
        if ($request->filled('activity')) {
            switch ($request->activity) {
                case 'active':
                    $query->where('last_activity', '>=', now()->subDays(7));
                    break;
                case 'inactive':
                    $query->where('last_activity', '<', now()->subDays(30));
                    break;
            }
        }

        $users = $query->orderBy('last_activity', 'desc')
                      ->paginate(20);

        return view('admin.global-telegram.index', compact('users'));
    }

    /**
     * Show specific global telegram user details
     */
    public function show(GlobalTelegramUser $globalUser)
    {
        $this->authorize('viewAny', Restaurant::class);
        
        // Get user's activity across all restaurants
        $restaurantActivity = $globalUser->getActivityAcrossRestaurants();
        
        // Get recent messages across all restaurants
        $recentMessages = TelegramMessage::whereIn('telegram_user_id', function($query) use ($globalUser) {
            $query->select('id')
                  ->from('telegram_users')
                  ->where('telegram_id', $globalUser->telegram_id);
        })
        ->with(['telegramUser.restaurant'])
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

        return view('admin.global-telegram.show', compact('globalUser', 'restaurantActivity', 'recentMessages'));
    }

    /**
     * Send message to user across all restaurants
     */
    public function sendMessageToAllRestaurants(Request $request, GlobalTelegramUser $globalUser)
    {
        $this->authorize('viewAny', Restaurant::class);
        
        $request->validate([
            'message' => 'required|string|max:4096',
            'restaurant_ids' => 'nullable|array',
            'restaurant_ids.*' => 'exists:restaurants,id'
        ]);

        $restaurantIds = $request->restaurant_ids ?? [];
        $successCount = 0;
        $errors = [];

        // Get all restaurants where this user has activity
        $userRestaurants = $globalUser->restaurantUsers()
            ->with('restaurant')
            ->get();

        foreach ($userRestaurants as $userRestaurant) {
            // Skip if specific restaurants are selected and this one isn't
            if (!empty($restaurantIds) && !in_array($userRestaurant->restaurant_id, $restaurantIds)) {
                continue;
            }

            try {
                $restaurant = $userRestaurant->restaurant;
                
                if (!$restaurant->bot_token) {
                    $errors[] = "Bot token o'rnatilmagan: {$restaurant->name}";
                    continue;
                }

                // Create TelegramService with restaurant's bot token
                $telegramService = new \App\Services\TelegramService($restaurant->bot_token);
                
                $result = $telegramService->sendMessage($globalUser->telegram_id, $request->message);
                
                if ($result['ok']) {
                    // Save outgoing message
                    TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $userRestaurant->id,
                        'message_id' => $result['result']['message_id'] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $request->message,
                        'message_data' => $result['result'] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                    
                    $successCount++;
                } else {
                    $errors[] = "Xabar yuborishda xatolik ({$restaurant->name}): " . ($result['description'] ?? 'Unknown error');
                }

            } catch (\Exception $e) {
                $errors[] = "Xatolik ({$restaurant->name}): " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Xabar {$successCount} ta restoranga yuborildi",
            'success_count' => $successCount,
            'errors' => $errors
        ]);
    }

    /**
     * Get user statistics across all restaurants
     */
    public function getUserStats(GlobalTelegramUser $globalUser)
    {
        $this->authorize('viewAny', Restaurant::class);
        
        $stats = [
            'total_restaurants' => $globalUser->restaurantUsers()->count(),
            'total_messages' => $globalUser->allMessages()->count(),
            'incoming_messages' => $globalUser->allMessages()->where('direction', 'incoming')->count(),
            'outgoing_messages' => $globalUser->allMessages()->where('direction', 'outgoing')->count(),
            'last_activity' => $globalUser->last_activity,
            'restaurant_activity' => $globalUser->getActivityAcrossRestaurants()
        ];

        return response()->json($stats);
    }

    /**
     * Get global statistics
     */
    public function getGlobalStats()
    {
        $this->authorize('viewAny', Restaurant::class);
        
        $stats = [
            'total_global_users' => GlobalTelegramUser::count(),
            'active_users' => GlobalTelegramUser::where('last_activity', '>=', now()->subDays(7))->count(),
            'total_restaurant_users' => TelegramUser::count(),
            'total_messages' => TelegramMessage::count(),
            'restaurants_with_bots' => Restaurant::whereNotNull('bot_token')->count(),
            'users_by_language' => GlobalTelegramUser::selectRaw('language_code, COUNT(*) as count')
                ->groupBy('language_code')
                ->get()
        ];

        return response()->json($stats);
    }
} 