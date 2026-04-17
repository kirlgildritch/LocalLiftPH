@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/buyer_profile.css') }}">

<div class="buyer-profile-page">
    <div class="container">
        <div class="buyer-profile-card">
            <h2>My Profile</h2>
            <div class="divider"></div>

            @if(session('success'))
                <p class="success-message">{{ session('success') }}</p>
            @endif

            <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="profile-image-section">
                    @if(auth()->user()->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="profile-preview">
                    @else
                        <i class="fa-regular fa-circle-user default-profile-icon"></i>
                    @endif
                </div>

                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}">
                    @error('name')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}">
                    @error('email')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                    @error('phone')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" rows="3">{{ old('address', auth()->user()->address ?? '') }}</textarea>
                    @error('address')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <hr class="section-line">

                <h4>Change Password</h4>

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password">
                    @error('current_password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password">
                    @error('password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation">
                </div>

                <button type="submit" class="save-btn">Update Profile</button>
            </form>
        </div>
    </div>
</div>
@endsection