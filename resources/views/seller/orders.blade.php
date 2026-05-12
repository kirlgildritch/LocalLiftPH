@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/orders.css') }}">

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <section class="seller-page-panel panel">
                        <div class="page-header">
                            <div>
                                <span class="section-kicker">Orders</span>
                                <h2>Seller Orders</h2>
                            </div>
                        </div>

                        <div class="table-panel table-panel--scroll">
                            <table class="seller-table">
                                <thead>
                                    <tr>
                                        <th>Products</th>
                                        <th>Customer</th>
                                        <th>Shipping Status</th>
                                        <th>Payment Method</th>
                                        <th>Payment Status</th>
                                        <th>Earning Status</th>
                                        <th>Return / Refund</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($orders as $order)
                                        @php
                                            $nextStatuses = $order->nextShippingStatuses();
                                            $orderItems = $order->items ?? collect();
                                            $firstItem = $orderItems->first();
                                            $firstItemName = $firstItem?->product?->name ?? 'Product unavailable';
                                            $firstItemVariant = $firstItem?->variant_name ?: $firstItem?->variant?->displayName();
                                            $firstItemQuantity = max(1, (int) ($firstItem->quantity ?? 1));
                                            $additionalItemCount = max($orderItems->count() - 1, 0);
                                            $productSummary = $firstItem
                                                ? $firstItemName . ($firstItemVariant ? ' (' . $firstItemVariant . ')' : '') . ' x' . $firstItemQuantity
                                                : 'No items';
                                            $productMeta = 'Order #' . $order->id;

                                            if ($additionalItemCount > 0) {
                                                $productMeta .= ' | +' . $additionalItemCount . ' more item' . ($additionalItemCount > 1 ? 's' : '');
                                            }
                                            $returnRequest = $order->returnRequest;
                                        @endphp
                                        <tr>
                                            <td class="order-products-cell">
                                                <strong class="order-products-title">{{ $productSummary }}</strong>
                                                <span class="order-products-meta">{{ $productMeta }}</span>
                                            </td>
                                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="status-chip {{ $order->shippingToneClass() }}">
                                                    {{ $order->shippingStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $order->paymentMethodLabel() }}</td>
                                            <td>
                                                <span class="status-chip {{ $order->paymentToneClass() }}">
                                                    {{ $order->paymentStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-chip {{ $order->earningToneClass() }}">
                                                    {{ $order->earningStatusLabel() }}
                                                </span>
                                            </td>
                                            <td class="return-request-cell">
                                                @if($returnRequest)
                                                    <span class="status-chip {{ $returnRequest->toneClass() }}">
                                                        {{ $returnRequest->statusLabel() }}
                                                    </span>
                                                    <span class="order-products-meta">
                                                        {{ $returnRequest->reason }} | {{ \Illuminate\Support\Str::headline($returnRequest->preferred_resolution) }}
                                                    </span>
                                                    <span class="order-products-meta">
                                                        {{ $returnRequest->details }}
                                                    </span>
                                                    @if($returnRequest->media->isNotEmpty())
                                                        <div class="return-evidence-links">
                                                            @foreach($returnRequest->media as $media)
                                                                <a href="{{ $media->url }}" target="_blank" rel="noopener">
                                                                    {{ ucfirst($media->type) }} {{ $loop->iteration }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if($returnRequest->status === \App\Models\OrderReturnRequest::STATUS_PENDING)
                                                        <form method="POST"
                                                            action="{{ route('seller.return-requests.update', $returnRequest) }}"
                                                            class="return-review-form">
                                                            @csrf
                                                            @method('PATCH')
                                                            <textarea name="seller_response" rows="2" required
                                                                placeholder="Response to buyer"></textarea>
                                                            <div class="return-review-actions">
                                                                <button type="submit" name="status" value="approved"
                                                                    class="table-action secondary">Approve</button>
                                                                <button type="submit" name="status" value="rejected"
                                                                    class="table-action danger">Reject</button>
                                                            </div>
                                                        </form>
                                                    @elseif(filled($returnRequest->seller_response))
                                                        <span class="order-products-meta">
                                                            Response: {{ $returnRequest->seller_response }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="empty-text">None</span>
                                                @endif
                                            </td>
                                            <td>&#8369; {{ number_format($order->total_price ?? 0, 2) }}</td>
                                            <td>
                                                @if($nextStatuses)
                                                    <form method="POST"
                                                        action="{{ route('seller.orders.shipping-status', $order) }}"
                                                        data-enable-loading
                                                        style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="shipping_status" class="table-select">
                                                            @foreach($nextStatuses as $status)
                                                                <option value="{{ $status }}">
                                                                    {{ \App\Models\Order::progressStatuses()[$status]['label'] ?? ucfirst(str_replace('_', ' ', $status)) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="table-action secondary"
                                                            data-enable-loading data-loading-text="Updating...">Update</button>
                                                    </form>
                                                @else
                                                    <span class="empty-text">No more updates</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="empty-text">No orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($orders->hasPages())
                            <div class="seller-orders-pagination">
                                @if($orders->onFirstPage())
                                    <span class="table-action secondary seller-orders-pagination-button is-disabled">Previous</span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}"
                                        class="table-action secondary seller-orders-pagination-button">Previous</a>
                                @endif

                                <span class="seller-orders-pagination-meta">
                                    Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                                </span>

                                @if($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl() }}"
                                        class="table-action secondary seller-orders-pagination-button">Next</a>
                                @else
                                    <span class="table-action secondary seller-orders-pagination-button is-disabled">Next</span>
                                @endif
                            </div>
                        @endif
                    </section>
                </main>
            </div>
        </div>
    </section>
@endsection
