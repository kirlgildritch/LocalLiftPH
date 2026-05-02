@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">
            @include('seller.partials.sidebar')

            <main class="dashboard-main">
                <section class="seller-page-panel panel profile-shell">
                    <div class="page-header">
                        <div>
                            <span class="section-kicker">Profile</span>
                            <h2>My Profile</h2>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data" class="profile-form">
                        @csrf
                        @method('PATCH')

                        <div class="profile-image-section">
                            @if($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="profile-preview">
                            @else
                                <i class="fa-regular fa-circle-user default-profile-icon"></i>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="profile_image">Profile Image</label>
                            <input type="file" name="profile_image" id="profile_image" accept="image/*">
                            @error('profile_image')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
                            @error('name')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>

                        <hr class="section-line">

                        <div class="subsection-heading">
                            <h3>Change Password</h3>
                            <p>Update your seller login details here.</p>
                        </div>

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

                        <button type="submit" class="page-action-btn">Update Profile</button>
                    </form>
                </section>
            </main>
        </div>
    </div>
</section>
@endsection
