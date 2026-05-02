@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/earnings.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/orders.css') }}">

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <section class="seller-page-panel panel">
                        <div class="page-header">
                            <div>
                                <span class="section-kicker">Earnings</span>
                                <h2>Payout and revenue overview</h2>
                            </div>
                        </div>

                        <div class="earnings-stats">
                            <article class="mini-stat panel">
                                <span>Pending Earnings</span>
                                <strong class="highlight">&#8369;
                                    {{ number_format($stats['pending_earnings'] ?? 0, 2) }}</strong>
                            </article>

                            <article class="mini-stat panel">
                                <span>Available Earnings</span>
                                <strong>&#8369; {{ number_format($stats['available_earnings'] ?? 0, 2) }}</strong>
                            </article>

                            <article class="mini-stat panel">
                                <span>Paid Out</span>
                                <strong>&#8369; {{ number_format($stats['paid_out_earnings'] ?? 0, 2) }}</strong>
                            </article>

                            <article class="mini-stat panel">
                                <span>Cancelled</span>
                                <strong>&#8369; {{ number_format($stats['reversed_earnings'] ?? 0, 2) }}</strong>
                            </article>

                            <article class="mini-stat panel">
                                <span>Today</span>
                                <strong>&#8369; {{ number_format($stats['today_earnings'] ?? 0, 2) }}</strong>
                            </article>

                            <article class="mini-stat panel">
                                <span>This Week</span>
                                <strong>&#8369; {{ number_format($stats['weekly_earnings'] ?? 0, 2) }}</strong>
                            </article>

                            <article class="mini-stat panel">
                                <span>This Month</span>
                                <strong>&#8369; {{ number_format($stats['monthly_earnings'] ?? 0, 2) }}</strong>
                            </article>

                            <article class="mini-stat panel">
                                <span>Overall Earnings</span>
                                <strong>&#8369; {{ number_format($stats['overall_earnings'] ?? 0, 2) }}</strong>
                            </article>
                        </div>

                        <form method="GET" class="earnings-filters panel">
                            <label>
                                <span>Earning Status</span>
                                <select name="status">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ ($filters['status'] ?? 'all') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                <span>Month</span>
                                <input type="month" name="month" value="{{ $filters['month'] ?? '' }}">
                            </label>

                            <label>
                                <span>From</span>
                                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}">
                            </label>

                            <label>
                                <span>To</span>
                                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}">
                            </label>

                            <div class="earnings-filter-actions">
                                <button type="submit" class="table-action secondary">Apply</button>
                                <a href="{{ route('seller.earnings') }}" class="table-action ghost">Reset</a>
                            </div>
                        </form>

                        <div class="table-panel">
                            <table class="seller-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Buyer</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Amount</th>
                                        <th>Shipping</th>
                                        <th>Payment</th>
                                        <th>Earning</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($historyOrders as $order)
                                        <tr>
                                            <td>#{{ $order['id'] }}</td>
                                            <td>{{ $order['buyer_name'] }}</td>
                                            <td class="earnings-product-cell">{{ $order['product_summary'] }}</td>
                                            <td>{{ $order['quantity'] }}</td>
                                            <td class="{{ $order['is_reversed'] ? 'earnings-negative-text' : '' }}">
                                                {{ $order['is_reversed'] ? '-' : '' }}&#8369;
                                                {{ number_format($order['total'], 2) }}
                                            </td>
                                            <td>
                                                <span class="status-chip {{ $order['shipping_status_tone'] }}">
                                                    {{ $order['shipping_status_label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-chip {{ $order['payment_status_tone'] }}">
                                                    {{ $order['payment_status_label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-chip {{ $order['earning_status_tone'] }}">
                                                    {{ $order['earning_status_label'] }}
                                                </span>
                                            </td>
                                            <td>{{ $order['date_label'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="empty-text">No earnings records found.</td>
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
