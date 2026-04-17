@extends('layouts.app')
@section('title', 'LocalLift PH - Login')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

<section class="auth-page">
    <div class="container auth-shell">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <span>Login</span>
        </div>

        <div class="auth-layout">
            <div class="auth-card panel">
                <div class="auth-card-header">
                    <h2>Log In</h2>
                    <p>Use your email and password to access your account.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                required
                                autofocus
                            >
                        </div>
                        @error('email')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
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
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="forgot">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="auth-btn">Log In</button>
                </form>

                <div class="auth-footer">
                    Don't have an account?
                    <a href="{{ route('register') }}">Create one here</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
