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

            @php
                $baseShopQuery = array_filter([
                    'category' => $categorySlug,
                    'sort' => $sort,
                    'province' => $province,
                    'city' => $city,
                    'near_me' => $nearMe ? 1 : null,
                ], fn ($value) => filled($value));
                $allCities = collect($locationOptions ?? collect())->flatten()->unique()->sort()->values();
                $availableCities = filled($province)
                    ? collect(($locationOptions ?? collect())->get($province, []))
                    : $allCities;
            @endphp

            <div class="market-layout">
                <aside class="market-sidebar">
                    <div class="panel sidebar-panel">
                        <h3>Categories</h3>

                        <div class="mobile-category-dropdown">
                            <select onchange="if(this.value) window.location.href=this.value">
                                <option value="{{ route('shops.index', array_filter(array_merge($baseShopQuery, ['category' => null]), fn ($value) => filled($value))) }}" {{ empty($categorySlug) ? 'selected' : '' }}>
                                    All ({{ $categories->sum('products_count') }})
                                </option>
                                @foreach($categories as $categoryOption)
                                    <option
                                        value="{{ route('shops.index', array_filter(array_merge($baseShopQuery, ['category' => $categoryOption->slug]), fn ($value) => filled($value))) }}"
                                        {{ $categorySlug === $categoryOption->slug ? 'selected' : '' }}>
                                        {{ $categoryOption->name }} ({{ $categoryOption->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-list">
                            <a href="{{ route('shops.index', array_filter(array_merge($baseShopQuery, ['category' => null]), fn ($value) => filled($value))) }}"
                                class="filter-item {{ empty($categorySlug) ? 'active' : '' }}">
                                <div class="filter-label"><span class="dot"></span> All</div>
                                <span class="count">{{ $categories->sum('products_count') }}</span>
                            </a>
                            @foreach($categories as $categoryOption)
                                <a href="{{ route('shops.index', array_filter(array_merge($baseShopQuery, ['category' => $categoryOption->slug]), fn ($value) => filled($value))) }}"
                                    class="filter-item {{ $categorySlug === $categoryOption->slug ? 'active' : '' }}">
                                    <div class="filter-label"><span class="dot"></span> {{ $categoryOption->name }}</div>
                                    <span class="count">{{ $categoryOption->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <form action="{{ route('shops.index') }}" method="GET" class="panel sidebar-panel location-filter-panel">
                        <h3>Browse By Location</h3>
                        @if(!empty($categorySlug))
                            <input type="hidden" name="category" value="{{ $categorySlug }}">
                        @endif
                        <input type="hidden" name="sort" value="{{ $sort }}">

                        <div class="location-filter-stack">
                            <select name="province">
                                <option value="">All provinces</option>
                                @foreach(($locationOptions ?? collect())->keys() as $provinceOption)
                                    <option value="{{ $provinceOption }}" {{ $province === $provinceOption ? 'selected' : '' }}>
                                        {{ $provinceOption }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="city">
                                <option value="">All cities / municipalities</option>
                                @foreach($availableCities as $cityOption)
                                    <option value="{{ $cityOption }}" {{ $city === $cityOption ? 'selected' : '' }}>
                                        {{ $cityOption }}
                                    </option>
                                @endforeach
                            </select>

                            @if($buyerLocation)
                                <label class="near-me-toggle">
                                    <input type="checkbox" name="near_me" value="1" {{ $nearMe ? 'checked' : '' }}>
                                    <span>Prioritize shops near my saved address</span>
                                </label>
                            @else
                                <small class="location-helper">Save a buyer address to enable nearest sorting.</small>
                            @endif
                        </div>

                        <div class="location-filter-actions">
                            <button class="action-btn primary-btn full-btn" type="submit">Apply Location</button>
                            @if($province || $city || $nearMe)
                                <a class="action-btn secondary-btn full-btn"
                                    href="{{ route('shops.index', array_filter([
                                        'category' => $categorySlug,
                                        'sort' => $sort === 'nearest' ? 'newest' : $sort,
                                    ], fn ($value) => filled($value))) }}">
                                    Clear Location
                                </a>
                            @endif
                        </div>
                    </form>

                    <form action="{{ route('shops.index') }}" method="GET" class="panel sidebar-panel">
                        <h3>Sort Results</h3>
                        @if(!empty($categorySlug))
                            <input type="hidden" name="category" value="{{ $categorySlug }}">
                        @endif
                        @if(!empty($province))
                            <input type="hidden" name="province" value="{{ $province }}">
                        @endif
                        @if(!empty($city))
                            <input type="hidden" name="city" value="{{ $city }}">
                        @endif
                        <select name="sort" onchange="this.form.submit()">
                            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                            @if($buyerLocation)
                                <option value="nearest" {{ $sort === 'nearest' ? 'selected' : '' }}>Nearest to Me</option>
                            @endif
                            <option value="most_products" {{ $sort === 'most_products' ? 'selected' : '' }}>Most Products</option>
                            <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                        </select>
                    </form>
                </aside>

                <div class="market-main">
                    @if($province || $city || $nearMe)
                        <div class="panel location-results-banner">
                            <div>
                                <strong>Location browsing active</strong>
                                <p>
                                    @if($nearMe && $buyerLocation)
                                        Prioritizing shops near {{ $buyerLocation->city }}, {{ $buyerLocation->province }}.
                                    @elseif($city && $province)
                                        Showing shops in {{ $city }}, {{ $province }}.
                                    @elseif($province)
                                        Showing shops in {{ $province }}.
                                    @else
                                        Showing shops in {{ $city }}.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="shops-grid" data-skeleton-group data-skeleton-delay="420">
                        @forelse($shops as $shop)
                            @php($locationLabel = \App\Support\LocationBrowsing::matchLabel($shop->sellerProfile, $buyerLocation))
                            <article class="shop-card panel skeleton-shell is-loading" data-skeleton-item data-skeleton-kind="shop-card">
                                <div class="shop-logo">
                                    <div class="shop-logo-frame skeleton skeleton-image">
                                        @if(!empty($shop->sellerProfile?->shop_logo))
                                            <img src="{{ asset('storage/' . $shop->sellerProfile->shop_logo) }}" alt="Shop Logo" loading="lazy" decoding="async">
                                        @else
                                            <div class="shop-logo-placeholder">
                                                <i class="fa-solid fa-store"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="shop-card-body">
                                    <div class="shop-card-badge-row skeleton skeleton-text">
                                        <span class="shop-badge">
                                            <i class="fa-solid fa-store"></i>
                                            Local Seller
                                        </span>
                                    </div>

                                    <div class="shop-title-row">
                                        <h3 class="skeleton skeleton-text">{{ $shop->sellerProfile?->store_name ?? $shop->name }}</h3>
                                        <x-seller-trust-badge :seller="$shop->sellerProfile" compact icon-only />
                                    </div>

                                    <div class="shop-products skeleton skeleton-text">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                        <span>{{ $shop->products_count }} product{{ $shop->products_count != 1 ? 's' : '' }}
                                            available</span>
                                    </div>

                                    @if($locationLabel)
                                        <div class="shop-location skeleton skeleton-text">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <span>{{ $locationLabel }}</span>
                                        </div>
                                    @endif

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
