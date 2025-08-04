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
    // Git pull qilamiz
    shell_exec('cd /home/host1801295/simpsons.uz/htdocs/www && git pull origin main');

    // Composer va cache lar (ixtiyoriy)
    shell_exec('cd /home/host1801295/simpsons.uz/htdocs/www && composer install --no-dev --optimize-autoloader');
    shell_exec('cd /home/host1801295/simpsons.uz/htdocs/www && php artisan config:cache');

    return response('OK', 200);
}); 