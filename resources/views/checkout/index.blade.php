@extends('layouts.app')
@section('title', 'LocalLift PH - Checkout')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    @php
        $paymentMethods = $paymentMethods ?? \App\Models\Order::paymentMethods();
        $selectedPaymentMethod = $selectedPaymentMethod ?? \App\Models\Order::PAYMENT_METHOD_COD;
        $selectedPayment = $paymentMethods[$selectedPaymentMethod] ?? reset($paymentMethods);
        $voucherCode = old('voucher_code');
    @endphp

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
                    <section class="checkout-card checkout-card--payment panel">
                        <div class="card-header">
                            <div class="step-title">
                                <span class="step-number">1</span>
                                <div>
                                    <span class="toolbar-label">Step</span>
                                    <h3>Shipping Address</h3>
                                </div>
                            </div>
                            <a href="{{ route('buyer.addresses', ['return_to' => request()->fullUrl()]) }}"
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
                                    <p>Please add a delivery address before placing an order.</p>
                                    <a href="{{ route('buyer.addresses', ['return_to' => request()->fullUrl()]) }}"
                                        class="action-btn secondary-btn">
                                        Add Delivery Address
                                    </a>
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
                            <span class="action-link">Auto</span>
                        </div>

                        <div class="card-body">
                            <div class="delivery-method-list">
                                @foreach(($groupedCartItems ?? collect()) as $sellerId => $sellerCartItems)
                                    @php
                                        $seller = $sellerCartItems->first()?->product?->user;
                                        $estimate = ($deliveryEstimates ?? collect())->get($sellerId);
                                        $sellerShipping = $sellerCartItems->sum(fn($item) => (float) ($item->product->shipping_fee ?? 0) * (int) $item->quantity);
                                    @endphp

                                    <article class="delivery-method-card">
                                        <div class="delivery-method-card__icon">
                                            <i class="fa-solid fa-truck-fast"></i>
                                        </div>

                                        <div class="delivery-method-card__copy">
                                            <strong>{{ $seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller' }}</strong>
                                            <span>{{ $estimate['label'] ?? 'Standard local courier' }}</span>
                                            <p>
                                                Estimated delivery:
                                                <b>{{ $estimate['date_range'] ?? '3-5 days' }}</b>
                                            </p>
                                            @if(!empty($estimate['is_fallback']))
                                                <small>Estimate uses standard courier timing because seller location is limited.</small>
                                            @endif
                                        </div>

                                        <div class="delivery-method-card__price">
                                            <span>Shipping</span>
                                            <strong>&#8369; {{ number_format($sellerShipping, 2) }}</strong>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
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
                        </div>

                        <div class="card-body">
                            <div class="payment-method-grid" data-payment-methods>
                                @foreach($paymentMethods as $methodKey => $method)
                                    <label class="payment-method-card {{ $selectedPaymentMethod === $methodKey ? 'is-selected' : '' }}">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="{{ $methodKey }}"
                                            form="checkout-submit-form"
                                            data-payment-choice
                                            data-payment-label="{{ $method['label'] }}"
                                            data-payment-short-label="{{ $method['short_label'] }}"
                                            data-payment-instructions="{{ $method['instructions'] }}"
                                            {{ $selectedPaymentMethod === $methodKey ? 'checked' : '' }}
                                        >
                                        <span class="payment-method-card__icon">
                                            <i class="fa-solid {{ $method['icon'] }}"></i>
                                        </span>
                                        <span class="payment-method-card__copy">
                                            <strong>{{ $method['label'] }}</strong>
                                            <small>{{ $method['description'] }}</small>
                                        </span>
                                        <span class="payment-method-card__check">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="payment-note" data-payment-note>
                                <i class="fa-solid fa-circle-info"></i>
                                <div>
                                    <strong data-payment-note-title>{{ $selectedPayment['label'] }}</strong>
                                    <p data-payment-note-copy>{{ $selectedPayment['instructions'] }}</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="checkout-sidebar">
                    <div class="order-summary panel">
                        <span class="section-kicker">Final Review</span>
                        <h3>Review Your Order</h3>

                        <div class="review-checklist">
                            <div>
                                <i class="fa-solid fa-location-dot"></i>
                                <span>Delivering to</span>
                                <strong>{{ $defaultAddress?->city ?? 'Saved address' }}{{ filled($defaultAddress?->province) ? ', ' . $defaultAddress->province : '' }}</strong>
                            </div>
                            <div>
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Estimated delivery</span>
                                <strong>{{ $overallDeliveryEstimate['date_range'] ?? '3-5 days' }}</strong>
                            </div>
                            <div>
                                <i class="fa-solid fa-money-bill-wave"></i>
                                <span>Payment</span>
                                <strong data-payment-summary>{{ $selectedPayment['short_label'] }}</strong>
                            </div>
                        </div>

                        <div class="summary-items">
                            @forelse(($groupedCartItems ?? collect()) as $sellerId => $sellerCartItems)
                                @php
                                    $seller = $sellerCartItems->first()?->product?->user;
                                    $sellerSubtotal = $sellerCartItems->sum(fn($item) => ($item->product?->discountedPrice((float) ($item->variant?->price ?? $item->product->price ?? 0)) ?? 0) * (int) $item->quantity);
                                    $sellerShipping = $sellerCartItems->sum(fn($item) => (float) ($item->product->shipping_fee ?? 0) * (int) $item->quantity);
                                    $estimate = ($deliveryEstimates ?? collect())->get($sellerId);
                                @endphp
                                <div class="summary-shop-group">
                                    <div class="summary-shop-head">
                                        <div>
                                            <h4>{{ $seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller' }}</h4>
                                            <p>{{ $sellerCartItems->count() }} item{{ $sellerCartItems->count() !== 1 ? 's' : '' }} · Delivery {{ $estimate['date_range'] ?? '3-5 days' }}</p>
                                        </div>

                                        <div class="summary-price">
                                            <strong>&#8369; {{ number_format($sellerSubtotal + $sellerShipping, 2) }}</strong>
                                            <span>Shop total</span>
                                        </div>
                                    </div>

                                    @foreach($sellerCartItems as $item)
                                        @php
                                            $variant = $item->variant;
                                            $originalUnitPrice = (float) ($variant?->price ?? $item->product->price ?? 0);
                                            $unitPrice = $item->product?->discountedPrice($originalUnitPrice) ?? $originalUnitPrice;
                                            $hasDiscount = $item->product?->hasActiveDiscount() && $unitPrice < $originalUnitPrice;
                                            $productImage = $variant?->image ?: ($item->product->image ?? null);
                                        @endphp
                                        <div class="summary-item">
                                            <div class="summary-product">
                                                <div class="summary-image">
                                                    <img src="{{ $productImage ? asset('storage/' . $productImage) : asset('assets/images/default-product.png') }}"
                                                        alt="{{ $item->product->name ?? 'Product' }}">
                                                </div>
                                                <div>
                                                    <h4>{{ $item->product->name ?? 'Product' }}</h4>
                                                    @if($variant)
                                                        <p>Option: {{ $variant->displayName() }}</p>
                                                    @endif
                                                    <p>Qty {{ $item->quantity }} · Shipping &#8369; {{ number_format(((float) ($item->product->shipping_fee ?? 0)) * (int) $item->quantity, 2) }}</p>
                                                </div>
                                            </div>

                                            <div class="summary-price">
                                                <strong>&#8369; {{ number_format($unitPrice * (int) $item->quantity, 2) }}</strong>
                                                <span>
                                                    @if($hasDiscount)
                                                        <span class="checkout-price-original">&#8369; {{ number_format($originalUnitPrice, 2) }}</span>
                                                    @endif
                                                    <span class="{{ $hasDiscount ? 'checkout-price-sale' : '' }}">&#8369; {{ number_format($unitPrice, 2) }} each</span>
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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

                        <div class="summary-line">
                            <span>Voucher</span>
                            <strong>Optional</strong>
                        </div>

                        <div class="summary-total">
                            <span>Total</span>
                            <strong>&#8369; {{ number_format($total, 2) }}</strong>
                        </div>

                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-submit-form" data-enable-loading>
                            @csrf
                            @foreach(($selectedCartItemIds ?? collect()) as $selectedCartItemId)
                                <input type="hidden" name="selected_cart_items[]" value="{{ $selectedCartItemId }}">
                            @endforeach
                            <div class="form-group" style="margin-bottom: 14px;">
                                <label for="voucher_code">Voucher / Coupon</label>
                                <input type="text" id="voucher_code" name="voucher_code" value="{{ $voucherCode }}" placeholder="Enter code, if any">
                                @error('voucher_code')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>
                            <button
                                type="submit"
                                class="action-btn primary-btn full-btn"
                                data-enable-loading
                                data-loading-text="Placing Order..."
                                {{ ($hasSavedAddress ?? false) ? '' : 'disabled' }}
                            >
                                Place Order - <span data-payment-button-label>{{ $selectedPayment['short_label'] }}</span>
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const choices = Array.from(document.querySelectorAll('[data-payment-choice]'));
            const summary = document.querySelector('[data-payment-summary]');
            const buttonLabel = document.querySelector('[data-payment-button-label]');
            const noteTitle = document.querySelector('[data-payment-note-title]');
            const noteCopy = document.querySelector('[data-payment-note-copy]');

            const syncPaymentDisplay = function (choice) {
                if (!choice) {
                    return;
                }

                document.querySelectorAll('.payment-method-card').forEach(function (card) {
                    card.classList.toggle('is-selected', card.contains(choice));
                });

                if (summary) {
                    summary.textContent = choice.dataset.paymentShortLabel || choice.dataset.paymentLabel || 'Payment';
                }

                if (buttonLabel) {
                    buttonLabel.textContent = choice.dataset.paymentShortLabel || choice.dataset.paymentLabel || 'Payment';
                }

                if (noteTitle) {
                    noteTitle.textContent = choice.dataset.paymentLabel || 'Payment method';
                }

                if (noteCopy) {
                    noteCopy.textContent = choice.dataset.paymentInstructions || '';
                }
            };

            choices.forEach(function (choice) {
                choice.addEventListener('change', function () {
                    syncPaymentDisplay(choice);
                });
            });

            syncPaymentDisplay(choices.find(function (choice) {
                return choice.checked;
            }));
        });
    </script>
@endsection
