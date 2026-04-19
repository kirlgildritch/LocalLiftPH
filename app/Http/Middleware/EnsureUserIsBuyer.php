<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsBuyer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized.');
        }

        if (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Buyers only.');
        }

        return $next($request);
    }
}