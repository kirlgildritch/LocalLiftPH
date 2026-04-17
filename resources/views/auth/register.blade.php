@extends('layouts.app')
@section('title', 'LocalLift PH - Register')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

<section class="auth-page">
    <div class="container auth-shell">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <span>Register</span>
        </div>

        <div class="auth-layout">
            <div class="auth-card panel">
                <div class="auth-card-header">
                    <h2>Create Account</h2>
                    <p>Fill out the form below to start buying or selling on LocalLift PH.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter your full name"
                                required
                            >
                        </div>
                        @error('name')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

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
                            >
                        </div>
                        @error('email')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="role">Register As</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user-tag"></i>
                            <select id="role" name="role" required>
                                <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
                                <option value="seller" {{ old('role') == 'seller' ? 'selected' : '' }}>Seller</option>
                            </select>
                        </div>
                        @error('role')
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
                                placeholder="Create a password"
                                required
                            >
                        </div>
                        @error('password')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                placeholder="Re-enter your password"
                                required
                            >
                        </div>
                    </div>

                    <div class="terms">
                        <input type="checkbox" checked>
                        <span>I agree to the <a href="#">Terms and Conditions</a></span>
                    </div>

                    <button type="submit" class="auth-btn">Create Account</button>
                </form>

                <div class="auth-footer">
                    Already have an account?
                    <a href="{{ route('login') }}">Log in here</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
