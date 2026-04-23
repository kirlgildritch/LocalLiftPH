@extends('layouts.seller-auth')
@section('title', 'Seller Center - Login')
@section('content')
<section class="seller-auth-shell">
    <aside class="seller-auth-hero">
        <div class="seller-auth-brand">
            <div class="seller-auth-brand-mark">
                <i class="fa-solid fa-store"></i>
            </div>
            <div class="seller-auth-brand-copy">
                <strong>LocalLift Seller Center</strong>
                <span>Dedicated Workspace</span>
            </div>
        </div>

        <div class="seller-auth-hero-copy">
            <span class="seller-auth-kicker">Seller Entry Point</span>
            <h1>Run your shop away from the buyer side.</h1>
            <p>Seller Center has its own login flow, dashboard, catalog tools, and operational workspace so managing the business stays separate from public marketplace browsing.</p>

            <ul class="seller-auth-hero-points">
                <li><i class="fa-solid fa-check"></i><span>Manage listings, orders, and earnings in one place</span></li>
                <li><i class="fa-solid fa-check"></i><span>Use a seller-only authentication flow and dashboard</span></li>
                <li><i class="fa-solid fa-check"></i><span>Complete shop setup after sign in if your store is not ready yet</span></li>
            </ul>
        </div>

        <div class="seller-auth-hero-foot">
            <div class="seller-auth-metric">
                <strong>Catalog</strong>
                <span>Listings and inventory controls</span>
            </div>
            <div class="seller-auth-metric">
                <strong>Orders</strong>
                <span>Track fulfillment and customer activity</span>
            </div>
            <div class="seller-auth-metric">
                <strong>Payouts</strong>
                <span>Review earnings and store settings</span>
            </div>
        </div>
    </aside>

    <section class="seller-auth-card">
        <div class="seller-auth-card-header">
            <h2>Seller Login</h2>
            <p>Log in to access your seller dashboard and continue managing your shop.</p>
        </div>

        @if (session('seller_center_notice'))
            <div class="seller-auth-notice">{{ session('seller_center_notice') }}</div>
        @endif

        <form method="POST" action="{{ route('seller.login.store') }}" class="seller-auth-form">
            @csrf

            <div class="seller-auth-field">
                <label for="email">Email Address</label>
                <div class="seller-auth-input">
                    <i class="fa-solid fa-envelope"></i>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your seller email"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <small class="seller-auth-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="seller-auth-field">
                <label for="password">Password</label>
                <div class="seller-auth-input">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                </div>
                @error('password')
                    <small class="seller-auth-error">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="seller-auth-submit">Log In to Seller Center</button>
        </form>

        <div class="seller-auth-footer">
            Need a seller account?
            <a href="{{ route('seller.register') }}">Create Seller Center account</a>
            <a href="{{ route('home') }}" class="seller-auth-alt-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Return to marketplace</span>
            </a>
        </div>
    </section>
</section>
@endsection
