<div class="modal-shell" id="product-approval-modal" hidden>
    <div class="modal-card modal-card--wide">
        <div class="modal-card__header">
            <h3 class="modal-title" id="product-modal-title"></h3>
            <button class="modal-close" type="button" data-close-modal="product-approval-modal">&times;</button>
        </div>
        <div class="modal-card__body">
            <div class="product-modal-grid">
                <div class="product-gallery">
                    <div class="admin-product-gallery" id="product-modal-gallery">
                        <button class="admin-product-gallery__arrow admin-product-gallery__arrow--prev" id="product-modal-prev"
                            type="button" aria-label="Previous media">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="admin-product-gallery__stage" id="product-modal-stage"></div>
                        <button class="admin-product-gallery__arrow admin-product-gallery__arrow--next" id="product-modal-next"
                            type="button" aria-label="Next media">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <div class="admin-product-gallery__counter" id="product-modal-counter">1 / 1</div>
                    </div>
                    <div class="thumb-strip" id="product-modal-thumbs"></div>
                </div>
                <div>
                    <div class="detail-list">
                        <div class="detail-list__item"><span>Category</span><strong
                                id="product-modal-category"></strong></div>
                        <div class="detail-list__item"><span>Shop</span><strong id="product-modal-shop"></strong>
                        </div>
                        <div class="detail-list__item"><span>Status</span><strong
                                id="product-modal-status"></strong></div>
                        <div class="detail-list__item"><span>Date submitted</span><strong
                                id="product-modal-submitted"></strong></div>
                        <div class="detail-list__item"><span>Price</span><strong id="product-modal-price"></strong>
                        </div>
                        <div class="detail-list__item"><span>Shipping fee</span><strong
                                id="product-modal-shipping"></strong></div>
                        <div class="detail-list__item"><span>Stock</span><strong id="product-modal-stock"></strong>
                        </div>
                        <div class="detail-list__item"><span>Condition</span><strong
                                id="product-modal-condition"></strong></div>
                        <div class="detail-list__item"><span>Dimensions</span><strong
                                id="product-modal-dimensions"></strong></div>
                        <div class="detail-list__item"><span>Weight</span><strong id="product-modal-weight"></strong>
                        </div>
                        <div class="detail-list__item detail-list__item--top"><span>Description</span><strong
                                id="product-modal-description"></strong></div>
                        <div class="detail-list__item"><span>Reports</span><strong id="product-modal-reports"></strong>
                        </div>
                        <div class="detail-list__item detail-list__item--top"><span>Rejection reason</span><strong
                                id="product-modal-rejection"></strong></div>
                    </div>

                    <div class="seller-box">
                        <div class="seller-box__header">Seller Information</div>
                        <div class="seller-box__body">
                            <div class="seller-box__profile">
                                <div class="avatar-circle" id="product-modal-seller-avatar"></div>
                                <div>
                                    <div class="seller-name" id="product-modal-seller-handle"></div>
                                    <div class="sub-line" id="product-modal-seller-name"></div>
                                </div>
                            </div>
                            <button class="action-button action-button--primary" type="button"
                                id="product-modal-open-seller">View Seller Profile</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-card__footer">
            <button class="button" type="button" data-close-modal="product-approval-modal">Close</button>
            <div class="footer-actions">
                <button class="action-button action-button--danger" type="button" id="product-modal-reject-button">
                    Reject
                </button>
                <form method="POST" id="product-modal-approve-form">
                    @csrf
                    @method('PATCH')
                    <button class="action-button action-button--success" type="submit" id="product-modal-approve-button">Approve</button>
                </form>
            </div>
        </div>
    </div>
</div>
