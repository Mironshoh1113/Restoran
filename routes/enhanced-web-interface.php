<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\Admin\RestaurantController;

/*
|--------------------------------------------------------------------------
| Enhanced Web Interface Routes
|--------------------------------------------------------------------------
|
| These routes demonstrate how to implement the enhanced Telegram web interface
| with all the new customization features.
|
*/

// Enhanced Web Interface Routes
Route::prefix('enhanced-web-interface')->group(function () {
    
    // Main enhanced interface
    Route::get('/{token}', [TelegramController::class, 'enhancedWebInterface'])
        ->name('enhanced.web.interface');
    
    // Enhanced interface from app
    Route::get('/', [TelegramController::class, 'enhancedWebInterfaceFromApp'])
        ->name('enhanced.web.interface.app');
    
    // Enhanced interface with direct bot token
    Route::get('/direct/{botToken}', function($botToken) {
        // Find restaurant by bot token
        $restaurant = \App\Models\Restaurant::where('bot_token', $botToken)->first();
        
        if (!$restaurant) {
            abort(404, 'Restoran topilmadi');
        }
        
        // Get categories with menu items
        $categories = \App\Models\Category::where('restaurant_id', $restaurant->id)
            ->with(['menuItems' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();
        
        // Get bot token for the interface
        $botToken = $restaurant->bot_token;
        
        // Return enhanced interface view
        return view('web-interface.enhanced', compact('restaurant', 'categories', 'botToken'));
    })->name('enhanced.web.interface.direct');
    
    // Order placement for enhanced interface
    Route::post('/{token}/order', [TelegramController::class, 'placeOrder'])
        ->name('enhanced.web.place-order');
    
    // Menu API for enhanced interface
    Route::get('/{token}/menu', [TelegramController::class, 'getMenu'])
        ->name('enhanced.web.get-menu');
});

// Admin Routes for Enhanced Customization
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    
    // Enhanced restaurant management
    Route::resource('restaurants', RestaurantController::class);
    
    // Enhanced restaurant customization
    Route::get('/restaurants/{restaurant}/customize', [RestaurantController::class, 'customize'])
        ->name('admin.restaurants.customize');
    
    // Save customization settings
    Route::post('/restaurants/{restaurant}/customize', [RestaurantController::class, 'saveCustomization'])
        ->name('admin.restaurants.save-customization');
    
    // Preview customization
    Route::get('/restaurants/{restaurant}/preview', [RestaurantController::class, 'previewCustomization'])
        ->name('admin.restaurants.preview');
    
    // Reset to default customization
    Route::post('/restaurants/{restaurant}/reset-customization', [RestaurantController::class, 'resetCustomization'])
        ->name('admin.restaurants.reset-customization');
});

