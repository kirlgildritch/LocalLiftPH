@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

<section class="auth">
    <div class="auth-container">
        <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Register</span>
    </div>
      

        <div class="auth-card">
            <h2>Create Your Account</h2>
            <p>Sign up to start buying and selling local products</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="input-group">
                    <label for="name"><i class="fa-solid fa-user"></i> Full Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user"></i>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Enter your full name..."
                            required
                        >
                    </div>
                    @error('name')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address..."
                            required
                        >
                    </div>
                    @error('email')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="role"><i class="fa-solid fa-user-tag"></i> Register As</label>
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
                    <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Create a password..."
                            required
                        >
                    </div>
                    @error('password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="password_confirmation"><i class="fa-solid fa-rotate"></i> Confirm Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Re-enter your password..."
                            required
                        >
                    </div>
                </div>

                <div class="terms">
                    <input type="checkbox" checked>
                    <span>I agree to the <a href="#">Terms & Conditions</a></span>
                </div>

                <button type="submit" class="auth-btn">SIGN UP</button>
            </form>

            <div class="auth-footer">
                Already have an account?
                <a href="{{ route('login') }}">Log in here</a>
            </div>
        </div>

    </div>
</section>
@endsection