@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Dashboard')
@section('page-title', 'Welcome, Admin!')
@section('page-description', 'Dashboard content updated from the provided document, with clickable product and seller modals.')

@section('content')
    @php
        $stats = [
            ['label' => 'Pending Products', 'value' => '12', 'note' => 'Awaiting Approval', 'tone' => 'primary'],
            ['label' => 'Pending Sellers', 'value' => '5', 'note' => 'Verification Needed', 'tone' => 'warning'],
            ['label' => 'Total Sellers', 'value' => '320', 'note' => 'Approved Sellers', 'tone' => 'success'],
            ['label' => 'Total Buyers', 'value' => '1,560', 'note' => 'Registered Buyers', 'tone' => 'danger'],
        ];

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
            ],
        ];

        $reports = [
            'Suspicious Activity Reported',
            'Inappropriate Product Listing',
        ];
    @endphp

    <div class="page-stack">
        <section class="summary-grid">
            @foreach ($stats as $stat)
                <article class="summary-card summary-card--{{ $stat['tone'] }}">
                    <p class="summary-card__label">{{ $stat['label'] }}</p>
                    <div class="summary-card__value">
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $stat['note'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="content-grid">
            <div class="stack">
                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Pending Product Approvals</h3>
                    </div>

                    <div class="product-card-grid">
                        @foreach ($products as $product)
                            <article class="product-card">
                                <div class="product-card__body">
                                    <h4 class="product-card__name">{{ $product['name'] }}</h4>
                                    <div class="muted-row">
                                        <i class="fa-solid fa-user"></i>
                                        <span>{{ $product['seller'] }}</span>
                                    </div>
                                </div>

                                <div class="hero-thumb hero-thumb--{{ $product['thumb'] }}"></div>

                                <div class="product-card__body">
                                    <div class="product-card__meta">
                                        <div class="meta-row">
                                            <span>Category:</span>
                                            <strong>{{ $product['category'] }}</strong>
                                        </div>
                                        <div class="meta-row">
                                            <span>Price:</span>
                                            <strong>{{ $product['price'] }}</strong>
                                        </div>
                                    </div>

                                    <div class="muted-row">
                                        <span>Seller:</span>
                                        <strong>{{ $product['seller'] }}</strong>
                                    </div>
                                </div>

                                <div class="product-card__body">
                                    <div class="button-row">
                                        <button class="action-button action-button--success" type="button">Approve</button>
                                        <button class="action-button action-button--danger" type="button">Reject</button>
                                    </div>
                                </div>

                                <div class="product-card__footer">
                                    <button class="secondary-link" type="button" data-open-product="{{ $product['id'] }}">
                                        View Details <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </article>

                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Recent Reports</h3>
                    </div>

                    <div class="report-list">
                        @foreach ($reports as $report)
                            <div class="report-list__row">
                                <span>{{ $report }}</span>
                                <a class="section-link" href="{{ route('admin.reports') }}">View <i class="fa-solid fa-chevron-right"></i></a>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>

            <div class="stack">
                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Seller Verification Requests</h3>
                    </div>

                    <div class="seller-request-card">
                        <div class="seller-request-card__profile">
                            <div class="avatar-circle">S</div>
                            <div>
                                <div class="seller-name">HandmadeBySara</div>
                                <div class="sub-line">Sara P.</div>
                            </div>
                        </div>

                        <div class="seller-request-card__details spacer-top">
                            <div class="detail-line">
                                <span><i class="fa-solid fa-location-dot"></i> artisan</span>
                            </div>
                            <div class="detail-line">
                                <span><i class="fa-solid fa-location-dot"></i> Location: Austin, TX</span>
                            </div>
                            <div>
                                <span class="status-pill status-pill--pending"><i class="fa-regular fa-id-card"></i> ID Verification Pending</span>
                            </div>
                        </div>

                        <div class="button-row spacer-top">
                            <button class="action-button action-button--success" type="button">Approve</button>
                            <button class="action-button action-button--danger" type="button">Reject</button>
                        </div>
                    </div>
                </article>

                <article class="panel-card">
                    <div class="section-card__header">
                        <h3 class="section-title">Site Statistics</h3>
                    </div>

                    <div class="site-stats-card">
                        <div class="site-stat-list">
                            <div class="site-stat-row"><span><i class="fa-solid fa-user"></i> Active Sellers:</span><strong>320</strong></div>
                            <div class="site-stat-row"><span><i class="fa-solid fa-users"></i> Active Buyers:</span><strong>1,560</strong></div>
                            <div class="site-stat-row"><span><i class="fa-solid fa-box"></i> Total Products:</span><strong>4,230</strong></div>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
@endsection

@push('modals')
    <div class="modal-shell" id="dashboard-product-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title" id="dashboard-product-title"></h3>
                <button class="modal-close" type="button" data-close-modal="dashboard-product-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="product-modal-grid">
                    <div class="product-gallery">
                        <div class="hero-thumb" id="dashboard-product-hero"></div>
                        <div class="thumb-strip" id="dashboard-product-thumbs"></div>

                        <div>
                            <h4 class="section-title">Product Details</h4>
                            <div class="detail-list">
                                <div class="detail-list__item"><span>Category:</span><strong id="dashboard-product-category-left"></strong></div>
                                <div class="detail-list__item"><span>Materials:</span><strong id="dashboard-product-materials-left"></strong></div>
                                <div class="detail-list__item"><span>Dimensions:</span><strong id="dashboard-product-dimensions-left"></strong></div>
                                <div class="detail-list__item"><span>Weight:</span><strong id="dashboard-product-weight-left"></strong></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="detail-list">
                            <div class="detail-list__item"><span>Category:</span><strong id="dashboard-product-category-right"></strong></div>
                            <div class="detail-list__item"><span>Price:</span><strong id="dashboard-product-price"></strong></div>
                            <div class="detail-list__item"><span>Materials:</span><strong id="dashboard-product-materials-right"></strong></div>
                            <div class="detail-list__item"><span>Dimensions:</span><strong id="dashboard-product-dimensions-right"></strong></div>
                            <div class="detail-list__item"><span>Weight:</span><strong id="dashboard-product-weight-right"></strong></div>
                        </div>

                        <div class="seller-box">
                            <div class="seller-box__header">Seller Information</div>
                            <div class="seller-box__body">
                                <div class="seller-box__profile">
                                    <div class="avatar-circle" id="dashboard-seller-avatar"></div>
                                    <div>
                                        <div class="seller-name" id="dashboard-seller-handle"></div>
                                        <div class="sub-line" id="dashboard-seller-name"></div>
                                    </div>
                                </div>
                                <button class="action-button action-button--primary" type="button" id="dashboard-open-seller-profile">View Seller Profile</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="dashboard-product-modal">Close</button>
                <div class="footer-actions">
                    <button class="action-button action-button--danger" type="button">Reject</button>
                    <button class="action-button action-button--success" type="button">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="dashboard-seller-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title">Seller Profile: <span id="dashboard-modal-seller-handle"></span></h3>
                <button class="modal-close" type="button" data-close-modal="dashboard-seller-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="seller-box__profile">
                    <div class="avatar-circle" id="dashboard-modal-seller-avatar"></div>
                    <div>
                        <div class="seller-name" id="dashboard-modal-seller-username"></div>
                        <div class="sub-line" id="dashboard-modal-seller-fullname"></div>
                    </div>
                    <span class="status-pill status-pill--pending">Pending</span>
                </div>

                <div class="tabs">
                    <button class="tab-button is-active" type="button">Shop Info</button>
                    <button class="tab-button is-active" type="button">Verification Documents</button>
                    <button class="tab-button" type="button">Products</button>
                </div>

                <div class="document-row">
                    <p class="section-title">Verification Documents</p>
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
                <button class="button" type="button" data-close-modal="dashboard-seller-modal">Close</button>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
            const products = @json($products);
            const map = Object.fromEntries(products.map((product) => [product.id, product]));
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
                    if (event.target === shell) {
                        closeModal(shell.id);
                    }
                });
            });

            document.querySelectorAll('[data-open-product]').forEach((button) => {
                button.addEventListener('click', () => {
                    const product = map[button.dataset.openProduct];
                    if (!product) return;
                    activeProduct = product;
                    document.getElementById('dashboard-product-title').textContent = product.name;
                    document.getElementById('dashboard-product-category-left').textContent = product.category;
                    document.getElementById('dashboard-product-materials-left').textContent = product.materials;
                    document.getElementById('dashboard-product-dimensions-left').textContent = product.dimensions;
                    document.getElementById('dashboard-product-weight-left').textContent = product.weight;
                    document.getElementById('dashboard-product-category-right').textContent = product.category;
                    document.getElementById('dashboard-product-price').textContent = product.price;
                    document.getElementById('dashboard-product-materials-right').textContent = product.materials;
                    document.getElementById('dashboard-product-dimensions-right').textContent = product.dimensions;
                    document.getElementById('dashboard-product-weight-right').textContent = product.weight;
                    document.getElementById('dashboard-seller-avatar').textContent = product.avatar;
                    document.getElementById('dashboard-seller-handle').textContent = product.seller;
                    document.getElementById('dashboard-seller-name').textContent = product.seller_name;

                    const hero = document.getElementById('dashboard-product-hero');
                    hero.className = `hero-thumb hero-thumb--${product.thumb}`;

                    const strip = document.getElementById('dashboard-product-thumbs');
                    strip.innerHTML = '';
                    for (let i = 0; i < 3; i += 1) {
                        const thumb = document.createElement('div');
                        thumb.className = `mini-thumb mini-thumb--${product.thumb}`;
                        strip.appendChild(thumb);
                    }

                    openModal('dashboard-product-modal');
                });
            });

            const sellerButton = document.getElementById('dashboard-open-seller-profile');
            if (sellerButton) {
                sellerButton.addEventListener('click', () => {
                    if (!activeProduct) return;
                    document.getElementById('dashboard-modal-seller-handle').textContent = activeProduct.seller;
                    document.getElementById('dashboard-modal-seller-avatar').textContent = activeProduct.avatar;
                    document.getElementById('dashboard-modal-seller-username').textContent = activeProduct.seller;
                    document.getElementById('dashboard-modal-seller-fullname').textContent = activeProduct.seller_name;
                    closeModal('dashboard-product-modal');
                    openModal('dashboard-seller-modal');
                });
            }
        })();
    </script>
@endpush
