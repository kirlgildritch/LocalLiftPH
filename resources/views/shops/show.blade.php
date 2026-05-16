@extends('layouts.app')
@section('title', 'LocalLift PH - Shop')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/shop_details.css') }}">
@php
    $ownsShop = auth()->check() && (int) $user->id === (int) auth()->id();
    $shopCategories = $products->groupBy(fn ($product) => $product->category?->name ?? 'Uncategorized');
    $canReportSeller = auth('web')->check() && ! $ownsShop;
    $shopReviewsToggleUrl = $showAllReviews
        ? route('shops.show', $user) . '#shop-reviews'
        : route('shops.show', array_merge(request()->query(), ['user' => $user->getRouteKey(), 'show_reviews' => 'all'])) . '#shop-reviews';
@endphp

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
                        <div class="shop-kicker-row">
                            <span class="section-kicker">Local Seller</span>
                        </div>
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

                    <div class="shop-hero-title-row">
                        <h1>{{ $user->sellerProfile?->store_name ?? 'My Shop' }}</h1>
                        <x-seller-trust-badge :seller="$user->sellerProfile" icon-only />
                    </div>

                        @if(filled($user->sellerProfile?->store_description))
                        <p class="shop-description">
                            {{ $user->sellerProfile?->store_description }}
                        </p>
                        @endif

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
                                <form action="{{ $isFollowing ? route('shops.unfollow', $user) : route('shops.follow', $user) }}" method="POST" data-shop-follow-form data-follow-url="{{ route('shops.follow', $user) }}" data-unfollow-url="{{ route('shops.unfollow', $user) }}">
                                    @csrf
                                    @if($isFollowing)
                                        @method('DELETE')
                                    @endif
                                    <button type="submit" class="action-btn secondary-btn" data-shop-follow-button>
                                        <i class="fa-{{ $isFollowing ? 'solid' : 'regular' }} fa-heart" data-shop-follow-icon></i>&nbsp;
                                        <span data-shop-follow-label>{{ $isFollowing ? 'Following' : 'Follow Shop' }}</span>
                                    </button>
                                </form>
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
                                <i class="fa-regular fa-heart"></i>&nbsp; Follow Shop
                            </a>
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
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div>
                        <strong><span data-shop-follower-count>{{ $followerCount ?? 0 }}</span> Follower{{ ($followerCount ?? 0) === 1 ? '' : 's' }}</strong>
                        <span>Buyers following updates from this shop.</span>
                    </div>
                </div>

                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <strong>Shop trust</strong>
                        <span>{{ $user->sellerProfile?->hasVerifiedSellerBadge() ? 'Verified shops show a check beside their name.' : 'Committed to quality products and excellent service.' }}</span>
                    </div>
                </div>
            </div>

            @include('vouchers.partials.buyer-voucher-list', [
                'vouchers' => $sellerVouchers ?? collect(),
                'title' => 'Shop Vouchers',
            ])
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
                            <p>
                                LocalLift seller
                                <x-seller-trust-badge :seller="$user->sellerProfile" compact icon-only />
                            </p>
                        </div>
                    </div>

                    <div class="shop-sidebar-stats">
                        <div class="stat-chip">
                            <strong>{{ $products->count() }}</strong>
                            <span>Active products</span>
                        </div>
                        <div class="stat-chip">
                            <strong data-shop-follower-count>{{ $followerCount ?? 0 }}</strong>
                            <span>Followers</span>
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
                <div class="panel content-panel" id="shop-reviews">
                    <div class="content-header content-header--split">
                        <div>
                            <h2>Shop Reviews</h2>
                            <p class="shop-section-copy">Recent buyer feedback from this seller’s visible products.</p>
                        </div>

                        <div class="shop-review-summary">
                            <strong>{{ $sellerReviewAverage > 0 ? number_format($sellerReviewAverage, 1) : '0.0' }}</strong>
                            <span>{{ $sellerReviewCount }} review{{ $sellerReviewCount !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>

                    @if($sellerReviewCount > $initialReviewsLimit)
                        <div class="shop-review-toggle">
                            <a href="{{ $shopReviewsToggleUrl }}" class="action-btn secondary-btn">
                                {{ $showAllReviews ? 'Show Fewer Reviews' : 'View All Reviews' }}
                            </a>
                        </div>
                    @endif

                    @if($sellerReviews->isEmpty())
                        <div class="shop-review-empty">
                            <h3>No reviews yet</h3>
                            <p>This shop has not received buyer feedback yet.</p>
                        </div>
                    @else
                        <div class="shop-review-list">
                            @foreach($sellerReviews as $review)
                                @include('shops.partials.review-card', ['review' => $review])
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="panel content-panel">
                    <div class="content-header" id="shop-products">
                        <div>
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

                    @foreach($shopCategories as $category => $categoryProducts)
                        <span id="category-{{ \Illuminate\Support\Str::slug($category) }}"></span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-shop-follow-form]');

    if (!form) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const button = form.querySelector('[data-shop-follow-button]');
    const icon = form.querySelector('[data-shop-follow-icon]');
    const label = form.querySelector('[data-shop-follow-label]');
    const countTargets = Array.from(document.querySelectorAll('[data-shop-follower-count]'));

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (button) {
            button.disabled = true;
        }

        const isUnfollow = form.querySelector('input[name="_method"]')?.value === 'DELETE';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: new FormData(form),
                credentials: 'same-origin',
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Unable to update shop follow status.');
            }

            form.action = payload.is_following ? form.dataset.unfollowUrl : form.dataset.followUrl;
            form.querySelector('input[name="_method"]')?.remove();

            if (payload.is_following) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
            }

            if (icon) {
                icon.className = 'fa-' + (payload.is_following ? 'solid' : 'regular') + ' fa-heart';
            }

            if (label) {
                label.textContent = payload.is_following ? 'Following' : 'Follow Shop';
            }

            countTargets.forEach((target) => {
                target.textContent = String(payload.follower_count ?? 0);
            });
        } catch (error) {
            window.alert(error.message || 'Unable to update shop follow status.');
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    });
});
</script>
@endsection
