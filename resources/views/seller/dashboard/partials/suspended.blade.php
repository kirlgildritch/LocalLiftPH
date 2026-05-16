                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Account Status</span>
                            <h1>Seller account suspended</h1>
                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>Status</strong>
                                    <p>Suspended</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Reason</strong>
                                    <p>{{ $seller?->suspension_reason ?: 'Account under moderation review.' }}</p>
                                </article>
                            </div>
                        </section>
