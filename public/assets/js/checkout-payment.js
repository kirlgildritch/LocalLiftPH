document.addEventListener('DOMContentLoaded', function () {
    const choices = Array.from(document.querySelectorAll('[data-payment-choice]'));
    const summary = document.querySelector('[data-payment-summary]');
    const buttonLabel = document.querySelector('[data-payment-button-label]');
    const noteTitle = document.querySelector('[data-payment-note-title]');
    const noteCopy = document.querySelector('[data-payment-note-copy]');

    const syncPaymentDisplay = function (choice) {
        if (!choice) {
            return;
        }

        document.querySelectorAll('.payment-method-card').forEach(function (card) {
            card.classList.toggle('is-selected', card.contains(choice));
        });

        if (summary) {
            summary.textContent = choice.dataset.paymentShortLabel || choice.dataset.paymentLabel || 'Payment';
        }

        if (buttonLabel) {
            buttonLabel.textContent = choice.dataset.paymentShortLabel || choice.dataset.paymentLabel || 'Payment';
        }

        if (noteTitle) {
            noteTitle.textContent = choice.dataset.paymentLabel || 'Payment method';
        }

        if (noteCopy) {
            noteCopy.textContent = choice.dataset.paymentInstructions || '';
        }
    };

    choices.forEach(function (choice) {
        choice.addEventListener('change', function () {
            syncPaymentDisplay(choice);
        });
    });

    syncPaymentDisplay(choices.find(function (choice) {
        return choice.checked;
    }));
});
