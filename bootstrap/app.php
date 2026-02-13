<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\NoCache;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\LogPageNavigation;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        // ========================================
        // REGISTER MIDDLEWARE ALIASES
        // ========================================
        $middleware->alias([
            'check.role' => CheckRole::class,
            'auth'    => Authenticate::class,
            'nocache' => NoCache::class,
        ]);
        
        // ========================================
        // REGISTER GLOBAL WEB MIDDLEWARE
        // ========================================
        $middleware->web(append: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            
            // ADD PAGE NAVIGATION LOGGING MIDDLEWARE
            LogPageNavigation::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();