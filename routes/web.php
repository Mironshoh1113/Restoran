<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BotController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\TelegramController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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

// Web interface for Telegram users
Route::get('/web-interface/{token}', [TelegramController::class, 'webInterface'])->name('web.interface');
Route::get('/web-interface', [TelegramController::class, 'webInterfaceFromApp'])->name('web.interface.app');
Route::post('/web-interface/{token}/order', [TelegramController::class, 'placeOrder'])->name('web.place-order');
Route::post('/web-interface/order', [TelegramController::class, 'placeOrderWithoutToken'])->name('web.place-order-no-token');
Route::get('/web-interface/{token}/menu', [TelegramController::class, 'getMenu'])->name('web.get-menu');

// Git Webhook Route for Auto Deployment
Route::post('/webhook', function () {
    // Set error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Log file
    $logFile = storage_path('logs/webhook.log');

    // Function to log messages
    function logMessage($message) {
        global $logFile;
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    // Get the raw POST data
    $payload = file_get_contents('php://input');
    $headers = getallheaders();

    // Verify the request is from Git (optional but recommended)
    $signature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, env('WEBHOOK_SECRET', 'YOUR_WEBHOOK_SECRET'));

    if (!hash_equals($expectedSignature, $signature)) {
        logMessage('Invalid signature');
        http_response_code(403);
        exit('Invalid signature');
    }

    // Parse the JSON payload
    $data = json_decode($payload, true);

    if (!$data) {
        logMessage('Invalid JSON payload');
        http_response_code(400);
        exit('Invalid JSON payload');
    }

    // Check if this is a push to the main branch
    $ref = $data['ref'] ?? '';
    $branch = str_replace('refs/heads/', '', $ref);

    if ($branch !== 'main' && $branch !== 'master') {
        logMessage("Ignoring push to branch: $branch");
        http_response_code(200);
        exit('Ignoring non-main branch');
    }

    // Log the deployment trigger
    logMessage("Deployment triggered for branch: $branch");

    // Execute the deployment script
    $deployScript = base_path('deploy.sh');
    $output = [];
    $returnCode = 0;

    if (file_exists($deployScript)) {
        exec("bash $deployScript 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            logMessage('Deployment completed successfully');
            http_response_code(200);
            echo 'Deployment completed successfully';
        } else {
            logMessage('Deployment failed: ' . implode("\n", $output));
            http_response_code(500);
            echo 'Deployment failed';
        }
    } else {
        logMessage('Deployment script not found: ' . $deployScript);
        http_response_code(500);
        echo 'Deployment script not found';
    }
})->name('webhook');

require __DIR__.'/auth.php';
