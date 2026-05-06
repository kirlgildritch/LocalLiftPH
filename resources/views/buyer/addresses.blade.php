@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_addresses.css') }}">

    <section class="address-page">
        <div class="container address-shell">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>Addresses</span>
            </div>

            <div class="address-hero panel">
                <div class="hero-copy">
                    <span class="section-kicker">Address Book</span>
                    <h1>Manage Delivery Addresses</h1>

                </div>

                <div class="hero-actions">
                    @if($returnTo)
                        <a href="{{ $returnTo }}" class="action-btn secondary-btn">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back
                        </a>
                    @endif

                    <a href="{{ route('buyer.addresses.create', array_filter(['return_to' => $returnTo])) }}"
                        class="action-btn primary-btn">
                        <i class="fa-solid fa-plus"></i>
                        Add New Address
                    </a>
                </div>
            </div>

            @if(session('address_success'))
                <div class="success-message">{{ session('address_success') }}</div>
            @endif

            <div class="address-list">
                @forelse($addresses as $address)
                    <article class="address-card panel">
                        <div class="address-card-top">
                            <div>
                                <h3>{{ $address->full_name }}</h3>
                                <p class="address-phone">{{ $address->phone }}</p>
                            </div>

                            <div class="address-tags">
                                @if($address->is_default)
                                    <span class="tag default-tag">Default</span>
                                @endif

                                @if($address->label)
                                    <span class="tag">{{ $address->label }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="address-content">
                            <p>{{ $address->street_address }}</p>
                            @if($address->landmark)
                                <p>Landmark: {{ $address->landmark }}</p>
                            @endif
                            <p>
                                {{ $address->barangay }},
                                {{ $address->city }},
                                {{ $address->province }},
                                {{ $address->region }}
                                @if($address->postal_code), {{ $address->postal_code }} @endif
                            </p>
                        </div>

                        <div class="address-actions">
                            @if(!$address->is_default)
                                <form action="{{ route('buyer.addresses.default', $address) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if($returnTo)
                                        <input type="hidden" name="return_to" value="{{ $returnTo }}">
                                    @endif
                                    <button type="submit" class="action-btn secondary-btn">Set Default</button>
                                </form>
                            @endif

                            <button type="button" class="action-btn secondary-btn open-edit-address"
                                data-id="{{ $address->id }}" data-full_name="{{ $address->full_name }}"
                                data-phone="{{ $address->phone }}" data-region="{{ $address->region }}"
                                data-province="{{ $address->province }}" data-city="{{ $address->city }}"
                                data-barangay="{{ $address->barangay }}" data-postal_code="{{ $address->postal_code }}"
                                data-landmark="{{ $address->landmark }}" data-label="{{ $address->label }}"
                                data-street_address="{{ $address->street_address }}"
                                data-is_default="{{ $address->is_default ? 1 : 0 }}">
                                Edit
                            </button>

                            <form action="{{ route('buyer.addresses.destroy', $address) }}" method="POST"
                                onsubmit="return confirm('Delete this address?')">
                                @csrf
                                @method('DELETE')
                                @if($returnTo)
                                    <input type="hidden" name="return_to" value="{{ $returnTo }}">
                                @endif
                                <button type="submit" class="action-btn danger-btn">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-box panel">
                        <h3>No saved addresses yet</h3>
                        <p>Add your first delivery address to speed up checkout.</p>
                        <a href="{{ route('buyer.addresses.create', array_filter(['return_to' => $returnTo])) }}"
                            class="action-btn primary-btn">Create Address</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <div class="address-modal-overlay {{ $errors->any() && old('editing_address_id') ? 'show' : '' }}"
        id="editAddressModal">
        <div class="address-modal panel">
            <button type="button" class="close-address-modal" id="closeEditAddressModal">&times;</button>

            <div class="form-heading form-heading--modal">
                <div>
                    <span class="section-kicker">Address Book</span>
                    <h2>Edit Address</h2>
                    <p>Update delivery details without breaking your existing saved addresses.</p>
                </div>
            </div>

            @if($errors->any() && old('editing_address_id'))
                <div class="error-summary" role="alert">
                    <strong>Please review the required address details.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" id="editAddressForm" class="address-form js-ph-address-form"
                action="{{ old('editing_address_id') ? route('buyer.addresses.update', old('editing_address_id')) : '' }}">
                @csrf
                @method('PATCH')
                @if($returnTo)
                    <input type="hidden" name="return_to" value="{{ $returnTo }}">
                @endif
                <input type="hidden" name="editing_address_id" id="edit_address_id" value="{{ old('editing_address_id') }}">

                <div class="form-section">
                    <div class="form-section-header">
                        <div>
                            <h3>Recipient Details</h3>
                            <p>Keep the receiver information complete and checkout-friendly.</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_full_name">Full Name <span class="required-dot">*</span></label>
                            <input type="text" name="full_name" id="edit_full_name" placeholder="Full Name"
                                value="{{ old('full_name') }}" required>
                            @error('full_name')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_phone">Phone Number <span class="required-dot">*</span></label>
                            <input type="tel" name="phone" id="edit_phone" placeholder="Phone Number"
                                value="{{ old('phone') }}" required>
                            @error('phone')<small class="field-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <div>
                            <h3>Location</h3>
                            <p>Use the dependent dropdowns to keep the address hierarchy consistent.</p>
                        </div>
                    </div>

                    <p class="field-error location-feedback" data-location-feedback hidden></p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_region">Region <span class="required-dot">*</span></label>
                            <select name="region" id="edit_region" data-selected="{{ old('region') }}" required>
                                <option value="" selected disabled>Select region</option>
                            </select>
                            @error('region')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_province">Province <span class="required-dot">*</span></label>
                            <select name="province" id="edit_province" data-selected="{{ old('province') }}" required>
                                <option value="" selected disabled>Select province</option>
                            </select>
                            @error('province')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_city">City / Municipality <span class="required-dot">*</span></label>
                            <select name="city" id="edit_city" data-selected="{{ old('city') }}" required>
                                <option value="" selected disabled>Select city / municipality</option>
                            </select>
                            @error('city')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_barangay">Barangay <span class="required-dot">*</span></label>
                            <select name="barangay" id="edit_barangay" data-selected="{{ old('barangay') }}" required>
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
                            <p>Make the exact drop-off point easy to find for delivery.</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group form-group--full">
                            <label for="edit_street_address">Street Address <span class="required-dot">*</span></label>
                            <textarea name="street_address" id="edit_street_address" rows="3" placeholder="Street Address"
                                required>{{ old('street_address') }}</textarea>
                            @error('street_address')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_postal_code">Postal Code <span class="required-dot">*</span></label>
                            <input type="text" name="postal_code" id="edit_postal_code" placeholder="Postal Code"
                                value="{{ old('postal_code') }}" required>
                            @error('postal_code')<small class="field-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_landmark">Landmark <span class="required-dot">*</span></label>
                            <input type="text" name="landmark" id="edit_landmark" placeholder="Landmark"
                                value="{{ old('landmark') }}" required>
                            @error('landmark')<small class="field-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="default-row">
                        <div>
                            <span>Set as default address</span>
                            <p class="field-note">Use this for your next order by default.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="is_default" id="edit_is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <div class="label-row">
                        <div>
                            <span>Label as</span>
                            <p class="field-note">Optional label to help organize saved addresses.</p>
                        </div>

                        <div class="label-buttons">
                            <label>
                                <input type="radio" name="label" value="Home" id="edit_label_home" {{ old('label') == 'Home' ? 'checked' : '' }}>
                                <span>Home</span>
                            </label>

                            <label>
                                <input type="radio" name="label" value="Work" id="edit_label_work" {{ old('label') == 'Work' ? 'checked' : '' }}>
                                <span>Work</span>
                            </label>

                            <label>
                                <input type="radio" name="label" value="Other" id="edit_label_other" {{ old('label') == 'Other' ? 'checked' : '' }}>
                                <span>Other</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-actions form-actions--modal">
                    <button type="button" class="action-btn secondary-btn" id="cancelEditAddressModal">Cancel</button>
                    <button type="submit" class="action-btn primary-btn save-btn">Update Address</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/buyer-address-form.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editAddressModal');
            const closeBtn = document.getElementById('closeEditAddressModal');
            const cancelBtn = document.getElementById('cancelEditAddressModal');
            const form = document.getElementById('editAddressForm');
            const body = document.body;
            const baseUpdateUrl = @json(url('/my-addresses'));

            if (!modal || !closeBtn || !cancelBtn || !form) {
                return;
            }

            const openModal = function () {
                modal.classList.add('show');
                body.classList.add('modal-open');
            };

            const closeModal = function () {
                modal.classList.remove('show');
                body.classList.remove('modal-open');
            };

            const setLabel = function (label) {
                document.getElementById('edit_label_home').checked = label === 'Home';
                document.getElementById('edit_label_work').checked = label === 'Work';
                document.getElementById('edit_label_other').checked = label === 'Other';
            };

            document.querySelectorAll('.open-edit-address').forEach(function (button) {
                button.addEventListener('click', async function () {
                    const id = this.dataset.id;
                    const locationValues = {
                        region: this.dataset.region || '',
                        province: this.dataset.province || '',
                        city: this.dataset.city || '',
                        barangay: this.dataset.barangay || '',
                    };

                    form.action = `${baseUpdateUrl}/${id}`;
                    document.getElementById('edit_address_id').value = id;
                    document.getElementById('edit_full_name').value = this.dataset.full_name || '';
                    document.getElementById('edit_phone').value = this.dataset.phone || '';
                    document.getElementById('edit_street_address').value = this.dataset.street_address || '';
                    document.getElementById('edit_postal_code').value = this.dataset.postal_code || '';
                    document.getElementById('edit_landmark').value = this.dataset.landmark || '';
                    document.getElementById('edit_is_default').checked = this.dataset.is_default === '1';
                    setLabel(this.dataset.label || '');

                    await window.LocalLiftAddressForm.init(form, locationValues);
                    openModal();
                });
            });

            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            @if($errors->any() && old('editing_address_id'))
                openModal();
                window.LocalLiftAddressForm.init(form, {
                    region: @json(old('region')),
                    province: @json(old('province')),
                    city: @json(old('city')),
                    barangay: @json(old('barangay')),
                });
            @endif
        });
    </script>
@endsection