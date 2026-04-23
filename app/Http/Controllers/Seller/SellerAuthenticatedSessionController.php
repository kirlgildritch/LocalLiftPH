<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller as SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerAuthenticatedSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('seller')->check()) {
            return redirect()->route('seller.dashboard');
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return view('seller.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('seller')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::guard('seller')->user();

        if (! $user?->isSeller() || $user->isAdmin()) {
            Auth::guard('seller')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('seller.login')
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => trans('auth.failed'),
                ]);
        }

        Auth::shouldUse('seller');

        return redirect()->route('seller.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('seller')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.login');
    }
}
