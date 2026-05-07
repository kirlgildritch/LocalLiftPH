<?php

use App\Support\ReviewUploadLimit;
use Illuminate\Http\Exceptions\PostTooLargeException;
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
        $exceptions->render(function (PostTooLargeException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => ReviewUploadLimit::tooLargeMessage(),
                ], 413);
            }

            return response()->view('errors.413', [
                'uploadErrorMessage' => ReviewUploadLimit::tooLargeMessage(),
                'serverUploadMax' => ReviewUploadLimit::humanSize(ReviewUploadLimit::phpUploadMaxBytes()),
                'serverPostMax' => ReviewUploadLimit::humanSize(ReviewUploadLimit::phpPostMaxBytes()),
                'reviewFileMax' => ReviewUploadLimit::humanSize(ReviewUploadLimit::appMaxFileBytes()),
            ], 413);
        });
    })
    ->create();
