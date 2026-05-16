<div class="modal-shell" id="product-seller-modal" hidden>
    <div class="modal-card modal-card--wide">
        <div class="modal-card__header">
            <h3 class="modal-title">Seller Profile: <span id="product-seller-modal-handle"></span></h3>
            <button class="modal-close" type="button" data-close-modal="product-seller-modal">&times;</button>
        </div>
        <div class="modal-card__body">
            <div class="seller-box__profile">
                <div class="avatar-circle" id="product-seller-modal-avatar"></div>
                <div>
                    <div class="seller-name" id="product-seller-modal-username"></div>
                    <div class="sub-line" id="product-seller-modal-fullname"></div>
                </div>
                <span class="status-pill" id="product-seller-modal-status"></span>
            </div>

            <div class="tabs">
                <button class="tab-button is-active" type="button" data-seller-tab="shop">Shop Info</button>
                <button class="tab-button" type="button" data-seller-tab="documents">Verification Documents</button>
                <button class="tab-button" type="button" data-seller-tab="products">Products</button>
            </div>

            <div class="tab-panel" data-seller-panel="shop">
                <div class="detail-list">
                    <div class="detail-list__item"><span>Shop name</span><strong
                            id="product-seller-shop-name"></strong></div>
                    <div class="detail-list__item"><span>Owner name</span><strong
                            id="product-seller-owner-name"></strong></div>
                    <div class="detail-list__item"><span>Email</span><strong id="product-seller-email"></strong></div>
                    <div class="detail-list__item"><span>Phone</span><strong id="product-seller-phone"></strong></div>
                    <div class="detail-list__item detail-list__item--top"><span>Address</span><strong
                            id="product-seller-address"></strong></div>
                    <div class="detail-list__item detail-list__item--top"><span>Description</span><strong
                            id="product-seller-description"></strong></div>
                    <div class="detail-list__item"><span>Status</span><strong id="product-seller-status-text"></strong>
                    </div>
                    <div class="detail-list__item"><span>Date registered</span><strong
                            id="product-seller-registered"></strong></div>
                    <div class="detail-list__item"><span>Verification</span><strong
                            id="product-seller-verification"></strong></div>
                </div>
            </div>

            <div class="tab-panel" data-seller-panel="documents" hidden>
                <div class="document-row">
                    <h4 class="section-title">Verification Documents</h4>
                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--id"></div>
                        <div>
                            <div class="seller-name" id="product-seller-id-label">Government Issued ID</div>
                            <div class="sub-line" id="product-seller-id-meta">Uploaded seller verification document
                            </div>
                        </div>
                        <div><button class="action-button action-button--primary" type="button"
                                id="product-seller-id-link">View Passport</button></div>
                    </div>
                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--license"></div>
                        <div>
                            <div class="seller-name">Business License / Permit</div>
                            <div class="sub-line" id="product-seller-permit-meta">Uploaded only when applicable</div>
                        </div>
                        <div><button class="action-button action-button--primary" type="button"
                                id="product-seller-permit-link">View Business License</button></div>
                    </div>
                </div>
            </div>

            <div class="tab-panel" data-seller-panel="products" hidden>
                <div class="seller-products-list" id="product-seller-products-list"></div>
            </div>
        </div>
        <div class="modal-card__footer">
            <button class="button" type="button" data-close-modal="product-seller-modal">Close</button>
        </div>
    </div>
</div>
