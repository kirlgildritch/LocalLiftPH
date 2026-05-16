<div class="modal-shell" id="report-seller-modal" hidden>
    <div class="modal-card modal-card--wide">
        <div class="modal-card__header">
            <h3 class="modal-title">Reported Seller</h3>
            <button class="modal-close" type="button" data-close-modal="report-seller-modal">&times;</button>
        </div>
        <div class="modal-card__body report-target-modal-grid">
            <div class="report-seller-summary">
                <div class="seller-box__profile">
                    <div class="avatar-circle" id="report-seller-avatar"></div>
                    <div>
                        <div class="seller-name" id="report-seller-shop-name"></div>
                        <div class="sub-line" id="report-seller-owner-name"></div>
                    </div>
                </div>
                <div class="report-doc-grid">
                    <div class="report-doc-row">
                        <div>
                            <div class="seller-name" id="report-seller-id-type">ID / Passport</div>
                            <div class="sub-line">Verification document</div>
                        </div>
                        <button class="action-button action-button--primary" type="button"
                            id="report-seller-id-view">View</button>
                    </div>
                    <div class="report-doc-row">
                        <div>
                            <div class="seller-name">Business License</div>
                            <div class="sub-line">Business permit</div>
                        </div>
                        <button class="action-button action-button--primary" type="button"
                            id="report-seller-permit-view">View</button>
                    </div>
                </div>
            </div>
            <div class="detail-list">
                <div class="detail-list__item"><span>Email</span><strong id="report-seller-email"></strong></div>
                <div class="detail-list__item"><span>Phone</span><strong id="report-seller-phone"></strong></div>
                <div class="detail-list__item detail-list__item--top"><span>Address</span><strong
                        id="report-seller-address"></strong></div>
                <div class="detail-list__item"><span>Status</span><strong id="report-seller-status"></strong></div>
                <div class="detail-list__item"><span>Products</span><strong id="report-seller-products-count"></strong>
                </div>
                <div class="detail-list__item detail-list__item--top"><span>Description</span><strong
                        id="report-seller-description"></strong></div>
                <div class="detail-list__item detail-list__item--top"><span>Suspension Reason</span><strong
                        id="report-seller-suspension-reason"></strong></div>
            </div>
        </div>
        <div class="modal-card__footer">
            <button class="button" type="button" data-close-modal="report-seller-modal">Close</button>
        </div>
    </div>
</div>
