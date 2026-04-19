@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/seller_setup.css') }}">

    <section class="seller-setup-page">
        <div class="container seller-setup-shell">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>Seller Setup</span>
            </div>



            <div class="seller-setup-card panel">
                <div class="setup-topbar">
                    <div>
                        <span class="section-kicker">Application Flow</span>
                        <h2>Complete Seller Setup</h2>
                        <p>Fill in your shop details, business information, and final confirmation before submitting your
                            application.</p>
                    </div>
                </div>

                <div class="setup-stepper">
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-copy">
                            <strong>Shop Information</strong>
                            <span>Store basics</span>
                        </div>
                    </div>

                    <div class="step-line"></div>

                    <div class="step-item" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-copy">
                            <strong>Business Information</strong>
                            <span>Verification details</span>
                        </div>
                    </div>

                    <div class="step-line"></div>

                    <div class="step-item" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-copy">
                            <strong>Submit</strong>
                            <span>Final confirmation</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('seller.setup.store') }}" method="POST" class="seller-form">
                    @csrf

                    <div class="form-step active" id="step-1">
                        <div class="step-header">
                            <h3>Shop Information</h3>
                            <p>Set the public storefront details buyers will see first.</p>
                        </div>

                        <div class="form-grid form-grid-single">
                            <div class="form-group">
                                <label for="store_name">Shop Name</label>
                                <input type="text" name="store_name" id="store_name" value="{{ old('store_name') }}"
                                    placeholder="Enter your shop name">
                                @error('store_name')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="store_description">Store Description</label>
                                <textarea name="store_description" id="store_description" rows="5"
                                    placeholder="Describe your shop">{{ old('store_description') }}</textarea>
                                @error('store_description')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="contact_number">Phone Number</label>
                                <input type="text" name="contact_number" id="contact_number"
                                    value="{{ old('contact_number') }}" placeholder="e.g. 09XXXXXXXXX">
                                @error('contact_number')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions form-actions-end">
                            <button type="button" class="action-btn primary-btn" onclick="nextStep(2)">Next</button>
                        </div>
                    </div>

                    <div class="form-step" id="step-2">
                        <div class="step-header">
                            <h3>Business Information</h3>
                            <p>Provide the business and pickup details used to support order fulfillment.</p>
                        </div>

                        <div class="form-grid">
                            <div class="form-group form-group-wide">
                                <label for="address">Pickup / Store Address</label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}"
                                    placeholder="Enter your store address">
                                @error('address')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="business_type">Business Type</label>
                                <select name="business_type" id="business_type">
                                    <option value="">Select business type</option>
                                    <option value="individual" {{ old('business_type') == 'individual' ? 'selected' : '' }}>
                                        Individual</option>
                                    <option value="reseller" {{ old('business_type') == 'reseller' ? 'selected' : '' }}>
                                        Reseller</option>
                                    <option value="small_business" {{ old('business_type') == 'small_business' ? 'selected' : '' }}>Small Business</option>
                                </select>
                                @error('business_type')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="valid_id">Valid ID / Permit Number</label>
                                <input type="text" name="valid_id" id="valid_id" value="{{ old('valid_id') }}"
                                    placeholder="Optional or required based on your setup">
                                @error('valid_id')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="action-btn secondary-btn" onclick="nextStep(1)">Back</button>
                            <button type="button" class="action-btn primary-btn" onclick="nextStep(3)">Next</button>
                        </div>
                    </div>

                    <div class="form-step" id="step-3">
                        <div class="step-header">
                            <h3>Submit</h3>
                            <p>Review your seller application before sending it for approval.</p>
                        </div>

                        <div class="review-box">
                            <h4>Application Review</h4>
                            <p>Please make sure your shop profile, contact information, and business details are accurate
                                before submitting.</p>
                        </div>

                        <label class="agreement-box" for="agree">
                            <input type="checkbox" name="agree" id="agree" value="1" {{ old('agree') ? 'checked' : '' }}>
                            <span>I confirm that the information I provided is correct and ready for seller review.</span>
                        </label>
                        @error('agree')
                            <small class="error-text">{{ $message }}</small>
                        @enderror

                        <div class="form-actions">
                            <button type="button" class="action-btn secondary-btn" onclick="nextStep(2)">Back</button>
                            <button type="submit" class="action-btn primary-btn">Submit Application</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        function nextStep(stepNumber) {
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });

            document.querySelectorAll('.step-item').forEach(item => {
                item.classList.remove('active', 'completed');
            });

            document.getElementById('step-' + stepNumber).classList.add('active');

            document.querySelectorAll('.step-item').forEach(item => {
                const itemStep = parseInt(item.getAttribute('data-step'));
                if (itemStep < stepNumber) {
                    item.classList.add('completed');
                } else if (itemStep === stepNumber) {
                    item.classList.add('active');
                }
            });
        }
    </script>
@endsection
