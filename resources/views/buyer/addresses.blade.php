@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/buyer_addresses.css') }}">

<section class="address-page">
    <div class="container address-shell">
        @php($returnTo = request('return_to'))

        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <span>Addresses</span>
        </div>

        <div>


            <div class="hero-actions">
                @if($returnTo)
                    <a href="{{ $returnTo }}" class="action-btn secondary-btn">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back
                    </a>
                @endif

                <a href="{{ route('buyer.addresses.create', ['return_to' => $returnTo]) }}"
                    class="action-btn primary-btn">
                    <i class="fa-solid fa-plus"></i>
                    Add New Address
                </a>
            </div>
        </div>


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
                                <button type="submit" class="action-btn secondary-btn">Set Default</button>
                            </form>
                        @endif

                        <button type="button" class="action-btn secondary-btn open-edit-address"
                            data-id="{{ $address->id }}" data-full_name="{{ $address->full_name }}"
                            data-phone="{{ $address->phone }}" data-region="{{ $address->region }}"
                            data-province="{{ $address->province }}" data-city="{{ $address->city }}"
                            data-barangay="{{ $address->barangay }}" data-postal_code="{{ $address->postal_code }}"
                            data-label="{{ $address->label }}" data-street_address="{{ $address->street_address }}"
                            data-is_default="{{ $address->is_default ? 1 : 0 }}">
                            Edit
                        </button>

                        <form action="{{ route('buyer.addresses.destroy', $address) }}" method="POST"
                            onsubmit="return confirm('Delete this address?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn danger-btn">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty-box panel">
                    <h3>No saved addresses yet</h3>
                    <p>Add your first delivery address to speed up checkout.</p>
                    <a href="{{ route('buyer.addresses.create') }}" class="action-btn primary-btn">Create Address</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<div class="address-modal-overlay" id="editAddressModal">
    <div class="address-modal panel">
        <button type="button" class="close-address-modal" id="closeEditAddressModal">&times;</button>

        <h2>Edit Address</h2>
        <div class="divider"></div>

        <form method="POST" id="editAddressForm" class="address-form">
            @csrf
            @method('PATCH')

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_full_name">Full Name</label>
                    <input type="text" name="full_name" id="edit_full_name" placeholder="Full Name">
                </div>

                <div class="form-group">
                    <label for="edit_phone">Phone Number</label>
                    <input type="text" name="phone" id="edit_phone" placeholder="Phone Number">
                </div>

                <div class="form-group">
                    <label for="edit_region">Region</label>
                    <input type="text" name="region" id="edit_region" placeholder="Region">
                </div>

                <div class="form-group">
                    <label for="edit_province">Province</label>
                    <input type="text" name="province" id="edit_province" placeholder="Province">
                </div>

                <div class="form-group">
                    <label for="edit_city">City</label>
                    <input type="text" name="city" id="edit_city" placeholder="City">
                </div>

                <div class="form-group">
                    <label for="edit_barangay">Barangay</label>
                    <input type="text" name="barangay" id="edit_barangay" placeholder="Barangay">
                </div>

                <div class="form-group">
                    <label for="edit_postal_code">Postal Code</label>
                    <input type="text" name="postal_code" id="edit_postal_code" placeholder="Postal Code">
                </div>
            </div>

            <div class="form-group">
                <label for="edit_street_address">Street Address</label>
                <textarea name="street_address" id="edit_street_address" rows="3"
                    placeholder="Street Address"></textarea>
            </div>

            <div class="modal-section">
                <div class="default-row">
                    <span>Set as default address</span>
                    <label class="switch">
                        <input type="checkbox" name="is_default" id="edit_is_default" value="1">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <div class="modal-section">
                <div class="label-row">
                    <span>Label as</span>
                    <div class="label-buttons">
                        <label>
                            <input type="radio" name="label" value="Home" id="edit_label_home">
                            <span>Home</span>
                        </label>

                        <label>
                            <input type="radio" name="label" value="Work" id="edit_label_work">
                            <span>Work</span>
                        </label>

                        <label>
                            <input type="radio" name="label" value="Other" id="edit_label_other">
                            <span>Other</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="action-btn primary-btn full-btn">Update Address</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editAddressModal');
        const closeBtn = document.getElementById('closeEditAddressModal');
        const form = document.getElementById('editAddressForm');

        if (!modal || !closeBtn || !form) return;

        document.querySelectorAll('.open-edit-address').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const label = this.dataset.label || '';
                form.action = `/my-addresses/${id}`;
                document.getElementById('edit_full_name').value = this.dataset.full_name || '';
                document.getElementById('edit_phone').value = this.dataset.phone || '';
                document.getElementById('edit_region').value = this.dataset.region || '';
                document.getElementById('edit_province').value = this.dataset.province || '';
                document.getElementById('edit_city').value = this.dataset.city || '';
                document.getElementById('edit_barangay').value = this.dataset.barangay || '';
                document.getElementById('edit_postal_code').value = this.dataset.postal_code || '';
                document.getElementById('edit_street_address').value = this.dataset.street_address || '';
                document.getElementById('edit_label_home').checked = label === 'Home';
                document.getElementById('edit_label_work').checked = label === 'Work';
                document.getElementById('edit_label_other').checked = label === 'Other';
                document.getElementById('edit_is_default').checked = this.dataset.is_default == '1';

                modal.classList.add('show');
                document.body.classList.add('modal-open');
            });
        });

        closeBtn.addEventListener('click', function () {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        });

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        });
    });
</script>
@endsection