<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

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
     * Test bot connection
     */
    public function test(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $botInfo = $telegramService->getMe();
            
            if ($botInfo['ok']) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Bot ulanishi muvaffaqiyatli',
                    'bot_info' => $botInfo['result']
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Bot token noto\'g\'ri'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Xatolik: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Set webhook
     */
    public function setWebhook(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        $request->validate([
            'webhook_url' => 'required|url'
        ]);

        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $result = $telegramService->setWebhook($request->webhook_url);
            
            if ($result['ok']) {
                return response()->json(['success' => true, 'message' => 'Webhook muvaffaqiyatli o\'rnatildi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Webhook o\'rnatishda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        
        if (!$restaurant->bot_token) {
            return response()->json(['success' => false, 'message' => 'Bot token o\'rnatilmagan']);
        }

        try {
            $telegramService = new TelegramService($restaurant->bot_token);
            $result = $telegramService->deleteWebhook();
            
            if ($result['ok']) {
                return response()->json(['success' => true, 'message' => 'Webhook muvaffaqiyatli o\'chirildi']);
            } else {
                return response()->json(['success' => false, 'message' => 'Webhook o\'chirishda xatolik: ' . ($result['description'] ?? 'Nomalum xatolik')]);
            }
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
} 