<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['web', \App\Http\Middleware\UseBroadcastAuthGuard::class]]
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'seller' => \App\Http\Middleware\EnsureUserIsSeller::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'buyer' => \App\Http\Middleware\EnsureUserIsBuyer::class,
            'frontend' => \App\Http\Middleware\RedirectAdminFromFrontend::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
