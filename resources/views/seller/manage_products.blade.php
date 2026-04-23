@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/manage_products.css') }}">

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <section class="seller-page-panel panel">
                        <div class="page-header">
                            <div>
                                <span class="section-kicker">Catalog</span>
                                <h2>My Products</h2>
                            </div>

                            <a href="{{ url('/add-product') }}" class="page-action-btn">
                                <i class="fa-solid fa-plus"></i> Add Product
                            </a>
                        </div>

                        <div class="product-status-tabs">
                            <a href="{{ route('seller.products.index', ['status' => 'live']) }}" class="product-status-tab {{ $currentTab === 'live' ? 'active' : '' }}">
                                <span>Live</span>
                                <strong>({{ $statusCounts['live'] }})</strong>
                            </a>

                            <a href="{{ route('seller.products.index', ['status' => 'sold_out']) }}" class="product-status-tab {{ $currentTab === 'sold_out' ? 'active' : '' }}">
                                <span>Sold Out</span>
                                <strong>({{ $statusCounts['sold_out'] }})</strong>
                            </a>

                            <a href="{{ route('seller.products.index', ['status' => 'reviewing']) }}" class="product-status-tab {{ $currentTab === 'reviewing' ? 'active' : '' }}">
                                <span>Reviewing</span>
                                <strong>({{ $statusCounts['reviewing'] }})</strong>
                            </a>

                            <a href="{{ route('seller.products.index', ['status' => 'violation']) }}" class="product-status-tab {{ $currentTab === 'violation' ? 'active' : '' }}">
                                <span>Violation</span>
                                <strong>({{ $statusCounts['violation'] }})</strong>
                            </a>

                            <a href="{{ route('seller.products.index', ['status' => 'delisted']) }}" class="product-status-tab {{ $currentTab === 'delisted' ? 'active' : '' }}">
                                <span>Delisted</span>
                                <strong>({{ $statusCounts['delisted'] }})</strong>
                            </a>
                        </div>

                        @if($statusCounts['reviewing'] > 0)
                            <div class="reviewing-note">
                                Your products under review are not visible to buyers yet.
                            </div>
                        @endif

                        @if(session('success'))
                            <p class="seller-feedback success-message">{{ session('success') }}</p>
                        @endif

                        <div class="table-panel">
                            <table class="seller-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        @php
                                            $displayStatus = 'Delisted';
                                            $statusClass = 'delisted';

                                            if ($product->status === 'pending') {
                                                $displayStatus = 'Reviewing';
                                                $statusClass = 'reviewing';
                                            } elseif ($product->status === 'rejected') {
                                                $displayStatus = 'Violation';
                                                $statusClass = 'violation';
                                            } elseif ($product->status === 'approved' && (int) $product->is_active === 1 && (int) $product->stock > 0) {
                                                $displayStatus = 'Live';
                                                $statusClass = 'live';
                                            } elseif ($product->status === 'approved' && (int) $product->is_active === 1 && (int) $product->stock <= 0) {
                                                $displayStatus = 'Sold Out';
                                                $statusClass = 'sold-out';
                                            }
                                        @endphp

                                        <tr>
                                            <td>
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                        class="product-image">
                                                @else
                                                    <span class="muted-label">No Image</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="seller-product-name">{{ $product->name }}</div>
                                                <div class="seller-review-inline">
                                                    <span class="seller-rating-chip">
                                                        <i class="fa-solid fa-star"></i>
                                                        {{ $product->reviews_avg_rating ? number_format((float) $product->reviews_avg_rating, 1) : 'New' }}
                                                    </span>
                                                    <span class="seller-review-count">
                                                        {{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                            <td>PHP {{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>
                                                <div class="seller-rating-chip">
                                                    <i class="fa-solid fa-star"></i>
                                                    {{ $product->reviews_avg_rating ? number_format((float) $product->reviews_avg_rating, 1) : 'New' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-chip {{ $statusClass }}">{{ $displayStatus }}</span>
                                            </td>

                                            <td class="action-buttons">
                                                <a href="{{ url('/edit-product/' . $product->id) }}"
                                                    class="table-action secondary">Edit</a>

                                                <form action="{{ url('/delete-product/' . $product->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="table-action danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr class="seller-review-row">
                                            <td colspan="8">
                                                <div class="seller-review-panel">
                                                    <strong>Recent Reviews</strong>

                                                    @if($product->reviews->isNotEmpty())
                                                        <div class="seller-review-list">
                                                            @foreach($product->reviews->take(3) as $review)
                                                                <article class="seller-review-card">
                                                                    <div class="seller-review-card-header">
                                                                        <span>{{ $review->user->name ?? 'Buyer' }}</span>
                                                                        <span>{{ $review->created_at->format('M d, Y') }}</span>
                                                                    </div>
                                                                    <div class="seller-rating-chip">
                                                                        <i class="fa-solid fa-star"></i>
                                                                        {{ $review->rating }}/5
                                                                    </div>
                                                                    <p>{{ $review->comment ?: 'Verified buyer rating submitted.' }}</p>
                                                                </article>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p class="empty-review-copy">No ratings or reviews for this product yet.</p>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="empty-text">No products found in this status.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </section>
@endsection
