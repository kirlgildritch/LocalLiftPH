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

                                    @if(!empty($defaultAddress->street))
                                        <p>{{ $defaultAddress->street }}</p>
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
                                <strong>Free Shipping</strong>
                            </div>

                            <label class="radio-option">
                                <input type="radio" name="shipping_method" checked disabled>
                                <span>Standard Shipping (P0.00)</span>
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
                            @forelse($cartItems as $item)
                                <div class="summary-item">
                                    <div class="summary-product">
                                        <div class="summary-image">
                                            <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}"
                                                alt="{{ $item->product->name ?? 'Product' }}"
                                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        </div>
                                        <div>
                                            <h4>{{ $item->product->name ?? 'Product' }}</h4>
                                            <p>{{ $item->product->user->name ?? 'LocalLift Seller' }}</p>
                                        </div>
                                    </div>

                                    <div class="summary-price">
                                        <strong>P{{ number_format($item->product->price, 2) }}</strong>
                                        <span>x{{ $item->quantity }}</span>
                                    </div>
                                </div>
                            @empty
                                <p>Your cart is empty.</p>
                            @endforelse
                        </div>

                        <div class="summary-line">
                            <span>Subtotal</span>
                            <strong>P{{ number_format($total, 2) }}</strong>
                        </div>

                        <div class="summary-line">
                            <span>Shipping Fee</span>
                            <strong>P0.00</strong>
                        </div>

                        <div class="summary-total">
                            <span>Total</span>
                            <strong>P{{ number_format($total, 2) }}</strong>
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