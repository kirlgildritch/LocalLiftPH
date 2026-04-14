@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/earnings.css') }}">

<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">

            @include('seller.partials.sidebar')

            <main class="dashboard-main">
                <div class="products-header">
                    <h2>Earnings</h2>
                </div>

                <div class="divider"></div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="top"><i class="fa-solid fa-wallet"></i> Total Earnings</div>
                        <div class="value">₱{{ number_format($totalEarnings, 2) }}</div>
                    </div>

                    <div class="stat-card">
                        <div class="top"><i class="fa-solid fa-chart-line"></i> This Month</div>
                        <div class="value">₱{{ number_format($monthlyEarnings, 2) }}</div>
                    </div>

                    <div class="stat-card">
                        <div class="top"><i class="fa-solid fa-clock"></i> Pending Payout</div>
                        <div class="value highlight">₱{{ number_format($pendingPayout, 2) }}</div>
                    </div>
                </div>

                <div class="products-table-wrapper" style="margin-top: 24px;">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="empty-text">No earnings records yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>

        </div>
    </div>
</section>
@endsection