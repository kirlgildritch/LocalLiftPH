@extends('layouts.admin')

@section('title', 'Orders')
@section('eyebrow', 'Fulfillment')
@section('page-title', 'Orders')
@section('page-description', 'Monitor buyer orders and current shipping progress.')

@section('content')
    <div class="page-stack">
        <article class="table-card admin-orders-card">
            <div class="admin-orders-scroll">
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
                                <td>&#8369; {{ number_format($order->total_price, 2) }}</td>
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

            @if ($orders->hasPages())
                @php
                    $startPage = max(1, $orders->currentPage() - 1);
                    $endPage = min($orders->lastPage(), $orders->currentPage() + 1);
                @endphp
                <div class="pagination-bar">
                    @if ($orders->onFirstPage())
                        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a class="pagination-button" href="{{ $orders->previousPageUrl() }}"><i
                                class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach ($orders->getUrlRange($startPage, $endPage) as $page => $url)
                        <a class="pagination-button {{ $page === $orders->currentPage() ? 'is-active' : '' }}"
                            href="{{ $url }}">{{ $page }}</a>
                    @endforeach

                    @if ($orders->hasMorePages())
                        <a class="pagination-button" href="{{ $orders->nextPageUrl() }}"><i
                                class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            @endif
        </article>
    </div>
@endsection

@push('styles')
    <style>
        .admin-orders-card {
            display: flex;
            flex-direction: column;
            min-height: 34rem;
        }

        .admin-orders-scroll {
            flex: 1 1 auto;
            overflow-x: auto;
        }

        .admin-orders-card .pagination-bar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: auto;
            padding-top: 18px;
            align-self: center;
            flex-wrap: wrap;
        }

        .pagination-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            min-width: 42px;
            height: 42px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            color: #8fa7c4;
            font-weight: 700;
            text-decoration: none;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
            transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
        }

        .pagination-button:hover {
            background: rgba(66, 165, 245, 0.1);
            border-color: rgba(66, 165, 245, 0.28);
            color: #dfeaff;
        }

        .pagination-button.is-active {
            background: linear-gradient(135deg, #4f8df0, #3e6fdb);
            border-color: rgba(96, 165, 250, 0.4);
            color: #fff;
            box-shadow: 0 12px 24px rgba(62, 111, 219, 0.24);
        }

        .pagination-button.is-disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        @media (max-width: 720px) {
            .admin-orders-card {
                min-height: 0;
            }

            .admin-orders-card .pagination-bar {
                width: 100%;
            }
        }
    </style>
@endpush
