@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_orders.css') }}">

    <section class="orders-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>My Orders</span>
            </div>

            @if(session('success'))
                <div class="feedback-banner success-banner panel">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="feedback-banner error-banner panel">{{ session('error') }}</div>
            @endif

            <div class="orders-toolbar panel">
                <div class="toolbar-copy">
                    <span class="toolbar-label">History</span>
                    <h2>My Orders</h2>
                </div>

                <div class="orders-tabs">
                    <a href="{{ route('buyer.orders') }}" class="tab-btn {{ $currentStatus === 'all' ? 'active' : '' }}">
                        All ({{ $statusCounts->sum() }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::STATUS_PENDING]) }}" class="tab-btn {{ $currentStatus === \App\Models\Order::STATUS_PENDING ? 'active' : '' }}">
                        Pending ({{ $statusCounts->get(\App\Models\Order::STATUS_PENDING, 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::STATUS_CONFIRMED]) }}" class="tab-btn {{ $currentStatus === \App\Models\Order::STATUS_CONFIRMED ? 'active' : '' }}">
                        Confirmed ({{ $statusCounts->get(\App\Models\Order::STATUS_CONFIRMED, 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::STATUS_PROCESSING]) }}" class="tab-btn {{ $currentStatus === \App\Models\Order::STATUS_PROCESSING ? 'active' : '' }}">
                        Processing ({{ $statusCounts->get(\App\Models\Order::STATUS_PROCESSING, 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::STATUS_SHIPPED]) }}" class="tab-btn {{ $currentStatus === \App\Models\Order::STATUS_SHIPPED ? 'active' : '' }}">
                        Shipped ({{ $statusCounts->get(\App\Models\Order::STATUS_SHIPPED, 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::STATUS_DELIVERED]) }}" class="tab-btn {{ $currentStatus === \App\Models\Order::STATUS_DELIVERED ? 'active' : '' }}">
                        Delivered ({{ $statusCounts->get(\App\Models\Order::STATUS_DELIVERED, 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::STATUS_CANCELLED]) }}" class="tab-btn {{ $currentStatus === \App\Models\Order::STATUS_CANCELLED ? 'active' : '' }}">
                        Cancelled ({{ $statusCounts->get(\App\Models\Order::STATUS_CANCELLED, 0) }})
                    </a>
                </div>
            </div>

            <div class="orders-list">
                @forelse($orders as $order)
                    <article class="order-card panel">
                        <div class="order-card-top">
                            <div class="shop-info">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <div>
                                    <span class="toolbar-label">Order</span>
                                    <strong>#{{ $order->id }}</strong>
                                </div>
                            </div>

                            <div class="order-status {{ \Illuminate\Support\Str::slug($order->status) }}">
                                {{ $order->statusLabel() }}
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
                                        <p>Date: {{ $order->created_at->format('M d, Y') }}</p>
                                        <p>Quantity: {{ $item->quantity }}</p>
                                    </div>

                                    <div class="order-product-price">
                                        P{{ number_format($item->price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="order-card-footer">
                            <div class="total-text">
                                <span>Total</span>
                                <strong>P{{ number_format($order->total_price, 2) }}</strong>
                            </div>

                            <div class="order-actions">
                                <a href="{{ route('buyer.orders.show', $order) }}" class="order-btn secondary-btn">View Order</a>

                                @if($order->canBeCancelled())
                                    <button
                                        type="button"
                                        class="order-btn secondary-btn open-cancel-order"
                                        data-order-id="{{ $order->id }}"
                                        data-order-action="{{ route('buyer.orders.cancel', $order) }}"
                                    >
                                        Cancel Order
                                    </button>
                                @elseif(in_array($order->status, [\App\Models\Order::STATUS_DELIVERED, \App\Models\Order::STATUS_CANCELLED], true))
                                    <form action="{{ route('buyer.orders.buyAgain', $order) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="order-btn primary-btn">
                                            {{ $order->status === \App\Models\Order::STATUS_CANCELLED ? 'Reorder' : 'Buy Again' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-orders panel">
                        <i class="fa-regular fa-clipboard"></i>
                        <h3>No orders yet</h3>
                        <p>Your order history will appear here once you complete your first checkout.</p>
                        <a href="{{ route('products.index') }}" class="order-btn primary-btn">Browse Products</a>
                    </div>
                @endforelse
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
