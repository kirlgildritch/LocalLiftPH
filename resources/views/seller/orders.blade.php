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


                        <div class="table-panel">
                            <table class="seller-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Shipping Status</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($orders as $order)
                                        @php
                                            $canManageShipping = $order->sellerOwnsAllItems(auth('seller')->user());
                                            $nextStatuses = $order->nextShippingStatuses();
                                        @endphp
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="status-chip {{ $order->shippingToneClass() }}">
                                                    {{ $order->shippingStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>&#8369; {{ number_format($order->total_price ?? 0, 2) }}</td>
                                            <td>
                                                @if($canManageShipping && $nextStatuses)
                                                    <form method="POST"
                                                        action="{{ route('seller.orders.shipping-status', $order) }}"
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
                                                        <button type="submit" class="table-action secondary">Update</button>
                                                    </form>
                                                @elseif(!$canManageShipping)
                                                    <span class="empty-text">Mixed-seller order</span>
                                                @else
                                                    <span class="empty-text">No more updates</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-text">No orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </section>
@endsection