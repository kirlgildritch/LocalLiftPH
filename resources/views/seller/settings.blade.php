@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">
@php
    $currentShopStatus = old('shop_status', $seller ? $seller->effectiveShopStatus() : \App\Models\Seller::SHOP_STATUS_OPEN);
    $currentShopStatusUntil = old('shop_status_until', optional($seller?->shop_status_until)->format('Y-m-d'));
    $sellerSettingsScript = asset('assets/js/seller-settings-pages.js') . '?v=' . @filemtime(public_path('assets/js/seller-settings-pages.js'));
@endphp

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

                    @include('seller.settings.partials.tabs')

                    <div class="tab-content-wrapper">
                        @include('seller.settings.partials.general')
                        @include('seller.settings.partials.payout')
                        @include('seller.settings.partials.inventory')
                        @include('seller.settings.partials.status')
                    </div>
                </section>
            </main>
        </div>
    </div>
</section>

<script src="{{ asset('assets/js/buyer-address-form.js') }}" defer></script>
<script src="{{ $sellerSettingsScript }}" defer></script>
@endsection
