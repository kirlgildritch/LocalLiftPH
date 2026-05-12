<div class="cancel-modal-overlay" id="returnRequestModal">
    <div class="cancel-modal panel">
        <button type="button" class="cancel-modal-close" data-close-return-modal>&times;</button>

        <div class="cancel-modal-header">
            <span class="toolbar-label">After-Sales Support</span>
            <h3>Request Return / Refund</h3>
            <p>Submit this only for completed orders with an issue. The seller will review your reason and respond.</p>
        </div>

        <form method="POST" id="returnRequestForm" class="cancel-order-form" enctype="multipart/form-data">
            @csrf

            <div class="return-form-grid">
                <label class="return-field">
                    <span>Reason</span>
                    <select name="reason" required>
                        <option value="">Select a reason</option>
                        <option value="Damaged item">Damaged item</option>
                        <option value="Wrong item received">Wrong item received</option>
                        <option value="Missing item">Missing item</option>
                        <option value="Item not as described">Item not as described</option>
                        <option value="Quality issue">Quality issue</option>
                        <option value="Other">Other</option>
                    </select>
                </label>

                <label class="return-field">
                    <span>Preferred resolution</span>
                    <select name="preferred_resolution" required>
                        <option value="refund">Refund</option>
                        <option value="return_and_refund">Return and refund</option>
                        <option value="replacement">Replacement</option>
                    </select>
                </label>
            </div>

            <label class="other-reason-wrap is-visible">
                <span>Details</span>
                <textarea name="details" rows="4" maxlength="1000" required
                    placeholder="Describe the issue clearly. Include condition, missing parts, or what was different from the listing."></textarea>
            </label>

            <label class="return-field">
                <span>Evidence photos/videos</span>
                <input type="file" name="evidence[]" accept="image/*,video/*" multiple>
            </label>

            <div class="return-policy-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>Requests are accepted within 7 days after the order is completed.</span>
            </div>

            <div class="cancel-modal-actions">
                <button type="button" class="order-btn secondary-btn" data-close-return-modal>Keep Order</button>
                <button type="submit" class="order-btn danger-btn">Submit Request</button>
            </div>
        </form>
    </div>
</div>
