@extends('layouts.admin')

@section('title', 'Orders')
@section('eyebrow', 'Fulfillment')
@section('page-title', 'Orders')
@section('page-description', 'Orders table and detail modal updated from the provided document.')

@section('content')
    @php
        $orders = [
            ['id' => '#10025', 'date' => 'Apr 19, 2024', 'buyer' => 'crafty_emma', 'buyer_name' => 'Crafty Emma', 'buyer_avatar' => 'CE', 'buyer_class' => 'gold', 'seller' => 'woodwonder_jake', 'seller_name' => 'Woodwonder_Jake', 'seller_avatar' => 'WJ', 'seller_class' => 'slate', 'total' => '$45.00', 'status' => 'Shipped', 'status_class' => 'success', 'thumb' => 'wood'],
            ['id' => '#10024', 'date' => 'Apr 17, 2024', 'buyer' => 'beads_girl', 'buyer_name' => 'Beads Girl', 'buyer_avatar' => 'BG', 'buyer_class' => 'rose', 'seller' => 'handmadeby_sara', 'seller_name' => 'handmadeby_sara', 'seller_avatar' => 'HS', 'seller_class' => 'teal', 'total' => '$18.00', 'status' => 'Pending', 'status_class' => 'pending', 'thumb' => 'bear'],
            ['id' => '#10023', 'date' => 'Apr 15, 2024', 'buyer' => 'artisan_alex', 'buyer_name' => 'Artisan Alex', 'buyer_avatar' => 'AA', 'buyer_class' => 'olive', 'seller' => 'clayandcolor', 'seller_name' => 'clayandcolor', 'seller_avatar' => 'CC', 'seller_class' => 'slate', 'total' => '$60.00', 'status' => 'Delivered', 'status_class' => 'delivered', 'thumb' => 'mug'],
            ['id' => '#10022', 'date' => 'Apr 12, 2024', 'buyer' => 'student_sam', 'buyer_name' => 'Student Sam', 'buyer_avatar' => 'SS', 'buyer_class' => 'gold', 'seller' => 'crafty_jenny', 'seller_name' => 'crafty_jenny', 'seller_avatar' => 'CJ', 'seller_class' => 'slate', 'total' => '$25.00', 'status' => 'Cancelled', 'status_class' => 'cancelled', 'thumb' => 'earrings'],
            ['id' => '#10021', 'date' => 'Apr 10, 2024', 'buyer' => 'macrame_mia', 'buyer_name' => 'Macrame Mia', 'buyer_avatar' => 'MM', 'buyer_class' => 'teal', 'seller' => 'beads_and_knots', 'seller_name' => 'beads_and_knots', 'seller_avatar' => 'BK', 'seller_class' => 'rose', 'total' => '$50.00', 'status' => 'Shipped', 'status_class' => 'success', 'thumb' => 'macrame'],
            ['id' => '#10020', 'date' => 'Apr 8, 2024', 'buyer' => 'potterypete', 'buyer_name' => 'Pottery Pete', 'buyer_avatar' => 'PP', 'buyer_class' => 'olive', 'seller' => 'clayandcolor', 'seller_name' => 'clayandcolor', 'seller_avatar' => 'CC', 'seller_class' => 'slate', 'total' => '$75.00', 'status' => 'Delivered', 'status_class' => 'delivered', 'thumb' => 'mug'],
        ];
    @endphp

    <div class="page-stack">
        <div class="filter-bar">
            <div class="chip is-active">All</div>
            <div class="inline-select">Last 30 Days <i class="fa-solid fa-chevron-down"></i></div>
            <div class="inline-select"><i class="fa-solid fa-xmark"></i></div>
            <div class="inline-select">All Statuses <i class="fa-solid fa-chevron-down"></i></div>
            <div class="inline-select">All Sellers <i class="fa-solid fa-chevron-down"></i></div>
            <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search orders..." /></div>
        </div>

        <article class="table-card">
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <div class="order-id-cell">
                                        <div class="mini-thumb mini-thumb--{{ $order['thumb'] }}"></div>
                                        <span class="order-id-link">{{ $order['id'] }}</span>
                                    </div>
                                </td>
                                <td>{{ $order['date'] }}</td>
                                <td>
                                    <div class="muted-row">
                                        <div class="avatar-photo avatar-photo--{{ $order['buyer_class'] }}" style="width: 2.2rem; height: 2.2rem; font-size: 0.78rem;">{{ $order['buyer_avatar'] }}</div>
                                        <span>{{ $order['buyer'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="muted-row">
                                        <div class="avatar-photo avatar-photo--{{ $order['seller_class'] }}" style="width: 2.2rem; height: 2.2rem; font-size: 0.78rem;">{{ $order['seller_avatar'] }}</div>
                                        <span>{{ $order['seller'] }}</span>
                                    </div>
                                </td>
                                <td class="product-title">{{ $order['total'] }}</td>
                                <td><span class="status-pill status-pill--{{ $order['status_class'] }}">{{ $order['status'] }}</span></td>
                                <td><button class="action-button action-button--primary" type="button" data-order-view="{{ $order['id'] }}"><i class="fa-solid fa-magnifying-glass"></i> View Details</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-bar">
                <button class="pagination-button">Previous</button>
                <button class="pagination-button is-active">1</button>
                <button class="pagination-button">Next</button>
            </div>
        </article>
    </div>
@endsection

@push('modals')
    <div class="modal-shell" id="order-detail-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title"><span id="order-modal-id"></span> | Order Details</h3>
                <button class="modal-close" type="button" data-close-modal="order-detail-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="order-detail-grid">
                    <div>
                        <div class="order-header">
                            <div class="mini-thumb" id="order-modal-thumb"></div>
                            <div class="order-header__meta">
                                <div class="seller-name" id="order-modal-date"></div>
                                <div class="muted-row"><div class="avatar-photo avatar-photo--slate" style="width: 2rem; height: 2rem; font-size: 0.72rem;" id="order-modal-seller-avatar"></div> <span id="order-modal-seller-handle"></span></div>
                            </div>
                        </div>

                        <div class="spacer-top">
                            <h4 class="section-title">Product Information</h4>
                            <div class="order-product-line">
                                <div class="muted-row">
                                    <div class="mini-thumb" id="order-modal-product-thumb"></div>
                                    <strong id="order-modal-product-name"></strong>
                                </div>
                                <strong id="order-modal-total"></strong>
                            </div>
                            <div class="summary-rows">
                                <div class="summary-row"><span>Subtotal:</span><strong id="order-modal-subtotal"></strong></div>
                                <div class="summary-row"><span>Shipping:</span><strong id="order-modal-shipping"></strong></div>
                                <div class="summary-row total"><span>Total:</span><strong id="order-modal-grand-total"></strong></div>
                            </div>
                        </div>

                        <div class="info-split spacer-top">
                            <div>
                                <h4 class="section-title">Buyer Information</h4>
                                <div class="contact-card spacer-top">
                                    <div class="contact-card__header">
                                        <div class="avatar-photo" id="order-modal-buyer-avatar"></div>
                                        <div class="seller-name" id="order-modal-buyer-name"></div>
                                    </div>
                                    <div class="contact-lines">
                                        +1 123-456-7890<br>
                                        Emma R.<br>
                                        123 Handmade Ln<br>
                                        Austin, TX 78702<br>
                                        United States
                                    </div>
                                </div>

                                <div class="spacer-top">
                                    <h4 class="section-title">Comments</h4>
                                    <textarea class="field-textarea" placeholder="Add a note..."></textarea>
                                </div>
                            </div>

                            <div>
                                <h4 class="section-title">Seller Information</h4>
                                <div class="contact-card spacer-top">
                                    <div class="contact-card__header">
                                        <div class="avatar-photo" id="order-modal-seller-avatar-large"></div>
                                        <div class="seller-name" id="order-modal-seller-name"></div>
                                    </div>
                                    <div class="contact-lines">
                                        +1 987-654-3210<br>
                                        jake@woodycrafts.com
                                    </div>
                                </div>

                                <div class="spacer-top">
                                    <h4 class="section-title">Shipping Information</h4>
                                    <div class="contact-lines spacer-top">
                                        USPS. Priority Mail<br>
                                        Tracking: 9405 5056 9530 000 1234 56
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-badge-col">
                        <button class="status-button status-button--shipped" type="button">Shipped</button>
                        <button class="status-button status-button--delivered" type="button">Delivered</button>
                        <button class="status-button status-button--cancelled" type="button">Cancelled</button>
                        <button class="action-button action-button--primary" type="button">Update Status</button>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="order-detail-modal">Close</button>
                <div class="footer-actions">
                    <button class="action-button action-button--primary" type="button">Send</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
            const orders = @json($orders);
            const orderMap = Object.fromEntries(orders.map((order) => [order.id, order]));

            function openModal(id) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.hidden = false;
                document.body.classList.add('is-modal-open');
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.hidden = true;
                if (![...document.querySelectorAll('.modal-shell')].some((item) => !item.hidden)) {
                    document.body.classList.remove('is-modal-open');
                }
            }

            document.querySelectorAll('[data-close-modal]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.dataset.closeModal));
            });

            document.querySelectorAll('.modal-shell').forEach((shell) => {
                shell.addEventListener('click', (event) => {
                    if (event.target === shell) closeModal(shell.id);
                });
            });

            document.querySelectorAll('[data-order-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    const order = orderMap[button.dataset.orderView];
                    if (!order) return;
                    document.getElementById('order-modal-id').textContent = order.id;
                    document.getElementById('order-modal-date').textContent = order.date;
                    document.getElementById('order-modal-seller-handle').textContent = order.seller;
                    document.getElementById('order-modal-total').textContent = order.total;
                    document.getElementById('order-modal-subtotal').textContent = order.total;
                    document.getElementById('order-modal-shipping').textContent = '$5.00';
                    document.getElementById('order-modal-grand-total').textContent = order.total;
                    document.getElementById('order-modal-buyer-name').textContent = `${order.buyer_name} (${order.buyer})`;
                    document.getElementById('order-modal-seller-name').textContent = order.seller_name;
                    document.getElementById('order-modal-product-name').textContent = order.thumb === 'wood' ? 'Handmade Wood Coasters' : 'Marketplace Order Item';

                    const thumb = `mini-thumb mini-thumb--${order.thumb}`;
                    document.getElementById('order-modal-thumb').className = thumb;
                    document.getElementById('order-modal-product-thumb').className = thumb;

                    const sellerAvatarSmall = document.getElementById('order-modal-seller-avatar');
                    sellerAvatarSmall.className = `avatar-photo avatar-photo--${order.seller_class}`;
                    sellerAvatarSmall.textContent = order.seller_avatar;

                    const sellerAvatarLarge = document.getElementById('order-modal-seller-avatar-large');
                    sellerAvatarLarge.className = `avatar-photo avatar-photo--${order.seller_class}`;
                    sellerAvatarLarge.textContent = order.seller_avatar;

                    const buyerAvatar = document.getElementById('order-modal-buyer-avatar');
                    buyerAvatar.className = `avatar-photo avatar-photo--${order.buyer_class}`;
                    buyerAvatar.textContent = order.buyer_avatar;

                    openModal('order-detail-modal');
                });
            });
        })();
    </script>
@endpush
