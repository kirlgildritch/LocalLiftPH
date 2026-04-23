@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_orders.css') }}">

    <section class="orders-page">
        <div class="container">
            @php
                $hasRateableItems = $order->shippingStatus() === \App\Models\Order::SHIPPING_DELIVERED
                    && $order->items->contains(fn ($item) => $item->product && !$item->review);
            @endphp
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <a href="{{ route('buyer.orders') }}">My Orders</a>
                <span>&gt;</span>
                <span>Order #{{ $order->id }}</span>
            </div>

            @if(session('success'))
                <div class="feedback-banner success-banner panel">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="feedback-banner error-banner panel">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="feedback-banner error-banner panel">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="orders-toolbar panel">
                <div class="toolbar-copy">
                    <span class="toolbar-label">Details</span>
                    <h2>Order #{{ $order->id }}</h2>
                </div>

                <div class="order-status {{ $order->shippingToneClass() }}">
                    {{ $order->shippingStatusLabel() }}
                </div>
            </div>

            @include('buyer.partials.order-progress', ['order' => $order])

            <div class="order-detail-grid">
                @php
                    $orderSubtotal = $order->items->sum(fn ($item) => $item->price * $item->quantity);
                    $orderShipping = $order->shipping_fee ?? $order->items->sum(fn ($item) => ($item->shipping_fee ?? 0) * $item->quantity);
                @endphp
                <div class="panel detail-summary-card">
                    <div class="detail-summary-grid">
                        <div>
                            <span class="toolbar-label">Placed On</span>
                            <p>{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Shipping Status</span>
                            <p>{{ $order->shippingStatusLabel() }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Items</span>
                            <p>{{ $order->items->sum('quantity') }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Subtotal</span>
                            <p>P{{ number_format($orderSubtotal, 2) }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Shipping</span>
                            <p>P{{ number_format($orderShipping, 2) }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Total</span>
                            <p>P{{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="orders-list" id="rate-products">
                <article class="order-card panel">
                    <div class="order-card-top">
                        <div class="shop-info">
                            <i class="fa-solid fa-box-open"></i>
                            <div>
                                <span class="toolbar-label">Items</span>
                                <strong>{{ $order->items->count() }} product{{ $order->items->count() !== 1 ? 's' : '' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="order-items">
                        @foreach($order->items as $item)
                            <div class="order-card-body">
                                <img
                                    src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}"
                                    alt="{{ $item->product->name ?? 'Product' }}"
                                    class="order-product-img"
                                >

                                <div class="order-product-info">
                                    <h3>{{ $item->product->name ?? 'Product no longer available' }}</h3>
                                    <p>Sold by: {{ $item->product->user->name ?? 'LocalLift Seller' }}</p>
                                    <p>Quantity: {{ $item->quantity }}</p>
                                    <p>Unit Price: P{{ number_format($item->price, 2) }}</p>

                                    @if($order->shippingStatus() === \App\Models\Order::SHIPPING_DELIVERED && $item->product)
                                        <div class="order-item-actions">
                                            @if(!$item->review)
                                                <a href="{{ route('products.show', $item->product) }}?review_order_item={{ $item->id }}#product-reviews"
                                                    class="order-btn secondary-btn">
                                                    Rate Product
                                                </a>
                                            @else
                                                <span class="order-btn secondary-btn is-static">Reviewed</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="order-product-price">
                                    P{{ number_format($item->price * $item->quantity, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="order-card-footer">
                        <div class="total-text">
                            <div>
                                <span>Subtotal</span>
                                <strong>P{{ number_format($orderSubtotal, 2) }}</strong>
                            </div>
                            <div>
                                <span>Shipping</span>
                                <strong>P{{ number_format($orderShipping, 2) }}</strong>
                            </div>
                            <div>
                                <span>Order Total</span>
                                <strong>P{{ number_format($order->total_price, 2) }}</strong>
                            </div>
                        </div>

                        <div class="order-actions">
                            <a href="{{ route('buyer.orders') }}" class="order-btn secondary-btn">Back to Orders</a>

                            @if($order->canBeCancelled())
                                <button
                                    type="button"
                                    class="order-btn danger-btn open-cancel-order"
                                    data-order-id="{{ $order->id }}"
                                    data-order-action="{{ route('buyer.orders.cancel', $order) }}"
                                >
                                    Cancel Order
                                </button>
                            @elseif($order->canConfirmReceipt())
                                <form action="{{ route('buyer.orders.received', $order) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="order-btn primary-btn">
                                        Order Received
                                    </button>
                                </form>
                            @elseif(in_array($order->shippingStatus(), [\App\Models\Order::SHIPPING_DELIVERED, \App\Models\Order::SHIPPING_CANCELLED], true))
                                @if($hasRateableItems)
                                    <span class="order-btn secondary-btn is-static">Choose an item above to rate</span>
                                @endif

                                <form action="{{ route('buyer.orders.buyAgain', $order) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="order-btn primary-btn">
                                        {{ $order->shippingStatus() === \App\Models\Order::SHIPPING_CANCELLED ? 'Reorder' : 'Buy Again' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    @include('buyer.partials.cancel-order-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('cancelOrderModal');
            const form = document.getElementById('cancelOrderForm');
            const otherWrap = document.getElementById('otherReasonWrap');

            if (!modal || !form || !otherWrap) {
                return;
            }

            const syncOtherReason = () => {
                const otherInput = form.querySelector('input[value="Other"]');
                const isChecked = !!(otherInput && otherInput.checked);
                otherWrap.classList.toggle('is-visible', isChecked);
            };

            document.querySelectorAll('.open-cancel-order').forEach((button) => {
                button.addEventListener('click', function () {
                    form.action = this.dataset.orderAction;
                    modal.classList.add('show');
                    document.body.classList.add('modal-open');
                    syncOtherReason();
                });
            });

            modal.querySelectorAll('[data-close-cancel-modal]').forEach((button) => {
                button.addEventListener('click', function () {
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                });
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            });

            form.querySelectorAll('input[name="reasons[]"]').forEach((input) => {
                input.addEventListener('change', syncOtherReason);
            });

            syncOtherReason();
        });
    </script>
@endsection
