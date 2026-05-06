<?php
// app/Http/Controllers/Seller/SellerGoogleAuthController.php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SellerGoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        Config::set('services.google.redirect', route('seller.google.callback'));

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        Config::set('services.google.redirect', route('seller.google.callback'));

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            return redirect()
                ->route('seller.login')
                ->with('error', 'Google seller login failed. Please try again.');
        }

        if (!$googleUser->getEmail()) {
            return redirect()
                ->route('seller.login')
                ->with('error', 'Google account email is required.');
        }

        $provider = Config::get('auth.guards.seller.provider');
        $sellerModel = Config::get('auth.providers.' . $provider . '.model');

        if (!$sellerModel || !class_exists($sellerModel)) {
            return redirect()
                ->route('seller.login')
                ->with('error', 'Seller Google login is not configured correctly.');
        }

        $sellerInstance = new $sellerModel();
        $sellerTable = $sellerInstance->getTable();

        $seller = $sellerModel::query()
            ->where(function ($query) use ($sellerTable, $googleUser) {
                if (Schema::hasColumn($sellerTable, 'google_id')) {
                    $query->where('google_id', $googleUser->getId());
                }

                $query->orWhere('email', $googleUser->getEmail());
            })
            ->first();

        $buyerExists = User::where('email', $googleUser->getEmail())->exists();

        if ($seller) {
            $isSellerAccount = true;

            if (Schema::hasColumn($sellerTable, 'is_seller')) {
                $isSellerAccount = (bool) $seller->is_seller;
            }

            if (Schema::hasColumn($sellerTable, 'role')) {
                $isSellerAccount = $isSellerAccount || $seller->role === 'seller';
            }

            if (!$isSellerAccount) {
                return redirect()
                    ->route('seller.login')
                    ->with('error', 'This Google account is registered as a buyer. Please apply as a seller first.');
            }

            $updates = [];

            if (Schema::hasColumn($sellerTable, 'google_id') && empty($seller->google_id)) {
                $updates['google_id'] = $googleUser->getId();
            }

            if (Schema::hasColumn($sellerTable, 'email_verified_at') && empty($seller->email_verified_at)) {
                $updates['email_verified_at'] = now();
            }

            if (Schema::hasColumn($sellerTable, 'profile_image') && empty($seller->profile_image)) {
                $updates['profile_image'] = $googleUser->getAvatar();
            }

            if (Schema::hasColumn($sellerTable, 'avatar') && empty($seller->avatar)) {
                $updates['avatar'] = $googleUser->getAvatar();
            }

            if (!empty($updates)) {
                $seller->forceFill($updates)->save();
            }
        } else {
            if ($buyerExists) {
                return redirect()
                    ->route('seller.login')
                    ->with('error', 'This Google account is registered as a buyer. Please apply as a seller first.');
            }

            $data = [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google Seller',
                'email' => $googleUser->getEmail(),
            ];

            if (Schema::hasColumn($sellerTable, 'password')) {
                $data['password'] = Hash::make(Str::random(64));
            }

            if (Schema::hasColumn($sellerTable, 'google_id')) {
                $data['google_id'] = $googleUser->getId();
            }

            if (Schema::hasColumn($sellerTable, 'email_verified_at')) {
                $data['email_verified_at'] = now();
            }

            if (Schema::hasColumn($sellerTable, 'profile_image')) {
                $data['profile_image'] = $googleUser->getAvatar();
            }

            if (Schema::hasColumn($sellerTable, 'avatar')) {
                $data['avatar'] = $googleUser->getAvatar();
            }

            if (Schema::hasColumn($sellerTable, 'shop_name')) {
                $data['shop_name'] = ($googleUser->getName() ?: 'Google Seller') . ' Shop';
            }

            if (Schema::hasColumn($sellerTable, 'is_seller')) {
                $data['is_seller'] = true;
            }

            if (Schema::hasColumn($sellerTable, 'role')) {
                $data['role'] = 'seller';
            }

            if (Schema::hasColumn($sellerTable, 'status')) {
                $data['status'] = 'pending';
            }

            $seller = new $sellerModel();
            $seller->forceFill($data)->save();
        }

        Auth::guard('web')->logout();
        Auth::guard('seller')->login($seller, true);

        request()->session()->regenerate();
        request()->session()->forget('url.intended');

        return redirect()->route('seller.dashboard');
    }
}