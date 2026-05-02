@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">

    <section class="checkout-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <a href="{{ route('cart.index') }}">Cart</a>
                <span>&gt;</span>
                <span>Checkout</span>
            </div>



            <div class="checkout-layout">
                <div class="checkout-main">
                    <section class="checkout-card panel">
                        <div class="card-header">
                            <div class="step-title">
                                <span class="step-number">1</span>
                                <div>
                                    <span class="toolbar-label">Step</span>
                                    <h3>Shipping Address</h3>
                                </div>
                            </div>
                            <a href="{{ route('buyer.addresses', ['return_to' => route('checkout.index')]) }}"
                                class="action-link">Edit</a>
                        </div>

                        <div class="card-body">
                            <div class="shipping-address-box">
                                @if(isset($defaultAddress) && $defaultAddress)
                                    <p><strong>{{ $defaultAddress->full_name ?? auth()->user()->name }}</strong></p>
                                    <p>{{ $defaultAddress->phone ?? 'No phone number' }}</p>

                                    @if(!empty($defaultAddress->street_address))
                                        <p>{{ $defaultAddress->street_address }}</p>
                                    @endif

                                    @if(!empty($defaultAddress->landmark))
                                        <p>Landmark: {{ $defaultAddress->landmark }}</p>
                                    @endif

                                    <p>
                                        {{ $defaultAddress->barangay ?? '' }}
                                        @if(!empty($defaultAddress->barangay) && !empty($defaultAddress->city)), @endif
                                        {{ $defaultAddress->city ?? '' }}
                                        @if(!empty($defaultAddress->province)), {{ $defaultAddress->province }}@endif
                                        @if(!empty($defaultAddress->region)), {{ $defaultAddress->region }}@endif
                                        @if(!empty($defaultAddress->postal_code)), {{ $defaultAddress->postal_code }}@endif
                                    </p>
                                @else
                                    <p>No default address selected yet.</p>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section class="checkout-card panel">
                        <div class="card-header">
                            <div class="step-title">
                                <span class="step-number">2</span>
                                <div>
                                    <span class="toolbar-label">Step</span>
                                    <h3>Shipping Method</h3>
                                </div>
                            </div>
                            <span class="action-link">Choose</span>
                        </div>

                        <div class="card-body">
                            <div class="info-banner">
                                <span class="step-number small">1</span>
                                <strong>Product-Based Shipping</strong>
                            </div>

                            <label class="radio-option">
                                <input type="radio" name="shipping_method" checked disabled>
                                <span>Shipping fee is based on the selected products in your order.</span>
                            </label>
                        </div>
                    </section>

                    <section class="checkout-card panel">
                        <div class="card-header">
                            <div class="step-title">
                                <span class="step-number">3</span>
                                <div>
                                    <span class="toolbar-label">Step</span>
                                    <h3>Payment Information</h3>
                                </div>
                            </div>
                            <span class="action-link">Review</span>
                        </div>

                        <div class="card-body">
                            <div class="payment-option active">
                                <div class="payment-option-title">
                                    <span class="step-number small">4</span>
                                    <strong>Cash on Delivery</strong>
                                </div>

                                <div class="card-input-wrap">
                                    <input type="text" value="Pay when order arrives" readonly>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="checkout-sidebar">
                    <div class="order-summary panel">
                        <span class="section-kicker">Summary</span>
                        <h3>Order Summary</h3>

                        <div class="summary-items">
                            @forelse(($groupedCartItems ?? collect()) as $sellerCartItems)
                                @php
                                    $seller = $sellerCartItems->first()?->product?->user;
                                    $sellerSubtotal = $sellerCartItems->sum(fn($item) => (float) ($item->product->price ?? 0) * (int) $item->quantity);
                                @endphp
                                <div class="summary-item">
                                    <div class="summary-product">
                                        <div>
                                            <h4>{{ $seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller' }}</h4>
                                            <p>{{ $sellerCartItems->count() }} item{{ $sellerCartItems->count() !== 1 ? 's' : '' }} in this shop order</p>
                                        </div>
                                    </div>

                                    <div class="summary-price">
                                        <strong>&#8369; {{ number_format($sellerSubtotal, 2) }}</strong>
                                    </div>
                                </div>

                                @foreach($sellerCartItems as $item)
                                    <div class="summary-item">
                                        <div class="summary-product">
                                            <div class="summary-image">
                                                <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}"
                                                    alt="{{ $item->product->name ?? 'Product' }}"
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                            </div>
                                            <div>
                                                <h4>{{ $item->product->name ?? 'Product' }}</h4>
                                                <p>{{ $seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller' }}</p>
                                            </div>
                                        </div>

                                        <div class="summary-price">
                                            <strong>&#8369; {{ number_format($item->product->price, 2) }}</strong>
                                            <span>x{{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @empty
                                <p>Your cart is empty.</p>
                            @endforelse
                        </div>

                        <div class="summary-line">
                            <span>Subtotal</span>
                            <strong>&#8369; {{ number_format($subtotal, 2) }}</strong>
                        </div>

                        <div class="summary-line">
                            <span>Shipping Fee</span>
                            <strong>&#8369; {{ number_format($shippingFee, 2) }}</strong>
                        </div>

                        <div class="summary-total">
                            <span>Total</span>
                            <strong>&#8369; {{ number_format($total, 2) }}</strong>
                        </div>

                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            @foreach(($selectedCartItemIds ?? collect()) as $selectedCartItemId)
                                <input type="hidden" name="selected_cart_items[]" value="{{ $selectedCartItemId }}">
                            @endforeach
                            <button type="submit" class="action-btn primary-btn full-btn">Place Order</button>
                        </form>

                        <div class="coupon-box">
                            <input type="text" placeholder="Enter coupon code">
                            <button type="button">Apply</button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
