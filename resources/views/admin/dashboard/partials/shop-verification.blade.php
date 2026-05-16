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
