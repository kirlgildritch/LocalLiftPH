<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
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

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'is_seller' => true,
            'is_admin' => false,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('seller')->login($user);
        Auth::shouldUse('seller');

        return redirect()->route('seller.dashboard');
    }
}
