<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MenuItem;

echo "Menu items:\n";
$items = MenuItem::all(['id', 'name', 'image']);

foreach ($items as $item) {
    echo $item->id . ': ' . $item->name . ' - Image: ' . ($item->image ?? 'none') . "\n";
    
    if ($item->image) {
        echo "  - Image path: " . $item->image . "\n";
        echo "  - Image URL: " . $item->image_url . "\n";
        echo "  - Has image: " . ($item->hasImage() ? 'yes' : 'no') . "\n";
    }
    echo "\n";
} 