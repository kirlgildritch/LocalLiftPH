                        <section class="content-panel panel seller-application-panel">
                            <div class="panel-heading">
                                <div>
                                    <span class="section-kicker">Seller Registration</span>
                                    <h2>Complete your seller application</h2>
                                </div>
                            </div>

                            @if ($latestDocumentRequest && $latestDocumentRequest->status === \App\Models\SellerDocumentRequest::STATUS_PENDING)
                                <div class="status-card-grid">
                                    <article class="status-card panel">
                                        <strong>{{ $requestReasonLabel }}</strong>
                                        <p>{{ $latestDocumentRequest->admin_notes ?: 'Additional verification document requested.' }}</p>
                                    </article>
                                    <article class="status-card panel">
                                        <strong>Requested</strong>
                                        <p>{{ optional($latestDocumentRequest->requested_at)->format('M d, Y h:i A') ?: 'N/A' }}</p>
                                    </article>
                                </div>
                            @endif

                            <form action="{{ route('seller.dashboard.application.store') }}" method="POST"
                                enctype="multipart/form-data" class="seller-application-form js-ph-address-form">
                                @csrf

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="seller_type">Seller Type</label>
                                        <select name="seller_type" id="seller_type" required>
                                            <option value="">Select seller type</option>
                                            <option value="individual" {{ old('seller_type', $seller?->seller_type) === 'individual' ? 'selected' : '' }}>Small Seller /
                                                Individual Seller</option>
                                            <option value="registered_business" {{ old('seller_type', $seller?->seller_type) === 'registered_business' ? 'selected' : '' }}>Registered
                                                Business / Enterprise</option>
                                        </select>
                                        @error('seller_type')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="full_name">Full Name</label>
                                        <input type="text" id="full_name" name="full_name"
                                            value="{{ old('full_name', $seller?->full_name ?? auth('seller')->user()?->name) }}"
                                            required>
                                        @error('full_name')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="age">Age</label>
                                        <input type="number" id="age" name="age" min="18"
                                            value="{{ old('age', $seller?->age) }}" required>
                                        @error('age')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="text" id="phone_number" name="phone_number"
                                            value="{{ old('phone_number', $seller?->contact_number ?? auth('seller')->user()?->phone) }}"
                                            required>
                                        @error('phone_number')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email"
                                            value="{{ old('email', $seller?->email ?? auth('seller')->user()?->email) }}"
                                            required>
                                        @error('email')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group form-group-wide seller-location-intro">
                                        <strong>Pickup / Store Location</strong>
                                        <span>Used for seller verification, delivery estimates, and future local browsing filters.</span>
                                    </div>

                                    <p class="error-text location-feedback form-group-wide" data-location-feedback hidden></p>

                                    <div class="form-group form-group-wide">
                                        <label for="street_address">Street / Shop Address</label>
                                        <input type="text" id="street_address" name="street_address"
                                            value="{{ old('street_address', $seller?->street_address ?? '') }}"
                                            placeholder="Building, unit, street, market stall, or pickup point"
                                            required>
                                        @error('street_address')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="region">Region</label>
                                        <select name="region" id="region" data-selected="{{ old('region', $seller?->region) }}" required>
                                            <option value="" selected disabled>Select region</option>
                                        </select>
                                        @error('region')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="province">Province</label>
                                        <select name="province" id="province" data-selected="{{ old('province', $seller?->province) }}" required>
                                            <option value="" selected disabled>Select province</option>
                                        </select>
                                        @error('province')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="city">City / Municipality</label>
                                        <select name="city" id="city" data-selected="{{ old('city', $seller?->city) }}" required>
                                            <option value="" selected disabled>Select city / municipality</option>
                                        </select>
                                        @error('city')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="barangay">Barangay</label>
                                        <select name="barangay" id="barangay" data-selected="{{ old('barangay', $seller?->barangay) }}" required>
                                            <option value="" selected disabled>Select barangay</option>
                                        </select>
                                        @error('barangay')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" id="postal_code" name="postal_code"
                                            value="{{ old('postal_code', $seller?->postal_code) }}"
                                            inputmode="numeric" required>
                                        @error('postal_code')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="landmark">Landmark</label>
                                        <input type="text" id="landmark" name="landmark"
                                            value="{{ old('landmark', $seller?->landmark) }}"
                                            placeholder="Nearest mall, school, church, or main road" required>
                                        @error('landmark')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="valid_id_type">Valid ID Type</label>
                                        <select name="valid_id_type" id="valid_id_type" required>
                                            <option value="">Select valid ID</option>
                                            @foreach (['Passport', 'National ID', 'Driver\'s License', 'UMID', 'PhilHealth ID', 'Postal ID'] as $idType)
                                                <option value="{{ $idType }}" {{ old('valid_id_type', $seller?->valid_id_type) === $idType ? 'selected' : '' }}>{{ $idType }}</option>
                                            @endforeach
                                        </select>
                                        @error('valid_id_type')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="valid_id_number">Valid ID Number</label>
                                        <input type="text" id="valid_id_number" name="valid_id_number"
                                            value="{{ old('valid_id_number', $seller?->valid_id_number) }}" required>
                                        @error('valid_id_number')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="valid_id_document">Upload Valid ID / Passport</label>
                                        <input type="file" id="valid_id_document" name="valid_id_document"
                                            accept=".jpg,.jpeg,.png,.pdf,.webp">
                                        @if ($seller?->valid_id_path)
                                            <small class="muted-label">Existing file uploaded. Upload a new one only if you want to
                                                replace it.</small>
                                        @endif
                                        @error('valid_id_document')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="business_permit">Business Permit</label>
                                        <input type="file" id="business_permit" name="business_permit"
                                            accept=".jpg,.jpeg,.png,.pdf,.webp">
                                        <small class="muted-label">Optional for individual sellers. Required for registered
                                            businesses.</small>
                                        @if ($seller?->business_permit_path)
                                            <small class="muted-label">Existing permit uploaded. Upload a new one only if you want
                                                to replace it.</small>
                                        @endif
                                        @error('business_permit')<small class="error-text">{{ $message }}</small>@enderror
                                    </div>

                                    @if ($latestDocumentRequest && $latestDocumentRequest->status === \App\Models\SellerDocumentRequest::STATUS_PENDING)
                                        <div class="form-group form-group-wide">
                                            <label for="requested_document">Requested Document</label>
                                            <input type="file" id="requested_document" name="requested_document"
                                                accept=".jpg,.jpeg,.png,.pdf,.webp">
                                            <small class="muted-label">{{ $requestReasonLabel }}</small>
                                            @error('requested_document')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>
                                    @endif
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="page-action-btn">
                                        {{ $latestDocumentRequest && $latestDocumentRequest->status === \App\Models\SellerDocumentRequest::STATUS_PENDING ? 'Upload and Resubmit' : 'Submit Application' }}
                                    </button>
                                </div>
                            </form>
                        </section>
