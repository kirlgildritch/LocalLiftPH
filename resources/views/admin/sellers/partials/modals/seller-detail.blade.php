<div class="modal-shell" id="seller-detail-modal" hidden>
    <div class="modal-card modal-card--wide">
        <div class="modal-card__header">
            <h3 class="modal-title">Seller Details</h3>
            <button class="modal-close" type="button" data-close-modal="seller-detail-modal">&times;</button>
        </div>

        <div class="modal-card__body">
            <div class="seller-box__profile">
                <div class="avatar-photo avatar-photo--teal" id="seller-detail-avatar"></div>
                <div>
                    <div class="seller-name" id="seller-detail-name"></div>
                    <div class="sub-line" id="seller-detail-handle"></div>
                </div>
            </div>

            <div class="modal-meta-bar spacer-top">
                <strong id="seller-detail-products"></strong>
                <span>Joined <span id="seller-detail-date"></span></span>
                <span>Email <span id="seller-detail-email"></span></span>
            </div>

            <div class="spacer-top">
                <h4 class="section-title">Uploaded Documents</h4>
                <div class="doc-card-grid spacer-top">
                    <div class="doc-card">
                        <div class="doc-thumb doc-thumb--id"></div>
                        <div class="doc-card__content">
                            <div class="seller-name" id="seller-id-label">ID / Passport</div>
                            <div class="doc-card__status" id="seller-id-status">Uploaded</div>
                            <button class="button" type="button" id="seller-id-link">View</button>
                        </div>
                    </div>

                    <div class="doc-card">
                        <div class="doc-thumb doc-thumb--license"></div>
                        <div class="doc-card__content">
                            <div class="seller-name">Business License</div>
                            <div class="doc-card__status" id="seller-permit-status">Optional / Not uploaded</div>
                            <button class="button" type="button" id="seller-permit-link">View</button>
                        </div>
                    </div>

                    <div class="doc-card">
                        <div class="doc-thumb doc-thumb--address"></div>
                        <div class="doc-card__content">
                            <div class="seller-name" id="seller-requested-document-label">Requested Document</div>
                            <div class="doc-card__status" id="seller-requested-document-status">Not uploaded</div>
                            <button class="button" type="button" id="seller-requested-document-link">View</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="spacer-top">
                <h4 class="section-title">Request More Documents</h4>
                <form method="POST" id="seller-review-form" class="page-stack spacer-top">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="application_status" value="pending" id="seller-review-status">
                    <input type="hidden" name="request_more_documents" value="0" id="seller-request-more-documents">

                    <div class="seller-request-card">
                        <div class="seller-request-empty-state" id="seller-request-empty-state">
                            <strong>No document request yet.</strong>
                            <p>Send a request below if you need more verification documents from this seller.</p>
                        </div>

                        <div class="seller-request-details" id="seller-request-details" hidden>
                            <div class="seller-request-detail-row">
                                <span>Reason</span>
                                <strong id="seller-request-current-reason">None</strong>
                            </div>
                            <div class="seller-request-detail-row">
                                <span>Notes</span>
                                <strong id="seller-request-current-notes">None</strong>
                            </div>
                            <div class="seller-request-detail-row">
                                <span>Requested</span>
                                <strong id="seller-request-current-date">N/A</strong>
                            </div>
                            <div class="seller-request-detail-row">
                                <span>Status</span>
                                <span class="seller-request-status-badge" id="seller-request-current-status">None</span>
                            </div>
                        </div>
                    </div>

                    <div class="seller-request-form-row spacer-top">
                        <select class="field-select seller-request-select" id="seller-review-reason" name="document_request_reason">
                            <option value="">Select Reason</option>
                            @foreach ($documentRequestReasons as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="action-button action-button--warning seller-request-button" type="button"
                            id="request-documents-button">Request Documents</button>
                    </div>

                    <textarea class="field-textarea seller-request-notes" name="review_notes" id="seller-review-notes" rows="4"
                        placeholder="Add admin review notes or required document instructions..."></textarea>

                    <div class="alert-note seller-request-note">Additional document requests will notify the seller through the dashboard
                        review state.</div>
                </form>
            </div>
        </div>

        <div class="modal-card__footer">
            <div class="footer-actions">
                <button class="action-button action-button--success" type="button" data-status-submit="approved"
                    id="seller-approve-button">Verify
                    Seller</button>
                <button class="action-button action-button--danger" type="button" data-status-submit="rejected"
                    id="seller-reject-button">Reject
                    Seller</button>
                <button class="button" type="button" data-status-submit="pending" id="seller-pending-button">Save as Pending</button>
            </div>
        </div>
    </div>
</div>
