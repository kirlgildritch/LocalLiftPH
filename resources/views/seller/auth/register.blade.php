@extends('layouts.seller-auth')
@section('title', 'Seller Center - Register')

@section('content')
    <section class="seller-auth-page">
        <div class="seller-auth-topbar">
            <a href="{{ route('home') }}" class="seller-auth-brand">
                <div class="seller-auth-brand-mark">
                    <img src="{{ asset('assets/image/Logo.png') }}" alt="">
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
                <span class="seller-auth-kicker">Seller Onboarding</span>
                <h1>Start selling with LocalLift.</h1>
                <p>Create your seller account, set up your shop, publish products, and manage orders from your Seller
                    Center.</p>

                <div class="seller-auth-hero-points">
                    <span><i class="fa-solid fa-store"></i> Shop setup</span>
                    <span><i class="fa-solid fa-box"></i> Product listings</span>
                    <span><i class="fa-solid fa-chart-line"></i> Seller tools</span>
                </div>
            </aside>

            <section class="seller-auth-card">
                <div class="seller-auth-card-header">
                    <h2>Create Seller Account</h2>
                </div>

                <form method="POST" action="{{ route('seller.register.store') }}" class="seller-auth-form">
                    @csrf

                    <div class="seller-auth-field">
                        <div class="seller-auth-input">
                            <i class="fa-solid fa-user"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Full Name"
                                required>
                        </div>
                        @error('name')
                            <small class="seller-auth-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="seller-auth-field">
                        <div class="seller-auth-input">
                            <i class="fa-solid fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="Email Address" required>
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

                    <div class="seller-auth-field">
                        <div class="seller-auth-input">
                            <i class="fa-solid fa-lock"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="Confirm Password" required>
                        </div>
                    </div>

                    <button type="submit" class="seller-auth-submit">Create Account</button>
                </form>

                <div class="seller-auth-divider">
                    <span></span>
                    <small>OR</small>
                    <span></span>
                </div>

                <div class="seller-auth-footer">
                    Already have a seller account?
                    <a href="{{ route('seller.login') }}">Log In</a>
                </div>
            </section>
        </div>
    </section>
@endsection