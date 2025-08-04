<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BotController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\TelegramController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('restaurants', RestaurantController::class);
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::patch('orders/{order}/courier', [OrderController::class, 'assignCourier'])->name('orders.assign-courier');
        
        // Bot routes
        Route::get('bots', [BotController::class, 'index'])->name('bots.index');
        Route::get('bots/{restaurant}', [BotController::class, 'show'])->name('bots.show');
        Route::patch('bots/{restaurant}', [BotController::class, 'update'])->name('bots.update');
        Route::post('bots/{restaurant}/test', [BotController::class, 'test'])->name('bots.test');
        Route::post('bots/{restaurant}/webhook', [BotController::class, 'setWebhook'])->name('bots.set-webhook');
        Route::delete('bots/{restaurant}/webhook', [BotController::class, 'deleteWebhook'])->name('bots.delete-webhook');
        Route::post('bots/{restaurant}/send-test', [BotController::class, 'sendTestMessage'])->name('bots.send-test');
        
        // Project routes
        Route::get('restaurants/{restaurant}/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('restaurants/{restaurant}/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('restaurants/{restaurant}/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('restaurants/{restaurant}/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('restaurants/{restaurant}/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::patch('restaurants/{restaurant}/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('restaurants/{restaurant}/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        
        // Category routes
        Route::get('restaurants/{restaurant}/projects/{project}/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('restaurants/{restaurant}/projects/{project}/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::patch('restaurants/{restaurant}/projects/{project}/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('restaurants/{restaurant}/projects/{project}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Menu Item routes
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/create', [MenuItemController::class, 'create'])->name('menu-items.create');
        Route::post('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}', [MenuItemController::class, 'show'])->name('menu-items.show');
        Route::get('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('menu-items.edit');
        Route::patch('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
        Route::delete('restaurants/{restaurant}/projects/{project}/categories/{category}/menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');
    });
});

// Telegram webhook
Route::post('/telegram/webhook/{token}', [TelegramController::class, 'webhook'])->name('telegram.webhook');

require __DIR__.'/auth.php';
