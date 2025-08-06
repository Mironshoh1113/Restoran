<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;

echo "=== RESTAURANT AND BOT STATUS ===\n\n";

$restaurants = Restaurant::all();

foreach ($restaurants as $restaurant) {
    echo "Restaurant ID: {$restaurant->id}\n";
    echo "Name: {$restaurant->name}\n";
    echo "Bot Token: " . ($restaurant->bot_token ? 'SET' : 'NOT SET') . "\n";
    echo "Bot Username: " . ($restaurant->bot_username ?: 'NOT SET') . "\n";
    echo "Bot Name: " . ($restaurant->bot_name ?: 'NOT SET') . "\n";
    echo "Is Active: " . ($restaurant->is_active ? 'YES' : 'NO') . "\n";
    echo "Phone: " . ($restaurant->phone ?: 'NOT SET') . "\n";
    echo "Address: " . ($restaurant->address ?: 'NOT SET') . "\n";
    echo "---\n\n";
}

echo "=== WEBHOOK URLS ===\n\n";

foreach ($restaurants as $restaurant) {
    if ($restaurant->bot_token) {
        $webhookUrl = url("/telegram/webhook/{$restaurant->bot_token}");
        echo "Restaurant: {$restaurant->name}\n";
        echo "Webhook URL: {$webhookUrl}\n";
        echo "---\n\n";
    }
} 