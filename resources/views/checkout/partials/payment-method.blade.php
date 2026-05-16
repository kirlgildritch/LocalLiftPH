<section class="checkout-card panel">
    <div class="card-header">
        <div class="step-title">
            <span class="step-number">3</span>
            <div>
                <span class="toolbar-label">Step</span>
                <h3>Payment Information</h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="payment-method-grid" data-payment-methods>
            @foreach($paymentMethods as $methodKey => $method)
                <label class="payment-method-card {{ $selectedPaymentMethod === $methodKey ? 'is-selected' : '' }}">
                    <input
                        type="radio"
                        name="payment_method"
                        value="{{ $methodKey }}"
                        form="checkout-submit-form"
                        data-payment-choice
                        data-payment-label="{{ $method['label'] }}"
                        data-payment-short-label="{{ $method['short_label'] }}"
                        data-payment-instructions="{{ $method['instructions'] }}"
                        {{ $selectedPaymentMethod === $methodKey ? 'checked' : '' }}
                    >
                    <span class="payment-method-card__icon">
                        <i class="fa-solid {{ $method['icon'] }}"></i>
                    </span>
                    <span class="payment-method-card__copy">
                        <strong>{{ $method['label'] }}</strong>
                        <small>{{ $method['description'] }}</small>
                    </span>
                    <span class="payment-method-card__check">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </label>
            @endforeach
        </div>

        <div class="payment-note" data-payment-note>
            <i class="fa-solid fa-circle-info"></i>
            <div>
                <strong data-payment-note-title>{{ $selectedPayment['label'] }}</strong>
                <p data-payment-note-copy>{{ $selectedPayment['instructions'] }}</p>
            </div>
        </div>
    </div>
</section>
