@extends('layouts.log')
@section('title', 'LocalLift PH - Login')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

    <section class="auth-page">
        <div class="auth-topbar">
            <div class="container auth-topbar-inner">
                <a href="{{ route('home') }}" class="auth-brand">
                    <img src="{{ asset('assets/image/Logo.png') }}" alt="LocalLift Logo">
                    <div>
                        <strong>LocalLift</strong>
                        <span>PH</span>
                    </div>
                </a>

                <a href="{{ route('home') }}" class="auth-help">Need help?</a>
            </div>
        </div>

        <div class="auth-hero">
            <div class="container auth-shell">
                <div class="auth-promo">
                    <span class="promo-badge">Local marketplace</span>
                    <h1>Shop local products with ease.</h1>
                    <p>Discover trusted sellers, support local businesses, and enjoy a cleaner marketplace experience.</p>

                    <div class="promo-highlights">
                        <span><i class="fa-solid fa-store"></i> Local sellers</span>
                        <span><i class="fa-solid fa-shield-heart"></i> Trusted shops</span>
                        <span><i class="fa-solid fa-bag-shopping"></i> Easy shopping</span>
                    </div>
                </div>

                <div class="auth-card panel">
                    <div class="auth-card-header">
                        <h2>Log In</h2>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="auth-form">
                        @csrf

                        <div class="input-group">
                            <div class="input-wrap">
                                <i class="fa-solid fa-envelope"></i>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    placeholder="Email Address" required autofocus>
                            </div>
                            @error('email')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="input-group">
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input id="password" type="password" name="password" placeholder="Password" required>
                            </div>
                            @error('password')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="auth-btn">Log In</button>

                        <div class="forgot">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Forgot password?</a>
                            @endif
                        </div>
                    </form>

                    <div class="auth-divider">
                        <span></span>
                        <small>OR</small>
                        <span></span>
                    </div>

                    <div class="auth-footer">
                        New to LocalLift?
                        <a href="{{ route('register') }}">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection