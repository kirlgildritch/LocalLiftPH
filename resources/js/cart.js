
// ========================
// cart.js
// ========================
document.addEventListener('DOMContentLoaded', function () {
    const cartLayout = document.querySelector('.cart-layout');
    const selectAll = document.getElementById('select-all-cart-items');
    const itemCheckboxes = Array.from(document.querySelectorAll('.cart-item-checkbox'));
    const subtotalEl = document.getElementById('cart-summary-subtotal');
    const shippingEl = document.getElementById('cart-summary-shipping');
    const totalEl = document.getElementById('cart-summary-total');
    const selectedInputsContainer = document.getElementById('selected-cart-items-inputs');
    const checkoutForm = document.getElementById('cart-checkout-form');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (!cartLayout || !selectAll || !itemCheckboxes.length || !subtotalEl || !shippingEl || !totalEl || !selectedInputsContainer || !checkoutForm) return;

    const storageKey = cartLayout.dataset.selectionStorageKey;
    const buyNowSelectedId = cartLayout.dataset.selectedCartItemId;
    const flashedSelectedIds = (() => { try { return JSON.parse(cartLayout.dataset.selectedCartItemIds || '[]'); } catch { return []; }})();

    const formatPeso = value => `&#8369; ${Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    const loadSavedSelection = () => { try { const raw = window.localStorage.getItem(storageKey); return raw ? JSON.parse(raw) : []; } catch { return []; } };
    const saveSelection = selectedIds => { try { window.localStorage.setItem(storageKey, JSON.stringify(selectedIds)); } catch {} };

    const applySelection = selectedIds => {
        const selectedSet = new Set(selectedIds.map(String));
        itemCheckboxes.forEach(checkbox => { checkbox.checked = selectedSet.has(String(checkbox.value)); });
    };

    const syncSummary = () => {
        let selectedTotal = 0, selectedShipping = 0, selectedCount = 0;
        selectedInputsContainer.innerHTML = '';
        itemCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('.cart-item');
            if (!row) return;
            if (checkbox.checked) {
                selectedCount += 1;
                selectedTotal += Number(row.dataset.subtotal || 0);
                selectedShipping += Number(row.dataset.shipping || 0);
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'selected_cart_items[]'; input.value = checkbox.value;
                selectedInputsContainer.appendChild(input);
            }
        });
        subtotalEl.innerHTML = formatPeso(selectedTotal);
        shippingEl.innerHTML = formatPeso(selectedShipping);
        totalEl.innerHTML = formatPeso(selectedTotal + selectedShipping);
        selectAll.checked = selectedCount === itemCheckboxes.length;
        selectAll.indeterminate = selectedCount > 0 && selectedCount < itemCheckboxes.length;
        saveSelection(Array.from(selectedInputsContainer.querySelectorAll('input[name="selected_cart_items[]"]')).map(i=>i.value));
    };

    const setQuantityLimitState = (row, quantity) => {
        const maxStock = Math.max(0, Number(row.dataset.maxStock || 0));
        const nextQuantity = Number(quantity || 0);
        const decrementButton = row.querySelector('[data-cart-quantity-button="decrement"]');
        const incrementButton = row.querySelector('[data-cart-quantity-button="increment"]');
        const note = row.querySelector('[data-cart-quantity-note]');
        if (decrementButton) decrementButton.disabled = nextQuantity <= 1;
        if (incrementButton) incrementButton.disabled = maxStock <= 0 || nextQuantity >= maxStock;
        if (!note) return;
        if (maxStock <= 0) { note.hidden = false; note.textContent = 'Out of stock.'; return; }
        if (nextQuantity >= maxStock) { note.hidden = false; note.textContent = 'Max stock reached.'; return; }
        note.hidden = true; note.textContent = '';
    };

    const updateQuantityRow = (row, quantity, maxStock = null) => {
        const nextQuantity = Number(quantity || 1);
        const unitPrice = Number(row.dataset.unitPrice || 0);
        const unitShipping = Number(row.dataset.unitShipping || 0);
        const subtotal = unitPrice * nextQuantity;
        const shipping = unitShipping * nextQuantity;
        const quantityDisplay = row.querySelector('[data-cart-quantity-display]');
        const subtotalDisplay = row.querySelector('[data-cart-item-subtotal]');
        const decrementInput = row.querySelector('[data-cart-next-quantity="decrement"]');
        const incrementInput = row.querySelector('[data-cart-next-quantity="increment"]');
        if (maxStock !== null) row.dataset.maxStock = String(Math.max(0, Number(maxStock || 0)));
        row.dataset.subtotal = String(subtotal);
        row.dataset.shipping = String(shipping);
        if (quantityDisplay) quantityDisplay.value = nextQuantity;
        if (subtotalDisplay) subtotalDisplay.innerHTML = formatPeso(subtotal);
        if (decrementInput) decrementInput.value = String(Math.max(1, nextQuantity - 1));
        if (incrementInput) incrementInput.value = String(nextQuantity + 1);
        setQuantityLimitState(row, nextQuantity);
    };

    selectAll.addEventListener('change', () => { itemCheckboxes.forEach(c => c.checked = selectAll.checked); syncSummary(); });
    itemCheckboxes.forEach(c => c.addEventListener('change', syncSummary));

    cartLayout.addEventListener('submit', async event => {
        const form = event.target.closest('[data-cart-quantity-form]');
        if (!form) return;
        event.preventDefault();
        const row = form.closest('.cart-item');
        if (!row || row.dataset.quantityPending === '1') return;
        const nextQuantityInput = form.querySelector('input[name="quantity"]');
        const requestedQuantity = Number(nextQuantityInput?.value || 0);
        const maxStock = Math.max(0, Number(row.dataset.maxStock || 0));
        if (maxStock > 0 && requestedQuantity > maxStock) { setQuantityLimitState(row, maxStock); alert('Max stock reached.'); return; }
        row.dataset.quantityPending = '1';
        row.querySelectorAll('[data-cart-quantity-form] button[type="submit"]').forEach(b => b.disabled = true);
        try {
            const response = await fetch(form.action, { method: 'POST', headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest', ...(csrfToken?{'X-CSRF-TOKEN':csrfToken}:{}) }, body: new FormData(form), credentials: 'same-origin' });
            const payload = await response.json().catch(()=>({}));
            if(!response.ok) throw new Error(payload.message||'Unable to update the cart quantity.');
            updateQuantityRow(row, payload.cart_item?.quantity, payload.cart_item?.max_stock ?? null);
            syncSummary();
        } catch(error){ setQuantityLimitState(row, row.querySelector('[data-cart-quantity-display]')?.value || 1); alert(error.message||'Unable to update the cart quantity.'); }
        finally{ delete row.dataset.quantityPending; row.querySelectorAll('[data-cart-quantity-form] button[type="submit"]').forEach(b=>b.disabled=false); setQuantityLimitState(row, row.querySelector('[data-cart-quantity-display]')?.value || 1); }
    });

    checkoutForm.addEventListener('submit', event => { if(!selectedInputsContainer.querySelector('input[name="selected_cart_items[]"]')){ event.preventDefault(); alert('Select at least one cart item before checkout.'); } });

    if(flashedSelectedIds.length){ applySelection(flashedSelectedIds); } else if(buyNowSelectedId){ applySelection([buyNowSelectedId]); } else { const savedSelection=loadSavedSelection(); if(savedSelection.length) applySelection(savedSelection); }
    document.querySelectorAll('.cart-item').forEach(row=>updateQuantityRow(row, row.querySelector('[data-cart-quantity-display]')?.value||1));
    syncSummary();
});