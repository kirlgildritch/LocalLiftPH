@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">
@php($currentShopStatus = old('shop_status', $seller ? $seller->effectiveShopStatus() : \App\Models\Seller::SHOP_STATUS_OPEN))
@php($currentShopStatusUntil = old('shop_status_until', optional($seller?->shop_status_until)->format('Y-m-d')))

<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">
            @include('seller.partials.sidebar')

            <main class="dashboard-main">
                <section class="seller-page-panel panel">
                    <div class="page-header">
                        <div>
                            <span class="section-kicker">Settings</span>
                            <h2>Seller settings</h2>
                        </div>
                    </div>

                    <div class="settings-tabs">
                        <button class="tab-btn active" onclick="showSettingsTab(event, 'general')">General</button>
                        <button class="tab-btn" onclick="showSettingsTab(event, 'payout')">Payout</button>
                        <button class="tab-btn" onclick="showSettingsTab(event, 'inventory')">Inventory</button>
                        <button class="tab-btn" onclick="showSettingsTab(event, 'status')">Shop Status</button>
                    </div>

                    <div class="tab-content-wrapper">
                        <div id="general" class="settings-tab-content active">
                            <div class="settings-card panel">
                                <h3>Shop Information</h3>
                                <form action="{{ route('seller.settings.update') }}" method="POST" enctype="multipart/form-data">
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

                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea id="address" name="address" rows="3">{{ old('address', $seller->address ?? '') }}</textarea>
                                        @error('address')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
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

                                    <button type="submit" class="page-action-btn">Save</button>
                                </form>
                            </div>
                        </div>

                        <div id="payout" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Payout</h3>
                                <form action="{{ route('seller.settings.payout') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label for="payout_method">Method</label>
                                        <select id="payout_method" name="payout_method">
                                            <option value="gcash" {{ old('payout_method', $seller->payout_method ?? '') === 'gcash' ? 'selected' : '' }}>GCash</option>
                                            <option value="bank" {{ old('payout_method', $seller->payout_method ?? '') === 'bank' ? 'selected' : '' }}>Bank</option>
                                        </select>
                                        @error('payout_method')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="payout_account_name">Account Name</label>
                                        <input type="text" id="payout_account_name" name="payout_account_name" value="{{ old('payout_account_name', $seller->payout_account_name ?? '') }}">
                                        @error('payout_account_name')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="payout_account_number">Account Number</label>
                                        <input type="text" id="payout_account_number" name="payout_account_number" value="{{ old('payout_account_number', $seller->payout_account_number ?? '') }}">
                                        @error('payout_account_number')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button type="submit" class="page-action-btn">Save</button>
                                </form>
                            </div>
                        </div>

                        <div id="inventory" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Inventory</h3>
                                <form action="{{ route('seller.settings.inventory') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label for="low_stock_threshold">Low Stock Alert At</label>
                                        <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $seller->low_stock_threshold ?? 5) }}">
                                        @error('low_stock_threshold')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="checkbox-group">
                                        <label><input type="checkbox" name="hide_out_of_stock" value="1" {{ old('hide_out_of_stock', $seller->hide_out_of_stock ?? 0) ? 'checked' : '' }}> Hide sold-out products from buyers</label>
                                    </div>

                                    <button type="submit" class="page-action-btn">Save</button>
                                </form>
                            </div>
                        </div>

                        <div id="status" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Shop Status</h3>
                                <form action="{{ route('seller.settings.status') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="open" {{ $currentShopStatus === 'open' ? 'checked' : '' }}> Open</label></div>
                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="temporarily_closed" {{ $currentShopStatus === 'temporarily_closed' ? 'checked' : '' }}> Temporarily Closed</label></div>
                                    <div class="form-group settings-inline-date" data-status-until-group>
                                        <label for="shop_status_until">Until</label>
                                        <input type="date" id="shop_status_until" name="shop_status_until" value="{{ $currentShopStatusUntil }}">
                                    </div>
                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="vacation" {{ $currentShopStatus === 'vacation' ? 'checked' : '' }}> Vacation Mode</label></div>
                                    @error('shop_status')
                                        <small class="error-text">{{ $message }}</small>
                                    @enderror
                                    @error('shop_status_until')
                                        <small class="error-text">{{ $message }}</small>
                                    @enderror

                                    <button type="submit" class="page-action-btn">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</section>

<script>
    function showSettingsTab(event, tabId) {
        const tabContents = document.querySelectorAll('.settings-tab-content');
        const tabButtons = document.querySelectorAll('.tab-btn');

        tabContents.forEach(content => content.classList.remove('active'));
        tabButtons.forEach(button => button.classList.remove('active'));

        document.getElementById(tabId).classList.add('active');
        if (event?.currentTarget) {
            event.currentTarget.classList.add('active');
        } else {
            document.querySelector(`.tab-btn[onclick*="'${tabId}'"]`)?.classList.add('active');
        }

        if (window.location.hash !== `#${tabId}`) {
            history.replaceState(null, '', `#${tabId}`);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const tabId = window.location.hash ? window.location.hash.substring(1) : null;
        const statusInputs = document.querySelectorAll('input[name="shop_status"]');
        const statusUntilGroup = document.querySelector('[data-status-until-group]');
        const statusUntilInput = document.getElementById('shop_status_until');

        const syncStatusUntilVisibility = () => {
            const selectedStatus = document.querySelector('input[name="shop_status"]:checked')?.value;
            const showUntil = selectedStatus === 'temporarily_closed';

            statusUntilGroup?.classList.toggle('is-hidden', !showUntil);

            if (statusUntilInput) {
                statusUntilInput.disabled = !showUntil;

                if (!showUntil) {
                    statusUntilInput.value = '';
                }
            }
        };

        statusInputs.forEach((input) => {
            input.addEventListener('change', syncStatusUntilVisibility);
        });

        if (tabId && document.getElementById(tabId)) {
            showSettingsTab(null, tabId);
        }

        syncStatusUntilVisibility();
    });
</script>
@endsection
