<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UseBroadcastAuthGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            Auth::shouldUse('admin');
        } elseif (Auth::guard('seller')->check()) {
            Auth::shouldUse('seller');
        } elseif (Auth::guard('web')->check()) {
            Auth::shouldUse('web');
        }

        return $next($request);
    }
}
