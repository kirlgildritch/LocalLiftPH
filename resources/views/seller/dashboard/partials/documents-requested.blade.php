                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Documents Required</span>
                            <h1>Additional document requested</h1>

                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>{{ $requestReasonLabel }}</strong>
                                    <p>{{ $latestDocumentRequest?->admin_notes ?: 'Upload the requested document to continue review.' }}</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Requested</strong>
                                    <p>{{ optional($latestDocumentRequest?->requested_at)->format('M d, Y h:i A') ?: 'N/A' }}</p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Status</strong>
                                    <p>{{ $requestStatusLabel }}</p>
                                </article>
                            </div>

                            <div class="dashboard-empty-actions">
                                <a href="{{ route('seller.dashboard', ['resubmit' => 1]) }}" class="page-action-btn">Upload
                                    Documents</a>
                            </div>
                        </section>
