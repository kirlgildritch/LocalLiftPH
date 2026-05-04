<?php
// app/Http/Controllers/Auth/GoogleAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        Config::set('services.google.redirect', route('google.callback'));

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        Config::set('services.google.redirect', route('google.callback'));

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            return redirect()
                ->route('login')
                ->with('error', 'Google login failed. Please try again.');
        }

        if (!$googleUser->getEmail()) {
            return redirect()
                ->route('login')
                ->with('error', 'Google account email is required.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if ($this->isSellerAccount($user)) {
                return redirect()
                    ->route('login')
                    ->with('error', 'This Google account is registered as a seller. Please use the Seller Center login.');
            }

            $user->forceFill([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?: now(),
                'profile_image' => $user->profile_image ?: $googleUser->getAvatar(),
            ])->save();
        } else {
            $provider = Config::get('auth.guards.seller.provider');
            $sellerModel = Config::get('auth.providers.' . $provider . '.model');

            if ($sellerModel && class_exists($sellerModel) && $sellerModel !== User::class) {
                $sellerInstance = new $sellerModel();
                $sellerTable = $sellerInstance->getTable();

                $sellerExists = $sellerModel::query()
                    ->where(function ($query) use ($sellerTable, $googleUser) {
                        if (Schema::hasColumn($sellerTable, 'google_id')) {
                            $query->where('google_id', $googleUser->getId());
                        }

                        $query->orWhere('email', $googleUser->getEmail());
                    })
                    ->exists();

                if ($sellerExists) {
                    return redirect()
                        ->route('login')
                        ->with('error', 'This Google account is registered as a seller. Please use the Seller Center login.');
                }
            }

            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'password' => Str::random(64),
                'google_id' => $googleUser->getId(),
                'profile_image' => $googleUser->getAvatar(),
                'is_seller' => false,
                'is_admin' => false,
                'role' => 'buyer',
            ]);
        }

        Auth::guard('seller')->logout();
        Auth::guard('web')->login($user, true);

        request()->session()->regenerate();
        request()->session()->forget('url.intended');

        return redirect()->route('home');
    }

    private function isSellerAccount(User $user): bool
    {
        if (Schema::hasColumn($user->getTable(), 'is_seller') && (bool) $user->is_seller) {
            return true;
        }

        if (Schema::hasColumn($user->getTable(), 'role') && $user->role === 'seller') {
            return true;
        }

        return false;
    }
}