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
        @endphp


        <div
            class="cart-layout"
            data-selected-cart-item-id="{{ $selectedCartItemId ?? '' }}"
            data-selected-cart-item-ids='@json($selectedCartItemIds)'
            data-selection-storage-key="locallift-cart-selection-{{ auth()->id() }}"
        >
            <div class="cart-main">
                <div class="cart-toolbar panel">
                    <div class="toolbar-copy">
                        <span class="toolbar-label">Selection</span>
                        <h2>Shopping Cart</h2>
                    </div>

                    <div class="toolbar-note">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>You are eligible for free shipping.</span>
                    </div>
                </div>

                <div class="cart-list panel">
                    <div class="select-all-row">
                        <label>
                            <input type="checkbox" id="select-all-cart-items" {{ $hasSelectedCartItem || $hasSelectedCartItems ? '' : 'checked' }}>
                            <span>Select All</span>
                        </label>
                    </div>

                    <div class="cart-table-head">
                        <div>Select</div>
                        <div>Product</div>
                        <div>Price</div>
                        <div>Quantity</div>
                        <div>Subtotal</div>
                    </div>

                    @forelse($cartItems as $item)
                        @php
                            $subtotal = $item->product->price * $item->quantity;
                            $total += $subtotal;
                            $isChecked = !$hasSelectedCartItem && !$hasSelectedCartItems
                                || (int) $selectedCartItemId === (int) $item->id
                                || $selectedCartItemIds->contains((int) $item->id);
                            if ($isChecked) {
                                $selectedSubtotal += $subtotal;
                            }
                        @endphp

                        <article class="cart-item" data-cart-item-id="{{ $item->id }}" data-subtotal="{{ $subtotal }}">
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
                                    <span class="product-badge">Cart Item</span>
                                    <h3>{{ $item->product->name }}</h3>
                                    <p>{{ $item->product->shop_name ?? 'LocalLift Shop' }}</p>
                                </div>
                            </div>

                            <div class="item-price">P{{ number_format($item->product->price, 2) }}</div>

                            <div class="item-quantity">
                                <div class="qty-box">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                        <button type="submit">-</button>
                                    </form>

                                    <input type="text" value="{{ $item->quantity }}" readonly>

                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                        <button type="submit">+</button>
                                    </form>
                                </div>
                            </div>

                            <div class="item-subtotal">
                                <strong>P{{ number_format($subtotal, 2) }}</strong>

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
                    <span class="section-kicker">Summary</span>
                    <h3>Cart Summary</h3>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <strong id="cart-summary-subtotal">P{{ number_format($selectedSubtotal, 2) }}</strong>
                    </div>

                    <div class="summary-line">
                        <span>Shipping</span>
                        <strong>P0.00</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <strong id="cart-summary-total">P{{ number_format($selectedSubtotal, 2) }}</strong>
                    </div>

                    <form action="{{ route('checkout.index') }}" method="GET" id="cart-checkout-form">
                        <div id="selected-cart-items-inputs"></div>
                        <button type="submit" class="action-btn primary-btn full-btn">Checkout</button>
                    </form>

                    <div class="coupon-box">
                        <input type="text" placeholder="Enter coupon code">
                        <button type="button">Apply</button>
                    </div>
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
    const totalEl = document.getElementById('cart-summary-total');
    const selectedInputsContainer = document.getElementById('selected-cart-items-inputs');
    const checkoutForm = document.getElementById('cart-checkout-form');

    if (!cartLayout || !selectAll || !itemCheckboxes.length || !subtotalEl || !totalEl || !selectedInputsContainer || !checkoutForm) {
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

    const formatPeso = (value) => `P${Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

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
        } catch (error) {
            // Ignore storage failures and keep the cart usable.
        }
    };

    const applySelection = (selectedIds) => {
        const selectedSet = new Set(selectedIds.map(String));

        itemCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectedSet.has(String(checkbox.value));
        });
    };

    const syncSummary = () => {
        let selectedTotal = 0;
        let selectedCount = 0;
        const selectedIds = [];

        selectedInputsContainer.innerHTML = '';

        itemCheckboxes.forEach((checkbox) => {
            const row = checkbox.closest('.cart-item');
            if (!row) return;

            if (checkbox.checked) {
                selectedCount += 1;
                selectedTotal += Number(row.dataset.subtotal || 0);
                selectedIds.push(String(checkbox.value));

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_cart_items[]';
                input.value = checkbox.value;
                selectedInputsContainer.appendChild(input);
            }
        });

        subtotalEl.textContent = formatPeso(selectedTotal);
        totalEl.textContent = formatPeso(selectedTotal);
        selectAll.checked = selectedCount === itemCheckboxes.length;
        selectAll.indeterminate = selectedCount > 0 && selectedCount < itemCheckboxes.length;
        saveSelection(selectedIds);
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

    syncSummary();
});
</script>
@endsection
