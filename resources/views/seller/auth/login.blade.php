@extends('layouts.seller-auth')
@section('title', 'Seller Center - Login')

@section('content')
    <section class="seller-auth-page">
        <div class="seller-auth-topbar">
            <a href="{{ route('home') }}" class="seller-auth-brand">
                <div class="seller-auth-brand-mark">
                    <img src="{{ asset('assets/image/Logo.png') }}" alt="Logo">
                </div>
                <div class="seller-auth-brand-copy">
                    <strong>LocalLift Seller Center</strong>
                    <span>PH</span>
                </div>
            </a>

            <a href="{{ route('home') }}" class="seller-auth-help">Need Help?</a>
        </div>

        <div class="seller-auth-shell">
            <aside class="seller-auth-hero">
                <span class="seller-auth-kicker">Seller Entry Point</span>
                <h1>Manage your shop with confidence.</h1>
                <p>Access your seller dashboard, product listings, orders, and store tools in one dedicated workspace.</p>

                <div class="seller-auth-hero-points">
                    <span><i class="fa-solid fa-box"></i> Product tools</span>
                    <span><i class="fa-solid fa-truck-fast"></i> Order tracking</span>
                    <span><i class="fa-solid fa-store"></i> Seller workspace</span>
                </div>
            </aside>

            <section class="seller-auth-card">
                <div class="seller-auth-card-header">
                    <h2>Seller Login</h2>
                </div>

                @if (session('error'))
                    <div
                        style="margin-bottom: 16px; padding: 12px 14px; border-radius: 12px; background: #fee2e2; color: #991b1b; font-size: 14px; font-weight: 600;">
                        {{ session('error') }}
                    </div>
                @endif

                <a href="{{ route('seller.google.login') }}"
                    style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 12px 16px; border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.12); background: #ffffff; color: #1f2937; font-weight: 700; text-decoration: none; transition: 0.2s ease;">
                    <i class="fa-brands fa-google" style="font-size: 18px; color: #ea4335;"></i>
                    Continue with Google
                </a>

                <div class="seller-auth-divider">
                    <span></span>
                    <small>OR</small>
                    <span></span>
                </div>

                <form method="POST" action="{{ route('seller.login.store') }}" class="seller-auth-form">
                    @csrf

                    <div class="seller-auth-field">
                        <div class="seller-auth-input">
                            <i class="fa-solid fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="Email Address" required autofocus>
                        </div>
                        @error('email')
                            <small class="seller-auth-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="seller-auth-field">
                        <div class="seller-auth-input">
                            <i class="fa-solid fa-lock"></i>
                            <input id="password" type="password" name="password" placeholder="Password" required>
                        </div>
                        @error('password')
                            <small class="seller-auth-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="seller-auth-submit">Log In</button>
                </form>

                <div class="seller-auth-footer">
                    Need a seller account?
                    <a href="{{ route('seller.register') }}">Create Account</a>
                </div>
            </section>
        </div>
    </section>
@endsection