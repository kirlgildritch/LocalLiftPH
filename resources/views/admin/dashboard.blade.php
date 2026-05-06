@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Dashboard')
@section('page-title', 'Welcome, Admin!')
@section('page-description', 'Review sellers, products, orders, and reports from one workspace.')

@push('styles')
    <style>
        .dashboard-section-grid,
        .dashboard-mini-grid {
            display: grid;
            gap: 1rem;
        }

        .dashboard-section-grid {
            grid-template-columns: 1.5fr 1fr;
            align-items: start;
        }

        .dashboard-mini-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            padding: 1rem 1.25rem 1.25rem;
            align-items: stretch;
        }

        .dashboard-mini-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem;
            background: var(--surface-soft);
            display: grid;
            gap: 0.45rem;
            height: 100%;
            align-content: start;
        }

        .dashboard-mini-card strong {
            font-size: 1.65rem;
            line-height: 1;
            color: var(--text);
        }

        .dashboard-mini-card span,
        .dashboard-mini-card small,
        .activity-item__meta,
        .dashboard-inline-note {
            color: var(--muted);
        }

        .dashboard-mini-card small {
            font-size: 0.86rem;
        }

        .dashboard-mini-card--primary strong {
            color: var(--primary);
        }

        .dashboard-mini-card--success strong {
            color: var(--success);
        }

        .dashboard-mini-card--warning strong {
            color: #f39d12;
        }

        .dashboard-mini-card--danger strong {
            color: var(--danger);
        }

        .dashboard-activity-list,
        .dashboard-shop-list {
            display: grid;
            gap: 0.9rem;
            padding: 1rem 1.25rem 1.25rem;
        }

        .activity-item,
        .shop-verify-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem;
            background: var(--surface);
        }

        .activity-item {
            display: grid;
            gap: 0.7rem;
        }

        .activity-item__top,
        .shop-verify-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .activity-item__type {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .activity-item__type--primary {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .activity-item__type--success {
            background: var(--success-soft);
            color: var(--success);
        }

        .activity-item__type--warning {
            background: var(--warning-soft);
            color: #a27816;
        }

        .activity-item__type--danger {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .activity-item__title,
        .shop-verify-card__title,
        .dashboard-table-name {
            font-weight: 700;
            color: #30405e;
        }

        .activity-item__actions,
        .shop-verify-card__actions,
        .dashboard-inline-actions {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .dashboard-inline-actions form,
        .shop-verify-card__actions form {
            margin: 0;
        }

        .dashboard-panel-table {
            padding: 0 1.25rem 1.25rem;
        }

        .dashboard-panel-table .data-table {
            min-width: 0;
        }

        .dashboard-panel-table .data-table th,
        .dashboard-panel-table .data-table td {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }

        .dashboard-product-meta {
            display: grid;
            gap: 0.25rem;
        }

        .dashboard-empty {
            padding: 1rem 1.25rem 1.25rem;
        }

        .shop-verify-card__meta {
            display: grid;
            gap: 0.45rem;
            color: var(--muted);
            font-size: 0.94rem;
        }

        .admin-dashboard-summary .summary-card,
        .dashboard-overview-card {
            height: 100%;
        }

        @media (max-width: 1200px) {
            .dashboard-mini-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-section-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 1024px) and (max-width: 1440px) {
            .page {
                padding: 1.35rem;
            }

            .page-stack {
                gap: 1.25rem;
            }

            .admin-dashboard-summary {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.9rem;
            }

            .admin-dashboard-summary .summary-card {
                min-height: 112px;
                padding: 0.95rem 1.05rem;
                display: grid;
                align-content: space-between;
            }

            .admin-dashboard-summary .summary-card__label {
                margin-bottom: 0.4rem;
                font-size: 0.88rem;
                line-height: 1.35;
            }

            .admin-dashboard-summary .summary-card__value {
                display: grid;
                gap: 0.35rem;
                align-items: start;
            }

            .admin-dashboard-summary .summary-card__value strong {
                font-size: clamp(2rem, 1.8vw, 2.45rem);
                line-height: 0.96;
                overflow-wrap: anywhere;
            }

            .admin-dashboard-summary .summary-card__value span {
                padding-bottom: 0;
                font-size: 0.86rem;
                line-height: 1.35;
            }

            .admin-dashboard-sections {
                grid-template-columns: minmax(0, 1.22fr) minmax(0, 1.02fr);
                gap: 1rem;
            }

            .admin-dashboard-sections > .stack {
                gap: 1rem;
            }

            .dashboard-overview-card {
                display: grid;
                grid-template-rows: auto 1fr;
            }

            .dashboard-overview-card .section-card__header {
                padding: 0.95rem 1.1rem;
            }

            .dashboard-overview-card .dashboard-mini-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.85rem;
                padding: 1rem 1.1rem 1.1rem;
            }

            .dashboard-overview-card .dashboard-mini-card {
                min-height: 148px;
                padding: 0.95rem 0.85rem;
                gap: 0.4rem;
                align-content: space-between;
            }

            .dashboard-overview-card .dashboard-mini-card span {
                font-size: 0.84rem;
                line-height: 1.28;
                text-wrap: balance;
            }

            .dashboard-overview-card .dashboard-mini-card strong {
                font-size: clamp(1.7rem, 1.65vw, 2.05rem);
                line-height: 0.98;
                overflow-wrap: anywhere;
            }

            .dashboard-overview-card .dashboard-mini-card small {
                font-size: 0.78rem;
                line-height: 1.28;
            }
        }

        @media (max-width: 760px) {
            .dashboard-mini-grid {
                grid-template-columns: 1fr;
            }

            .activity-item__top,
            .shop-verify-card__top {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-stack">
        <section class="summary-grid admin-dashboard-summary">
            @foreach ($stats as $stat)
                <article class="summary-card summary-card--{{ $stat['tone'] }}">
                    <p class="summary-card__label">{{ $stat['label'] }}</p>
                    <div class="summary-card__value">
                        <strong>
                            @if(!empty($stat['currency']))
                                &#8369; {{ number_format((float) $stat['value'], 2) }}
                            @else
                                {{ $stat['value'] }}
                            @endif
                        </strong>
                        <span>{{ $stat['note'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="dashboard-section-grid admin-dashboard-sections">
            <div class="stack">
                <article class="panel-card dashboard-overview-card dashboard-overview-card--users">
                    <div class="section-card__header">
                        <h3 class="section-title">Sales Overview</h3>
                    </div>

                    <div class="dashboard-mini-grid">
                        @foreach ($salesOverview as $metric)
                            <article class="dashboard-mini-card dashboard-mini-card--{{ $metric['tone'] }}">
                                <span>{{ $metric['label'] }}</span>
                                <strong>
                                    @if($metric['currency'])
                                        &#8369; {{ number_format((float) $metric['value'], 2) }}
                                    @else
                                        {{ $metric['value'] }}
                                    @endif
                                </strong>
                                <small>{{ $metric['note'] }}</small>
                            </article>
                        @endforeach
                    </div>
                </article>

                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Order Monitoring</h3>
                        <a href="{{ route('admin.orders') }}" class="section-link">Open Orders <i class="fa-solid fa-chevron-right"></i></a>
                    </div>

                    <div class="dashboard-mini-grid">
                        @foreach ($orderMonitoring as $metric)
                            <article class="dashboard-mini-card dashboard-mini-card--{{ $metric['tone'] }}">
                                <span>{{ $metric['label'] }}</span>
                                <strong>{{ $metric['value'] }}</strong>
                                <small>Live order queue</small>
                            </article>
                        @endforeach
                    </div>

                    <div class="dashboard-panel-table">
                        <div style="overflow-x:auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Buyer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentOrders as $order)
                                        @php
                                            $statusClass = match ($order->shippingToneClass()) {
                                                'processing' => 'pending',
                                                'shipped' => 'delivered',
                                                'delivered' => 'success',
                                                'cancelled' => 'cancelled',
                                                default => 'pending',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="dashboard-table-name">#{{ $order->id }}</td>
                                            <td>{{ $order->user?->name ?? 'Buyer' }}</td>
                                            <td>&#8369; {{ number_format((float) $order->total_price, 2) }}</td>
                                            <td><span class="status-pill status-pill--{{ $statusClass }}">{{ $order->shippingStatusLabel() }}</span></td>
                                            <td>{{ $order->created_at?->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="sub-line">No orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>

                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Product Moderation</h3>
                        <a href="{{ route('admin.products') }}" class="section-link">Open Moderation <i class="fa-solid fa-chevron-right"></i></a>
                    </div>

                    <div class="dashboard-mini-grid">
                        @foreach ($productModeration as $metric)
                            <article class="dashboard-mini-card dashboard-mini-card--{{ $metric['tone'] }}">
                                <span>{{ $metric['label'] }}</span>
                                <strong>{{ $metric['value'] }}</strong>
                                <small>Catalog review</small>
                            </article>
                        @endforeach
                    </div>

                    <div class="dashboard-panel-table">
                        <div style="overflow-x:auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Seller</th>
                                        <th>Price</th>
                                        <th>Reports</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pendingProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="dashboard-product-meta">
                                                    <span class="dashboard-table-name">{{ $product->name }}</span>
                                                    <span class="sub-line">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $product->user?->name ?? 'Seller' }}</td>
                                            <td>&#8369; {{ number_format((float) $product->price, 2) }}</td>
                                            <td>{{ $product->pending_reports_count }}</td>
                                            <td>
                                                <div class="dashboard-inline-actions">
                                                    <form method="POST" action="{{ route('admin.products.approve', $product) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="action-button action-button--success" type="submit">Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.products.reject', $product) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="action-button action-button--danger" type="submit">Reject</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="sub-line">No pending products right now.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>

                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Recent Activity</h3>
                    </div>

                    @if ($recentActivity->isEmpty())
                        <div class="dashboard-empty sub-line">No recent activity.</div>
                    @else
                        <div class="dashboard-activity-list">
                            @foreach ($recentActivity as $activity)
                                <article class="activity-item">
                                    <div class="activity-item__top">
                                        <span class="activity-item__type activity-item__type--{{ $activity['tone'] }}">{{ $activity['type'] }}</span>
                                        <span class="dashboard-inline-note">{{ optional($activity['time'])->diffForHumans() }}</span>
                                    </div>
                                    <div>
                                        <div class="activity-item__title">{{ $activity['title'] }}</div>
                                        <div class="activity-item__meta">{{ $activity['meta'] }}</div>
                                    </div>
                                    <div class="activity-item__actions">
                                        <a href="{{ $activity['action_url'] }}" class="action-button action-button--primary">{{ $activity['action_label'] }}</a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </article>
            </div>

            <div class="stack">
                <article class="panel-card dashboard-overview-card">
                    <div class="section-card__header">
                        <h3 class="section-title">User Management</h3>
                    </div>

                    <div class="dashboard-mini-grid">
                        @foreach ($userManagement as $metric)
                            <article class="dashboard-mini-card dashboard-mini-card--{{ $metric['tone'] }}">
                                <span>{{ $metric['label'] }}</span>
                                <strong>{{ $metric['value'] }}</strong>
                                <small>Marketplace users</small>
                            </article>
                        @endforeach
                    </div>
                </article>

                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Shop Verification</h3>
                        <a href="{{ route('admin.sellers') }}" class="section-link">Open Seller Reviews <i class="fa-solid fa-chevron-right"></i></a>
                    </div>

                    @if ($pendingSellers->isEmpty())
                        <div class="dashboard-empty sub-line">No pending seller applications.</div>
                    @else
                        <div class="dashboard-shop-list">
                            @foreach ($pendingSellers as $seller)
                                @php
                                    $displayName = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');
                                @endphp
                                <article class="shop-verify-card">
                                    <div class="shop-verify-card__top">
                                        <div>
                                            <div class="shop-verify-card__title">{{ $displayName }}</div>
                                            <div class="sub-line">{{ $seller->seller_type === 'registered_business' ? 'Registered Business' : 'Individual Seller' }}</div>
                                        </div>
                                        <span class="status-pill status-pill--pending">Pending</span>
                                    </div>

                                    <div class="shop-verify-card__meta">
                                        <span>{{ $seller->user?->email ?? $seller->email ?? 'No email' }}</span>
                                        <span>{{ optional($seller->submitted_at ?? $seller->created_at)->format('M d, Y') }}</span>
                                    </div>

                                    <div class="shop-verify-card__actions">
                                        <form method="POST" action="{{ route('admin.sellers.status', $seller) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="application_status" value="approved">
                                            <button class="action-button action-button--success" type="submit">Approve</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.sellers.status', $seller) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="application_status" value="rejected">
                                            <button class="action-button action-button--danger" type="submit">Reject</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </article>
            </div>
        </section>
    </div>
@endsection
