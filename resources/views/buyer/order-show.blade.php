@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_orders.css') }}">

    <section class="orders-page">
        <div class="container">
            @php
                $groupPlacedAt = $groupOrders->sortBy('created_at')->first()?->created_at;
            @endphp
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <a href="{{ route('buyer.orders') }}">My Orders</a>
                <span>&gt;</span>
                <span>Checkout Summary</span>
            </div>

            <div class="orders-toolbar panel">
                <div class="toolbar-copy">
                    <span class="toolbar-label">Details</span>
                    <h2>
                        @if($groupSummary['shops'] > 1)
                            Checkout Summary
                        @else
                            Order #{{ $order->id }}
                        @endif
                    </h2>
                    @if($groupSummary['shops'] > 1)
                        <p class="order-group-meta">Grouped summary for {{ $groupSummary['shops'] }} shop orders placed in one checkout.</p>
                    @endif
                </div>

                <div class="order-actions">
                    <a href="{{ route('buyer.orders') }}" class="order-btn secondary-btn">Back to Orders</a>
                </div>
            </div>

            @if($groupOrders->count() === 1)
                @include('buyer.partials.order-progress', ['order' => $order])
            @endif

            <div class="order-detail-grid">
                <div class="panel detail-summary-card">
                    <div class="detail-summary-grid">
                        <div>
                            <span class="toolbar-label">Placed On</span>
                            <p>{{ $groupPlacedAt?->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Shops</span>
                            <p>{{ $groupSummary['shops'] }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Items</span>
                            <p>{{ $groupSummary['items'] }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Subtotal</span>
                            <p>&#8369; {{ number_format($groupSummary['subtotal'], 2) }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Shipping</span>
                            <p>&#8369; {{ number_format($groupSummary['shipping'], 2) }}</p>
                        </div>
                        <div>
                            <span class="toolbar-label">Total</span>
                            <p>&#8369; {{ number_format($groupSummary['total'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="orders-list" id="rate-products">
                @foreach($groupOrders as $shopOrder)
                    @php
                        $shopHasRateableItems = $shopOrder->shippingStatus() === \App\Models\Order::SHIPPING_COMPLETED
                            && $shopOrder->items->contains(fn ($item) => $item->product && !$item->review);
                        $shopSubtotal = $shopOrder->subtotalAmount();
                    @endphp
                    <article class="order-card panel">
                        <div class="order-card-top">
                            <div class="shop-info">
                                <i class="fa-solid fa-store"></i>
                                <div>
                                    <span class="toolbar-label">{{ $shopOrder->shopDisplayName() }}</span>
                                    <strong>Order #{{ $shopOrder->id }}</strong>
                                </div>
                            </div>

                            <div class="order-status {{ $shopOrder->shippingToneClass() }}">
                                {{ $shopOrder->shippingStatusLabel() }}
                            </div>
                        </div>

                        @if($groupOrders->count() > 1)
                            <div class="order-card-body order-card-body--summary">
                                <div class="order-product-info">
                                    <p>Date: {{ $shopOrder->created_at->format('M d, Y h:i A') }}</p>
                                    <p>Items: {{ $shopOrder->itemCount() }}</p>
                                    <p>Shipping Fee: &#8369; {{ number_format($shopOrder->shipping_fee, 2) }}</p>
                                </div>

                                <div class="order-product-price">
                                    &#8369; {{ number_format($shopOrder->total_price, 2) }}
                                </div>
                            </div>
                        @endif

                        <div class="order-items">
                            @foreach($shopOrder->items as $item)
                                <div class="order-card-body">
                                    <img
                                        src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}"
                                        alt="{{ $item->product->name ?? 'Product' }}"
                                        class="order-product-img"
                                    >

                                    <div class="order-product-info">
                                        <h3>{{ $item->product->name ?? 'Product no longer available' }}</h3>
                                        <p>Quantity: {{ $item->quantity }}</p>
                                        <p>Unit Price: &#8369; {{ number_format($item->price, 2) }}</p>

                                        @if($shopOrder->shippingStatus() === \App\Models\Order::SHIPPING_COMPLETED && $item->product)
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
                                        &#8369; {{ number_format($item->price * $item->quantity, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="order-card-footer">
                            <div class="total-text">
                                <div>
                                    <span>Subtotal</span>
                                    <strong>&#8369; {{ number_format($shopSubtotal, 2) }}</strong>
                                </div>
                                <div>
                                    <span>Shipping</span>
                                    <strong>&#8369; {{ number_format($shopOrder->shipping_fee, 2) }}</strong>
                                </div>
                                <div>
                                    <span>Order Total</span>
                                    <strong>&#8369; {{ number_format($shopOrder->total_price, 2) }}</strong>
                                </div>
                            </div>

                            <div class="order-actions">
                                @if($shopOrder->canBeCancelled())
                                    <button
                                        type="button"
                                        class="order-btn danger-btn open-cancel-order"
                                        data-order-id="{{ $shopOrder->id }}"
                                        data-order-action="{{ route('buyer.orders.cancel', $shopOrder) }}"
                                    >
                                        Cancel Order
                                    </button>
                                @elseif($shopOrder->canConfirmReceipt())
                                    <form action="{{ route('buyer.orders.received', $shopOrder) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="order-btn primary-btn">
                                            Order Received
                                        </button>
                                    </form>
                                @elseif(in_array($shopOrder->shippingStatus(), [\App\Models\Order::SHIPPING_COMPLETED, \App\Models\Order::SHIPPING_CANCELLED], true))
                                    @if($shopHasRateableItems)
                                        <span class="order-btn secondary-btn is-static">Choose an item above to rate</span>
                                    @endif

                                    <form action="{{ route('buyer.orders.buyAgain', $shopOrder) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="order-btn primary-btn">
                                            {{ $shopOrder->shippingStatus() === \App\Models\Order::SHIPPING_CANCELLED ? 'Reorder' : 'Buy Again' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
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
