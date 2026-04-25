@extends('layouts.app')
@section('title', 'LocalLift PH - Shops')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/shops.css') }}">

    <section class="market-page shops-page">
        <div class="container">
            <div class="page-intro">
                <div class="checkout-breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&gt;</span>
                    <span>Shops</span>
                </div>
            </div>

            <div class="market-layout">
                <aside class="market-sidebar">
                    <div class="panel sidebar-panel">
                        <h3>Categories</h3>

                        <div class="mobile-category-dropdown">
                            <select onchange="if(this.value) window.location.href=this.value">
                                <option value="{{ route('shops.index', array_filter(['sort' => $sort])) }}" {{ empty($categorySlug) ? 'selected' : '' }}>
                                    All ({{ $categories->sum('products_count') }})
                                </option>
                                @foreach($categories as $categoryOption)
                                    <option
                                        value="{{ route('shops.index', array_filter(['category' => $categoryOption->slug, 'sort' => $sort])) }}"
                                        {{ $categorySlug === $categoryOption->slug ? 'selected' : '' }}>
                                        {{ $categoryOption->name }} ({{ $categoryOption->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-list">
                            <a href="{{ route('shops.index', array_filter(['sort' => $sort])) }}"
                                class="filter-item {{ empty($categorySlug) ? 'active' : '' }}">
                                <div class="filter-label"><span class="dot"></span> All</div>
                                <span class="count">{{ $categories->sum('products_count') }}</span>
                            </a>
                            @foreach($categories as $categoryOption)
                                <a href="{{ route('shops.index', array_filter(['category' => $categoryOption->slug, 'sort' => $sort])) }}"
                                    class="filter-item {{ $categorySlug === $categoryOption->slug ? 'active' : '' }}">
                                    <div class="filter-label"><span class="dot"></span> {{ $categoryOption->name }}</div>
                                    <span class="count">{{ $categoryOption->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <div class="market-main">
                    <div class="shops-grid" data-skeleton-group data-skeleton-delay="420">
                        @forelse($shops as $shop)
                            <article class="shop-card panel skeleton-shell is-loading" data-skeleton-item data-skeleton-kind="shop-card">
                                <div class="shop-logo">
                                    <div class="shop-logo-frame skeleton skeleton-image">
                                        @if(!empty($shop->sellerProfile?->shop_logo))
                                            <img src="{{ asset('storage/' . $shop->sellerProfile->shop_logo) }}" alt="Shop Logo">
                                        @else
                                            <div class="shop-logo-placeholder">
                                                <i class="fa-solid fa-store"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="shop-card-body">
                                    <span class="shop-badge skeleton skeleton-text">
                                        <i class="fa-solid fa-store"></i>
                                        Local Seller
                                    </span>

                                    <h3 class="skeleton skeleton-text">{{ $shop->sellerProfile?->store_name ?? $shop->name }}</h3>

                                    <div class="shop-rating skeleton skeleton-text">
                                        <i class="fa-solid fa-star"></i>
                                        <span>Trusted Local Seller</span>
                                    </div>

                                    <div class="shop-products skeleton skeleton-text">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                        <span>{{ $shop->products_count }} product{{ $shop->products_count != 1 ? 's' : '' }}
                                            available</span>
                                    </div>

                                    <a href="{{ route('shops.show', $shop->id) }}" class="action-btn primary-btn skeleton skeleton-button">
                                        <span class="btn-left">
                                            <i class="fa-solid fa-store"></i>
                                            Visit Shop
                                        </span>
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="panel" style="padding: 20px;">
                                <p>No shops available yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