// API Routes for Enhanced Features
Route::prefix('api')->group(function () {
    
    // Enhanced menu API
    Route::get('/restaurants/{restaurant}/enhanced-menu', function($restaurant) {
        $restaurant = \App\Models\Restaurant::findOrFail($restaurant);
        $categories = \App\Models\Category::where('restaurant_id', $restaurant->id)
            ->with(['menuItems' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();
        
        return response()->json([
            'restaurant' => $restaurant,
            'categories' => $categories,
            'customization' => [
                'primary_color' => $restaurant->primary_color,
                'secondary_color' => $restaurant->secondary_color,
                'accent_color' => $restaurant->accent_color,
                'text_color' => $restaurant->text_color,
                'bg_color' => $restaurant->bg_color,
                'card_bg' => $restaurant->card_bg,
                'border_radius' => $restaurant->border_radius,
                'shadow' => $restaurant->shadow,
            ]
        ]);
    })->name('api.enhanced.menu');
    
    // Customization preview API
    Route::post('/restaurants/{restaurant}/preview-customization', function($restaurant, Request $request) {
        $restaurant = \App\Models\Restaurant::findOrFail($restaurant);
        
        // Validate customization data
        $validated = $request->validate([
            'primary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'secondary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'text_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'bg_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'card_bg' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'border_radius' => 'required|string',
            'shadow' => 'required|string',
        ]);
        
        // Return preview data
        return response()->json([
            'success' => true,
            'preview' => $validated,
            'message' => 'Customization preview generated successfully'
        ]);
    })->name('api.preview.customization');
});

// Demo Routes for Testing Enhanced Features
Route::prefix('demo')->group(function () {
    
    // Demo enhanced interface
    Route::get('/enhanced-interface', function() {
        // Create demo restaurant with customization
        $demoRestaurant = new \App\Models\Restaurant([
            'name' => 'Demo Restoran',
            'description' => 'Bu demo restoran hisoblanadi',
            'primary_color' => '#667eea',
            'secondary_color' => '#764ba2',
            'accent_color' => '#ff6b35',
            'text_color' => '#2c3e50',
            'bg_color' => '#f8f9fa',
            'card_bg' => '#ffffff',
            'border_radius' => '16px',
            'shadow' => '0 8px 32px rgba(0,0,0,0.1)',
        ]);
        
        // Create demo categories and menu items
        $categories = collect([
            new \App\Models\Category(['name' => 'Asosiy taomlar']),
            new \App\Models\Category(['name' => 'Salatlar']),
            new \App\Models\Category(['name' => 'Ichimliklar']),
        ]);
        
        // Add demo menu items
        $categories->each(function($category) {
            $category->menuItems = collect([
                new \App\Models\MenuItem([
                    'name' => 'Demo taom ' . rand(1, 100),
                    'description' => 'Bu demo taom hisoblanadi',
                    'price' => rand(10000, 50000),
                    'image' => null
                ])
            ]);
        });
        
        return view('web-interface.enhanced', compact('demoRestaurant', 'categories'));
    })->name('demo.enhanced.interface');
    
    // Demo customization panel
    Route::get('/customization-panel', function() {
        return view('demo.customization-panel');
    })->name('demo.customization.panel');
});

// Utility Routes for Enhanced Features
Route::prefix('utils')->group(function () {
    
    // Color scheme generator
    Route::get('/generate-color-scheme', function() {
        $schemes = [
            'modern' => [
                'primary_color' => '#667eea',
                'secondary_color' => '#764ba2',
                'accent_color' => '#ff6b35',
                'text_color' => '#2c3e50',
                'bg_color' => '#f8f9fa',
                'card_bg' => '#ffffff',
            ],
            'dark' => [
                'primary_color' => '#1a1a1a',
                'secondary_color' => '#2a2a2a',
                'accent_color' => '#00d4ff',
                'text_color' => '#ffffff',
                'bg_color' => '#000000',
                'card_bg' => '#1a1a1a',
            ],
            'warm' => [
                'primary_color' => '#ff6b35',
                'secondary_color' => '#f7931e',
                'accent_color' => '#ffd23f',
                'text_color' => '#2c3e50',
                'bg_color' => '#fff8f0',
                'card_bg' => '#ffffff',
            ],
            'cool' => [
                'primary_color' => '#4facfe',
                'secondary_color' => '#00f2fe',
                'accent_color' => '#667eea',
                'text_color' => '#2c3e50',
                'bg_color' => '#f0f8ff',
                'card_bg' => '#ffffff',
            ]
        ];
        
        return response()->json($schemes);
    })->name('utils.color.schemes');
    
    // Export customization settings
    Route::get('/export-customization/{restaurant}', function($restaurant) {
        $restaurant = \App\Models\Restaurant::findOrFail($restaurant);
        
        $customization = [
            'restaurant_name' => $restaurant->name,
            'customization' => [
                'primary_color' => $restaurant->primary_color,
                'secondary_color' => $restaurant->secondary_color,
                'accent_color' => $restaurant->accent_color,
                'text_color' => $restaurant->text_color,
                'bg_color' => $restaurant->bg_color,
                'card_bg' => $restaurant->card_bg,
                'border_radius' => $restaurant->border_radius,
                'shadow' => $restaurant->shadow,
            ],
            'exported_at' => now()->toISOString(),
            'version' => '2.0.0'
        ];
        
        return response()->json($customization);
    })->name('utils.export.customization');
});

/*
|--------------------------------------------------------------------------
| Route Notes
|--------------------------------------------------------------------------
|
| 1. Enhanced Web Interface Routes:
|    - /enhanced-web-interface/{token} - Main enhanced interface
|    - /enhanced-web-interface - Interface from app
|    - /enhanced-web-interface/direct/{botToken} - Direct access
|
| 2. Admin Customization Routes:
|    - /admin/restaurants/{id}/customize - Customization panel
|    - /admin/restaurants/{id}/preview - Preview changes
|    - /admin/restaurants/{id}/reset-customization - Reset to defaults
|
| 3. API Routes:
|    - /api/restaurants/{id}/enhanced-menu - Enhanced menu data
|    - /api/restaurants/{id}/preview-customization - Preview API
|
| 4. Demo Routes:
|    - /demo/enhanced-interface - Demo enhanced interface
|    - /demo/customization-panel - Demo customization panel
|
| 5. Utility Routes:
|    - /utils/generate-color-scheme - Color scheme generator
|    - /utils/export-customization/{id} - Export customization
|
*/ 