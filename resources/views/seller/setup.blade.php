@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/seller_setup.css') }}">

<div class="seller-setup-page">
    <div class="seller-setup-container">
        <div class="seller-setup-card">
            <div class="setup-left">
                <span class="setup-badge">Start Selling Today</span>
                <h1>Become a Seller</h1>
                <p>
                    Open your local shop on LocalLift PH and start selling your products
                    to nearby customers in a simple and easy way.
                </p>

                <ul class="setup-benefits">
                    <li><i class="fas fa-store"></i> Create your own store profile</li>
                    <li><i class="fas fa-box-open"></i> Add and manage products</li>
                    <li><i class="fas fa-bag-shopping"></i> Reach more local buyers</li>
                    <li><i class="fas fa-chart-line"></i> Track your shop growth</li>
                </ul>
            </div>

            <div class="setup-right">
                <h2>Seller Information</h2>

                <form action="{{ route('seller.store') }}" method="POST" class="seller-form">
                    @csrf

                    <div class="form-group">
                        <label for="store_name">Store Name</label>
                        <input type="text" name="store_name" id="store_name" value="{{ old('store_name') }}" placeholder="Enter your store name">
                        @error('store_name')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="store_description">Store Description</label>
                        <textarea name="store_description" id="store_description" rows="4" placeholder="Describe your store">{{ old('store_description') }}</textarea>
                        @error('store_description')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 09XXXXXXXXX">
                        @error('contact_number')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address">Store Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="Enter your store address">
                        @error('address')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="agree" id="agree" value="1">
                        <label for="agree">
                            I confirm that the information I provided is correct.
                        </label>
                    </div>
                    @error('agree')
                        <small class="error-text">{{ $message }}</small>
                    @enderror

                    <button type="submit" class="setup-btn">Become a Seller</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection