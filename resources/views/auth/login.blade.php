@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">

<section class="auth">
     
    <div class="auth-container">
     <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Login</span>
    </div>
       

        <div class="auth-card">
            <h2>Welcome Back</h2>
            <p>Log in to your LocalLift PH account</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <label for="email"><i class="fa-solid fa-user"></i> Email Address</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address..."
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
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
                            placeholder="••••••••"
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

                <button type="submit" class="auth-btn">LOG IN</button>
            </form>

            <div class="auth-footer">
                Don't have an account?
                <a href="{{ route('register') }}">Register here</a>
            </div>
        </div>

    </div>
</section>
@endsection