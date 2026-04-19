@extends('layouts.app')
@section('title', 'Seller Center - Register')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

<section class="auth-page">
    <div class="container auth-shell">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <span>Seller Center Register</span>
        </div>

        <div class="auth-layout">
            <div class="auth-card panel">
                <div class="auth-card-header">
                    <h2>Create Seller Account</h2>
                    <p>Create a separate Seller Center account, then complete your shop setup.</p>
                </div>

                <form method="POST" action="{{ route('seller.register.store') }}" class="auth-form">
                    @csrf

                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="Enter your full name" required>
                        </div>
                        @error('name')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="Enter your seller email address" required>
                        </div>
                        @error('email')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input id="password" type="password" name="password" placeholder="Create a password"
                                required>
                        </div>
                        @error('password')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="Re-enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">Create Seller Account</button>
                </form>

                <div class="auth-footer">
                    Already have a seller account?
                    <a href="{{ route('seller.login') }}">Log in here</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
