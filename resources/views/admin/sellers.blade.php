@extends('layouts.admin')

@section('title', 'Manage Sellers')
@section('eyebrow', 'Verification')
@section('page-title', 'Manage Sellers')
@section('page-description', 'Seller management page rebuilt from the document, including seller details and uploaded document modals.')

@section('content')
    @php
        $stats = [
            ['label' => 'Total Sellers', 'value' => '342', 'tone' => 'green'],
            ['label' => 'Pending Verification', 'value' => '8', 'tone' => 'orange'],
            ['label' => 'Reported Sellers', 'value' => '5', 'tone' => 'red'],
            ['label' => 'Banned Sellers', 'value' => '12', 'tone' => 'blue'],
        ];

        $sellers = [
            ['id' => 'craftyjen', 'name' => 'CraftyJen', 'handle' => 'CraftyJen', 'category' => 'Jewelry', 'products' => 24, 'status' => 'Active', 'date' => '11/22/2023', 'avatar' => 'CJ', 'avatar_class' => 'gold'],
            ['id' => 'bens', 'name' => "Ben's Workshop", 'handle' => "Ben's Workshop", 'category' => 'Home Decor', 'products' => 35, 'status' => 'Active', 'date' => '09/10/2023', 'avatar' => 'BW', 'avatar_class' => 'teal'],
            ['id' => 'sarah', 'name' => "Sarah's Sculptures", 'handle' => "Sarah's Sculptures", 'category' => 'Handmade', 'products' => 21, 'status' => 'Active', 'date' => '07/26/2023', 'avatar' => 'SS', 'avatar_class' => 'rose'],
            ['id' => 'anna', 'name' => "Anna's Art Studio", 'handle' => "Anna's Art Studio", 'category' => 'Art', 'products' => 0, 'status' => 'Pending', 'date' => '10/09/2023', 'avatar' => 'AA', 'avatar_class' => 'slate'],
            ['id' => 'alex', 'name' => "Alex's Crafts", 'handle' => "Alex's Crafts", 'category' => 'Toys', 'products' => 42, 'status' => 'Active', 'date' => '06/19/2023', 'avatar' => 'AC', 'avatar_class' => 'olive'],
            ['id' => 'willow', 'name' => 'WillowWeave', 'handle' => 'WillowWeave', 'category' => 'Accessories', 'products' => 16, 'status' => 'Active', 'date' => '05/10/2023', 'avatar' => 'WW', 'avatar_class' => 'teal'],
            ['id' => 'mason', 'name' => "Mason's Handmade", 'handle' => "Home Decor", 'category' => 'Home Decor', 'products' => 0, 'status' => 'Pending', 'date' => '11/19/2023', 'avatar' => 'MH', 'avatar_class' => 'rose'],
            ['id' => 'knit', 'name' => 'KnitByElla', 'handle' => 'KnitByElla', 'category' => 'Toys', 'products' => 32, 'status' => 'Active', 'date' => '03/15/2023', 'avatar' => 'KE', 'avatar_class' => 'gold'],
        ];
    @endphp

    <div class="page-stack">
        <p class="sub-line" style="font-size: 1.05rem; margin: 0;">View and manage all registered sellers.</p>

        <section class="seller-stats-grid">
            @foreach ($stats as $stat)
                <article class="seller-stat-card seller-stat-card--{{ $stat['tone'] }}">
                    <p>{{ $stat['label'] }}</p>
                    <strong>{{ $stat['value'] }}</strong>
                </article>
            @endforeach
        </section>

        <div class="filter-bar">
            <div class="search-box search-box--grow"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search sellers..." /></div>
            <div class="inline-select"><i class="fa-solid fa-gear"></i> Filter <i class="fa-solid fa-chevron-down"></i></div>
            <div class="inline-select"><i class="fa-solid fa-magnifying-glass"></i></div>
        </div>

        <article class="table-card">
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Date Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sellers as $seller)
                            <tr>
                                <td>
                                    <div class="seller-cell">
                                        <div class="avatar-photo avatar-photo--{{ $seller['avatar_class'] }}">{{ $seller['avatar'] }}</div>
                                        <div class="seller-cell__text">
                                            <div class="seller-name">{{ $seller['name'] }}</div>
                                            <div class="sub-line">{{ $seller['handle'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $seller['category'] }}</td>
                                <td>{{ $seller['products'] }}</td>
                                <td>
                                    <span class="status-pill {{ $seller['status'] === 'Active' ? 'status-pill--success' : 'status-pill--pending' }}">
                                        {{ $seller['status'] }}
                                    </span>
                                </td>
                                <td>{{ $seller['date'] }}</td>
                                <td>
                                    <div class="table-actions__primary">
                                        <button class="action-button action-button--primary" type="button" data-seller-view="{{ $seller['id'] }}"><i class="fa-solid fa-magnifying-glass"></i> View</button>
                                        <button class="action-button action-button--warning" type="button">Suspend</button>
                                        <button class="action-button action-button--danger" type="button">Ban</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-bar">
                <button class="pagination-button"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="pagination-button is-active">1</button>
                <button class="pagination-button">2</button>
                <button class="pagination-button">3</button>
                <button class="pagination-button"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </article>
    </div>
@endsection

@push('modals')
    <div class="modal-shell" id="seller-detail-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title">Seller Details</h3>
                <button class="modal-close" type="button" data-close-modal="seller-detail-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="seller-box__profile">
                    <div class="avatar-photo" id="seller-detail-avatar"></div>
                    <div>
                        <div class="seller-name" id="seller-detail-name"></div>
                        <div class="sub-line" id="seller-detail-handle"></div>
                    </div>
                </div>

                <div class="modal-meta-bar spacer-top">
                    <strong>342 Sales</strong>
                    <span>Joined <span id="seller-detail-date"></span></span>
                    <span>Email craftyjen@email.com</span>
                </div>

                <div class="spacer-top">
                    <h4 class="section-title">Uploaded Documents</h4>
                    <div class="doc-card-grid spacer-top">
                        <div class="doc-card">
                            <div class="doc-thumb doc-thumb--id"></div>
                            <div class="doc-card__content">
                                <div class="seller-name">ID / Passport</div>
                                <div class="doc-card__status">Uploaded</div>
                                <button class="button" type="button" data-open-document="ID / Passport">View</button>
                            </div>
                        </div>
                        <div class="doc-card">
                            <div class="doc-thumb doc-thumb--license"></div>
                            <div class="doc-card__content">
                                <div class="seller-name">Business License</div>
                                <div class="doc-card__status">Uploaded</div>
                                <button class="button" type="button" data-open-document="Business License">View</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="spacer-top">
                    <h4 class="section-title">Request More Documents</h4>
                    <div class="form-row spacer-top">
                        <select class="field-select">
                            <option>Select Reason</option>
                            <option>Proof of Address</option>
                            <option>Tax Identification Number</option>
                            <option>Bank Statement</option>
                        </select>
                        <button class="action-button action-button--warning" type="button">Request Documents</button>
                    </div>
                    <div class="alert-note">Additional document requests will notify the seller by email.</div>
                </div>
            </div>
            <div class="modal-card__footer">
                <div class="footer-actions">
                    <button class="action-button action-button--success" type="button">Verify Seller</button>
                    <button class="action-button action-button--danger" type="button">Reject Seller</button>
                    <button class="button" type="button">Save as Pending</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="seller-document-modal" hidden>
        <div class="modal-card">
            <div class="modal-card__header">
                <h3 class="modal-title">Uploaded Document</h3>
                <button class="modal-close" type="button" data-close-modal="seller-document-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <h4 class="section-title" id="seller-document-title"></h4>
                <div class="doc-thumb doc-thumb--license" id="seller-document-preview" style="width: 22rem; height: 30rem; margin: 1.5rem auto 0;"></div>
            </div>
            <div class="modal-card__footer">
                <div class="footer-actions">
                    <button class="action-button action-button--success" type="button">Cover</button>
                    <button class="button" type="button" data-close-modal="seller-document-modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
            const sellers = @json($sellers);
            const sellerMap = Object.fromEntries(sellers.map((seller) => [seller.id, seller]));

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

            document.querySelectorAll('[data-seller-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    const seller = sellerMap[button.dataset.sellerView];
                    if (!seller) return;
                    const avatar = document.getElementById('seller-detail-avatar');
                    avatar.className = `avatar-photo avatar-photo--${seller.avatar_class}`;
                    avatar.textContent = seller.avatar;
                    document.getElementById('seller-detail-name').textContent = seller.name;
                    document.getElementById('seller-detail-handle').textContent = seller.handle;
                    document.getElementById('seller-detail-date').textContent = seller.date;
                    openModal('seller-detail-modal');
                });
            });

            document.querySelectorAll('[data-open-document]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('seller-document-title').textContent = button.dataset.openDocument;
                    document.getElementById('seller-document-preview').className = button.dataset.openDocument === 'ID / Passport'
                        ? 'doc-thumb doc-thumb--id'
                        : 'doc-thumb doc-thumb--license';
                    closeModal('seller-detail-modal');
                    openModal('seller-document-modal');
                });
            });
        })();
    </script>
@endpush
