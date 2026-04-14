@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/orders.css') }}">

<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">

            {{-- SIDEBAR (same as dashboard) --}}
            @include('seller.partials.sidebar')

            {{-- MAIN CONTENT --}}
            <main class="dashboard-main">

                <div class="products-header">
                    <h2>Orders</h2>
                </div>

                <div class="divider"></div>

                @if(session('success'))
                    <p class="success-message">{{ session('success') }}</p>
                @endif

                <div class="products-table-wrapper">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->customer_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="status {{ strtolower($order->status ?? 'pending') }}">
                                            {{ $order->status ?? 'Pending' }}
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($order->total ?? 0, 2) }}</td>
                                    <td>
                                        <a href="#" class="edit-btn">View</a>
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

            </main>

        </div>
    </div>
</section>
@endsection