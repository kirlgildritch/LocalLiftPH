@extends('layouts.app')
@section('title', 'LocalLift PH - Shop')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/shop_details.css') }}">
@php($ownsShop = auth()->check() && (int) $user->id === (int) auth()->id())
@php($shopCategories = $products->groupBy(fn($product) => $product->category?->name ?? 'Uncategorized'))

<section class="shop-detail-page">
    <div class="container">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <a href="{{ route('shops.index') }}">Shops</a>
            <span>&gt;</span>
            <span>{{ $user->name }}</span>
        </div>

        <div class="shop-hero panel">
            <div class="shop-hero-copy">
                <span class="section-kicker">Curated Storefront</span>

                <h3>{{ $user->sellerProfile->store_name ?? 'My Shop' }}</h3>

                <p class="shop-description">
                    {{ $user->sellerProfile->store_description ?? 'No shop description available yet.' }}
                </p>

                <div class="shop-meta">
                    <span><i class="fa-solid fa-phone"></i>
                        {{ $user->sellerProfile->contact_number ?? 'No contact number' }}</span>
                    <span><i class="fa-solid fa-location-dot"></i>
                        {{ $user->sellerProfile->address ?? 'No address provided' }}</span>
                </div>

                <div class="shop-hero-actions">
                    <a href="#shop-products" class="action-btn secondary-btn">Browse Products</a>
                    @auth
                        @if(!$ownsShop)
                            <form action="{{ route('messages.start', $user) }}" method="POST" data-chat-start-form>
                                @csrf
                                <button type="submit" class="action-btn secondary-btn">Message Seller</button>
                            </form>
                        @else
                            <span class="action-btn secondary-btn" aria-disabled="true">This is your shop</span>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="action-btn secondary-btn">Message Seller</a>
                    @endauth
                </div>
            </div>

            <div class="shop-hero-card">
                <div class="shop-hero-art">
                    @if(!empty($user->sellerProfile?->shop_logo))
                        <img src="{{ asset('storage/' . $user->sellerProfile->shop_logo) }}" alt="Shop Logo"
                            class="shop-logo">
                    @else
                        <div class="shop-logo-placeholder">
                            <i class="fa-solid fa-store"></i>
                        </div>
                    @endif
                </div>

                <div class="shop-highlight-grid">
                    <div class="shop-highlight">
                        <strong>Member Seller</strong>
                        <span>Part of the LocalLift marketplace community.</span>
                    </div>
                    <div class="shop-highlight">
                        <strong>{{ $products->count() }} Active Products</strong>
                        <span>Browse available items from this shop.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="shop-detail-layout">
            <aside class="shop-sidebar">
                <div class="panel sidebar-panel">
                    <div class="shop-sidebar-brand">
                        <span class="shop-avatar">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                        <div>
                            <h2>{{ $user->name }}</h2>
                            <p>LocalLift seller</p>
                        </div>
                    </div>

                    <div class="shop-sidebar-stats">
                        <div class="stat-chip">
                            <strong>{{ $products->count() }}</strong>
                            <span>Active products</span>
                        </div>
                    </div>
                </div>

                <div class="panel sidebar-panel">
                    <h3>Categories</h3>
                    <div class="filter-list">
                        <div class="filter-item active">
                            <div class="filter-label"><span class="dot"></span> All Products</div>
                            <span class="count">{{ $products->count() }}</span>
                        </div>

                        @foreach($shopCategories as $category => $categoryProducts)
                            <div class="filter-item">
                                <div class="filter-label">
                                    <span class="dot"></span> {{ $category }}
                                </div>
                                <span class="count">{{ $categoryProducts->count() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="panel sidebar-panel">
                    <h3>Sort Results</h3>
                    <select>
                        <option>Newest</option>
                        <option>Price Low to High</option>
                        <option>Price High to Low</option>
                    </select>
                </div>
            </aside>

            <div class="shop-main">
                <div class="panel content-panel">
                    <div class="tab-row">
                        <a href="#shop-products" class="tab-link active">Products</a>

                    </div>

                    <div class="content-header" id="shop-products">
                        <div>
                            <span class="section-kicker">Shop Catalog</span>
                            <h2>Available products</h2>
                        </div>
                    </div>

                    <div class="product-grid product-card-grid">
                        @forelse($products as $product)
                            <x-product-card :product="$product" />
                        @empty
                            <p>This shop has no products yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection