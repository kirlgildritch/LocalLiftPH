

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">

<section class="cart-page">
    <div class="container">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <a href="{{ route('products.index') }}">Products</a>
            <span>&gt;</span>
            <span>Cart</span>
        </div>

        @php
            $total = 0;
            $selectedCartItemId = session('selected_cart_item_id');
            $selectedCartItemIds = collect(session('selected_cart_item_ids', []))
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values();
            $hasSelectedCartItem = filled($selectedCartItemId);
            $hasSelectedCartItems = $selectedCartItemIds->isNotEmpty();
            $selectedSubtotal = 0;
            $selectedShipping = 0;
        @endphp

        <div
            class="cart-layout"
            data-selected-cart-item-id="{{ $selectedCartItemId ?? '' }}"
            data-selected-cart-item-ids='@json($selectedCartItemIds)'
            data-selection-storage-key="locallift-cart-selection-{{ auth()->id() }}"
        >
            <div class="cart-main">
               

                <div class="cart-list panel">
                    <div class="select-all-row">
                        <label>
                            <input type="checkbox" id="select-all-cart-items">
                            <span>Select All</span>
                        </label>
                    </div>

                    <div class="cart-table-head">
                        <div>Product</div>
                        <div>Price</div>
                        <div>Quantity</div>
                        <div>Subtotal</div>
                    </div>

                    @forelse($cartItems as $item)
                        @php
                            $subtotal = $item->product->price * $item->quantity;
                            $shipping = ($item->product->shipping_fee ?? 0) * $item->quantity;
                            $total += $subtotal;
                            $isChecked = (int) $selectedCartItemId === (int) $item->id
                                || $selectedCartItemIds->contains((int) $item->id);
                            if ($isChecked) {
                                $selectedSubtotal += $subtotal;
                                $selectedShipping += $shipping;
                            }
                        @endphp

                        <article
                            class="cart-item"
                            data-cart-item-id="{{ $item->id }}"
                            data-max-stock="{{ max(0, (int) ($item->product->stock ?? 0)) }}"
                            data-subtotal="{{ $subtotal }}"
                            data-shipping="{{ $shipping }}"
                            data-unit-price="{{ (float) $item->product->price }}"
                            data-unit-shipping="{{ (float) ($item->product->shipping_fee ?? 0) }}"
                        >
                            <div class="item-select">
                                <input
                                    type="checkbox"
                                    class="cart-item-checkbox"
                                    value="{{ $item->id }}"
                                    {{ $isChecked ? 'checked' : '' }}
                                >
                            </div>

                            <div class="item-product">
                                <div class="product-image">
                                    <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}" alt="{{ $item->product->name }}">
                                </div>

                                <div class="product-copy">
                                    <h3>{{ $item->product->name }}</h3>
                                    <p>{{ $item->product->user?->sellerProfile?->store_name ?? 'LocalLift Shop' }}</p>
                                </div>
                            </div>

                            <div class="item-price">&#8369; {{ number_format($item->product->price, 2) }}</div>

                            <div class="item-quantity">
                                <div class="qty-box">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" data-cart-quantity-form>
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}" data-cart-next-quantity="decrement">
                                        <button type="submit" data-cart-quantity-button="decrement">-</button>
                                    </form>

                                    <input type="text" value="{{ $item->quantity }}" readonly data-cart-quantity-display>

                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" data-cart-quantity-form>
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}" data-cart-next-quantity="increment">
                                        <button type="submit" data-cart-quantity-button="increment">+</button>
                                    </form>
                                </div>
                                <small class="quantity-note" data-cart-quantity-note hidden></small>
                            </div>

                            <div class="item-subtotal">
                                <strong data-cart-item-subtotal>&#8369; {{ number_format($subtotal, 2) }}</strong>

                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            <span class="section-kicker">Empty Cart</span>
                            <h3>No products in your cart yet</h3>
                            <p>Browse the product catalog and add items here when you are ready to check out.</p>
                            <a href="{{ route('products.index') }}" class="action-btn primary-btn">Explore Products</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="cart-sidebar">
                <div class="cart-summary panel">
                    <div class="summary-title">
                        <h3>Cart Summary</h3>
                    </div>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <strong id="cart-summary-subtotal">&#8369; {{ number_format($selectedSubtotal, 2) }}</strong>
                    </div>

                    <div class="summary-line">
                        <span>Shipping</span>
                        <strong id="cart-summary-shipping">&#8369; {{ number_format($selectedShipping, 2) }}</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <strong id="cart-summary-total">&#8369; {{ number_format($selectedSubtotal + $selectedShipping, 2) }}</strong>
                    </div>

                    <form action="{{ route('checkout.index') }}" method="GET" id="cart-checkout-form" data-enable-loading>
                        <div id="selected-cart-items-inputs"></div>
                        <button type="submit" class="action-btn primary-btn full-btn" data-enable-loading data-loading-text="Loading Checkout...">Checkout</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>

