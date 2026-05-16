<div class="modal-shell" id="reject-reason-modal" hidden>
    <div class="modal-card">
        <div class="modal-card__header">
            <h3 class="modal-title" id="reject-modal-title">Reject Product</h3>
            <button class="modal-close" type="button" data-close-modal="reject-reason-modal">&times;</button>
        </div>
        <form method="POST" id="reject-modal-form">
            @csrf
            @method('PATCH')
            <div class="modal-card__body">
                <div class="reason-grid">
                    @foreach ($rejectionReasons as $key => $label)
                        <label class="reason-option">
                            <input type="radio" name="rejection_reason_key" value="{{ $key }}">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <label class="reason-textarea">
                    <span>Custom</span>
                    <textarea name="rejection_reason_custom" rows="3" maxlength="500"
                        placeholder="Optional"></textarea>
                </label>
                <input type="hidden" name="action" id="reject-modal-action" value="">
                <div id="reject-modal-product-ids"></div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="reject-reason-modal">Cancel</button>
                <button class="action-button action-button--danger" type="submit">Reject</button>
            </div>
        </form>
    </div>
</div>
