@extends('layouts.admin')

@section('title', 'Product Approvals')
@section('eyebrow', 'Moderation')
@section('page-title', 'Product Approvals')
@section('page-description', 'Product approval screen rebuilt from the document, including view-details and seller-profile modals.')

@section('content')
    @php
        $products = [
            [
                'id' => 'earrings',
                'name' => 'Handcrafted Clay Earrings',
                'seller' => 'crafty_jenny',
                'seller_name' => 'Jenny M.',
                'category' => 'Jewelry',
                'price' => '$15.00',
                'materials' => 'Polymer clay, gold-plated brass hooks, jump rings',
                'dimensions' => '1.5" length, 0.6" width',
                'weight' => '8 grams',
                'thumb' => 'earrings',
                'avatar' => 'CJ',
                'seller_status' => 'Pending',
            ],
            [
                'id' => 'bear',
                'name' => 'Knitted Mini Plush Bear',
                'seller' => 'artisan_alex',
                'seller_name' => 'Alex R.',
                'category' => 'Toys',
                'price' => '$20.00',
                'materials' => 'Merino wool, polyfill stuffing, safety eyes',
                'dimensions' => '5" height, 3" width',
                'weight' => '120 grams',
                'thumb' => 'bear',
                'avatar' => 'AA',
                'seller_status' => 'Pending',
            ],
            [
                'id' => 'macrame',
                'name' => 'Boho Macrame Wall Hanging',
                'seller' => 'macrame_mia',
                'seller_name' => 'Mia L.',
                'category' => 'Home Decor',
                'price' => '$30.00',
                'materials' => 'Natural cotton rope, driftwood dowel',
                'dimensions' => '18" width, 36" length',
                'weight' => '350 grams',
                'thumb' => 'macrame',
                'avatar' => 'MM',
                'seller_status' => 'Pending',
            ],
            [
                'id' => 'mug',
                'name' => 'Hand-Painted Pottery Mug',
                'seller' => 'clayandcolor',
                'seller_name' => 'Dana K.',
                'category' => 'Home Decor',
                'price' => '$25.00',
                'materials' => 'Stoneware clay, food-safe glazes, lead-free paint',
                'dimensions' => '3.5" height, 3" diameter',
                'weight' => '310 grams',
                'thumb' => 'mug',
                'avatar' => 'CC',
                'seller_status' => 'Pending',
            ],
        ];
    @endphp

    <div class="page-stack">
        <div class="table-card__header" style="padding: 0; border: 0;">
            <div>
                <h3 class="section-title">Pending Products</h3>
                <p class="sub-line">12 Pending Products</p>
            </div>
            <div class="toolbar-row">
                <div class="chip is-active">All</div>
                <div class="chip">Jewelry</div>
                <div class="chip"><i class="fa-solid fa-flask"></i> Toys</div>
                <div class="chip">Home Decor</div>
                <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search products..." /></div>
            </div>
        </div>

        <article class="table-card">
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell"><span class="checkbox"></span></th>
                            <th>Product</th>
                            <th>Seller</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="checkbox-cell"><span class="checkbox"></span></td>
                                <td>
                                    <div class="product-cell">
                                        <div class="mini-thumb mini-thumb--{{ $product['thumb'] }}"></div>
                                        <div class="product-cell__text">
                                            <div class="product-title">{{ $product['name'] }}</div>
                                            <div class="sub-line"><i class="fa-solid fa-user"></i> {{ $product['seller'] }}</div>
                                            <div class="sub-line">Category: {{ $product['category'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-cell__text">
                                        <div>{{ $product['seller'] }}</div>
                                        <div class="product-title">{{ $product['price'] }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <div class="table-actions__primary">
                                            <button class="action-button action-button--success" type="button">Approve</button>
                                            <button class="action-button action-button--danger" type="button">Reject</button>
                                            <button class="action-button action-button--primary" type="button" data-product-view="{{ $product['id'] }}">
                                                <i class="fa-solid fa-magnifying-glass"></i> View Details
                                            </button>
                                        </div>
                                        <div class="table-actions__secondary">
                                            <button type="button"><i class="fa-solid fa-rotate-left"></i> Undo</button>
                                            <button type="button" data-product-view="{{ $product['id'] }}"><i class="fa-regular fa-file-lines"></i> View Details</button>
                                        </div>
                                    </div>
                                </td>
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
    <div class="modal-shell" id="product-approval-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title" id="product-modal-title"></h3>
                <button class="modal-close" type="button" data-close-modal="product-approval-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="product-modal-grid">
                    <div class="product-gallery">
                        <div class="hero-thumb" id="product-modal-hero"></div>
                        <div class="thumb-strip" id="product-modal-thumbs"></div>
                        <div>
                            <h4 class="section-title">Product Details</h4>
                            <div class="detail-list">
                                <div class="detail-list__item"><span>Category:</span><strong id="product-modal-category-left"></strong></div>
                                <div class="detail-list__item"><span>Materials:</span><strong id="product-modal-materials-left"></strong></div>
                                <div class="detail-list__item"><span>Dimensions:</span><strong id="product-modal-dimensions-left"></strong></div>
                                <div class="detail-list__item"><span>Weight:</span><strong id="product-modal-weight-left"></strong></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="detail-list">
                            <div class="detail-list__item"><span>Category:</span><strong id="product-modal-category-right"></strong></div>
                            <div class="detail-list__item"><span>Price:</span><strong id="product-modal-price"></strong></div>
                            <div class="detail-list__item"><span>Materials:</span><strong id="product-modal-materials-right"></strong></div>
                            <div class="detail-list__item"><span>Dimensions:</span><strong id="product-modal-dimensions-right"></strong></div>
                            <div class="detail-list__item"><span>Weight:</span><strong id="product-modal-weight-right"></strong></div>
                        </div>

                        <div class="seller-box">
                            <div class="seller-box__header">Seller Information</div>
                            <div class="seller-box__body">
                                <div class="seller-box__profile">
                                    <div class="avatar-circle" id="product-modal-seller-avatar"></div>
                                    <div>
                                        <div class="seller-name" id="product-modal-seller-handle"></div>
                                        <div class="sub-line" id="product-modal-seller-name"></div>
                                    </div>
                                </div>
                                <button class="action-button action-button--primary" type="button" id="product-modal-open-seller">View Seller Profile</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="product-approval-modal">Close</button>
                <div class="footer-actions">
                    <button class="action-button action-button--danger" type="button">Reject</button>
                    <button class="action-button action-button--success" type="button">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="product-seller-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title">Seller Profile: <span id="product-seller-modal-handle"></span></h3>
                <button class="modal-close" type="button" data-close-modal="product-seller-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="seller-box__profile">
                    <div class="avatar-circle" id="product-seller-modal-avatar"></div>
                    <div>
                        <div class="seller-name" id="product-seller-modal-username"></div>
                        <div class="sub-line" id="product-seller-modal-fullname"></div>
                    </div>
                    <span class="status-pill status-pill--pending" id="product-seller-modal-status"></span>
                </div>

                <div class="tabs">
                    <button class="tab-button" type="button">Shop Info</button>
                    <button class="tab-button is-active" type="button">Verification Documents</button>
                    <button class="tab-button" type="button">Products</button>
                </div>

                <div class="document-row">
                    <h4 class="section-title">Verification Documents</h4>
                    <div class="sub-line">These documents have been submitted by the seller for verification.</div>
                    <div><span class="status-pill status-pill--pending"><i class="fa-regular fa-clock"></i> Verification Status: Pending</span></div>

                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--id"></div>
                        <div>
                            <div class="seller-name">Government Issued ID</div>
                            <div class="sub-line">Uploaded Apr 22, 2024</div>
                        </div>
                        <div><span class="status-pill status-pill--success"><i class="fa-solid fa-check"></i> Uploaded</span></div>
                    </div>
                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--license"></div>
                        <div>
                            <div class="seller-name">Business License / Permit</div>
                            <div class="sub-line">Uploaded Apr 22, 2024</div>
                        </div>
                        <div><span class="status-pill status-pill--success"><i class="fa-solid fa-check"></i> Uploaded</span></div>
                    </div>
                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--address"></div>
                        <div>
                            <div class="seller-name">Proof of Address</div>
                            <div class="sub-line">Uploaded Apr 22, 2024</div>
                        </div>
                        <div><span class="status-pill status-pill--success"><i class="fa-solid fa-check"></i> Uploaded</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="product-seller-modal">Close</button>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
            const products = @json($products);
            const byId = Object.fromEntries(products.map((product) => [product.id, product]));
            let activeProduct = null;

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

            document.querySelectorAll('[data-product-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    const product = byId[button.dataset.productView];
                    if (!product) return;
                    activeProduct = product;
                    document.getElementById('product-modal-title').textContent = product.name;
                    document.getElementById('product-modal-category-left').textContent = product.category;
                    document.getElementById('product-modal-materials-left').textContent = product.materials;
                    document.getElementById('product-modal-dimensions-left').textContent = product.dimensions;
                    document.getElementById('product-modal-weight-left').textContent = product.weight;
                    document.getElementById('product-modal-category-right').textContent = product.category;
                    document.getElementById('product-modal-price').textContent = product.price;
                    document.getElementById('product-modal-materials-right').textContent = product.materials;
                    document.getElementById('product-modal-dimensions-right').textContent = product.dimensions;
                    document.getElementById('product-modal-weight-right').textContent = product.weight;
                    document.getElementById('product-modal-seller-avatar').textContent = product.avatar;
                    document.getElementById('product-modal-seller-handle').textContent = product.seller;
                    document.getElementById('product-modal-seller-name').textContent = product.seller_name;
                    document.getElementById('product-modal-hero').className = `hero-thumb hero-thumb--${product.thumb}`;

                    const thumbs = document.getElementById('product-modal-thumbs');
                    thumbs.innerHTML = '';
                    for (let i = 0; i < 3; i += 1) {
                        const element = document.createElement('div');
                        element.className = `mini-thumb mini-thumb--${product.thumb}`;
                        thumbs.appendChild(element);
                    }

                    openModal('product-approval-modal');
                });
            });

            const sellerTrigger = document.getElementById('product-modal-open-seller');
            if (sellerTrigger) {
                sellerTrigger.addEventListener('click', () => {
                    if (!activeProduct) return;
                    document.getElementById('product-seller-modal-handle').textContent = activeProduct.seller;
                    document.getElementById('product-seller-modal-avatar').textContent = activeProduct.avatar;
                    document.getElementById('product-seller-modal-username').textContent = activeProduct.seller;
                    document.getElementById('product-seller-modal-fullname').textContent = activeProduct.seller_name;
                    document.getElementById('product-seller-modal-status').textContent = activeProduct.seller_status;
                    closeModal('product-approval-modal');
                    openModal('product-seller-modal');
                });
            }
        })();
    </script>
@endpush
