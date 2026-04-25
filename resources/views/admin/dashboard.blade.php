@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Dashboard')
@section('page-title', 'Welcome, Admin!')
@section('page-description', 'Review pending sellers and product approvals from one workspace.')

@section('content')
    <div class="page-stack">
        <section class="summary-grid">
            @foreach ($stats as $stat)
                <article class="summary-card summary-card--{{ $stat['tone'] }}">
                    <p class="summary-card__label">{{ $stat['label'] }}</p>
                    <div class="summary-card__value">
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $stat['note'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="content-grid">
            <div class="stack">
                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Pending Product Approvals</h3>
                    </div>

                    <div class="product-card-grid">
                        @forelse ($pendingProducts as $product)
                            <article class="product-card">
                                <div class="product-card__body">
                                    <h4 class="product-card__name">{{ $product->name }}</h4>
                                    <div class="muted-row">
                                        <i class="fa-solid fa-user"></i>
                                        <span>{{ $product->user->name ?? 'Seller' }}</span>
                                    </div>
                                </div>

                                <div class="hero-thumb hero-thumb--earrings"></div>

                                <div class="product-card__body">
                                    <div class="product-card__meta">
                                        <div class="meta-row">
                                            <span>Category:</span>
                                            <strong>{{ $product->category->name ?? 'Uncategorized' }}</strong>
                                        </div>
                                        <div class="meta-row">
                                            <span>Price:</span>
                                            <strong>&#8369; {{ number_format($product->price, 2) }}</strong>
                                        </div>
                                    </div>

                                    <div class="muted-row">
                                        <span>Status:</span>
                                        <strong>{{ ucfirst($product->status) }}</strong>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="sub-line">No pending product approvals right now.</p>
                        @endforelse
                    </div>
                </article>
            </div>

            <div class="stack">
                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Seller Verification Requests</h3>
                    </div>

                    @forelse ($pendingSellers as $seller)
                        <div class="seller-request-card">
                            <div class="seller-request-card__profile">
                                <div class="avatar-circle">{{ strtoupper(substr($seller->full_name ?? $seller->user?->name ?? 'S', 0, 1)) }}</div>
                                <div>
                                    <div class="seller-name">{{ $seller->full_name ?? $seller->user?->name ?? 'Seller' }}</div>
                                    <div class="sub-line">{{ $seller->seller_type === 'registered_business' ? 'Registered Business' : 'Individual Seller' }}</div>
                                </div>
                            </div>

                            <div class="seller-request-card__details spacer-top">
                                <div class="detail-line">
                                    <span><i class="fa-solid fa-phone"></i> {{ $seller->contact_number }}</span>
                                </div>
                                <div class="detail-line">
                                    <span><i class="fa-solid fa-location-dot"></i> {{ $seller->address }}</span>
                                </div>
                                <div>
                                    <span class="status-pill status-pill--pending"><i class="fa-regular fa-id-card"></i> Pending seller review</span>
                                </div>
                            </div>

                            <div class="button-row spacer-top">
                                <a href="{{ route('admin.sellers') }}" class="action-button action-button--primary">Open Review</a>
                            </div>
                        </div>
                    @empty
                        <p class="sub-line">No pending seller applications.</p>
                    @endforelse
                </article>
            </div>
        </section>
    </div>
@endsection
