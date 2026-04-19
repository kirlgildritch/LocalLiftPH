@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">

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

                    @if(session('success'))
                        <p class="seller-feedback success-message">{{ session('success') }}</p>
                    @endif

                    @if(session('error'))
                        <p class="seller-feedback error-message">{{ session('error') }}</p>
                    @endif

                    <div class="settings-tabs">
                        <button class="tab-btn active" onclick="showSettingsTab(event, 'general')">General</button>
                        <button class="tab-btn" onclick="showSettingsTab(event, 'notifications')">Notifications</button>
                        <button class="tab-btn" onclick="showSettingsTab(event, 'policies')">Policies</button>
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
                                    </div>

                                    <button type="submit" class="page-action-btn">Save General Settings</button>
                                </form>
                            </div>
                        </div>

                        <div id="notifications" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Notification Preferences</h3>
                                <form action="{{ route('seller.settings.notifications') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="checkbox-group"><label><input type="checkbox" name="notify_orders" value="1" {{ old('notify_orders', $seller->notify_orders ?? 1) ? 'checked' : '' }}> New order notifications</label></div>
                                    <div class="checkbox-group"><label><input type="checkbox" name="notify_messages" value="1" {{ old('notify_messages', $seller->notify_messages ?? 1) ? 'checked' : '' }}> New message notifications</label></div>
                                    <div class="checkbox-group"><label><input type="checkbox" name="notify_low_stock" value="1" {{ old('notify_low_stock', $seller->notify_low_stock ?? 0) ? 'checked' : '' }}> Low stock alerts</label></div>
                                    <div class="checkbox-group"><label><input type="checkbox" name="notify_promotions" value="1" {{ old('notify_promotions', $seller->notify_promotions ?? 0) ? 'checked' : '' }}> Promotions and updates</label></div>

                                    <button type="submit" class="page-action-btn">Save Notification Settings</button>
                                </form>
                            </div>
                        </div>

                        <div id="policies" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Store Policies</h3>
                                <form action="{{ route('seller.settings.policies') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label for="return_policy">Return Policy</label>
                                        <textarea id="return_policy" name="return_policy" rows="4">{{ old('return_policy', $seller->return_policy ?? '') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="cancellation_policy">Cancellation Policy</label>
                                        <textarea id="cancellation_policy" name="cancellation_policy" rows="4">{{ old('cancellation_policy', $seller->cancellation_policy ?? '') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="shipping_notes">Shipping Notes</label>
                                        <textarea id="shipping_notes" name="shipping_notes" rows="4">{{ old('shipping_notes', $seller->shipping_notes ?? '') }}</textarea>
                                    </div>

                                    <button type="submit" class="page-action-btn">Save Store Policies</button>
                                </form>
                            </div>
                        </div>

                        <div id="payout" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Payout Information</h3>
                                <form action="{{ route('seller.settings.payout') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group"><label for="gcash_number">GCash Number</label><input type="text" id="gcash_number" name="gcash_number" value="{{ old('gcash_number', $seller->gcash_number ?? '') }}"></div>
                                    <div class="form-group"><label for="bank_name">Bank Name</label><input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $seller->bank_name ?? '') }}"></div>
                                    <div class="form-group"><label for="account_name">Account Name</label><input type="text" id="account_name" name="account_name" value="{{ old('account_name', $seller->account_name ?? '') }}"></div>
                                    <div class="form-group"><label for="account_number">Account Number</label><input type="text" id="account_number" name="account_number" value="{{ old('account_number', $seller->account_number ?? '') }}"></div>

                                    <button type="submit" class="page-action-btn">Save Payout Information</button>
                                </form>
                            </div>
                        </div>

                        <div id="inventory" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Inventory Settings</h3>
                                <form action="{{ route('seller.settings.inventory') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="form-group">
                                        <label for="low_stock_threshold">Low Stock Threshold</label>
                                        <input type="number" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', $seller->low_stock_threshold ?? 5) }}">
                                    </div>

                                    <div class="checkbox-group">
                                        <label><input type="checkbox" name="hide_out_of_stock" value="1" {{ old('hide_out_of_stock', $seller->hide_out_of_stock ?? 0) ? 'checked' : '' }}> Hide out-of-stock products</label>
                                    </div>

                                    <button type="submit" class="page-action-btn">Save Inventory Settings</button>
                                </form>
                            </div>
                        </div>

                        <div id="status" class="settings-tab-content">
                            <div class="settings-card panel">
                                <h3>Shop Status</h3>
                                <form action="{{ route('seller.settings.status') }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="open" {{ old('shop_status', $seller->shop_status ?? 'open') === 'open' ? 'checked' : '' }}> Open</label></div>
                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="closed" {{ old('shop_status', $seller->shop_status ?? '') === 'closed' ? 'checked' : '' }}> Temporarily Closed</label></div>
                                    <div class="radio-group"><label><input type="radio" name="shop_status" value="vacation" {{ old('shop_status', $seller->shop_status ?? '') === 'vacation' ? 'checked' : '' }}> Vacation Mode</label></div>

                                    <button type="submit" class="page-action-btn">Save Shop Status</button>
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
        event.currentTarget.classList.add('active');
    }
</script>
@endsection