<script>
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

    if (!cartLayout || !selectAll || !itemCheckboxes.length || !subtotalEl || !shippingEl || !totalEl || !selectedInputsContainer || !checkoutForm) {
        return;
    }

    const storageKey = cartLayout.dataset.selectionStorageKey;
    const buyNowSelectedId = cartLayout.dataset.selectedCartItemId;
    const flashedSelectedIds = (() => {
        try {
            return JSON.parse(cartLayout.dataset.selectedCartItemIds || '[]');
        } catch (error) {
            return [];
        }
    })();

    const formatPeso = (value) => `&#8369; ${Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    const loadSavedSelection = () => {
        try {
            const raw = window.localStorage.getItem(storageKey);
            return raw ? JSON.parse(raw) : [];
        } catch (error) {
            return [];
        }
    };

    const saveSelection = (selectedIds) => {
        try {
            window.localStorage.setItem(storageKey, JSON.stringify(selectedIds));
        } catch (error) {}
    };

    const applySelection = (selectedIds) => {
        const selectedSet = new Set(selectedIds.map(String));

        itemCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectedSet.has(String(checkbox.value));
        });
    };

    const syncSummary = () => {
        let selectedTotal = 0;
        let selectedShipping = 0;
        let selectedCount = 0;
        const selectedIds = [];

        selectedInputsContainer.innerHTML = '';

        itemCheckboxes.forEach((checkbox) => {
            const row = checkbox.closest('.cart-item');
            if (!row) return;

            if (checkbox.checked) {
                selectedCount += 1;
                selectedTotal += Number(row.dataset.subtotal || 0);
                selectedShipping += Number(row.dataset.shipping || 0);
                selectedIds.push(String(checkbox.value));

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_cart_items[]';
                input.value = checkbox.value;
                selectedInputsContainer.appendChild(input);
            }
        });

        subtotalEl.innerHTML = formatPeso(selectedTotal);
        shippingEl.innerHTML = formatPeso(selectedShipping);
        totalEl.innerHTML = formatPeso(selectedTotal + selectedShipping);
        selectAll.checked = selectedCount === itemCheckboxes.length;
        selectAll.indeterminate = selectedCount > 0 && selectedCount < itemCheckboxes.length;
        saveSelection(selectedIds);
    };

    const setQuantityLimitState = (row, quantity) => {
        const maxStock = Math.max(0, Number(row.dataset.maxStock || 0));
        const nextQuantity = Number(quantity || 0);
        const decrementButton = row.querySelector('[data-cart-quantity-button="decrement"]');
        const incrementButton = row.querySelector('[data-cart-quantity-button="increment"]');
        const note = row.querySelector('[data-cart-quantity-note]');

        if (decrementButton) {
            decrementButton.disabled = nextQuantity <= 1;
        }

        if (incrementButton) {
            incrementButton.disabled = maxStock <= 0 || nextQuantity >= maxStock;
        }

        if (!note) {
            return;
        }

        if (maxStock <= 0) {
            note.hidden = false;
            note.textContent = 'Out of stock.';
            return;
        }

        if (nextQuantity >= maxStock) {
            note.hidden = false;
            note.textContent = 'Max stock reached.';
            return;
        }

        note.hidden = true;
        note.textContent = '';
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

        if (maxStock !== null) {
            row.dataset.maxStock = String(Math.max(0, Number(maxStock || 0)));
        }

        row.dataset.subtotal = String(subtotal);
        row.dataset.shipping = String(shipping);

        if (quantityDisplay) {
            quantityDisplay.value = nextQuantity;
        }

        if (subtotalDisplay) {
            subtotalDisplay.innerHTML = formatPeso(subtotal);
        }

        if (decrementInput) {
            decrementInput.value = String(Math.max(1, nextQuantity - 1));
        }

        if (incrementInput) {
            incrementInput.value = String(nextQuantity + 1);
        }

        setQuantityLimitState(row, nextQuantity);
    };

    const setQuantityButtonsState = (row, disabled) => {
        row.querySelectorAll('[data-cart-quantity-form] button[type="submit"]').forEach((button) => {
            button.disabled = disabled;
        });
    };

    selectAll.addEventListener('change', function () {
        itemCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
        syncSummary();
    });

    itemCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', syncSummary);
    });

    cartLayout.addEventListener('submit', async function (event) {
        const form = event.target.closest('[data-cart-quantity-form]');

        if (!form) {
            return;
        }

        event.preventDefault();

        const row = form.closest('.cart-item');

        if (!row || row.dataset.quantityPending === '1') {
            return;
        }

        const nextQuantityInput = form.querySelector('input[name="quantity"]');
        const requestedQuantity = Number(nextQuantityInput?.value || 0);
        const maxStock = Math.max(0, Number(row.dataset.maxStock || 0));

        if (maxStock > 0 && requestedQuantity > maxStock) {
            setQuantityLimitState(row, maxStock);
            window.alert('Max stock reached.');
            return;
        }

        row.dataset.quantityPending = '1';
        setQuantityButtonsState(row, true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: new FormData(form),
                credentials: 'same-origin',
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Unable to update the cart quantity.');
            }

            updateQuantityRow(row, payload.cart_item?.quantity, payload.cart_item?.max_stock ?? null);
            syncSummary();
        } catch (error) {
            setQuantityLimitState(row, row.querySelector('[data-cart-quantity-display]')?.value || 1);
            window.alert(error.message || 'Unable to update the cart quantity.');
        } finally {
            delete row.dataset.quantityPending;
            setQuantityButtonsState(row, false);
            setQuantityLimitState(row, row.querySelector('[data-cart-quantity-display]')?.value || 1);
        }
    });

    checkoutForm.addEventListener('submit', function (event) {
        if (!selectedInputsContainer.querySelector('input[name="selected_cart_items[]"]')) {
            event.preventDefault();
            window.alert('Select at least one cart item before checkout.');
        }
    });

    if (flashedSelectedIds.length) {
        applySelection(flashedSelectedIds);
    } else if (buyNowSelectedId) {
        applySelection([buyNowSelectedId]);
    } else {
        const savedSelection = loadSavedSelection();
        if (savedSelection.length) {
            applySelection(savedSelection);
        }
    }

    document.querySelectorAll('.cart-item').forEach((row) => {
        updateQuantityRow(row, row.querySelector('[data-cart-quantity-display]')?.value || 1);
    });

    syncSummary();
});
</script>
@endsection
