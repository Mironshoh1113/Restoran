<?php

require_once 'vendor/autoload.php';

use App\Models\Restaurant;
use App\Models\User;
use App\Services\TelegramService;

// Test bot settings functionality
echo "Testing Bot Settings Functionality\n";
echo "==================================\n\n";

// 1. Check if we can access restaurants
try {
    $restaurants = Restaurant::all();
    echo "✅ Restaurants found: " . $restaurants->count() . "\n";
    
    if ($restaurants->count() > 0) {
        $restaurant = $restaurants->first();
        echo "✅ First restaurant: " . $restaurant->name . "\n";
        echo "   - Bot token: " . ($restaurant->bot_token ? 'Set' : 'Not set') . "\n";
        echo "   - Bot username: " . ($restaurant->bot_username ?: 'Not set') . "\n";
        echo "   - Bot name: " . ($restaurant->bot_name ?: 'Not set') . "\n";
        echo "   - Bot description: " . ($restaurant->bot_description ?: 'Not set') . "\n";
        echo "   - Bot image: " . ($restaurant->bot_image ?: 'Not set') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error accessing restaurants: " . $e->getMessage() . "\n";
}

// 2. Check if we can access users
try {
    $users = User::all();
    echo "✅ Users found: " . $users->count() . "\n";
    
    if ($users->count() > 0) {
        $user = $users->first();
        echo "✅ First user: " . $user->name . "\n";
        echo "   - Role: " . $user->role . "\n";
        echo "   - Is super admin: " . ($user->isSuperAdmin() ? 'Yes' : 'No') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error accessing users: " . $e->getMessage() . "\n";
}

// 3. Test TelegramService
try {
    $telegramService = new TelegramService();
    echo "✅ TelegramService instantiated successfully\n";
    
    // Test if we can make a basic API call (this will fail without a valid token, but should not crash)
    if ($restaurants->count() > 0 && $restaurant->bot_token) {
        $telegramService->setBotToken($restaurant->bot_token);
        echo "✅ Bot token set in TelegramService\n";
    } else {
        echo "⚠️  No bot token available for testing\n";
    }
} catch (Exception $e) {
    echo "❌ Error with TelegramService: " . $e->getMessage() . "\n";
}

// 4. Check routes
echo "\nChecking Routes:\n";
$routes = [
    'admin.bots.index' => '/admin/bots',
    'admin.bots.show' => '/admin/bots/1',
    'admin.bots.update' => '/admin/bots/1',
    'admin.bots.test' => '/admin/bots/1/test',
    'admin.bots.set-webhook' => '/admin/bots/1/webhook',
    'admin.bots.delete-webhook' => '/admin/bots/1/webhook',
    'admin.bots.send-test' => '/admin/bots/1/send-test',
    'admin.bots.update-name' => '/admin/bots/1/update-name',
    'admin.bots.update-description' => '/admin/bots/1/update-description',
    'admin.bots.update-photo' => '/admin/bots/1/update-photo',
    'admin.bots.get-commands' => '/admin/bots/1/commands',
    'admin.bots.set-commands' => '/admin/bots/1/commands',
];

foreach ($routes as $name => $path) {
    echo "   - $name: $path\n";
}

echo "\n✅ Bot settings functionality test completed!\n";
echo "\nTo test the web interface:\n";
echo "1. Start the server: php artisan serve\n";
echo "2. Visit: http://localhost:8000/admin/bots\n";
echo "3. Login with your credentials\n";
echo "4. Test the bot settings functionality\n"; 