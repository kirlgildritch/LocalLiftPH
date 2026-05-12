@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/shop_preview.css') }}">

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <section class="seller-page-panel panel">
                        <div class="page-header">
                            <div>
                                <span class="section-kicker">Storefront</span>
                                <h2>Shop Preview</h2>
                            </div>
                        </div>

                        <div class="shop-preview-card panel">
                            <div class="shop-top">
                                <div class="shop-logo-wrap">
                                    @if(!empty($seller->shop_logo))
                                        <img src="{{ asset('storage/' . $seller->shop_logo) }}" alt="Shop Logo"
                                            class="shop-logo">
                                    @else
                                        <div class="shop-logo-placeholder">
                                            <i class="fa-solid fa-store"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="shop-main-info">
                                    <h3>{{ $seller->store_name ?? 'My Shop' }}</h3>
                                    @if(filled($seller->store_description))
                                        <p class="shop-description">
                                            {{ $seller->store_description }}
                                        </p>
                                    @endif

                                    <div class="shop-meta">
                                        <span><i class="fa-solid fa-phone"></i>
                                            {{ $seller->contact_number ?? 'No contact number' }}</span>
                                        <span><i class="fa-solid fa-location-dot"></i>
                                            {{ $seller?->formattedLocation() ?: 'No address set' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="preview-section">
                            <div class="section-title-row">
                                <h3>Products</h3>
                                <span class="product-count">{{ $products->count() }} items</span>
                            </div>


                        </div>
                    </section>
                </main>
            </div>
        </div>
    </section>
@endsection
