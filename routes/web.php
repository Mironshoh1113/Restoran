<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\BotController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\GlobalTelegramController;
use App\Http\Controllers\TelegramController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
	return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'getStats'])->middleware(['auth', 'verified'])->name('dashboard.stats');

Route::middleware('auth')->group(function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	Route::prefix('admin')->name('admin.')->group(function () {
		Route::resource('restaurants', RestaurantController::class);
		Route::resource('orders', OrderController::class)->only(['index', 'show']);
		Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
		Route::patch('orders/{order}/courier', [OrderController::class, 'assignCourier'])->name('orders.assign-courier');
		Route::patch('orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.update-payment');
		
		// Courier routes
		Route::resource('couriers', CourierController::class);
		
		// Bot routes
		Route::get('bots', [BotController::class, 'index'])->name('bots.index');
		
		// Redirect bot show page to restaurant edit page (merged functionality)
		Route::get('bots/{restaurant}', function(App\Models\Restaurant $restaurant) {
			return redirect()->route('admin.restaurants.edit', $restaurant);
		})->name('bots.show');
		
		Route::patch('bots/{restaurant}', [BotController::class, 'update'])->name('bots.update');
		Route::post('bots/{restaurant}/test', [BotController::class, 'test'])->name('bots.test');
		Route::post('bots/{restaurant}/webhook', [BotController::class, 'setWebhook'])->name('bots.set-webhook');
		Route::delete('bots/{restaurant}/webhook', [BotController::class, 'deleteWebhook'])->name('bots.delete-webhook');
		Route::post('bots/{restaurant}/webhook/auto', [BotController::class, 'setWebhookAuto'])->name('bots.set-webhook-auto');
		Route::get('bots/{restaurant}/webhook/info', [BotController::class, 'getWebhookInfo'])->name('bots.webhook-info');
		Route::get('bots/{restaurant}/info', [BotController::class, 'getBotInfo'])->name('bots.info');
		Route::post('bots/{restaurant}/send-test', [BotController::class, 'sendTestMessage'])->name('bots.send-test');
		
		// Multi-bot management routes
		Route::get('bots/stats/all', [BotController::class, 'getAllUsersStats'])->name('bots.all-stats');
		Route::post('bots/send-multiple', [BotController::class, 'sendMessageToMultipleRestaurants'])->name('bots.send-multiple');
		Route::get('bots/users/all', [BotController::class, 'getAllUsers'])->name('bots.all-users');
		Route::post('bots/test-multiple', [BotController::class, 'testMultipleBots'])->name('bots.test-multiple');
		Route::post('bots/set-webhooks-multiple', [BotController::class, 'setMultipleWebhooks'])->name('bots.set-webhooks-multiple');
		
		// Global Telegram Users Management
		Route::prefix('global-telegram')->name('global-telegram.')->group(function () {
			Route::get('/', [GlobalTelegramController::class, 'index'])->name('index');
			Route::get('/stats', [GlobalTelegramController::class, 'getGlobalStats'])->name('stats');
			Route::get('/{globalUser}', [GlobalTelegramController::class, 'show'])->name('show');
			Route::get('/{globalUser}/stats', [GlobalTelegramController::class, 'getUserStats'])->name('user-stats');
			Route::post('/{globalUser}/send-message', [GlobalTelegramController::class, 'sendMessageToAllRestaurants'])->name('send-message');
		});
		
		// New bot management routes
		Route::post('bots/{restaurant}/update-name', [BotController::class, 'updateBotName'])->name('bots.update-name');
		Route::post('bots/{restaurant}/update-description', [BotController::class, 'updateBotDescription'])->name('bots.update-description');
		Route::post('bots/{restaurant}/update-photo', [BotController::class, 'updateBotPhoto'])->name('bots.update-photo');
		Route::get('bots/{restaurant}/commands', [BotController::class, 'getBotCommands'])->name('bots.get-commands');
		Route::post('bots/{restaurant}/commands', [BotController::class, 'setBotCommands'])->name('bots.set-commands');
		
		// Telegram users management routes
		Route::get('bots/{restaurant}/users', [BotController::class, 'users'])->name('bots.users');
		Route::post('bots/{restaurant}/users/send', [BotController::class, 'sendMessageToUsers'])->name('bots.send-to-users');
		Route::post('bots/{restaurant}/users/send-all', [BotController::class, 'sendMessageToAllUsers'])->name('bots.send-to-all-users');
		Route::get('bots/{restaurant}/users/stats', [BotController::class, 'getUsersStats'])->name('bots.users-stats');
		
		// Conversation routes
		Route::get('bots/{restaurant}/users/{telegramUser}/conversation', [BotController::class, 'conversation'])->name('bots.conversation');
		Route::post('bots/{restaurant}/users/{telegramUser}/send', [BotController::class, 'sendMessageToUser'])->name('bots.send-to-user');
		Route::get('bots/{restaurant}/users/{telegramUser}/messages', [BotController::class, 'getNewMessages'])->name('bots.get-new-messages');
		Route::post('bots/{restaurant}/users/{telegramUser}/read', [BotController::class, 'markMessagesAsRead'])->name('bots.mark-as-read');
		// Real-time message updates
		Route::get('bots/{restaurant}/users/{telegramUser}/messages/new', [BotController::class, 'getNewMessages'])->name('bots.get-new-messages-rt');
		
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

// Web interface for Telegram users
Route::get('/web-interface/{token}', [TelegramController::class, 'webInterface'])->name('web.interface');
Route::get('/web-interface', [TelegramController::class, 'webInterfaceFromApp'])->name('web.interface.app');
Route::get('/web-interface/checkout', function(\Illuminate\Http\Request $request) {
	return view('web-interface.checkout');
})->name('web.interface.checkout');
Route::post('/web-interface/{token}/order', [TelegramController::class, 'placeOrder'])->name('web.place-order');
Route::post('/web-interface/order', [TelegramController::class, 'placeOrderWithoutToken'])->name('web.place-order-no-token');
Route::get('/web-interface/{token}/menu', [TelegramController::class, 'getMenu'])->name('web.get-menu');
Route::get('/web-interface/menu', [TelegramController::class, 'getMenuWithoutToken'])->name('web.get-menu-no-token');

// Enhanced web interface for Telegram users
Route::get('/enhanced-web-interface/{token}', [TelegramController::class, 'webInterface'])->name('enhanced.web.interface');
Route::get('/enhanced-web-interface', [TelegramController::class, 'webInterfaceFromApp'])->name('enhanced.web.interface.app');
Route::post('/enhanced-web-interface/{token}/order', [TelegramController::class, 'placeOrder'])->name('enhanced.web.place-order');
Route::post('/enhanced-web-interface/order', [TelegramController::class, 'placeOrderWithoutToken'])->name('enhanced.web.place-order-no-token');

// Telegram Web App direct access
Route::get('/web-interface/app/{botToken}', function($botToken) {
	$restaurant = \App\Models\Restaurant::where('bot_token', $botToken)->where('is_active', true)->first();
	
	if (!$restaurant) {
		return response('Restaurant not found or not active', 404);
	}
	
	$categories = \App\Models\Category::where('restaurant_id', $restaurant->id)
		->with(['menuItems' => function($query) {
			$query->where('is_active', true);
		}])
		->get();
	
	$user = (object) [
		'id' => 0,
		'name' => 'Telegram User',
		'phone' => null
	];
	
	return view('web-interface.index', compact('restaurant', 'user', 'categories', 'botToken'));
})->name('web.interface.app.direct');

// Direct web interface access with bot token
Route::get('/web-interface/direct/{botToken}', function($botToken) {
	$restaurant = \App\Models\Restaurant::where('bot_token', $botToken)->first();
	
	if (!$restaurant || !$restaurant->is_active) {
		return response('Restaurant not found or not active', 404);
	}
	
	$categories = \App\Models\Category::where('restaurant_id', $restaurant->id)
		->with(['menuItems' => function($query) {
			$query->where('is_active', true);
		}])
		->get();
	
	$user = (object) [
		'id' => 0,
		'name' => 'Guest User',
		'phone' => null
	];
	
	return view('web-interface.index', compact('restaurant', 'user', 'categories', 'botToken'));
})->name('web.interface.direct');

// Redirect Web App settings to edit page (merged functionality)
Route::get('/admin/restaurants/{restaurant}/web-app-settings', function(App\Models\Restaurant $restaurant) {
	return redirect()->route('admin.restaurants.edit', $restaurant);
})->name('admin.restaurants.web-app-settings')->middleware(['auth', 'verified']);

require __DIR__.'/auth.php'; 