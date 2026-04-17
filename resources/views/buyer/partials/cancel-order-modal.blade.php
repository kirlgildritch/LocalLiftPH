<div class="cancel-modal-overlay" id="cancelOrderModal">
    <div class="cancel-modal panel">
        <button type="button" class="cancel-modal-close" data-close-cancel-modal>
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="cancel-modal-header">
            <span class="section-kicker">Cancel Order</span>
            <h3>Why are you cancelling this order?</h3>
            <p>Select one or more reasons so the cancellation stays clear in the order history.</p>
        </div>

        <form method="POST" id="cancelOrderForm" class="cancel-order-form">
            @csrf
            @method('PATCH')

            <div class="cancel-reasons-list">
                @foreach([
                    'Changed my mind',
                    'Item price too high',
                    'Found better price elsewhere',
                    'Item damaged / defective',
                    'Delivery delay',
                    'Other',
                ] as $reason)
                    <label class="reason-option">
                        <input type="checkbox" name="reasons[]" value="{{ $reason }}" {{ collect(old('reasons', []))->contains($reason) ? 'checked' : '' }}>
                        <span>{{ $reason }}</span>
                    </label>
                @endforeach
            </div>

            <div class="other-reason-wrap {{ collect(old('reasons', []))->contains('Other') ? 'is-visible' : '' }}" id="otherReasonWrap">
                <label for="cancel_other_reason">Tell us more</label>
                <textarea id="cancel_other_reason" name="other_reason" rows="4" placeholder="Type your cancellation reason here...">{{ old('other_reason') }}</textarea>
                @error('other_reason')
                    <small class="form-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="cancel-modal-actions">
                <button type="submit" class="order-btn danger-btn">Cancel Order</button>
                <button type="button" class="order-btn secondary-btn" data-close-cancel-modal>Nevermind</button>
            </div>
        </form>
    </div>
</div>
