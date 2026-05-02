@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/manage_products.css') }}">

    <section class="dashboard-wrapper seller-product-reviews-page">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    @include('seller.partials.success-toast')

                    <section class="seller-page-panel panel seller-product-reviews-panel">
                        <div class="page-header seller-product-reviews-header">
                            <div>
                                <span class="section-kicker">Catalog</span>
                                <h2>Product Reviews</h2>
                                <p>See all buyer feedback for this product in one place.</p>
                            </div>

                            <a href="{{ route('seller.products.index') }}" class="table-action secondary">
                                <i class="fa-solid fa-arrow-left"></i>
                                Back to Products
                            </a>
                        </div>

                        <section class="seller-review-product-summary">
                            <div class="seller-review-product-main">
                                <div class="seller-review-product-thumb">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="seller-review-product-placeholder">No Image</div>
                                    @endif
                                </div>

                                <div class="seller-review-product-copy">
                                    <h3>{{ $product->name }}</h3>
                                    <p>{{ $product->category?->name ?? 'Uncategorized' }}</p>
                                    <strong>&#8369; {{ number_format($product->price, 2) }}</strong>
                                </div>
                            </div>

                            <div class="seller-review-summary-cards">
                                <article class="seller-review-summary-card">
                                    <span>Average Rating</span>
                                    <strong>{{ $product->reviews_avg_rating ? number_format((float) $product->reviews_avg_rating, 1) : 'New' }}</strong>
                                </article>
                                <article class="seller-review-summary-card">
                                    <span>Total Reviews</span>
                                    <strong>{{ $product->reviews_count }}</strong>
                                </article>
                            </div>
                        </section>

                        @if($reviews->isEmpty())
                            <div class="seller-review-empty-state">
                                <h3>No reviews yet</h3>
                                <p>This product has not received buyer feedback yet.</p>
                            </div>
                        @else
                            <div class="seller-review-page-list">
                                @foreach($reviews as $review)
                                    <article class="seller-review-page-card">
                                        <div class="seller-review-page-header">
                                            <div>
                                                <strong>{{ $review->user->name ?? 'Buyer' }}</strong>
                                                <span>{{ $review->created_at->format('M d, Y') }}</span>
                                            </div>

                                            <div class="seller-rating-chip">
                                                <i class="fa-solid fa-star"></i>
                                                {{ $review->rating }}/5
                                            </div>
                                        </div>

                                        <p>{{ $review->comment ?: 'Verified buyer rating submitted.' }}</p>
                                    </article>
                                @endforeach
                            </div>

                            @if($reviews->hasPages())
                                <div class="seller-review-pagination">
                                    @if($reviews->onFirstPage())
                                        <span class="table-action secondary seller-review-pagination-button is-disabled">Previous</span>
                                    @else
                                        <a href="{{ $reviews->previousPageUrl() }}"
                                            class="table-action secondary seller-review-pagination-button">Previous</a>
                                    @endif

                                    <span class="seller-review-pagination-meta">
                                        Page {{ $reviews->currentPage() }} of {{ $reviews->lastPage() }}
                                    </span>

                                    @if($reviews->hasMorePages())
                                        <a href="{{ $reviews->nextPageUrl() }}"
                                            class="table-action secondary seller-review-pagination-button">Next</a>
                                    @else
                                        <span class="table-action secondary seller-review-pagination-button is-disabled">Next</span>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </section>
                </main>
            </div>
        </div>
    </section>

    <style>
        .seller-product-reviews-panel {
            display: grid;
            gap: 22px;
            padding: 24px;
        }

        .seller-product-reviews-header p {
            margin: 10px 0 0;
            color: #8fa7c4;
            line-height: 1.75;
        }

        .seller-review-product-summary {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 18px;
            padding: 20px;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.03);
        }

        .seller-review-product-main {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .seller-review-product-thumb {
            width: 96px;
            height: 96px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(187, 222, 251, 0.12);
            background: rgba(255, 255, 255, 0.03);
            flex-shrink: 0;
        }

        .seller-review-product-thumb img,
        .seller-review-product-placeholder {
            width: 100%;
            height: 100%;
        }

        .seller-review-product-thumb img {
            object-fit: cover;
            display: block;
        }

        .seller-review-product-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8fa7c4;
            font-size: 12px;
            font-weight: 600;
        }

        .seller-review-product-copy {
            display: grid;
            gap: 8px;
        }

        .seller-review-product-copy h3,
        .seller-review-empty-state h3,
        .seller-review-page-header strong {
            margin: 0;
            color: #f5f9ff;
        }

        .seller-review-product-copy p,
        .seller-review-empty-state p,
        .seller-review-page-card p,
        .seller-review-page-header span {
            margin: 0;
            color: #8fa7c4;
        }

        .seller-review-summary-cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(140px, 1fr));
            gap: 12px;
        }

        .seller-review-summary-card {
            display: grid;
            gap: 8px;
            padding: 18px;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 20px;
            background: rgba(7, 15, 27, 0.42);
        }

        .seller-review-summary-card span {
            color: #8fa7c4;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .seller-review-summary-card strong {
            color: #f5f9ff;
            font-size: 1.4rem;
        }

        .seller-review-empty-state,
        .seller-review-page-card {
            padding: 20px;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.03);
        }

        .seller-review-page-list {
            display: grid;
            gap: 14px;
        }

        .seller-review-page-card {
            display: grid;
            gap: 14px;
        }

        .seller-review-page-header {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 14px;
        }

        .seller-review-page-header > div:first-child {
            display: grid;
            gap: 6px;
        }

        .seller-review-pagination {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            margin-top: 4px;
        }

        .seller-review-pagination-meta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.03);
            color: #8fa7c4;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }

        .seller-review-pagination-button {
            min-width: 118px;
        }

        .seller-review-pagination > :first-child {
            justify-self: start;
        }

        .seller-review-pagination > :last-child {
            justify-self: end;
        }

        .is-disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        @media (max-width: 900px) {
            .seller-review-product-summary,
            .seller-review-page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .seller-review-summary-cards {
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .seller-review-summary-cards {
                grid-template-columns: 1fr;
            }

            .seller-review-product-main {
                align-items: flex-start;
            }

            .seller-review-pagination {
                grid-template-columns: 1fr;
            }

            .seller-review-pagination-button,
            .seller-review-pagination > :first-child,
            .seller-review-pagination > :last-child {
                justify-self: stretch;
            }

            .seller-review-pagination-button {
                width: 100%;
            }
        }
    </style>
@endsection
