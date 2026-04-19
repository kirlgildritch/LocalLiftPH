<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            if (Auth::guard('seller')->check()) {
                return redirect()->route('seller.dashboard');
            }

            if (Auth::guard('web')->check()) {
                return redirect()->route('home');
            }

            return redirect()->route('admin.login');
        }

        Auth::shouldUse('admin');

        return $next($request);
    }
}
