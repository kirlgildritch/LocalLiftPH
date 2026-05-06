@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_addresses.css') }}">

    <section class="address-page">
        <div class="container address-shell">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <a href="{{ route('buyer.addresses', array_filter(['return_to' => $returnTo ?? null])) }}">Addresses</a>
                <span>&gt;</span>
                <span>New Address</span>
            </div>

            <form action="{{ route('buyer.addresses.store') }}" method="POST"
                class="address-form-shell address-form js-ph-address-form">
                @csrf
                @if(!empty($returnTo))
                    <input type="hidden" name="return_to" value="{{ $returnTo }}">
                @endif

                <div class="form-panel panel">
                    <div class="form-heading">
                        <div>
                            <span class="section-kicker">Address Book</span>
                            <h2>Add Delivery Address</h2>

                        </div>

                    </div>

                    @if(session('success'))
                        <div class="success-message">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="error-summary" role="alert">
                            <strong>Please review the required address details.</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-section">
                        <div class="form-section-header">
                            <div>
                                <h3>Recipient Details</h3>

                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="full_name">Full Name <span class="required-dot">*</span></label>
                                <input id="full_name" type="text" name="full_name" placeholder="Juan Dela Cruz"
                                    value="{{ old('full_name', auth()->user()->name) }}" required>
                                @error('full_name')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number <span class="required-dot">*</span></label>
                                <input id="phone" type="tel" name="phone" placeholder="09XXXXXXXXX"
                                    value="{{ old('phone') }}" inputmode="numeric" required>
                                @error('phone')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-header">
                            <div>
                                <h3>Location</h3>

                            </div>
                        </div>

                        <p class="field-error location-feedback" data-location-feedback hidden></p>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="region">Region <span class="required-dot">*</span></label>
                                <select id="region" name="region" data-selected="{{ old('region') }}" required>
                                    <option value="" selected disabled>Select region</option>
                                </select>
                                @error('region')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="province">Province <span class="required-dot">*</span></label>
                                <select id="province" name="province" data-selected="{{ old('province') }}" required>
                                    <option value="" selected disabled>Select province</option>
                                </select>
                                @error('province')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="city">City / Municipality <span class="required-dot">*</span></label>
                                <select id="city" name="city" data-selected="{{ old('city') }}" required>
                                    <option value="" selected disabled>Select city / municipality</option>
                                </select>
                                @error('city')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="barangay">Barangay <span class="required-dot">*</span></label>
                                <select id="barangay" name="barangay" data-selected="{{ old('barangay') }}" required>
                                    <option value="" selected disabled>Select barangay</option>
                                </select>
                                @error('barangay')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-header">
                            <div>
                                <h3>Address Details</h3>

                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group form-group--full">
                                <label for="street_address">Street Address <span class="required-dot">*</span></label>
                                <textarea id="street_address" name="street_address" rows="3"
                                    placeholder="House number, building, street name"
                                    required>{{ old('street_address') }}</textarea>
                                @error('street_address')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="postal_code">Postal Code <span class="required-dot">*</span></label>
                                <input id="postal_code" type="text" name="postal_code" placeholder="e.g. 8000"
                                    value="{{ old('postal_code') }}" inputmode="numeric" required>
                                @error('postal_code')<small class="field-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="landmark">Landmark <span class="required-dot">*</span></label>
                                <input id="landmark" type="text" name="landmark" placeholder="Near school, church, mall"
                                    value="{{ old('landmark') }}" required>
                                @error('landmark')<small class="field-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="default-row">
                            <div>
                                <span>Set as default address</span>

                            </div>
                            <label class="switch">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="label-row">
                            <div>
                                <span>Label as</span>
                                <p class="field-note">Optional, but helpful when you save multiple delivery addresses.</p>
                            </div>

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

                    <div class="form-actions">
                        <a href="{{ route('buyer.addresses', array_filter(['return_to' => $returnTo ?? null])) }}"
                            class="action-btn secondary-btn">
                            Cancel
                        </a>
                        <button type="submit" class="action-btn primary-btn save-btn">Save Address</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="{{ asset('assets/js/buyer-address-form.js') }}"></script>
@endsection