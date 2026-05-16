<div class="modal-shell" id="report-detail-modal" hidden>
    <div class="modal-card modal-card--wide">
        <div class="modal-card__header">
            <h3 class="modal-title">Report Details</h3>
            <button class="modal-close" type="button" data-close-modal="report-detail-modal">&times;</button>
        </div>
        <div class="modal-card__body report-detail-grid">
            <div class="report-summary">
                <div class="report-thumb-icon report-thumb-icon--large" id="report-detail-thumb">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <div class="seller-name" id="report-detail-target"></div>
                    <div class="muted-row"><i class="fa-solid fa-user"></i> <span id="report-detail-seller"></span></div>
                </div>
            </div>

            <div class="report-meta">
                <div class="report-meta-row">
                    <div class="seller-name">Target:</div>
                    <div id="report-detail-type"></div>
                </div>
                <div class="report-meta-row">
                    <div class="seller-name">Reporter:</div>
                    <div id="report-detail-reporter"></div>
                </div>
                <div class="report-meta-row">
                    <div class="seller-name">Reason:</div>
                    <div id="report-detail-reason"></div>
                </div>
                <div class="report-meta-row">
                    <div class="seller-name">Submitted Date:</div>
                    <div id="report-detail-submitted-date"></div>
                </div>
                <div class="report-meta-row">
                    <div class="seller-name">Status:</div>
                    <span class="status-pill status-pill--pending" id="report-detail-status"></span>
                </div>
            </div>

            <div class="report-summary-card">
                <h4 class="section-title">Reporter Message</h4>
                <p style="margin: 0;" id="report-detail-message"></p>
            </div>

            <div class="report-summary-card">
                <h4 class="section-title">Action History</h4>
                <div class="report-history-list" id="report-history-list"></div>
            </div>

            <form method="POST" id="report-action-form" class="report-action-stack">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" id="report-action-input">

                <label>
                    <span class="section-title" style="display:block;margin-bottom:0.75rem;">Admin Notes</span>
                    <textarea class="report-admin-notes" name="admin_notes" id="report-admin-notes"
                        placeholder="Add admin notes"></textarea>
                </label>
            </form>
        </div>
        <div class="modal-card__footer">
            <button class="button" type="button" data-close-modal="report-detail-modal">Close</button>
            <div class="report-action-toolbar" id="report-action-toolbar"></div>
        </div>
    </div>
</div>
