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
