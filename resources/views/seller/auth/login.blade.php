@extends('layouts.app')
@section('title', 'Seller Center - Login')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

<section class="auth-page">
    <div class="container auth-shell">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <span>Seller Center Login</span>
        </div>

        <div class="auth-layout">
            <div class="auth-card panel">
                <div class="auth-card-header">
                    <h2>Seller Center</h2>
                    <p>Log in to manage products, orders, earnings, and seller messages.</p>
                </div>

                <form method="POST" action="{{ route('seller.login.store') }}" class="auth-form">
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
                                placeholder="Enter your seller email"
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

                    <button type="submit" class="auth-btn">Log In to Seller Center</button>
                </form>

                <div class="auth-footer">
                    Need a seller account?
                    <a href="{{ route('seller.register') }}">Create Seller Center account</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
