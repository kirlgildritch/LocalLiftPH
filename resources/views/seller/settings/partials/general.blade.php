                        <div id="general" class="settings-tab-content active">
                            <div class="settings-card panel">
                                <h3>Shop Information</h3>
                                <form action="{{ route('seller.settings.update') }}" method="POST"
                                    enctype="multipart/form-data" data-enable-loading class="js-ph-address-form">
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label for="store_name">Shop Name</label>
                                        <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $seller->store_name ?? '') }}">
                                        @error('store_name')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="store_description">Shop Description</label>
                                        <textarea id="store_description" name="store_description" rows="4">{{ old('store_description', $seller->store_description ?? '') }}</textarea>
                                        @error('store_description')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_number">Contact Number</label>
                                        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $seller->contact_number ?? '') }}">
                                        @error('contact_number')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="seller-location-block">
                                        <div>
                                            <strong>Pickup / Store Location</strong>
                                            <p>Structured like buyer delivery addresses so LocalLift can support nearby browsing and better delivery estimates.</p>
                                        </div>
                                    </div>

                                    <p class="error-text location-feedback" data-location-feedback hidden></p>

                                    <div class="form-group">
                                        <label for="street_address">Street / Shop Address</label>
                                        <input type="text" id="street_address" name="street_address"
                                            value="{{ old('street_address', $seller->street_address ?? '') }}"
                                            placeholder="Building, unit, street, market stall, or pickup point" required>
                                        @error('street_address')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="settings-location-grid">
                                        <div class="form-group">
                                            <label for="region">Region</label>
                                            <select id="region" name="region" data-selected="{{ old('region', $seller->region ?? '') }}" required>
                                                <option value="" selected disabled>Select region</option>
                                            </select>
                                            @error('region')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="province">Province</label>
                                            <select id="province" name="province" data-selected="{{ old('province', $seller->province ?? '') }}" required>
                                                <option value="" selected disabled>Select province</option>
                                            </select>
                                            @error('province')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="city">City / Municipality</label>
                                            <select id="city" name="city" data-selected="{{ old('city', $seller->city ?? '') }}" required>
                                                <option value="" selected disabled>Select city / municipality</option>
                                            </select>
                                            @error('city')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="barangay">Barangay</label>
                                            <select id="barangay" name="barangay" data-selected="{{ old('barangay', $seller->barangay ?? '') }}" required>
                                                <option value="" selected disabled>Select barangay</option>
                                            </select>
                                            @error('barangay')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="postal_code">Postal Code</label>
                                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $seller->postal_code ?? '') }}" inputmode="numeric" required>
                                            @error('postal_code')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="landmark">Landmark</label>
                                            <input type="text" id="landmark" name="landmark" value="{{ old('landmark', $seller->landmark ?? '') }}" placeholder="Nearest mall, school, church, or main road" required>
                                            @error('landmark')<small class="error-text">{{ $message }}</small>@enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_logo">Shop Logo</label>
                                        @if(!empty($seller->shop_logo))
                                            <img src="{{ asset('storage/' . $seller->shop_logo) }}" width="80" class="shop-logo-preview">
                                        @endif
                                        <input type="file" name="shop_logo" accept="image/*">
                                        @error('shop_logo')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button type="submit" class="page-action-btn" data-enable-loading
                                        data-loading-text="Saving...">Save</button>
                                </form>
                            </div>
                        </div>
