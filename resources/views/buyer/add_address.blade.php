@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/buyer_addresses.css') }}">

<section class="address-page">
    <div class="container address-shell">
        @php($returnTo = request('return_to'))

        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <a href="{{ route('buyer.addresses') }}">Addresses</a>
            <span>&gt;</span>
            <span>New Address</span>
        </div>



        <form action="{{ route('buyer.addresses.store') }}" method="POST" class="address-form-shell">
            @csrf

            <div class="form-panel panel">
                <h2>Delivery Information</h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input id="full_name" type="text" name="full_name" placeholder="Full Name"
                            value="{{ old('full_name', auth()->user()->name) }}">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input id="phone" type="text" name="phone" placeholder="Phone Number"
                            value="{{ old('phone') }}">
                    </div>

                    <div class="form-group">
                        <label for="region">Region</label>
                        <input id="region" type="text" name="region" placeholder="Region" value="{{ old('region') }}">
                    </div>

                    <div class="form-group">
                        <label for="province">Province</label>
                        <input id="province" type="text" name="province" placeholder="Province"
                            value="{{ old('province') }}">
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input id="city" type="text" name="city" placeholder="City" value="{{ old('city') }}">
                    </div>

                    <div class="form-group">
                        <label for="barangay">Barangay</label>
                        <input id="barangay" type="text" name="barangay" placeholder="Barangay"
                            value="{{ old('barangay') }}">
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input id="postal_code" type="text" name="postal_code" placeholder="Postal Code"
                            value="{{ old('postal_code') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="street_address">Street Address</label>
                    <textarea id="street_address" name="street_address" rows="3"
                        placeholder="Street name, building, house number">{{ old('street_address') }}</textarea>
                </div>
                <br>

                <div class="modal-section">
                    <div class="default-row">
                        <span>Set as default address</span>
                        <label class="switch">
                            <input type="checkbox" name="is_default" value="1">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <br>
                <div class="modal-section">
                    <div class="label-row">
                        <span>Label as</span>
                        <div class="label-buttons">
                            <label>
                                <input type="radio" name="label" value="Home" {{ old('label') == 'Home' ? 'checked' : '' }}>
                                <span>Home</span>
                            </label>

                            <label>
                                <input type="radio" name="label" value="Work" {{ old('label') == 'Work' ? 'checked' : '' }}>
                                <span>Work</span>
                            </label>

                            <label>
                                <input type="radio" name="label" value="Other" {{ old('label') == 'Other' ? 'checked' : '' }}>
                                <span>Other</span>
                            </label>
                        </div>
                    </div>
                </div>
                <br>
                <button type="submit" class="action-btn primary-btn full-btn">Save Address</button>
            </div>


        </form>
    </div>
</section>
@endsection