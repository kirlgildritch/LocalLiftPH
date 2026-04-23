@extends('layouts.admin')

@section('title', 'Orders')
@section('eyebrow', 'Fulfillment')
@section('page-title', 'Orders')
@section('page-description', 'Monitor buyer orders and current shipping progress.')

@section('content')
    <div class="page-stack">
        <article class="table-card">
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Buyer</th>
                            <th>Seller(s)</th>
                            <th>Total</th>
                            <th>Shipping Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $sellerNames = $order->items
                                    ->map(fn ($item) => $item->product?->user?->name)
                                    ->filter()
                                    ->unique()
                                    ->values();
                                $statusClass = match ($order->shippingToneClass()) {
                                    'processing' => 'pending',
                                    'shipped' => 'success',
                                    'delivered' => 'delivered',
                                    'cancelled' => 'cancelled',
                                    default => 'pending',
                                };
                            @endphp
                            <tr>
                                <td class="product-title">#{{ $order->id }}</td>
                                <td>{{ $order->created_at?->format('M d, Y') }}</td>
                                <td>{{ $order->user->name ?? 'Buyer' }}</td>
                                <td>{{ $sellerNames->isNotEmpty() ? $sellerNames->join(', ') : 'Seller unavailable' }}</td>
                                <td>PHP {{ number_format($order->total_price, 2) }}</td>
                                <td><span class="status-pill status-pill--{{ $statusClass }}">{{ $order->shippingStatusLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-text">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
@endsection
