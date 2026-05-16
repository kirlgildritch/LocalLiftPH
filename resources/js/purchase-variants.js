// ========================
// purchase-variants.js
// ========================
const initPurchaseVariants = function () {
    const quantityBox = document.querySelector('[data-purchase-quantity-box]');
    if (!quantityBox) {
        return;
    }

    let maxStock = Math.max(0, Number(quantityBox.dataset.maxStock || 0));
    let minQuantity = maxStock > 0 ? 1 : 0;
    let unitPrice = Number(quantityBox.dataset.unitPrice || 0);
    const hasVariants = quantityBox.dataset.hasVariants === 'true';
    const display = quantityBox.querySelector('[data-quantity-display]');
    const decrementButton = quantityBox.querySelector('[data-quantity-decrement]');
    const incrementButton = quantityBox.querySelector('[data-quantity-increment]');
    const note = quantityBox.querySelector('[data-quantity-note]');
    const totalDisplay = document.querySelector('[data-purchase-total]');
    const quantityInputs = Array.from(document.querySelectorAll('[data-purchase-quantity]'));
    const variantInputs = Array.from(document.querySelectorAll('[data-purchase-variant-input]'));
    const submitButtons = Array.from(document.querySelectorAll('[data-purchase-submit]'));
    const variantButtons = Array.from(document.querySelectorAll('[data-variant-choice]'));
    const variantNote = document.querySelector('[data-variant-note]');
    const productDisplayPrice = document.querySelector('[data-product-display-price]');
    const variantModal = document.querySelector('[data-variant-modal]');
    const openVariantModalButtons = Array.from(document.querySelectorAll('[data-open-variant-modal]'));
    const closeVariantModalButtons = Array.from(document.querySelectorAll('[data-close-variant-modal]'));

    if (!display || !decrementButton || !incrementButton || !note || !totalDisplay || !quantityInputs.length) {
        return;
    }

    const formatPeso = value => '\u20B1 ' + Number(value).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const clampQuantity = value => {
        if (maxStock <= 0) {
            return 0;
        }

        return Math.min(maxStock, Math.max(minQuantity, value));
    };

    const updateQuantity = nextQuantity => {
        const quantity = clampQuantity(nextQuantity);
        display.value = quantity;
        totalDisplay.textContent = formatPeso(unitPrice * quantity);
        quantityInputs.forEach(input => input.value = String(quantity));

        decrementButton.disabled = quantity <= minQuantity;
        incrementButton.disabled = maxStock <= 0 || quantity >= maxStock;

        if (maxStock <= 0) {
            note.hidden = false;
            note.textContent = hasVariants ? 'Choose an available variant.' : 'Out of stock.';
            return;
        }
        if (quantity >= maxStock) {
            note.hidden = false;
            note.textContent = 'Max stock reached.';
            return;
        }
        note.hidden = true;
        note.textContent = '';
    };

    const setPurchasingEnabled = enabled => submitButtons.forEach(button => button.disabled = !enabled);

    const openVariantModal = () => {
        if (!variantModal) return;
        variantModal.hidden = false;
        variantModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    };

    const closeVariantModal = () => {
        if (!variantModal) return;
        variantModal.hidden = true;
        variantModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    };

    const selectVariant = button => {
        const selectedVariantId = button.dataset.variantId || '';
        variantButtons.forEach(v => v.classList.toggle('is-selected', v.dataset.variantId === selectedVariantId));

        maxStock = Math.max(0, Number(button.dataset.variantStock || 0));
        minQuantity = maxStock > 0 ? 1 : 0;
        unitPrice = Number(button.dataset.variantPrice || 0);
        variantInputs.forEach(input => input.value = selectedVariantId);

        if (variantNote) { variantNote.hidden = true; variantNote.textContent = ''; }
        if (productDisplayPrice) {
            productDisplayPrice.textContent = formatPeso(unitPrice);
        }

        setPurchasingEnabled(maxStock > 0);
        updateQuantity(maxStock > 0 ? 1 : 0);
        if (button.closest('[data-variant-modal]')) closeVariantModal();
    };

    decrementButton.addEventListener('click', () => updateQuantity(Number(display.value || minQuantity) - 1));
    incrementButton.addEventListener('click', () => updateQuantity(Number(display.value || minQuantity) + 1));
    variantButtons.forEach(button => button.addEventListener('click', () => { if (!button.disabled) selectVariant(button); }));
    openVariantModalButtons.forEach(button => button.addEventListener('click', openVariantModal));
    closeVariantModalButtons.forEach(button => button.addEventListener('click', closeVariantModal));
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && variantModal && !variantModal.hidden) closeVariantModal(); });

    if (hasVariants) setPurchasingEnabled(false);
    updateQuantity(Number(display.value || minQuantity));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPurchaseVariants, { once: true });
} else {
    initPurchaseVariants();
}
