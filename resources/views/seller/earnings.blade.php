@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/earnings.css') }}">

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
                            <span>Total Earnings</span>
                            <strong>PHP {{ number_format($totalEarnings, 2) }}</strong>
                        </article>

                        <article class="mini-stat panel">
                            <span>This Month</span>
                            <strong>PHP {{ number_format($monthlyEarnings, 2) }}</strong>
                        </article>

                        <article class="mini-stat panel">
                            <span>Pending Payout</span>
                            <strong class="highlight">PHP {{ number_format($pendingPayout, 2) }}</strong>
                        </article>
                    </div>

                    <div class="table-panel">
                        <table class="seller-table">
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
                </section>
            </main>
        </div>
    </div>
</section>
@endsection
