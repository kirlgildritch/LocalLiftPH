@php
    $modalId = $modalId ?? 'report-modal';
    $modalContext = $modalContext ?? 'product';
    $triggerLabel = $triggerLabel ?? 'Report listing';
    $sellerId = $sellerId ?? null;
    $productId = $productId ?? null;
    $reportErrors = $errors->getBag('reportSubmission');
    $hasReportErrors = $reportErrors->any();
    $shouldAutoOpen = session('report_modal_open') === $modalContext || $hasReportErrors;
@endphp

<button type="button" class="report-trigger-button" data-report-open="{{ $modalId }}" aria-label="{{ $triggerLabel }}">
    <i class="fa-solid fa-flag"></i>
</button>

<div class="report-modal-shell" id="{{ $modalId }}" @if(!$shouldAutoOpen) hidden @endif>
    <div class="report-modal-card">
        <div class="report-modal-header">
            <div>
                <span class="report-modal-kicker">Safety</span>
                <h3>Report {{ $modalContext === 'seller' ? 'Seller' : 'Product' }}</h3>
            </div>
            <button type="button" class="report-modal-close" data-report-close="{{ $modalId }}" aria-label="Close report form">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('reports.store') }}" method="POST" class="report-form">
            @csrf
            <input type="hidden" name="modal_context" value="{{ $modalContext }}">
            @if($productId)
                <input type="hidden" name="product_id" value="{{ $productId }}">
            @endif
            @if($sellerId)
                <input type="hidden" name="seller_id" value="{{ $sellerId }}">
            @endif

            @if($hasReportErrors)
                <div class="report-form-feedback report-form-feedback--error">
                    {{ $reportErrors->first() }}
                </div>
            @endif

            <label class="report-form-field" for="{{ $modalId }}-reason">
                <span>Reason</span>
                <select name="reason" id="{{ $modalId }}-reason" required>
                    <option value="">Select a reason</option>
                    <option value="spam" {{ old('reason') === 'spam' ? 'selected' : '' }}>Spam</option>
                    <option value="fake product" {{ old('reason') === 'fake product' ? 'selected' : '' }}>Fake product</option>
                    <option value="inappropriate" {{ old('reason') === 'inappropriate' ? 'selected' : '' }}>Inappropriate</option>
                    <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </label>

            <label class="report-form-field" for="{{ $modalId }}-message">
                <span>Message (optional)</span>
                <textarea name="message" id="{{ $modalId }}-message" rows="4" placeholder="Add extra details to help the admin team review this report.">{{ old('message') }}</textarea>
            </label>

            <div class="report-form-actions">
                <button type="submit" class="action-btn primary-btn">Submit Report</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById(@json($modalId));
        const openButton = document.querySelector('[data-report-open="{{ $modalId }}"]');
        const closeButtons = document.querySelectorAll('[data-report-close="{{ $modalId }}"]');

        if (!modal) {
            return;
        }

        const openModal = () => {
            modal.hidden = false;
            document.body.classList.add('report-modal-open');
        };

        const closeModal = () => {
            modal.hidden = true;
            if (![...document.querySelectorAll('.report-modal-shell')].some((shell) => !shell.hidden)) {
                document.body.classList.remove('report-modal-open');
            }
        };

        if (openButton) {
            openButton.addEventListener('click', openModal);
        }

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        if (!modal.hidden) {
            document.body.classList.add('report-modal-open');
        }
    });
</script>
