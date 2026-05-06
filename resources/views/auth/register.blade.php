@extends('layouts.log')
@section('title', 'LocalLift PH - Register')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

    <style>
        .google-auth-btn {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.12);
            background: #ffffff;
            color: #1f2937;
            font-weight: 700;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .google-auth-btn:hover {
            background: #f9fafb;
            color: #111827;
            transform: translateY(-1px);
        }

        .google-auth-btn i {
            font-size: 18px;
            color: #ea4335;
        }

        .auth-alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
        }
    </style>

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

                <a href="{{ route('home') }}" class="auth-help">Back to Home</a>
            </div>
        </div>

        <div class="auth-hero">
            <div class="container auth-shell">
                <div class="auth-promo">
                    <span class="promo-badge">Join LocalLift</span>
                    <h1>Create your account and start shopping local.</h1>
                    <p>Sign up to discover trusted local sellers, save products, place orders, and support nearby
                        businesses.</p>

                    <div class="promo-highlights">
                        <span><i class="fa-solid fa-store"></i> Local shops</span>
                        <span><i class="fa-solid fa-shield-heart"></i> Secure account</span>
                        <span><i class="fa-solid fa-bag-shopping"></i> Easy checkout</span>
                    </div>
                </div>

                <div class="auth-card panel">
                    <div class="auth-card-header">
                        <h2>Sign Up</h2>
                    </div>

                    @if (session('error'))
                        <div class="auth-alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <a href="{{ route('google.login') }}" class="google-auth-btn">
                        <i class="fa-brands fa-google"></i>
                        Continue with Google
                    </a>

                    <div class="auth-divider">
                        <span></span>
                        <small>OR</small>
                        <span></span>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="auth-form" data-enable-loading>
                        @csrf

                        <div class="input-group">
                            <div class="input-wrap">
                                <i class="fa-solid fa-user"></i>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Full Name"
                                    required>
                            </div>
                            @error('name')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="input-group">
                            <div class="input-wrap">
                                <i class="fa-solid fa-envelope"></i>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    placeholder="Email Address" required>
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

                        <div class="input-group">
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    placeholder="Confirm Password" required>
                            </div>
                        </div>

                        <div class="terms">
                            <input type="checkbox" checked required>
                            <span>I agree to the <a href="#">Terms and Conditions</a></span>
                        </div>

                        <button type="submit" class="auth-btn" data-enable-loading
                            data-loading-text="Creating Account...">Create Account</button>
                    </form>

                    <div class="auth-footer">
                        Already have an account?
                        <a href="{{ route('login') }}">Log In</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection