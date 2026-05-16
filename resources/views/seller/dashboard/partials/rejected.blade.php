                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Application Update</span>
                            <h1>Seller application rejected</h1>
                            <p>{{ $seller?->review_notes ?: 'Your application needs changes before it can be approved.' }}</p>

                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>Current Status</strong>
                                    <p>Rejected. Review the feedback and resubmit your registration from this dashboard.</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Next Step</strong>
                                    <p>Update your information or documents, then submit again for admin review.</p>
                                </article>
                            </div>

                            <div class="dashboard-empty-actions">
                                <a href="{{ route('seller.dashboard', ['resubmit' => 1]) }}" class="page-action-btn">Update and
                                    Resubmit</a>
                            </div>
                        </section>
