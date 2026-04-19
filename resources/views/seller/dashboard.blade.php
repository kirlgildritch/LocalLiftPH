@extends('layouts.seller')

@section('content')
<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">
            @include('seller.partials.sidebar')

            <main class="dashboard-main panel">
                <div class="dashboard-hero panel">
                    <div>
                        <span class="section-kicker">Seller Dashboard</span>
                        <h1>Run your shop with a cleaner, simpler workspace</h1>
                        <p>Track your store performance, watch incoming orders, and keep your products organized without extra clutter.</p>
                    </div>

                    <div class="dashboard-hero-stats">
                        <div class="hero-stat-card">
                            <strong>120</strong>
                            <span>Total orders</span>
                        </div>
                        <div class="hero-stat-card">
                            <strong>15</strong>
                            <span>Active products</span>
                        </div>
                    </div>
                </div>

                <div class="stats-grid">
                    <article class="stat-card panel">
                        <div class="stat-top">
                            <i class="fa-solid fa-coins"></i>
                            <span>Total Sales</span>
                        </div>
                        <strong>PHP 25,300</strong>
                        <p>Current revenue snapshot across completed orders.</p>
                    </article>

                    <article class="stat-card panel">
                        <div class="stat-top">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Orders Received</span>
                        </div>
                        <strong>120</strong>
                        <p>All orders captured from your storefront activity.</p>
                    </article>

                    <article class="stat-card panel">
                        <div class="stat-top">
                            <i class="fa-solid fa-cube"></i>
                            <span>Products Listed</span>
                        </div>
                        <strong>15</strong>
                        <p>Products currently visible in your seller catalog.</p>
                    </article>

                    <article class="stat-card panel">
                        <div class="stat-top">
                            <i class="fa-regular fa-clock"></i>
                            <span>Pending Orders</span>
                        </div>
                        <strong class="highlight">3</strong>
                        <p>Orders that still need confirmation or processing.</p>
                    </article>
                </div>

                <div class="dashboard-grid">
                    <section class="content-panel panel">
                        <div class="panel-heading">
                            <div>
                                <span class="section-kicker">Performance</span>
                                <h2>Sales Overview</h2>
                            </div>
                        </div>

                        <div class="chart-legend">
                            <div class="legend-item">
                                <span class="legend-line primary-line"></span>
                                This Month
                            </div>
                            <div class="legend-item">
                                <span class="legend-line muted-line"></span>
                                Last Month
                            </div>
                        </div>

                        <div class="chart-shell">
                            <div class="y-labels">
                                <span>300</span>
                                <span>200</span>
                                <span>100</span>
                                <span>0</span>
                            </div>

                            <div class="chart-box">
                                <svg viewBox="0 0 500 250" preserveAspectRatio="none">
                                    <polyline points="20,210 95,150 170,160 245,90 320,120 395,70" fill="none" stroke="rgba(187, 222, 251, 0.36)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="20" cy="210" r="7" fill="rgba(187, 222, 251, 0.36)" />
                                    <circle cx="95" cy="150" r="7" fill="rgba(187, 222, 251, 0.36)" />
                                    <circle cx="170" cy="160" r="7" fill="rgba(187, 222, 251, 0.36)" />
                                    <circle cx="245" cy="90" r="7" fill="rgba(187, 222, 251, 0.36)" />
                                    <circle cx="320" cy="120" r="7" fill="rgba(187, 222, 251, 0.36)" />
                                    <circle cx="395" cy="70" r="7" fill="rgba(187, 222, 251, 0.36)" />

                                    <polyline points="20,205 95,120 170,125 245,60 320,75 395,30" fill="none" stroke="#42A5F5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="20" cy="205" r="8" fill="#42A5F5" />
                                    <circle cx="95" cy="120" r="8" fill="#42A5F5" />
                                    <circle cx="170" cy="125" r="8" fill="#42A5F5" />
                                    <circle cx="245" cy="60" r="8" fill="#42A5F5" />
                                    <circle cx="320" cy="75" r="8" fill="#42A5F5" />
                                    <circle cx="395" cy="30" r="8" fill="#42A5F5" />
                                </svg>
                            </div>
                        </div>

                        <div class="months">
                            <span>Jan</span>
                            <span>Feb</span>
                            <span>Mar</span>
                            <span>Apr</span>
                            <span>May</span>
                            <span>Jun</span>
                        </div>
                    </section>

                    <section class="content-panel panel">
                        <div class="panel-heading">
                            <div>
                                <span class="section-kicker">Orders</span>
                                <h2>Recent Orders</h2>
                            </div>
                            <a href="{{ route('seller.orders') }}" class="inline-link">View All</a>
                        </div>

                        <div class="order-list">
                            <article class="order-item">
                                <div>
                                    <strong>#1023</strong>
                                    <span>Anna Santos</span>
                                </div>
                                <div class="order-meta">
                                    <span class="status-chip completed">Completed</span>
                                    <strong>PHP 850.00</strong>
                                </div>
                            </article>

                            <article class="order-item">
                                <div>
                                    <strong>#1022</strong>
                                    <span>Mark Reyes</span>
                                </div>
                                <div class="order-meta">
                                    <span class="status-chip processing">Processing</span>
                                    <strong>PHP 1,200.00</strong>
                                </div>
                            </article>

                            <article class="order-item">
                                <div>
                                    <strong>#1021</strong>
                                    <span>Liza Delacruz</span>
                                </div>
                                <div class="order-meta">
                                    <span class="status-chip shipped">Shipped</span>
                                    <strong>PHP 540.00</strong>
                                </div>
                            </article>

                            <article class="order-item">
                                <div>
                                    <strong>#1020</strong>
                                    <span>John Villanueva</span>
                                </div>
                                <div class="order-meta">
                                    <span class="status-chip pending">Pending</span>
                                    <strong>PHP 320.00</strong>
                                </div>
                            </article>
                        </div>
                    </section>
                </div>

                <section class="content-panel panel">
                    <div class="panel-heading">
                        <div>
                            <span class="section-kicker">Products</span>
                            <h2>Your Products</h2>
                        </div>
                        <a href="{{ url('/manage-products') }}" class="inline-link">Manage Products</a>
                    </div>

                    <div class="product-grid">
                        <article class="product-card panel">
                            <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?q=80&w=800&auto=format&fit=crop" alt="Beaded Bracelet">
                            <div class="product-copy">
                                <h3>Beaded Bracelet</h3>
                                <p>10 sales</p>
                                <div class="product-card-bottom">
                                    <strong>PHP 180.00</strong>
                                    <a href="{{ url('/manage-products') }}" class="mini-action">View</a>
                                </div>
                            </div>
                        </article>

                        <article class="product-card panel">
                            <img src="https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?q=80&w=800&auto=format&fit=crop" alt="Herbal Soap Set">
                            <div class="product-copy">
                                <h3>Herbal Soap Set</h3>
                                <p>25 in stock</p>
                                <div class="product-card-bottom">
                                    <strong>PHP 250.00</strong>
                                    <a href="{{ url('/manage-products') }}" class="mini-action">View</a>
                                </div>
                            </div>
                        </article>

                        <article class="product-card panel">
                            <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?q=80&w=800&auto=format&fit=crop" alt="Eco-Friendly Straws">
                            <div class="product-copy">
                                <h3>Eco-Friendly Straws</h3>
                                <p>5 sales</p>
                                <div class="product-card-bottom">
                                    <strong>PHP 150.00</strong>
                                    <a href="{{ url('/manage-products') }}" class="mini-action">View</a>
                                </div>
                            </div>
                        </article>

                        <article class="product-card panel">
                            <img src="https://images.unsplash.com/photo-1594223274512-ad4803739b7c?q=80&w=800&auto=format&fit=crop" alt="Woven Tote Bag">
                            <div class="product-copy">
                                <h3>Woven Tote Bag</h3>
                                <p>8 sales</p>
                                <div class="product-card-bottom">
                                    <strong>PHP 350.00</strong>
                                    <a href="{{ url('/manage-products') }}" class="mini-action">View</a>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </main>
        </div>
    </div>
</section>
@endsection
