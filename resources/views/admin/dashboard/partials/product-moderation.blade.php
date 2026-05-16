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
