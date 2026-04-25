@extends('layouts.admin')

@section('title', 'Product Approvals')
@section('eyebrow', 'Moderation')
@section('page-title', 'Product Approvals')

@section('content')
    @php
        $productModalData = $products->map(function ($product) {
            $seller = $product->user;
            $sellerProfile = $seller?->sellerProfile;
            $imageUrl = $product->image ? asset('storage/' . $product->image) : null;
            $sellerDisplay = $seller?->name ?? 'Seller';

            return [
                'id' => $product->id,
                'name' => $product->name,
                'seller' => '@' . \Illuminate\Support\Str::slug($sellerDisplay, '_'),
                'seller_name' => $sellerDisplay,
                'category' => $product->category->name ?? 'Uncategorized',
                'price' => '&#8369; ' . number_format((float) $product->price, 2),
                'materials' => $product->description ?: 'No materials/details provided.',
                'dimensions' => trim(collect([
                    $product->length_cm ? $product->length_cm . ' cm L' : null,
                    $product->width_cm ? $product->width_cm . ' cm W' : null,
                    $product->height_cm ? $product->height_cm . ' cm H' : null,
                ])->filter()->implode(', ')) ?: 'No dimensions provided',
                'weight' => $product->weight ? $product->weight . ' kg' : 'No weight provided',
                'thumb' => 'earrings',
                'thumb_url' => $imageUrl,
                'avatar' => strtoupper(substr($sellerDisplay, 0, 2)),
                'seller_status' => ucfirst($sellerProfile?->application_status ?? 'pending'),
                'approve_url' => route('admin.products.approve', $product),
                'reject_url' => route('admin.products.reject', $product),
                'seller_id_type' => $sellerProfile?->valid_id_type ?: 'Government Issued ID',
                'seller_id_url' => $sellerProfile?->valid_id_path ? asset('storage/' . $sellerProfile->valid_id_path) : null,
                'seller_permit_url' => $sellerProfile?->business_permit_path ? asset('storage/' . $sellerProfile->business_permit_path) : null,
            ];
        })->values();
    @endphp

    <div class="page-stack">
        @if (session('success'))
            <div class="alert-note">{{ session('success') }}</div>
        @endif

        <div class="table-card__header" style="padding: 0; border: 0;">
            <div>
                <h3 class="section-title">Pending Products</h3>

            </div>
            <div class="toolbar-row">

                <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text"
                        placeholder="Search products..." /></div>
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
                        @forelse ($products as $product)
                            @php
                                $sellerDisplay = $product->user?->name ?? 'Seller';
                            @endphp
                            <tr>
                                <td class="checkbox-cell"><span class="checkbox"></span></td>
                                <td>
                                    <div class="product-cell">
                                        <div class="mini-thumb mini-thumb--earrings"></div>
                                        <div class="product-cell__text">
                                            <div class="product-title">{{ $product->name }}</div>
                                            <div class="sub-line"><i class="fa-solid fa-user"></i>
                                                {{ '@' . \Illuminate\Support\Str::slug($sellerDisplay, '_') }}</div>
                                            <div class="sub-line">Category: {{ $product->category->name ?? 'Uncategorized' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-cell__text">
                                        <div>{{ '@' . \Illuminate\Support\Str::slug($sellerDisplay, '_') }}</div>
                                        <div class="product-title">&#8369; {{ number_format((float) $product->price, 2) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <div class="table-actions__primary">
                                            <form method="POST" action="{{ route('admin.products.approve', $product) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-button action-button--success"
                                                    type="submit">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.products.reject', $product) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-button action-button--danger"
                                                    type="submit">Reject</button>
                                            </form>
                                            <button class="action-button action-button--primary" type="button"
                                                data-product-view="{{ $product->id }}">
                                                <i class="fa-solid fa-magnifying-glass"></i> View Details
                                            </button>
                                        </div>
                                        <div class="table-actions__secondary">

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="sub-line">No pending products for approval.</td>
                            </tr>
                        @endforelse
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


                        </div>
                    </div>
                    <div>
                        <div class="detail-list">
                            <div class="detail-list__item"><span>Category:</span><strong
                                    id="product-modal-category-right"></strong></div>
                            <div class="detail-list__item"><span>Price:</span><strong id="product-modal-price"></strong>
                            </div>
                            <div class="detail-list__item"><span>Description:</span><strong
                                    id="product-modal-materials-right"></strong></div>
                            <div class="detail-list__item"><span>Dimensions:</span><strong
                                    id="product-modal-dimensions-right"></strong></div>
                            <div class="detail-list__item"><span>Weight:</span><strong
                                    id="product-modal-weight-right"></strong></div>
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
                                <button class="action-button action-button--primary" type="button"
                                    id="product-modal-open-seller">View Seller Profile</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="product-approval-modal">Close</button>
                <div class="footer-actions">
                    <form method="POST" id="product-modal-reject-form">
                        @csrf
                        @method('PATCH')
                        <button class="action-button action-button--danger" type="submit">Reject</button>
                    </form>
                    <form method="POST" id="product-modal-approve-form">
                        @csrf
                        @method('PATCH')
                        <button class="action-button action-button--success" type="submit">Approve</button>
                    </form>
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
                    <div><span class="status-pill status-pill--pending"><i class="fa-regular fa-clock"></i> Verification
                            Status: <span id="product-seller-review-status"></span></span></div>

                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--id"></div>
                        <div>
                            <div class="seller-name" id="product-seller-id-label">Government Issued ID</div>
                            <div class="sub-line">Uploaded seller verification document</div>
                        </div>
                        <div><a class="action-button action-button--primary" href="#" target="_blank"
                                id="product-seller-id-link">View</a></div>
                    </div>
                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--license"></div>
                        <div>
                            <div class="seller-name">Business License / Permit</div>
                            <div class="sub-line">Uploaded only when applicable</div>
                        </div>
                        <div><a class="action-button action-button--primary" href="#" target="_blank"
                                id="product-seller-permit-link">View</a></div>
                    </div>
                    <div class="document-row__item">
                        <div class="doc-thumb doc-thumb--address"></div>
                        <div>
                            <div class="seller-name">Proof of Address</div>
                            <div class="sub-line">Use seller review page for more document requests</div>
                        </div>
                        <div><span class="status-pill status-pill--pending">Check Seller Reviews</span></div>
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
            const products = @json($productModalData);
            const byId = Object.fromEntries(products.map((product) => [String(product.id), product]));
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

                    document.getElementById('product-modal-category-right').textContent = product.category;
                    document.getElementById('product-modal-price').innerHTML = product.price;
                    document.getElementById('product-modal-materials-right').innerHTML = product.materials;
                    document.getElementById('product-modal-dimensions-right').textContent = product.dimensions;
                    document.getElementById('product-modal-weight-right').textContent = product.weight;
                    document.getElementById('product-modal-seller-avatar').textContent = product.avatar;
                    document.getElementById('product-modal-seller-handle').textContent = product.seller;
                    document.getElementById('product-modal-seller-name').textContent = product.seller_name;

                    const hero = document.getElementById('product-modal-hero');
                    hero.className = `hero-thumb hero-thumb--${product.thumb}`;
                    if (product.thumb_url) {
                        hero.style.backgroundImage = `url('${product.thumb_url}')`;
                        hero.style.backgroundSize = 'cover';
                        hero.style.backgroundPosition = 'center';
                    } else {
                        hero.style.backgroundImage = '';
                    }

                    document.getElementById('product-modal-approve-form').action = product.approve_url;
                    document.getElementById('product-modal-reject-form').action = product.reject_url;

                    const thumbs = document.getElementById('product-modal-thumbs');
                    thumbs.innerHTML = '';
                    for (let i = 0; i < 3; i += 1) {
                        const element = document.createElement('div');
                        element.className = `mini-thumb mini-thumb--${product.thumb}`;
                        if (product.thumb_url) {
                            element.style.backgroundImage = `url('${product.thumb_url}')`;
                            element.style.backgroundSize = 'cover';
                            element.style.backgroundPosition = 'center';
                        }
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
                    document.getElementById('product-seller-review-status').textContent = activeProduct.seller_status;
                    document.getElementById('product-seller-id-label').textContent = activeProduct.seller_id_type;

                    const idLink = document.getElementById('product-seller-id-link');
                    const permitLink = document.getElementById('product-seller-permit-link');

                    if (activeProduct.seller_id_url) {
                        idLink.href = activeProduct.seller_id_url;
                        idLink.style.pointerEvents = 'auto';
                    } else {
                        idLink.href = '#';
                        idLink.style.pointerEvents = 'none';
                    }

                    if (activeProduct.seller_permit_url) {
                        permitLink.href = activeProduct.seller_permit_url;
                        permitLink.style.pointerEvents = 'auto';
                    } else {
                        permitLink.href = '#';
                        permitLink.style.pointerEvents = 'none';
                    }

                    closeModal('product-approval-modal');
                    openModal('product-seller-modal');
                });
            }
        })();
    </script>
@endpush