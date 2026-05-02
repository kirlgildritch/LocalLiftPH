<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Seller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (!Auth::guard('seller')->check()) {
            return redirect()->route('seller.login');
        }

        Auth::shouldUse('seller');

        $user = Auth::guard('seller')->user();
        $seller = Seller::where('user_id', $user->id)->first();

        if ($seller?->isSuspended()) {
            $allowedRoutes = [
                'seller.dashboard',
                'seller.logout',
                'seller.profile',
                'seller.profile.update',
            ];

            if (! in_array($request->route()?->getName(), $allowedRoutes, true)) {
                return redirect()
                    ->route('seller.dashboard')
                    ->with('error', 'Seller account suspended.');
            }
        }

        return $next($request);
    }
}
