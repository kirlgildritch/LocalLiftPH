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
                                    <img src="{{ asset('storage/' . $seller->shop_logo) }}" alt="Shop Logo" class="shop-logo">
                                @else
                                    <div class="shop-logo-placeholder">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="shop-main-info">
                                <h3>{{ $seller->store_name ?? 'My Shop' }}</h3>
                                <p class="shop-description">{{ $seller->store_description ?? 'No shop description available yet.' }}</p>

                                <div class="shop-meta">
                                    <span><i class="fa-solid fa-phone"></i> {{ $seller->contact_number ?? 'No contact number' }}</span>
                                    <span><i class="fa-solid fa-location-dot"></i> {{ $seller->address ?? 'No address set' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="preview-section">
                        <div class="section-title-row">
                            <h3>Products</h3>
                            <span class="product-count">{{ $products->count() }} items</span>
                        </div>

                        <div class="product-preview-grid">
                            @forelse($products as $product)
                                <div class="product-preview-card panel">
                                    <div class="product-image-wrap">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="product-image-placeholder">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="product-preview-content">
                                        <h4>{{ $product->name }}</h4>
                                        <p class="product-category">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                                        <div class="product-preview-bottom">
                                            <span class="product-price">PHP {{ number_format($product->price, 2) }}</span>
                                            <span class="product-stock">Stock: {{ $product->stock }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-preview panel">
                                    No products to preview yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</section>
@endsection
