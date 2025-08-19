<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'telegram/webhook/*',
            'api/telegram-webhook/*',
            'webhook',
            'web-interface/order',
        ]);
        
        // $middleware->web(append: [
        //     \App\Http\Middleware\HandleInertiaRequests::class,
        // ]);
        
        $middleware->alias([
        	'check.plan.limits' => \App\Http\Middleware\CheckPlanLimits::class,
        	'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
