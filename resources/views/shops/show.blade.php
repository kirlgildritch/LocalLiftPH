@extends('layouts.app')
@section('title', 'LocalLift PH - Shop')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/shop_details.css') }}">
@php($ownsShop = auth()->check() && (int) $user->id === (int) auth()->id())
@php($shopCategories = $products->groupBy(fn($product) => $product->category?->name ?? 'Uncategorized'))
@php($canReportSeller = auth('web')->check() && !$ownsShop)

<section class="shop-detail-page">
    <div class="container">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <a href="{{ route('shops.index') }}">Shops</a>
            <span>&gt;</span>
            <span>{{ $user->sellerProfile?->store_name ?? $user->name }}</span>
        </div>

        <div class="shop-hero panel">
            <div class="shop-hero-top">
                <div class="shop-hero-brand">
                    <div class="shop-hero-logo">
                        @if(!empty($user->sellerProfile?->shop_logo))
                            <img src="{{ asset('storage/' . $user->sellerProfile->shop_logo) }}" alt="Shop Logo">
                        @else
                            <div class="shop-hero-logo-placeholder">
                                <i class="fa-solid fa-store"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="shop-hero-copy">
                    <div class="shop-hero-copy-top">
                        <span class="section-kicker">Local Seller</span>
                        @if($canReportSeller)
                            @include('partials.report-modal', [
                                'modalId' => 'report-seller-modal',
                                'modalContext' => 'seller',
                                'triggerLabel' => 'Report seller',
                                'sellerId' => $user->id,
                            ])
                        @elseif(!auth('seller')->check() && !auth('admin')->check())
                            <a href="{{ route('login') }}" class="report-trigger-button" aria-label="Log in to report seller">
                                <i class="fa-solid fa-flag"></i>
                            </a>
                        @endif
                    </div>

                    <h1>{{ $user->sellerProfile?->store_name ?? 'My Shop' }}</h1>

                    <p class="shop-description">
                        {{ $user->sellerProfile?->store_description ?? 'No shop description available yet.' }}
                    </p>

                    <div class="shop-meta">
                        <span>
                            <i class="fa-solid fa-phone"></i>
                            {{ $user->sellerProfile?->contact_number ?? 'No contact number' }}
                        </span>
                        <span>
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $user->sellerProfile?->address ?? 'No address provided' }}
                        </span>
                    </div>

                    <div class="shop-hero-actions">
                        <a href="#shop-products" class="action-btn primary-btn">
                            <i class="fa-solid fa-bag-shopping"></i>&nbsp; Browse Products
                        </a>

                        @auth
                            @if(!$ownsShop)
                                <form action="{{ route('messages.start', $user) }}" method="POST" data-chat-start-form>
                                    @csrf
                                    <button type="submit" class="action-btn secondary-btn">
                                        <i class="fa-regular fa-message"></i>&nbsp; Message Seller
                                    </button>
                                </form>
                            @else
                                <span class="action-btn secondary-btn" aria-disabled="true">This is your shop</span>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="action-btn secondary-btn">
                                <i class="fa-regular fa-message"></i>&nbsp; Message Seller
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="shop-hero-info-grid">
                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>
                    <div>
                        <strong>Member Seller</strong>
                        <span>Part of the LocalLift marketplace community.</span>
                    </div>
                </div>

                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div>
                        <strong>{{ $products->count() }} Active Products</strong>
                        <span>Browse available items from this shop.</span>
                    </div>
                </div>

                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <strong>Trusted Seller</strong>
                        <span>Committed to quality products and excellent service.</span>
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

                    <div class="mobile-category-dropdown">
                        <select onchange="if(this.value) window.location.href=this.value">
                            <option value="#shop-products" selected>
                                All Products ({{ $products->count() }})
                            </option>

                            @foreach($shopCategories as $category => $categoryProducts)
                                <option value="#category-{{ \Illuminate\Support\Str::slug($category) }}">
                                    {{ $category }} ({{ $categoryProducts->count() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-list">
                        <a href="#shop-products" class="filter-item active">
                            <div class="filter-label"><span class="dot"></span> All Products</div>
                            <span class="count">{{ $products->count() }}</span>
                        </a>

                        @foreach($shopCategories as $category => $categoryProducts)
                            <a href="#category-{{ \Illuminate\Support\Str::slug($category) }}" class="filter-item">
                                <div class="filter-label">
                                    <span class="dot"></span> {{ $category }}
                                </div>
                                <span class="count">{{ $categoryProducts->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <div class="shop-main">
                <div class="panel content-panel">
                    <div class="content-header" id="shop-products">
                        <div>
                            <h2>Available products</h2>
                        </div>
                    </div>

                    <div class="product-grid product-card-grid" data-skeleton-group data-skeleton-delay="420">
                        @forelse($products as $product)
                            <x-product-card :product="$product" />
                        @empty
                            <p>This shop has no products yet.</p>
                        @endforelse
                    </div>

                    @foreach($shopCategories as $category => $categoryProducts)
                        <span id="category-{{ \Illuminate\Support\Str::slug($category) }}"></span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
