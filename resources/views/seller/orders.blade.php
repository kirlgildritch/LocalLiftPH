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

                    @if(session('success'))
                        <p class="seller-feedback success-message">{{ session('success') }}</p>
                    @endif

                    <div class="table-panel">
                        <table class="seller-table">
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
                                            <span class="status-chip {{ strtolower($order->status ?? 'pending') }}">
                                                {{ $order->status ?? 'Pending' }}
                                            </span>
                                        </td>
                                        <td>PHP {{ number_format($order->total ?? 0, 2) }}</td>
                                        <td>
                                            <a href="#" class="table-action secondary">View</a>
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
