                        <div class="stats-grid">
                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-solid fa-coins"></i>
                                    <span>Total Sales</span>
                                </div>
                                <strong>&#8369; {{ number_format($stats['total_sales'], 2) }}</strong>
                                <p>Current revenue snapshot across completed order items.</p>
                            </article>

                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Orders Received</span>
                                </div>
                                <strong>{{ $stats['orders_received'] }}</strong>
                                <p>Orders connected to your approved live product listings.</p>
                            </article>

                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-solid fa-cube"></i>
                                    <span>Products Listed</span>
                                </div>
                                <strong>{{ $stats['products_listed'] }}</strong>
                                <p>Approved products currently visible to buyers.</p>
                            </article>

                            <article class="stat-card panel">
                                <div class="stat-top">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Pending Orders</span>
                                </div>
                                <strong class="highlight">{{ $stats['pending_orders'] }}</strong>
                                <p>Orders that still need confirmation or processing.</p>
                            </article>
                        </div>

                        <div class="dashboard-grid">
                            <section class="content-panel panel orders-panel">
                                <div class="panel-heading">
                                    <div>
                                        <span class="section-kicker">Orders</span>
                                        <h2>Recent Orders</h2>
                                    </div>
                                    <a href="{{ route('seller.orders') }}" class="inline-link">View All</a>
                                </div>

                                <div class="order-list">
                                    @forelse ($recentOrders as $item)
                                        <article class="order-item">
                                            <div>
                                                <strong>#{{ $item->order?->id ?? $item->id }}</strong>
                                                <span>{{ $item->order?->customer_name ?? 'Buyer Order' }}</span>
                                            </div>
                                            <div class="order-meta">
                                                <span
                                                    class="status-chip {{ in_array($item->order?->status, ['completed', 'delivered']) ? 'completed' : (in_array($item->order?->status, ['processing']) ? 'processing' : (in_array($item->order?->status, ['shipped']) ? 'shipped' : 'pending')) }}">
                                                    {{ ucfirst($item->order?->status ?? 'pending') }}
                                                </span>
                                                <strong>&#8369; {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }}</strong>
                                            </div>
                                        </article>
                                    @empty
                                        <p class="empty-text">No orders yet for your live products.</p>
                                    @endforelse
                                </div>
                            </section>

                            <section class="content-panel panel conversations-panel">
                                <div class="panel-heading">
                                    <div>
                                        <span class="section-kicker">Inbox</span>
                                        <h2>Buyer Conversations</h2>
                                    </div>
                                </div>

                                <div class="dashboard-mini-metrics">
                                    <article class="hero-stat-card">
                                        <strong>{{ $stats['open_conversations'] }}</strong>
                                        <span>Inbox</span>
                                    </article>
                                    <article class="hero-stat-card">
                                        <strong>{{ $stats['active_products'] }}</strong>
                                        <span>Live listings</span>
                                    </article>
                                </div>
                            </section>
                        </div>
