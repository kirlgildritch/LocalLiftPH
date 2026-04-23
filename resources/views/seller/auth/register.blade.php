@extends('layouts.seller-auth')
@section('title', 'Seller Center - Register')
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
            <span class="seller-auth-kicker">Seller Onboarding</span>
            <h1>Open your seller workspace with a dedicated account.</h1>
            <p>Create a Seller Center account first, then continue to store setup, policies, payout details, and product publishing from the seller dashboard.</p>

            <ul class="seller-auth-hero-points">
                <li><i class="fa-solid fa-check"></i><span>Separate seller access from public marketplace activity</span></li>
                <li><i class="fa-solid fa-check"></i><span>Prepare your store profile before publishing products</span></li>
                <li><i class="fa-solid fa-check"></i><span>Use seller-only tools after registration</span></li>
            </ul>
        </div>

        <div class="seller-auth-hero-foot">
            <div class="seller-auth-metric">
                <strong>Setup</strong>
                <span>Store details and verification flow</span>
            </div>
            <div class="seller-auth-metric">
                <strong>Control</strong>
                <span>Seller-only access and dashboard actions</span>
            </div>
            <div class="seller-auth-metric">
                <strong>Launch</strong>
                <span>Start listing products after onboarding</span>
            </div>
        </div>
    </aside>

    <section class="seller-auth-card">
        <div class="seller-auth-card-header">
            <h2>Create Seller Account</h2>
            <p>Register for Seller Center, then complete your shop setup in the seller workspace.</p>
        </div>

        <form method="POST" action="{{ route('seller.register.store') }}" class="seller-auth-form">
            @csrf

            <div class="seller-auth-field">
                <label for="name">Full Name</label>
                <div class="seller-auth-input">
                    <i class="fa-solid fa-user"></i>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                </div>
                @error('name')
                    <small class="seller-auth-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="seller-auth-field">
                <label for="email">Email Address</label>
                <div class="seller-auth-input">
                    <i class="fa-solid fa-envelope"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter your seller email address" required>
                </div>
                @error('email')
                    <small class="seller-auth-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="seller-auth-field">
                <label for="password">Password</label>
                <div class="seller-auth-input">
                    <i class="fa-solid fa-lock"></i>
                    <input id="password" type="password" name="password" placeholder="Create a password" required>
                </div>
                @error('password')
                    <small class="seller-auth-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="seller-auth-field">
                <label for="password_confirmation">Confirm Password</label>
                <div class="seller-auth-input">
                    <i class="fa-solid fa-lock"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Re-enter your password" required>
                </div>
            </div>

            <button type="submit" class="seller-auth-submit">Create Seller Account</button>
        </form>

        <div class="seller-auth-footer">
            Already have a seller account?
            <a href="{{ route('seller.login') }}">Log in here</a>
            <a href="{{ route('home') }}" class="seller-auth-alt-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Return to marketplace</span>
            </a>
        </div>
    </section>
</section>
@endsection
