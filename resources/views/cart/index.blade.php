

@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">

    <section class="cart-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <a href="{{ route('products.index') }}">Products</a>
                <span>&gt;</span>
                <span>Cart</span>
            </div>

            @php
    $total = 0;
    $selectedCartItemId = session('selected_cart_item_id');
    $selectedCartItemIds = collect(session('selected_cart_item_ids', []))
        ->map(fn($id) => (int) $id)
        ->filter()
        ->values();
    $hasSelectedCartItem = filled($selectedCartItemId);
    $hasSelectedCartItems = $selectedCartItemIds->isNotEmpty();
    $selectedSubtotal = 0;
    $selectedShipping = 0;
            @endphp

            <div
                class="cart-layout"
                data-selected-cart-item-id="{{ $selectedCartItemId ?? '' }}"
                data-selected-cart-item-ids='@json($selectedCartItemIds)'
                data-selection-storage-key="locallift-cart-selection-{{ auth()->id() }}"
            >
                <div class="cart-main">


                    <div class="cart-list panel">
                        <div class="select-all-row">
                            <label>
                                <input type="checkbox" id="select-all-cart-items">
                                <span>Select All</span>
                            </label>
                        </div>

                        <div class="cart-table-head">
                            <div>Product</div>
                            <div>Price</div>
                            <div>Quantity</div>
                            <div>Subtotal</div>
                        </div>

                        @forelse($cartItems as $item)
                            @php
        $variant = $item->variant;
        $unitPrice = (float) ($variant?->price ?? $item->product->price ?? 0);
        $availableStock = max(0, (int) ($variant?->stock ?? $item->product->stock ?? 0));
        $productImage = $variant?->image ?: $item->product->image;
        $subtotal = $unitPrice * $item->quantity;
        $shipping = ($item->product->shipping_fee ?? 0) * $item->quantity;
        $total += $subtotal;
        $isChecked = (int) $selectedCartItemId === (int) $item->id
            || $selectedCartItemIds->contains((int) $item->id);
        if ($isChecked) {
            $selectedSubtotal += $subtotal;
            $selectedShipping += $shipping;
        }
                            @endphp

                            <article
                                class="cart-item"
                                data-cart-item-id="{{ $item->id }}"
                                data-max-stock="{{ $availableStock }}"
                                data-subtotal="{{ $subtotal }}"
                                data-shipping="{{ $shipping }}"
                                data-unit-price="{{ $unitPrice }}"
                                data-unit-shipping="{{ (float) ($item->product->shipping_fee ?? 0) }}"
                            >
                                <div class="item-select">
                                    <input
                                        type="checkbox"
                                        class="cart-item-checkbox"
                                        value="{{ $item->id }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                    >
                                </div>

                                <div class="item-product">
                                    <div class="product-image">
                                        <img src="{{ $productImage ? asset('storage/' . $productImage) : asset('assets/images/default-product.png') }}" alt="{{ $item->product->name }}">
                                    </div>

                                    <div class="product-copy">
                                        <h3>{{ $item->product->name }}</h3>
                                        @if($variant)
                                            <p>Option: {{ $variant->displayName() }}</p>
                                        @endif
                                        <p>{{ $item->product->user?->sellerProfile?->store_name ?? 'LocalLift Shop' }}</p>
                                    </div>
                                </div>

                                <div class="item-price">&#8369; {{ number_format($unitPrice, 2) }}</div>

                                <div class="item-quantity">
                                    <div class="qty-box">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" data-cart-quantity-form>
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}" data-cart-next-quantity="decrement">
                                            <button type="submit" data-cart-quantity-button="decrement">-</button>
                                        </form>

                                        <input type="text" value="{{ $item->quantity }}" readonly data-cart-quantity-display>

                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" data-cart-quantity-form>
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}" data-cart-next-quantity="increment">
                                            <button type="submit" data-cart-quantity-button="increment">+</button>
                                        </form>
                                    </div>
                                    <small class="quantity-note" data-cart-quantity-note hidden></small>
                                </div>

                                <div class="item-subtotal">
                                    <strong data-cart-item-subtotal>&#8369; {{ number_format($subtotal, 2) }}</strong>

                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-btn">Remove</button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="empty-state">
                                <span class="section-kicker">Empty Cart</span>
                                <h3>No products in your cart yet</h3>
                                <p>Browse the product catalog and add items here when you are ready to check out.</p>
                                <a href="{{ route('products.index') }}" class="action-btn primary-btn">Explore Products</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <aside class="cart-sidebar">
                    <div class="cart-summary panel">
                        <div class="summary-title">
                            <h3>Cart Summary</h3>
                        </div>

                        <div class="summary-line">
                            <span>Subtotal</span>
                            <strong id="cart-summary-subtotal">&#8369; {{ number_format($selectedSubtotal, 2) }}</strong>
                        </div>

                        <div class="summary-line">
                            <span>Shipping</span>
                            <strong id="cart-summary-shipping">&#8369; {{ number_format($selectedShipping, 2) }}</strong>
                        </div>

                        <div class="summary-total">
                            <span>Total</span>
                            <strong id="cart-summary-total">&#8369; {{ number_format($selectedSubtotal + $selectedShipping, 2) }}</strong>
                        </div>

                        @unless($hasSavedAddress ?? false)
                            <div class="cart-checkout-warning" role="alert">
                                <strong>Please add a delivery address before placing an order.</strong>
                                <a href="{{ route('buyer.addresses', ['return_to' => route('cart.index')]) }}" class="action-btn secondary-btn full-btn">
                                    Add Delivery Address
                                </a>
                            </div>
                        @endunless

                        <form action="{{ route('checkout.index') }}" method="GET" id="cart-checkout-form" data-enable-loading>
                            <div id="selected-cart-items-inputs"></div>
                            <button
                                type="submit"
                                class="action-btn primary-btn full-btn"
                                data-enable-loading
                                data-loading-text="Loading Checkout..."
                                {{ ($hasSavedAddress ?? false) ? '' : 'disabled' }}
                            >
                                Checkout
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @vite(['resources/js/cart.js'])
@endsection
