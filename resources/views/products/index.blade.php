@extends('layouts.app')
@section('title', 'LocalLift PH - Products')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/productsStyle.css') }}">
    @php
        $marketPaginationScript = asset('assets/js/market-pagination.js') . '?v=' . @filemtime(public_path('assets/js/market-pagination.js'));
    @endphp

    <section class="market-page products-page">
        <div class="container">
            <div class="page-intro">
                <div class="checkout-breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&gt;</span>
                    <span>Products</span>
                </div>
            </div>

            @php
                $baseProductQuery = array_filter([
                    'search' => $search,
                    'category' => $categorySlug,
                    'sort' => $sort,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
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
                                <option
                                    value="{{ route('products.index', array_filter(array_merge($baseProductQuery, ['category' => null]), fn ($value) => filled($value))) }}"
                                    {{ empty($categorySlug) ? 'selected' : '' }}>
                                    All ({{ $categories->sum('products_count') }})
                                </option>
                                @foreach($categories as $categoryOption)
                                    <option
                                        value="{{ route('products.index', array_filter(array_merge($baseProductQuery, ['category' => $categoryOption->slug]), fn ($value) => filled($value))) }}"
                                        {{ $categorySlug === $categoryOption->slug ? 'selected' : '' }}>
                                        {{ $categoryOption->name }} ({{ $categoryOption->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-list">
                            <a href="{{ route('products.index', array_filter(array_merge($baseProductQuery, ['category' => null]), fn ($value) => filled($value))) }}"
                                class="filter-item {{ empty($categorySlug) ? 'active' : '' }}">
                                <div class="filter-label"><span class="dot"></span> All</div>
                                <span class="count">{{ $categories->sum('products_count') }}</span>
                            </a>
                            @foreach($categories as $categoryOption)
                                <a href="{{ route('products.index', array_filter(array_merge($baseProductQuery, ['category' => $categoryOption->slug]), fn ($value) => filled($value))) }}"
                                    class="filter-item {{ $categorySlug === $categoryOption->slug ? 'active' : '' }}">
                                    <div class="filter-label"><span class="dot"></span> {{ $categoryOption->name }}</div>
                                    <span class="count">{{ $categoryOption->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <form action="{{ route('products.index') }}" method="GET" class="panel sidebar-panel">
                        <h3>Filter By Price</h3>
                        <div class="price-labels">
                            <span>Min</span>
                            <span>Max</span>
                        </div>

                        @if(!empty($search))
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        @if(!empty($categorySlug))
                            <input type="hidden" name="category" value="{{ $categorySlug }}">
                        @endif
                        @if(!empty($province))
                            <input type="hidden" name="province" value="{{ $province }}">
                        @endif
                        @if(!empty($city))
                            <input type="hidden" name="city" value="{{ $city }}">
                        @endif
                        @if($nearMe)
                            <input type="hidden" name="near_me" value="1">
                        @endif
                        <input type="hidden" name="sort" value="{{ $sort }}">

                        <div class="price-filter-inputs">
                            <input type="number" name="min_price" min="0" step="0.01" value="{{ $minPrice }}"
                                placeholder="0">
                            <input type="number" name="max_price" min="0" step="0.01" value="{{ $maxPrice }}"
                                placeholder="1000">
                        </div>

                        <button class="action-btn primary-btn full-btn" type="submit">Filter</button>
                    </form>

                    <form action="{{ route('products.index') }}" method="GET" class="panel sidebar-panel location-filter-panel">
                        <h3>Browse By Location</h3>
                        @if(!empty($search))
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        @if(!empty($categorySlug))
                            <input type="hidden" name="category" value="{{ $categorySlug }}">
                        @endif
                        @if($minPrice !== null)
                            <input type="hidden" name="min_price" value="{{ $minPrice }}">
                        @endif
                        @if($maxPrice !== null)
                            <input type="hidden" name="max_price" value="{{ $maxPrice }}">
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
                                    href="{{ route('products.index', array_filter([
                                        'search' => $search,
                                        'category' => $categorySlug,
                                        'sort' => $sort === 'nearest' ? 'newest' : $sort,
                                        'min_price' => $minPrice,
                                        'max_price' => $maxPrice,
                                    ], fn ($value) => filled($value))) }}">
                                    Clear Location
                                </a>
                            @endif
                        </div>
                    </form>

                    <form action="{{ route('products.index') }}" method="GET" class="panel sidebar-panel">
                        <h3>Sort Results</h3>
                        @if(!empty($search))
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        @if(!empty($categorySlug))
                            <input type="hidden" name="category" value="{{ $categorySlug }}">
                        @endif
                        @if($minPrice !== null)
                            <input type="hidden" name="min_price" value="{{ $minPrice }}">
                        @endif
                        @if($maxPrice !== null)
                            <input type="hidden" name="max_price" value="{{ $maxPrice }}">
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
                            <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price Low to High</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price High to Low
                            </option>
                        </select>
                    </form>
                </aside>

                <div class="market-main">
                    @if(session('success'))
                        <div
                            style="margin-bottom: 15px; padding: 12px; background: #e8f7ee; color: #1f7a3d; border-radius: 8px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(!empty($search))
                        <div class="panel" style="padding: 10px; margin-bottom: 16px;">
                            <p>Search results for: <strong>{{ $search }}</strong></p>
                        </div>
                    @endif

                    @if($province || $city || $nearMe)
                        <div class="panel location-results-banner">
                            <div>
                                <strong>Location browsing active</strong>
                                <p>
                                    @if($nearMe && $buyerLocation)
                                        Prioritizing sellers near {{ $buyerLocation->city }}, {{ $buyerLocation->province }}.
                                    @elseif($city && $province)
                                        Showing sellers in {{ $city }}, {{ $province }}.
                                    @elseif($province)
                                        Showing sellers in {{ $province }}.
                                    @else
                                        Showing sellers in {{ $city }}.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(!empty($search) && isset($shops) && $shops->count())
                        <div class="panel" style="padding: 20px; margin-bottom: 20px;">
                            <h3 style="margin-bottom: 14px;">Matching Shops</h3>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                @foreach($shops as $shop)
                                    <div style="padding: 16px; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                            <img src="{{ !empty($shop->profile_image) ? asset('storage/' . $shop->profile_image) : asset('assets/images/default-product.png') }}"
                                                alt="{{ $shop->name }}"
                                                loading="lazy"
                                                decoding="async"
                                                style="width: 52px; height: 52px; object-fit: cover; border-radius: 50%;">
                                            <div>
                                                <h4 style="margin: 0;">{{ $shop->name }}</h4>
                                                <small style="color: #9fb3c8;">
                                                    {{ $shop->products_count }} product{{ $shop->products_count != 1 ? 's' : '' }}
                                                </small>
                                            </div>
                                        </div>

                                        <a href="{{ route('shops.show', $shop->id) }}" class="action-btn secondary-btn">
                                            Visit Shop
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div
                        data-market-pagination-root
                        data-market-pagination-count="{{ max($products->count(), 12) }}"
                        data-market-pagination-scroll-target=".market-main"
                    >
                        @include('products.partials.results', [
                            'products' => $products,
                            'search' => $search,
                            'buyerLocation' => $buyerLocation,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="{{ $marketPaginationScript }}" defer></script>
@endsection
