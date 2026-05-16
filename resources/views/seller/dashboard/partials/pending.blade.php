                        <section class="dashboard-status-state panel">
                            <span class="section-kicker">Pending Review</span>
                            <h1>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? 'Resubmitted for Review' : 'Application Submitted' }}</h1>
                            <div class="status-card-grid">
                                <article class="status-card panel">
                                    <strong>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? 'Latest Request' : 'Application Submitted' }}</strong>
                                    <p>
                                        @if ($latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED)
                                            {{ $requestReasonLabel }}. {{ $latestDocumentRequest->admin_notes ?: 'Document resubmitted for review.' }}
                                        @else
                                            Your seller details and uploaded documents are now in the admin review queue.
                                        @endif
                                    </p>
                                </article>
                                <article class="status-card panel">
                                    <strong>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? 'Requested' : 'Pending Admin Review' }}</strong>
                                    <p>
                                        @if ($latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED)
                                            {{ optional($latestDocumentRequest->requested_at)->format('M d, Y h:i A') ?: 'N/A' }}
                                        @else
                                            Your dashboard analytics and shop controls will unlock after approval.
                                        @endif
                                    </p>
                                </article>
                                <article class="status-card panel">
                                    <strong>Status</strong>
                                    <p>{{ $latestDocumentRequest?->status === \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED ? $requestStatusLabel : 'Under Review' }}</p>
                                </article>
                            </div>
                        </section>
