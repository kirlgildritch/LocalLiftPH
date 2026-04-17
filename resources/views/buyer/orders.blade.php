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


            <div class="orders-toolbar panel">
                <div class="toolbar-copy">
                    <span class="toolbar-label">History</span>
                    <h2>My Orders</h2>
                </div>

                <div class="orders-tabs">
                    <a href="{{ route('buyer.orders') }}" class="tab-btn {{ $currentStatus === 'all' ? 'active' : '' }}">
                        All ({{ $statusCounts->sum() }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => 'pending']) }}"
                        class="tab-btn {{ $currentStatus === 'pending' ? 'active' : '' }}">
                        Pending ({{ $statusCounts->get('pending', 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => 'to ship']) }}"
                        class="tab-btn {{ $currentStatus === 'to ship' ? 'active' : '' }}">
                        To Ship ({{ $statusCounts->get('to ship', 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => 'to receive']) }}"
                        class="tab-btn {{ $currentStatus === 'to receive' ? 'active' : '' }}">
                        To Receive ({{ $statusCounts->get('to receive', 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => 'delivered']) }}"
                        class="tab-btn {{ $currentStatus === 'delivered' ? 'active' : '' }}">
                        Delivered ({{ $statusCounts->get('delivered', 0) }})
                    </a>
                    <a href="{{ route('buyer.orders', ['status' => 'cancelled']) }}"
                        class="tab-btn {{ $currentStatus === 'cancelled' ? 'active' : '' }}">
                        Cancelled ({{ $statusCounts->get('cancelled', 0) }})
                    </a>
                </div>
            </div>

            <div class="orders-list">
                @forelse($orders as $order)
                    <article class="order-card panel">
                        <div class="order-card-top">
                            <div class="shop-info">
                                <i class="fa-solid fa-store"></i>
                                <div>
                                    <span class="toolbar-label">Order</span>
                                    <strong>#{{ $order->id }}</strong>
                                </div>
                            </div>

                            <div class="order-status {{ \Illuminate\Support\Str::slug($order->status) }}">
                                {{ ucfirst($order->status) }}
                            </div>
                        </div>

                        <div class="order-items">
                            @foreach($order->items as $item)
                                <div class="order-card-body">
                                    <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}"
                                        alt="{{ $item->product->name ?? 'Product' }}" class="order-product-img">

                                    <div class="order-product-info">
                                        <h3>{{ $item->product->name ?? 'Product no longer available' }}</h3>
                                        <p>Order #{{ $order->id }}</p>
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
                                <a href="#" class="order-btn secondary-btn">View Details</a>

                                @if($order->status === 'delivered')
                                    <a href="#" class="order-btn primary-btn">Buy Again</a>
                                @elseif($order->status === 'to ship')
                                    <a href="#" class="order-btn primary-btn">Track Order</a>
                                @elseif($order->status === 'cancelled')
                                    <a href="#" class="order-btn primary-btn">Reorder</a>
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
@endsection