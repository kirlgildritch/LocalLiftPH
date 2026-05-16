<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\RegisterSellerAccountRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SellerRegisteredUserController extends Controller
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

        return view('seller.auth.register');
    }

    public function store(RegisterSellerAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_seller' => true,
            'is_admin' => false,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('seller')->login($user);
        Auth::shouldUse('seller');

        return redirect()->route('seller.dashboard');
    }
}
