<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Deploy webhook
Route::post('/deploy', function () {
    try {
        // Get the current directory
        $currentDir = base_path();
        
        // Git pull
        $gitPull = shell_exec("cd {$currentDir} && git pull origin main 2>&1");
        
        // Composer install
        $composerInstall = shell_exec("cd {$currentDir} && composer install --no-dev --optimize-autoloader 2>&1");
        
        // Cache config
        $configCache = shell_exec("cd {$currentDir} && php artisan config:cache 2>&1");
        
        return response()->json([
            'success' => true,
            'message' => 'Deployment completed successfully',
            'git_pull' => $gitPull,
            'composer_install' => $composerInstall,
            'config_cache' => $configCache
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}); 