<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use App\Models\TelegramUser;
use App\Models\TelegramMessage;

class BotController extends Controller
{
    use AuthorizesRequests;

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Show bot settings page
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $restaurants = Restaurant::with('owner')->get();
        } else {
            $restaurants = Restaurant::where('owner_user_id', $user->id)->get();
        }
        
        return view('admin.bots.index', compact('restaurants'));
    }

    /**
     * Show bot settings for specific restaurant
     */
    public function show(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);
        
        // Get bot info if token exists
        $botInfo = null;
        $webhookInfo = null;
        
        if ($restaurant->bot_token) {
            try {
                $telegramService = new TelegramService($restaurant->bot_token);
                $botInfo = $telegramService->getMe();
                $webhookInfo = $telegramService->getWebhookInfo();
            } catch (\Exception $e) {
                // Bot token might be invalid
            }
        }
        
        return view('admin.bots.show', compact('restaurant', 'botInfo', 'webhookInfo'));
    }

    /**
     * Update bot settings
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'bot_name' => 'nullable|string|max:255',
            'bot_username' => 'nullable|string|max:255',
            'bot_token' => 'nullable|string',
            'bot_description' => 'nullable|string',
            'bot_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'webhook_url' => 'nullable|url'
        ]);

        $oldToken = $restaurant->bot_token;
        
        $data = [
            'bot_name' => $request->bot_name,
            'bot_username' => $request->bot_username,
            'bot_token' => $request->bot_token,
            'bot_description' => $request->bot_description,
        ];

        // Handle bot image upload
        if ($request->hasFile('bot_image')) {
            // Delete old image
            if ($restaurant->bot_image) {
                Storage::disk('public')->delete($restaurant->bot_image);
            }
            
            $imagePath = $request->file('bot_image')->store('bot-images', 'public');
            $data['bot_image'] = $imagePath;
        }

        $restaurant->update($data);

        // Set webhook if token and URL provided
        if ($request->bot_token && $request->webhook_url) {
            try {
                $telegramService = new TelegramService($request->bot_token);
                
                // Set webhook
                $webhookResult = $telegramService->setWebhook($request->webhook_url);
                
                // Set bot commands
                $commands = config('telegram.commands');
                $telegramService->setMyCommands($commands);
                
                if ($webhookResult['ok']) {
                    return redirect()->back()->with('success', 'Bot sozlamalari yangilandi va webhook o\'rnatildi.');
                } else {
                    return redirect()->back()->with('error', 'Webhook o\'rnatishda xatolik: ' . ($webhookResult['description'] ?? 'Nomalum xatolik'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Bot sozlamalarida xatolik: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Bot sozlamalari yangilandi.');
    }

    /**
     * Update bot name via Telegram API
     */
    public function updateBotName(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'bot_name' => 'required|string|max:255'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $result = $telegramService->setMyName($request->bot_name);
            
            if ($result['ok']) {
                $restaurant->update(['bot_name' => $request->bot_name]);
                return response()->json(['success' => true, 'message' => 'Bot nomi muvaffaqiyatli yangilandi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Bot nomini yangilashda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Update bot description via Telegram API
     */
    public function updateBotDescription(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'bot_description' => 'required|string|max:512'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $result = $telegramService->setMyDescription($request->bot_description);
            
            if ($result['ok']) {
                $restaurant->update(['bot_description' => $request->bot_description]);
                return response()->json(['success' => true, 'message' => 'Bot tavsifi muvaffaqiyatli yangilandi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Bot tavsifini yangilashda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload bot profile photo
     */
    public function updateBotPhoto(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'bot_photo' => 'required|image|mimes:jpeg,png,jpg|max:1024'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            
            // Upload to Telegram
            $result = $telegramService->setProfilePhoto($request->file('bot_photo'));
            
            if ($result['ok']) {
                // Save locally
                $imagePath = $request->file('bot_photo')->store('bot-images', 'public');
                $restaurant->update(['bot_image' => $imagePath]);
                
                return response()->json(['success' => true, 'message' => 'Bot rasmi muvaffaqiyatli yangilandi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Bot rasmini yangilashda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Get bot commands
     */
    public function getBotCommands(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);
        
        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $result = $telegramService->getMyCommands();
            
            if ($result['ok']) {
                return response()->json(['success' => true, 'commands' => $result['result']]);
            } else {
                return response()->json(['success' => false, 'message' => 'Buyruqlarni olishda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Set bot commands
     */
    public function setBotCommands(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'commands' => 'required|array',
            'commands.*.command' => 'required|string',
            'commands.*.description' => 'required|string'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $result = $telegramService->setMyCommands($request->commands);
            
            if ($result['ok']) {
                return response()->json(['success' => true, 'message' => 'Bot buyruqlari muvaffaqiyatli yangilandi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Bot buyruqlarini yangilashda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Set webhook for bot
     */
    public function setWebhook(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            // Create TelegramService with restaurant's bot token
            $telegramService = new TelegramService($restaurant->bot_token);
            
            // Test bot connection first
            $botInfo = $telegramService->getMe();
            if (!$botInfo['ok']) {
                return response()->json(['success' => false, 'message' => 'Bot token noto\'g\'ri: ' . ($botInfo['description'] ?? 'Unknown error')]);
            }
            
            // Set webhook URL for this specific bot
            $webhookUrl = url('/telegram-webhook/' . $restaurant->bot_token);
            $result = $telegramService->setWebhook($webhookUrl);
            
            if ($result['ok']) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Webhook muvaffaqiyatli o\'rnatildi',
                    'webhook_url' => $webhookUrl,
                    'bot_info' => $botInfo['result']
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Webhook o\'rnatishda xatolik: ' . ($result['description'] ?? 'Unknown error')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete webhook for bot
     */
    public function deleteWebhook(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            // Create TelegramService with restaurant's bot token
            $telegramService = new TelegramService($restaurant->bot_token);
            
            $result = $telegramService->deleteWebhook();
            
            if ($result['ok']) {
                return response()->json(['success' => true, 'message' => 'Webhook muvaffaqiyatli o\'chirildi']);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Webhook o\'chirishda xatolik: ' . ($result['description'] ?? 'Unknown error')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Test bot connection
     */
    public function test(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            // Create TelegramService with restaurant's bot token
            $telegramService = new TelegramService($restaurant->bot_token);
            
            // Test bot connection
            $botInfo = $telegramService->getMe();
            if (!$botInfo['ok']) {
                return response()->json(['success' => false, 'message' => 'Bot token noto\'g\'ri: ' . ($botInfo['description'] ?? 'Unknown error')]);
            }
            
            // Get webhook info
            $webhookInfo = $telegramService->getWebhookInfo();
            
            return response()->json([
                'success' => true,
                'message' => 'Bot muvaffaqiyatli ulangan',
                'bot_info' => $botInfo['result'],
                'webhook_info' => $webhookInfo['result'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Send test message
     */
    public function sendTestMessage(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'chat_id' => 'required|numeric',
            'message' => 'required|string'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $result = $telegramService->sendMessage($request->chat_id, $request->message);
            
            if ($result['ok']) {
                return response()->json(['success' => true, 'message' => 'Xabar muvaffaqiyatli yuborildi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Xabar yuborishda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Show telegram users for restaurant
     */
    public function users(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);
        
        // Get users from this restaurant AND users who have messaged this restaurant's bot
        $users = \App\Models\TelegramUser::where(function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id)
                  ->orWhereExists(function($subQuery) use ($restaurant) {
                      $subQuery->select(\Illuminate\Support\Facades\DB::raw(1))
                               ->from('telegram_messages')
                               ->whereColumn('telegram_messages.telegram_user_id', 'telegram_users.id')
                               ->where('telegram_messages.restaurant_id', $restaurant->id);
                  });
        })
        ->with(['restaurant', 'messages' => function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        }])
        ->orderBy('last_activity', 'desc')
        ->paginate(20);
        
        return view('admin.bots.users', compact('restaurant', 'users'));
    }

    /**
     * Send message to selected users
     */
    public function sendMessageToUsers(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
            'message' => 'required|string|max:4096'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            // Create TelegramService with restaurant's bot token
            $telegramService = new TelegramService($restaurant->bot_token);
            
            // Test bot connection first
            $botInfo = $telegramService->getMe();
            if (!$botInfo['ok']) {
                return response()->json(['success' => false, 'message' => 'Bot token noto\'g\'ri: ' . ($botInfo['description'] ?? 'Unknown error')]);
            }
            
            // Get selected users for this specific restaurant only
            $users = TelegramUser::where('restaurant_id', $restaurant->id)
                ->whereIn('telegram_id', $request->user_ids) // Use telegram_id instead of id
                ->get();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($users as $user) {
                $result = $telegramService->sendMessage($user->telegram_id, $request->message);
                
                if ($result['ok']) {
                    $successCount++;
                    
                    // Save outgoing message to database
                    TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $user->id,
                        'message_id' => $result['result']['message_id'] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $request->message,
                        'message_data' => $result['result'] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                } else {
                    $errorCount++;
                    $errors[] = "User {$user->telegram_id}: " . ($result['description'] ?? 'Unknown error');
                }
            }
            
            $message = "Xabar {$successCount} ta foydalanuvchiga yuborildi.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} ta xatolik yuz berdi.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'result' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'total_users' => $users->count(),
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Send message to all users
     */
    public function sendMessageToAllUsers(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'message' => 'required|string|max:4096'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            // Create TelegramService with restaurant's bot token
            $telegramService = new TelegramService($restaurant->bot_token);
            
            // Test bot connection first
            $botInfo = $telegramService->getMe();
            if (!$botInfo['ok']) {
                return response()->json(['success' => false, 'message' => 'Bot token noto\'g\'ri: ' . ($botInfo['description'] ?? 'Unknown error')]);
            }
            
            // Get all telegram users for this specific restaurant only
            $users = TelegramUser::where('restaurant_id', $restaurant->id)
                ->where('is_active', true)
                ->get();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($users as $user) {
                $result = $telegramService->sendMessage($user->telegram_id, $request->message);
                
                if ($result['ok']) {
                    $successCount++;
                    
                    // Save outgoing message to database
                    TelegramMessage::create([
                        'restaurant_id' => $restaurant->id,
                        'telegram_user_id' => $user->id,
                        'message_id' => $result['result']['message_id'] ?? null,
                        'direction' => 'outgoing',
                        'message_text' => $request->message,
                        'message_data' => $result['result'] ?? null,
                        'message_type' => 'text',
                        'is_read' => false,
                    ]);
                } else {
                    $errorCount++;
                    $errors[] = "User {$user->telegram_id}: " . ($result['description'] ?? 'Unknown error');
                }
            }
            
            $message = "Xabar {$successCount} ta foydalanuvchiga yuborildi.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} ta xatolik yuz berdi.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'result' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'total_users' => $users->count(),
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Get users statistics for restaurant
     */
    public function getUsersStats(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);
        
        try {
            $totalUsers = TelegramUser::where('restaurant_id', $restaurant->id)->count();
            $activeUsers = TelegramUser::where('restaurant_id', $restaurant->id)
                ->where('is_active', true)
                ->count();
            $recentUsers = TelegramUser::where('restaurant_id', $restaurant->id)
                ->where('last_activity', '>=', now()->subDays(7))
                ->count();
            $todayUsers = TelegramUser::where('restaurant_id', $restaurant->id)
                ->where('last_activity', '>=', now()->startOfDay())
                ->count();
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'recent_users' => $recentUsers,
                    'today_users' => $todayUsers
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Show conversation with user
     */
    public function conversation(Restaurant $restaurant, TelegramUser $telegramUser)
    {
        $this->authorize('view', $restaurant);
        
        // Get messages from this restaurant for this user
        $messages = \App\Models\TelegramMessage::where('restaurant_id', $restaurant->id)
            ->where('telegram_user_id', $telegramUser->id)
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get();
        
        // Get user info from global table if exists
        $globalUser = \App\Models\GlobalTelegramUser::where('telegram_id', $telegramUser->telegram_id)->first();
        
        return view('admin.bots.conversation', compact('restaurant', 'telegramUser', 'messages', 'globalUser'));
    }

    /**
     * Send message to specific user
     */
    public function sendMessageToUser(Request $request, Restaurant $restaurant, TelegramUser $telegramUser)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'message' => 'required|string|max:4096'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            // Create TelegramService with restaurant's bot token
            $telegramService = new TelegramService($restaurant->bot_token);
            
            // Test bot connection first
            $botInfo = $telegramService->getMe();
            if (!$botInfo['ok']) {
                return response()->json(['success' => false, 'message' => 'Bot token noto\'g\'ri: ' . ($botInfo['description'] ?? 'Unknown error')]);
            }
            
            $result = $telegramService->sendMessage($telegramUser->telegram_id, $request->message);
            
            if ($result['ok']) {
                // Save outgoing message to database
                \App\Models\TelegramMessage::create([
                    'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $telegramUser->id,
                    'message_id' => $result['result']['message_id'] ?? null,
                    'direction' => 'outgoing',
                    'message_text' => $request->message,
                    'message_data' => $result['result'] ?? null,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Xabar muvaffaqiyatli yuborildi',
                    'result' => $result['result']
                ]);
            } else {
                return response()->json([
                    'success' => false, 'message' => 'Xabar yuborishda xatolik: ' . ($result['description'] ?? 'Unknown error')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Get new messages from user
     */
    public function getNewMessages(Restaurant $restaurant, TelegramUser $telegramUser)
    {
        $this->authorize('view', $restaurant);
        
        // Ensure the user belongs to this restaurant
        if ($telegramUser->restaurant_id !== $restaurant->id) {
            return response()->json(['success' => false, 'message' => 'Foydalanuvchi topilmadi']);
        }
        
        $messages = TelegramMessage::where('restaurant_id', $restaurant->id)
            ->where('telegram_user_id', $telegramUser->id)
            ->where('direction', 'incoming')
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead(Restaurant $restaurant, TelegramUser $telegramUser)
    {
        $this->authorize('view', $restaurant);
        
        // Ensure the user belongs to this restaurant
        if ($telegramUser->restaurant_id !== $restaurant->id) {
            return response()->json(['success' => false, 'message' => 'Foydalanuvchi topilmadi']);
        }
        
        TelegramMessage::where('restaurant_id', $restaurant->id)
            ->where('telegram_user_id', $telegramUser->id)
            ->where('direction', 'incoming')
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true, 'message' => 'Xabarlar o\'qildi deb belgilandi']);
    }

    /**
     * Get users statistics for all restaurants
     */
    public function getAllUsersStats()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $restaurants = Restaurant::with('telegramUsers')->get();
        } else {
            $restaurants = Restaurant::where('owner_user_id', $user->id)->with('telegramUsers')->get();
        }
        
        $stats = [];
        $totalUsers = 0;
        $totalActiveUsers = 0;
        $totalMessages = 0;
        
        foreach ($restaurants as $restaurant) {
            $userCount = $restaurant->telegramUsers()->count();
            $activeUserCount = $restaurant->telegramUsers()->where('is_active', true)->count();
            $messageCount = \App\Models\TelegramMessage::whereHas('telegramUser', function($query) use ($restaurant) {
                $query->where('restaurant_id', $restaurant->id);
            })->count();
            
            $stats[] = [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'bot_username' => $restaurant->bot_username,
                'user_count' => $userCount,
                'active_user_count' => $activeUserCount,
                'message_count' => $messageCount,
                'is_active' => !empty($restaurant->bot_token)
            ];
            
            $totalUsers += $userCount;
            $totalActiveUsers += $activeUserCount;
            $totalMessages += $messageCount;
        }
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'totals' => [
                'restaurants' => $restaurants->count(),
                'users' => $totalUsers,
                'active_users' => $totalActiveUsers,
                'messages' => $totalMessages
            ]
        ]);
    }

    /**
     * Send message to users across multiple restaurants
     */
    public function sendMessageToMultipleRestaurants(Request $request)
    {
        $request->validate([
            'restaurant_ids' => 'required|array',
            'restaurant_ids.*' => 'integer|exists:restaurants,id',
            'message' => 'required|string|max:4096',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer'
        ]);

        $user = Auth::user();
        $restaurantIds = $request->restaurant_ids;
        
        // Filter restaurants based on user permissions
        if (!$user->isSuperAdmin()) {
            $restaurantIds = Restaurant::where('owner_user_id', $user->id)
                ->whereIn('id', $restaurantIds)
                ->pluck('id')
                ->toArray();
        }
        
        $results = [];
        $totalSuccess = 0;
        $totalErrors = 0;
        
        foreach ($restaurantIds as $restaurantId) {
            $restaurant = Restaurant::find($restaurantId);
            
            if (!$restaurant || !$restaurant->bot_token) {
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant ? $restaurant->name : 'Unknown',
                    'success' => false,
                    'message' => 'Bot token o\'rnatilmagan'
                ];
                $totalErrors++;
                continue;
            }
            
            try {
                $telegramService = new TelegramService($restaurant->bot_token);
                
                // Test bot connection
                $botInfo = $telegramService->getMe();
                if (!$botInfo['ok']) {
                    $results[] = [
                        'restaurant_id' => $restaurantId,
                        'restaurant_name' => $restaurant->name,
                        'success' => false,
                        'message' => 'Bot token noto\'g\'ri: ' . ($botInfo['description'] ?? 'Unknown error')
                    ];
                    $totalErrors++;
                    continue;
                }
                
                // Get users for this restaurant
                $query = TelegramUser::where('restaurant_id', $restaurant->id);
                
                if ($request->user_ids) {
                    $query->whereIn('telegram_id', $request->user_ids);
                } else {
                    $query->where('is_active', true);
                }
                
                $users = $query->get();
                
                $successCount = 0;
                $errorCount = 0;
                $errors = [];
                
                foreach ($users as $telegramUser) {
                    try {
                        $result = $telegramService->sendMessage($telegramUser->telegram_id, $request->message);
                        
                        if ($result['ok']) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "User {$telegramUser->telegram_id}: " . ($result['description'] ?? 'Unknown error');
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = "User {$telegramUser->telegram_id}: " . $e->getMessage();
                    }
                }
                
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant->name,
                    'success' => true,
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'errors' => $errors
                ];
                
                $totalSuccess += $successCount;
                $totalErrors += $errorCount;
                
            } catch (\Exception $e) {
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant->name,
                    'success' => false,
                    'message' => 'Xatolik: ' . $e->getMessage()
                ];
                $totalErrors++;
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'summary' => [
                'total_success' => $totalSuccess,
                'total_errors' => $totalErrors,
                'restaurants_processed' => count($restaurantIds)
            ]
        ]);
    }

    /**
     * Get all users across multiple restaurants
     */
    public function getAllUsers(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $restaurants = Restaurant::all();
        } else {
            $restaurants = Restaurant::where('owner_user_id', $user->id)->get();
        }
        
        $restaurantId = $request->get('restaurant_id');
        $search = $request->get('search');
        $status = $request->get('status');
        
        $query = TelegramUser::with('restaurant');
        
        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        } else {
            $query->whereIn('restaurant_id', $restaurants->pluck('id'));
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        
        $users = $query->orderBy('last_activity', 'desc')->paginate(20);
        
        return view('admin.bots.all-users', compact('users', 'restaurants'));
    }

    /**
     * Test multiple bots at once
     */
    public function testMultipleBots(Request $request)
    {
        $request->validate([
            'restaurant_ids' => 'required|array',
            'restaurant_ids.*' => 'integer|exists:restaurants,id'
        ]);

        $user = Auth::user();
        $restaurantIds = $request->restaurant_ids;
        
        // Filter restaurants based on user permissions
        if (!$user->isSuperAdmin()) {
            $restaurantIds = Restaurant::where('owner_user_id', $user->id)
                ->whereIn('id', $restaurantIds)
                ->pluck('id')
                ->toArray();
        }
        
        $results = [];
        
        foreach ($restaurantIds as $restaurantId) {
            $restaurant = Restaurant::find($restaurantId);
            
            if (!$restaurant) {
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'success' => false,
                    'message' => 'Restoran topilmadi'
                ];
                continue;
            }
            
            if (!$restaurant->bot_token) {
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant->name,
                    'success' => false,
                    'message' => 'Bot token o\'rnatilmagan'
                ];
                continue;
            }
            
            try {
                $telegramService = new TelegramService($restaurant->bot_token);
                
                // Test bot connection
                $botInfo = $telegramService->getMe();
                if (!$botInfo['ok']) {
                    $results[] = [
                        'restaurant_id' => $restaurantId,
                        'restaurant_name' => $restaurant->name,
                        'success' => false,
                        'message' => 'Bot token noto\'g\'ri: ' . ($botInfo['description'] ?? 'Unknown error')
                    ];
                    continue;
                }
                
                // Test webhook
                $webhookInfo = $telegramService->getWebhookInfo();
                
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant->name,
                    'success' => true,
                    'bot_info' => $botInfo['result'],
                    'webhook_info' => $webhookInfo['result'],
                    'message' => 'Bot muvaffaqiyatli ishlayapti'
                ];
                
            } catch (\Exception $e) {
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant->name,
                    'success' => false,
                    'message' => 'Xatolik: ' . $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Set webhooks for multiple bots
     */
    public function setMultipleWebhooks(Request $request)
    {
        $request->validate([
            'restaurant_ids' => 'required|array',
            'restaurant_ids.*' => 'integer|exists:restaurants,id'
        ]);

        $user = Auth::user();
        $restaurantIds = $request->restaurant_ids;
        
        // Filter restaurants based on user permissions
        if (!$user->isSuperAdmin()) {
            $restaurantIds = Restaurant::where('owner_user_id', $user->id)
                ->whereIn('id', $restaurantIds)
                ->pluck('id')
                ->toArray();
        }
        
        $results = [];
        
        foreach ($restaurantIds as $restaurantId) {
            $restaurant = Restaurant::find($restaurantId);
            
            if (!$restaurant || !$restaurant->bot_token) {
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant ? $restaurant->name : 'Unknown',
                    'success' => false,
                    'message' => 'Bot token o\'rnatilmagan'
                ];
                continue;
            }
            
            try {
                $telegramService = new TelegramService($restaurant->bot_token);
                
                // Set webhook URL
                $webhookUrl = url('/telegram/webhook/' . $restaurant->bot_token);
                $result = $telegramService->setWebhook($webhookUrl);
                
                if ($result['ok']) {
                    $results[] = [
                        'restaurant_id' => $restaurantId,
                        'restaurant_name' => $restaurant->name,
                        'success' => true,
                        'webhook_url' => $webhookUrl,
                        'message' => 'Webhook muvaffaqiyatli o\'rnatildi'
                    ];
                } else {
                    $results[] = [
                        'restaurant_id' => $restaurantId,
                        'restaurant_name' => $restaurant->name,
                        'success' => false,
                        'message' => 'Webhook o\'rnatishda xatolik: ' . ($result['description'] ?? 'Unknown error')
                    ];
                }
                
            } catch (\Exception $e) {
                $results[] = [
                    'restaurant_id' => $restaurantId,
                    'restaurant_name' => $restaurant->name,
                    'success' => false,
                    'message' => 'Xatolik: ' . $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
} 