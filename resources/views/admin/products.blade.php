@extends('layouts.admin')

@section('title', 'Product Approvals')
@section('eyebrow', 'Moderation')
@section('page-title', 'Product Approvals')

@section('content')
    @php
        $statusMeta = [
            'pending' => ['label' => 'Pending', 'empty' => 'No pending products.'],
            'approved' => ['label' => 'Approved', 'empty' => 'No approved products.'],
            'rejected' => ['label' => 'Rejected', 'empty' => 'No rejected products.'],
            'reported' => ['label' => 'Reported', 'empty' => 'No reported products.'],
            'delisted' => ['label' => 'Delisted', 'empty' => 'No delisted products.'],
        ];

        $statusBadge = function ($product) {
            if ($product->status === \App\Models\Product::STATUS_APPROVED && ! $product->is_active) {
                return ['label' => 'Delisted', 'class' => 'status-pill--delivered'];
            }

            return match ($product->status) {
                \App\Models\Product::STATUS_APPROVED => ['label' => 'Approved', 'class' => 'status-pill--success'],
                \App\Models\Product::STATUS_REJECTED => ['label' => 'Rejected', 'class' => 'status-pill--cancelled'],
                default => ['label' => 'Pending', 'class' => 'status-pill--pending'],
            };
        };

        $sellerStatusBadge = function ($status) {
            return match ($status) {
                \App\Models\Seller::STATUS_APPROVED => 'status-pill--success',
                \App\Models\Seller::STATUS_REJECTED => 'status-pill--cancelled',
                default => 'status-pill--pending',
            };
        };

        $money = fn ($value) => 'PHP ' . number_format((float) $value, 2);

        $productModalData = $products
            ->map(function ($product) use ($statusBadge, $sellerStatusBadge, $money) {
                $seller = $product->user;
                $sellerProfile = $seller?->sellerProfile;
                $status = $statusBadge($product);
                $imageUrl = $product->image ? asset('storage/' . $product->image) : null;
                $sellerDisplay = $seller?->name ?? 'Seller';
                $shopName = $sellerProfile?->store_name ?: 'No shop name';
                $condition = $product->condition ? ucfirst((string) $product->condition) : 'Not set';
                $dimensions = trim(
                    collect([
                        $product->length_cm ? $product->length_cm . ' cm L' : null,
                        $product->width_cm ? $product->width_cm . ' cm W' : null,
                        $product->height_cm ? $product->height_cm . ' cm H' : null,
                    ])
                        ->filter()
                        ->implode(', '),
                ) ?: 'Not set';
                $sellerProducts = $seller?->products
                    ?->map(function ($sellerProduct) use ($statusBadge, $money) {
                        $productStatus = $statusBadge($sellerProduct);

                        return [
                            'id' => $sellerProduct->id,
                            'name' => $sellerProduct->name,
                            'image_url' => $sellerProduct->image ? asset('storage/' . $sellerProduct->image) : null,
                            'category' => $sellerProduct->category->name ?? 'Uncategorized',
                            'price' => $money($sellerProduct->price),
                            'stock' => (string) $sellerProduct->stock,
                            'status_label' => $productStatus['label'],
                            'status_class' => $productStatus['class'],
                            'date_added' => optional($sellerProduct->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                        ];
                    })
                    ->values()
                    ->all() ?? [];

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'seller' => '@' . \Illuminate\Support\Str::slug($sellerDisplay, '_'),
                    'seller_name' => $sellerDisplay,
                    'shop_name' => $shopName,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'price' => $money($product->price),
                    'shipping_fee' => $money($product->shipping_fee),
                    'description' => $product->description ?: 'No details provided.',
                    'dimensions' => $dimensions,
                    'weight' => $product->weight ? $product->weight . ' kg' : 'Not set',
                    'stock' => (string) $product->stock,
                    'condition' => $condition,
                    'status_label' => $status['label'],
                    'status_class' => $status['class'],
                    'submitted_at' => optional($product->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                    'image_url' => $imageUrl,
                    'pending_reports_count' => (int) $product->pending_reports_count,
                    'rejection_reason' => $product->rejection_reason ?: 'None',
                    'approve_url' => route('admin.products.approve', $product),
                    'reject_url' => route('admin.products.reject', $product),
                    'can_approve' => ! ($product->status === \App\Models\Product::STATUS_APPROVED && $product->is_active),
                    'can_reject' => $product->status !== \App\Models\Product::STATUS_REJECTED,
                    'avatar' => strtoupper(substr($sellerDisplay, 0, 2)),
                    'seller_status_label' => ucfirst($sellerProfile?->application_status ?? 'pending'),
                    'seller_status_class' => $sellerStatusBadge($sellerProfile?->application_status),
                    'seller_email' => $sellerProfile?->email ?: $seller?->email ?: 'N/A',
                    'seller_phone' => $sellerProfile?->contact_number ?: $seller?->phone ?: 'N/A',
                    'seller_address' => $sellerProfile?->address ?: $seller?->address ?: 'N/A',
                    'seller_description' => $sellerProfile?->store_description ?: 'No description provided.',
                    'seller_owner_name' => $sellerProfile?->full_name ?: $sellerDisplay,
                    'seller_registered_at' => optional($seller?->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                    'seller_verification_status' => ucfirst($sellerProfile?->application_status ?? 'pending'),
                    'seller_submitted_at' => optional($sellerProfile?->submitted_at ?? $sellerProfile?->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                    'seller_id_type' => $sellerProfile?->valid_id_type ?: 'Government Issued ID',
                    'seller_id_url' => $sellerProfile?->valid_id_path ? asset('storage/' . $sellerProfile->valid_id_path) : null,
                    'seller_permit_url' => $sellerProfile?->business_permit_path ? asset('storage/' . $sellerProfile->business_permit_path) : null,
                    'seller_products' => $sellerProducts,
                ];
            })
            ->values();

        $tabQuery = request()->except('status');
    @endphp

    <div class="page-stack">
        @if (session('success'))
            <div class="alert-note">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-note alert-note--danger">{{ $errors->first() }}</div>
        @endif

        <div class="status-tabs" role="tablist" aria-label="Product moderation statuses">
            @foreach ($statusMeta as $tab => $meta)
                <a class="chip {{ $currentTab === $tab ? 'is-active' : '' }}"
                    href="{{ route('admin.products', array_merge($tabQuery, ['status' => $tab])) }}">
                    <span>{{ $meta['label'] }}</span>
                    <strong>{{ $statusCounts[$tab] ?? 0 }}</strong>
                </a>
            @endforeach
        </div>

        <article class="table-card moderation-card">
            <div class="table-card__header moderation-header">
                <div>
                    <h3 class="section-title">{{ $statusMeta[$currentTab]['label'] }}</h3>
                </div>

                <form method="GET" action="{{ route('admin.products') }}" class="toolbar-row moderation-filters">
                    <input type="hidden" name="status" value="{{ $currentTab }}">

                    <label class="inline-select">
                        <span>Category</span>
                        <select name="category_id">
                            <option value="">All</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected((string) $filters['category_id'] === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="inline-select">
                        <span>Seller</span>
                        <select name="seller_id">
                            <option value="">All</option>
                            @foreach ($sellers as $seller)
                                @php
                                    $shopName = $seller->sellerProfile?->store_name ?: $seller->name;
                                @endphp
                                <option value="{{ $seller->id }}"
                                    @selected((string) $filters['seller_id'] === (string) $seller->id)>
                                    {{ $shopName }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="filter-input filter-input--compact">
                        <span>Min</span>
                        <input type="number" min="0" step="0.01" name="price_min"
                            value="{{ $filters['price_min'] }}" placeholder="0.00">
                    </label>

                    <label class="filter-input filter-input--compact">
                        <span>Max</span>
                        <input type="number" min="0" step="0.01" name="price_max"
                            value="{{ $filters['price_max'] }}" placeholder="0.00">
                    </label>

                    <label class="inline-select">
                        <span>Sort</span>
                        <select name="sort">
                            <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                            <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest</option>
                        </select>
                    </label>

                    <div class="filter-actions">
                        <button class="action-button action-button--primary" type="submit">Apply</button>
                        <a class="action-button action-button--light"
                            href="{{ route('admin.products', ['status' => $currentTab]) }}">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bulk-toolbar">
                <div class="bulk-toolbar__selection" id="bulk-selection-count">0 selected</div>
                <div class="bulk-toolbar__actions">
                    <button class="action-button action-button--success" type="button" id="bulk-approve-button" disabled>
                        Approve selected
                    </button>
                    <button class="action-button action-button--danger" type="button" id="bulk-reject-button" disabled>
                        Reject selected
                    </button>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input class="selection-checkbox" id="select-all-products" type="checkbox"
                                    aria-label="Select all products">
                            </th>
                            <th>Product</th>
                            <th>Seller</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $seller = $product->user;
                                $sellerProfile = $seller?->sellerProfile;
                                $sellerDisplay = $seller?->name ?? 'Seller';
                                $shopName = $sellerProfile?->store_name ?: 'No shop name';
                                $status = $statusBadge($product);
                                $imageUrl = $product->image ? asset('storage/' . $product->image) : null;
                                $canApprove = ! ($product->status === \App\Models\Product::STATUS_APPROVED && $product->is_active);
                                $canReject = $product->status !== \App\Models\Product::STATUS_REJECTED;
                            @endphp
                            <tr>
                                <td class="checkbox-cell">
                                    <input class="selection-checkbox product-select" type="checkbox"
                                        value="{{ $product->id }}" aria-label="Select {{ $product->name }}">
                                </td>
                                <td>
                                    <div class="product-cell">
                                        <button class="product-thumb-button" type="button"
                                            data-image-preview="{{ $imageUrl }}" data-image-title="{{ $product->name }}">
                                            @if ($imageUrl)
                                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                            @else
                                                <span>{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                                            @endif
                                        </button>
                                        <div class="product-cell__text">
                                            <div class="product-title">{{ $product->name }}</div>
                                            <div class="sub-line">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                            <div class="sub-line">{{ 'Price: ' . $money($product->price) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-cell__text">
                                        <div class="product-title">{{ $shopName }}</div>
                                        <div class="sub-line">{{ $sellerDisplay }}</div>
                                        <div class="sub-line">{{ '@' . \Illuminate\Support\Str::slug($sellerDisplay, '_') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-stack">
                                        <span class="status-pill {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        @if ($product->pending_reports_count > 0)
                                            <span class="status-pill status-pill--danger">
                                                {{ $product->pending_reports_count }} report{{ $product->pending_reports_count > 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                        <div class="sub-line">
                                            {{ optional($product->created_at)->format('M d, Y h:i A') ?: 'Unknown' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <div class="table-actions__primary">
                                            @if ($canApprove)
                                                <form method="POST" action="{{ route('admin.products.approve', $product) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="action-button action-button--success"
                                                        type="submit">Approve</button>
                                                </form>
                                            @endif

                                            @if ($canReject)
                                                <button class="action-button action-button--danger" type="button"
                                                    data-reject-url="{{ route('admin.products.reject', $product) }}"
                                                    data-reject-name="{{ $product->name }}">
                                                    Reject
                                                </button>
                                            @endif

                                            <button class="action-button action-button--primary" type="button"
                                                data-product-view="{{ $product->id }}">
                                                <i class="fa-solid fa-magnifying-glass"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="sub-line empty-table">{{ $statusMeta[$currentTab]['empty'] }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <form method="POST" id="bulk-approve-form" action="{{ route('admin.products.bulk') }}" hidden>
        @csrf
        @method('PATCH')
        <input type="hidden" name="action" value="approve">
        <div id="bulk-approve-ids"></div>
    </form>
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
                        <button class="hero-thumb product-image-button hero-thumb--earrings" id="product-modal-hero-button"
                            type="button">
                            <img id="product-modal-hero-image" alt="">
                            <span class="product-image-fallback" id="product-modal-hero-fallback"></span>
                        </button>
                        <div class="thumb-strip" id="product-modal-thumbs"></div>
                    </div>
                    <div>
                        <div class="detail-list">
                            <div class="detail-list__item"><span>Category</span><strong
                                    id="product-modal-category"></strong></div>
                            <div class="detail-list__item"><span>Shop</span><strong id="product-modal-shop"></strong>
                            </div>
                            <div class="detail-list__item"><span>Status</span><strong
                                    id="product-modal-status"></strong></div>
                            <div class="detail-list__item"><span>Date submitted</span><strong
                                    id="product-modal-submitted"></strong></div>
                            <div class="detail-list__item"><span>Price</span><strong id="product-modal-price"></strong>
                            </div>
                            <div class="detail-list__item"><span>Shipping fee</span><strong
                                    id="product-modal-shipping"></strong></div>
                            <div class="detail-list__item"><span>Stock</span><strong id="product-modal-stock"></strong>
                            </div>
                            <div class="detail-list__item"><span>Condition</span><strong
                                    id="product-modal-condition"></strong></div>
                            <div class="detail-list__item"><span>Dimensions</span><strong
                                    id="product-modal-dimensions"></strong></div>
                            <div class="detail-list__item"><span>Weight</span><strong id="product-modal-weight"></strong>
                            </div>
                            <div class="detail-list__item detail-list__item--top"><span>Description</span><strong
                                    id="product-modal-description"></strong></div>
                            <div class="detail-list__item"><span>Reports</span><strong id="product-modal-reports"></strong>
                            </div>
                            <div class="detail-list__item detail-list__item--top"><span>Rejection reason</span><strong
                                    id="product-modal-rejection"></strong></div>
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
                    <button class="action-button action-button--danger" type="button" id="product-modal-reject-button">
                        Reject
                    </button>
                    <form method="POST" id="product-modal-approve-form">
                        @csrf
                        @method('PATCH')
                        <button class="action-button action-button--success" type="submit">Approve</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="reject-reason-modal" hidden>
        <div class="modal-card">
            <div class="modal-card__header">
                <h3 class="modal-title" id="reject-modal-title">Reject Product</h3>
                <button class="modal-close" type="button" data-close-modal="reject-reason-modal">&times;</button>
            </div>
            <form method="POST" id="reject-modal-form">
                @csrf
                @method('PATCH')
                <div class="modal-card__body">
                    <div class="reason-grid">
                        @foreach ($rejectionReasons as $key => $label)
                            <label class="reason-option">
                                <input type="radio" name="rejection_reason_key" value="{{ $key }}">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <label class="reason-textarea">
                        <span>Custom</span>
                        <textarea name="rejection_reason_custom" rows="3" maxlength="500"
                            placeholder="Optional"></textarea>
                    </label>
                    <input type="hidden" name="action" id="reject-modal-action" value="">
                    <div id="reject-modal-product-ids"></div>
                </div>
                <div class="modal-card__footer">
                    <button class="button" type="button" data-close-modal="reject-reason-modal">Cancel</button>
                    <button class="action-button action-button--danger" type="submit">Reject</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-shell" id="image-preview-modal" hidden>
        <div class="modal-card modal-card--document">
            <div class="modal-card__header">
                <h3 class="modal-title" id="image-preview-title">Product Image</h3>
                <button class="modal-close" type="button" data-close-modal="image-preview-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="image-preview-shell">
                    <img id="image-preview-image" alt="">
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
                    <span class="status-pill" id="product-seller-modal-status"></span>
                </div>

                <div class="tabs">
                    <button class="tab-button is-active" type="button" data-seller-tab="shop">Shop Info</button>
                    <button class="tab-button" type="button" data-seller-tab="documents">Verification Documents</button>
                    <button class="tab-button" type="button" data-seller-tab="products">Products</button>
                </div>

                <div class="tab-panel" data-seller-panel="shop">
                    <div class="detail-list">
                        <div class="detail-list__item"><span>Shop name</span><strong
                                id="product-seller-shop-name"></strong></div>
                        <div class="detail-list__item"><span>Owner name</span><strong
                                id="product-seller-owner-name"></strong></div>
                        <div class="detail-list__item"><span>Email</span><strong id="product-seller-email"></strong></div>
                        <div class="detail-list__item"><span>Phone</span><strong id="product-seller-phone"></strong></div>
                        <div class="detail-list__item detail-list__item--top"><span>Address</span><strong
                                id="product-seller-address"></strong></div>
                        <div class="detail-list__item detail-list__item--top"><span>Description</span><strong
                                id="product-seller-description"></strong></div>
                        <div class="detail-list__item"><span>Status</span><strong id="product-seller-status-text"></strong>
                        </div>
                        <div class="detail-list__item"><span>Date registered</span><strong
                                id="product-seller-registered"></strong></div>
                        <div class="detail-list__item"><span>Verification</span><strong
                                id="product-seller-verification"></strong></div>
                    </div>
                </div>

                <div class="tab-panel" data-seller-panel="documents" hidden>
                    <div class="document-row">
                        <h4 class="section-title">Verification Documents</h4>
                        <div class="document-row__item">
                            <div class="doc-thumb doc-thumb--id"></div>
                            <div>
                                <div class="seller-name" id="product-seller-id-label">Government Issued ID</div>
                                <div class="sub-line" id="product-seller-id-meta">Uploaded seller verification document
                                </div>
                            </div>
                            <div><button class="action-button action-button--primary" type="button"
                                    id="product-seller-id-link">View Passport</button></div>
                        </div>
                        <div class="document-row__item">
                            <div class="doc-thumb doc-thumb--license"></div>
                            <div>
                                <div class="seller-name">Business License / Permit</div>
                                <div class="sub-line" id="product-seller-permit-meta">Uploaded only when applicable</div>
                            </div>
                            <div><button class="action-button action-button--primary" type="button"
                                    id="product-seller-permit-link">View Business License</button></div>
                        </div>
                    </div>
                </div>

                <div class="tab-panel" data-seller-panel="products" hidden>
                    <div class="seller-products-list" id="product-seller-products-list"></div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="product-seller-modal">Close</button>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="product-seller-document-modal" hidden>
        <div class="modal-card">
            <div class="modal-card__header">
                <h3 class="modal-title" id="product-seller-document-title">Document Preview</h3>
                <button class="modal-close" type="button" data-close-modal="product-seller-document-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="detail-list seller-document-detail-list">
                    <div class="detail-list__item"><span>Document type</span><strong
                            id="product-seller-document-type"></strong></div>
                    <div class="detail-list__item"><span>Seller name</span><strong
                            id="product-seller-document-seller"></strong></div>
                    <div class="detail-list__item"><span>Upload date</span><strong
                            id="product-seller-document-date"></strong></div>
                    <div class="detail-list__item"><span>Status</span><strong
                            id="product-seller-document-status"></strong></div>
                </div>
                <div class="seller-document-preview-shell" id="product-seller-document-preview-stage"></div>
            </div>
            <div class="modal-card__footer">
                <div class="footer-actions">
                    <a class="action-button action-button--primary" href="#" id="product-seller-document-download"
                        download>Download</a>
                    <button class="button" type="button" data-close-modal="product-seller-document-modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('styles')
    <style>
        .status-tabs {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .status-tabs .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
        }

        .status-tabs .chip strong {
            font-size: 0.92rem;
        }

        .moderation-header {
            gap: 1rem;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .moderation-filters {
            justify-content: flex-end;
            flex: 1 1 42rem;
        }

        .inline-select select,
        .filter-input input,
        .reason-textarea textarea {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text);
        }

        .inline-select select {
            min-width: 9rem;
            cursor: pointer;
        }

        .filter-input--compact {
            min-width: 8rem;
        }

        .filter-input span,
        .inline-select span,
        .reason-textarea span {
            color: var(--muted);
            font-size: 0.84rem;
            font-weight: 600;
        }

        .filter-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .bulk-toolbar {
            padding: 0.95rem 1.25rem;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            background: var(--surface-soft);
        }

        .bulk-toolbar__selection {
            font-weight: 700;
            color: #44506a;
        }

        .bulk-toolbar__actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .selection-checkbox {
            width: 1.1rem;
            height: 1.1rem;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .product-thumb-button,
        .product-image-button,
        .thumb-image-button {
            border: 0;
            padding: 0;
            cursor: pointer;
            background: transparent;
        }

        .product-thumb-button {
            width: 3.4rem;
            height: 3.4rem;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #eef3fb, #f8faff);
            font-weight: 700;
            color: #5d6b84;
        }

        .product-thumb-button img,
        .product-image-button img,
        .thumb-image-button img,
        .image-preview-shell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .thumb-image-button {
            width: 4.25rem;
            height: 4.25rem;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, #eef3fb, #f8faff);
        }

        .hero-thumb {
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #eef3fb, #f8faff);
        }

        .hero-thumb::before {
            content: none;
        }

        .product-image-fallback {
            font-size: 2.6rem;
            font-weight: 700;
            color: #5d6b84;
        }

        .detail-list__item strong {
            text-align: right;
            max-width: 65%;
        }

        .detail-list__item--top {
            align-items: flex-start;
        }

        .detail-list__item--top strong {
            white-space: pre-wrap;
        }

        .status-stack {
            display: grid;
            gap: 0.45rem;
            justify-items: start;
        }

        .empty-table {
            text-align: center;
            padding: 2rem 1rem;
        }

        .reason-grid {
            display: grid;
            gap: 0.8rem;
        }

        .reason-option {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            cursor: pointer;
        }

        .reason-option input {
            margin: 0;
            accent-color: var(--danger);
        }

        .reason-option span {
            font-weight: 600;
        }

        .reason-textarea {
            display: grid;
            gap: 0.55rem;
            margin-top: 1rem;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.95rem 1rem;
        }

        .reason-textarea textarea {
            resize: vertical;
            min-height: 5.5rem;
        }

        .image-preview-shell {
            width: 100%;
            max-height: 70vh;
            overflow: hidden;
            border-radius: 18px;
            background: #0f1728;
        }

        .image-preview-shell img {
            max-height: 70vh;
        }

        .seller-products-list {
            display: grid;
            gap: 0.9rem;
        }

        .seller-product-row {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.9rem;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 0.9rem;
        }

        .seller-product-thumb {
            width: 4rem;
            height: 4rem;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, #eef3fb, #f8faff);
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #5d6b84;
        }

        .seller-product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .seller-product-meta {
            display: grid;
            gap: 0.35rem;
        }

        .seller-product-meta-row {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .tab-panel {
            margin-top: 1rem;
        }

        .seller-document-detail-list {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .seller-document-preview-shell {
            width: 100%;
            min-height: 18rem;
            max-height: 65vh;
            border-radius: 16px;
            overflow: auto;
            border: 1px solid var(--border);
            background: #f7f9fc;
            display: grid;
            place-items: center;
            padding: 1rem;
        }

        .seller-document-preview-shell img,
        .seller-document-preview-shell iframe {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: calc(65vh - 2rem);
            border: 0;
            display: block;
        }

        .seller-document-preview-shell iframe {
            width: 100%;
            min-height: calc(65vh - 2rem);
            background: #fff;
        }

        .seller-document-empty {
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        @media (max-width: 960px) {
            .moderation-filters {
                justify-content: stretch;
            }

            .moderation-filters > * {
                flex: 1 1 12rem;
            }

            .detail-list__item {
                flex-direction: column;
                align-items: flex-start;
            }

            .detail-list__item strong {
                max-width: none;
                text-align: left;
            }

            .seller-product-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .bulk-toolbar {
                align-items: stretch;
            }

            .bulk-toolbar__actions,
            .filter-actions {
                width: 100%;
            }

            .bulk-toolbar__actions > *,
            .filter-actions > * {
                flex: 1 1 0;
                justify-content: center;
            }

            .status-tabs .chip {
                flex: 1 1 calc(50% - 0.4rem);
                justify-content: space-between;
            }

            .product-thumb-button {
                width: 3rem;
                height: 3rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const products = @json($productModalData);
            const byId = Object.fromEntries(products.map((product) => [String(product.id), product]));
            const bulkRoute = @json(route('admin.products.bulk'));
            const selectAll = document.getElementById('select-all-products');
            const productCheckboxes = Array.from(document.querySelectorAll('.product-select'));
            const bulkSelectionCount = document.getElementById('bulk-selection-count');
            const bulkApproveButton = document.getElementById('bulk-approve-button');
            const bulkRejectButton = document.getElementById('bulk-reject-button');
            const bulkApproveForm = document.getElementById('bulk-approve-form');
            const bulkApproveIds = document.getElementById('bulk-approve-ids');
            const rejectModalForm = document.getElementById('reject-modal-form');
            const rejectModalAction = document.getElementById('reject-modal-action');
            const rejectModalProductIds = document.getElementById('reject-modal-product-ids');
            const rejectModalTitle = document.getElementById('reject-modal-title');
            const productModalApproveForm = document.getElementById('product-modal-approve-form');
            const productModalRejectButton = document.getElementById('product-modal-reject-button');
            const productModalHeroButton = document.getElementById('product-modal-hero-button');
            const productModalHeroImage = document.getElementById('product-modal-hero-image');
            const productModalHeroFallback = document.getElementById('product-modal-hero-fallback');
            const imagePreviewTitle = document.getElementById('image-preview-title');
            const imagePreviewImage = document.getElementById('image-preview-image');
            const sellerTabButtons = Array.from(document.querySelectorAll('[data-seller-tab]'));
            const sellerTabPanels = Array.from(document.querySelectorAll('[data-seller-panel]'));
            const sellerProductsList = document.getElementById('product-seller-products-list');
            const sellerDocumentTitle = document.getElementById('product-seller-document-title');
            const sellerDocumentType = document.getElementById('product-seller-document-type');
            const sellerDocumentSeller = document.getElementById('product-seller-document-seller');
            const sellerDocumentDate = document.getElementById('product-seller-document-date');
            const sellerDocumentStatus = document.getElementById('product-seller-document-status');
            const sellerDocumentPreviewStage = document.getElementById('product-seller-document-preview-stage');
            const sellerDocumentDownload = document.getElementById('product-seller-document-download');
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

            function getSelectedIds() {
                return productCheckboxes.filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value);
            }

            function fillIds(container, ids) {
                container.innerHTML = '';
                ids.forEach((id) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'product_ids[]';
                    input.value = id;
                    container.appendChild(input);
                });
            }

            function updateBulkState() {
                const selectedIds = getSelectedIds();
                const count = selectedIds.length;
                bulkSelectionCount.textContent = `${count} selected`;
                bulkApproveButton.disabled = count === 0;
                bulkRejectButton.disabled = count === 0;
                if (selectAll) {
                    selectAll.checked = count > 0 && count === productCheckboxes.length;
                    selectAll.indeterminate = count > 0 && count < productCheckboxes.length;
                }
            }

            function openImagePreview(src, title) {
                if (!src) return;
                imagePreviewTitle.textContent = title || 'Product Image';
                imagePreviewImage.src = src;
                imagePreviewImage.alt = title || 'Product Image';
                openModal('image-preview-modal');
            }

            function fileExtension(url) {
                try {
                    return new URL(url, window.location.origin).pathname.split('.').pop().toLowerCase();
                } catch (error) {
                    return '';
                }
            }

            function activateSellerTab(tab) {
                sellerTabButtons.forEach((button) => {
                    button.classList.toggle('is-active', button.dataset.sellerTab === tab);
                });

                sellerTabPanels.forEach((panel) => {
                    panel.hidden = panel.dataset.sellerPanel !== tab;
                });
            }

            function setImage(button, image, fallback, src, label) {
                button.dataset.imagePreview = src || '';
                button.dataset.imageTitle = label || 'Product Image';

                if (src) {
                    image.src = src;
                    image.alt = label || 'Product image';
                    image.hidden = false;
                    fallback.hidden = true;
                    fallback.textContent = '';
                } else {
                    image.removeAttribute('src');
                    image.alt = '';
                    image.hidden = true;
                    fallback.hidden = false;
                    fallback.textContent = (label || '?').trim().charAt(0).toUpperCase();
                }
            }

            function renderThumbs(product) {
                const thumbs = document.getElementById('product-modal-thumbs');
                thumbs.innerHTML = '';

                const thumb = document.createElement('button');
                thumb.type = 'button';
                thumb.className = 'thumb-image-button';
                thumb.dataset.imagePreview = product.image_url || '';
                thumb.dataset.imageTitle = product.name;

                if (product.image_url) {
                    const image = document.createElement('img');
                    image.src = product.image_url;
                    image.alt = product.name;
                    thumb.appendChild(image);
                } else {
                    const fallback = document.createElement('span');
                    fallback.className = 'product-image-fallback';
                    fallback.textContent = product.name.charAt(0).toUpperCase();
                    thumb.appendChild(fallback);
                }

                thumbs.appendChild(thumb);
            }

            function renderSellerProducts(products) {
                sellerProductsList.innerHTML = '';

                if (!products || products.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'sub-line empty-table';
                    empty.textContent = 'No products yet.';
                    sellerProductsList.appendChild(empty);
                    return;
                }

                products.forEach((product) => {
                    const row = document.createElement('div');
                    row.className = 'seller-product-row';

                    const thumb = document.createElement('button');
                    thumb.type = 'button';
                    thumb.className = 'seller-product-thumb';
                    thumb.dataset.imagePreview = product.image_url || '';
                    thumb.dataset.imageTitle = product.name;

                    if (product.image_url) {
                        const image = document.createElement('img');
                        image.src = product.image_url;
                        image.alt = product.name;
                        thumb.appendChild(image);
                    } else {
                        thumb.textContent = product.name.charAt(0).toUpperCase();
                    }

                    const body = document.createElement('div');
                    body.className = 'seller-product-meta';
                    body.innerHTML = `
                        <div class="product-title">${product.name}</div>
                        <div class="seller-product-meta-row">
                            <span>${product.category}</span>
                            <strong>${product.price}</strong>
                            <span>Stock ${product.stock}</span>
                        </div>
                        <div class="seller-product-meta-row">
                            <span class="status-pill ${product.status_class}">${product.status_label}</span>
                            <span>${product.date_added}</span>
                        </div>
                    `;

                    row.appendChild(thumb);
                    row.appendChild(body);
                    sellerProductsList.appendChild(row);
                });
            }

            function openSellerDocumentModal(config) {
                if (!config.url) return;

                sellerDocumentTitle.textContent = config.title;
                sellerDocumentType.textContent = config.type;
                sellerDocumentSeller.textContent = config.sellerName;
                sellerDocumentDate.textContent = config.uploadDate;
                sellerDocumentStatus.textContent = config.status;
                sellerDocumentDownload.href = config.url;
                sellerDocumentDownload.setAttribute('download', config.filename || config.type || 'document');

                const extension = fileExtension(config.url);
                const isPdf = extension === 'pdf';

                sellerDocumentPreviewStage.innerHTML = isPdf ?
                    `<iframe src="${config.url}" title="${config.type}"></iframe>` :
                    `<img src="${config.url}" alt="${config.type}">`;

                openModal('product-seller-document-modal');
            }

            function openRejectModal(config) {
                rejectModalForm.reset();
                rejectModalProductIds.innerHTML = '';
                rejectModalAction.value = '';

                if (config.mode === 'bulk') {
                    rejectModalForm.action = bulkRoute;
                    rejectModalAction.value = 'reject';
                    fillIds(rejectModalProductIds, config.ids);
                    rejectModalTitle.textContent = 'Reject Selected Products';
                } else {
                    rejectModalForm.action = config.url;
                    rejectModalTitle.textContent = `Reject ${config.name}`;
                }

                openModal('reject-reason-modal');
            }

            function renderProductModal(product) {
                activeProduct = product;

                document.getElementById('product-modal-title').textContent = product.name;
                document.getElementById('product-modal-category').textContent = product.category;
                document.getElementById('product-modal-shop').textContent = product.shop_name;
                document.getElementById('product-modal-status').textContent = product.status_label;
                document.getElementById('product-modal-submitted').textContent = product.submitted_at;
                document.getElementById('product-modal-price').textContent = product.price;
                document.getElementById('product-modal-shipping').textContent = product.shipping_fee;
                document.getElementById('product-modal-stock').textContent = product.stock;
                document.getElementById('product-modal-condition').textContent = product.condition;
                document.getElementById('product-modal-dimensions').textContent = product.dimensions;
                document.getElementById('product-modal-weight').textContent = product.weight;
                document.getElementById('product-modal-description').textContent = product.description;
                document.getElementById('product-modal-reports').textContent = `${product.pending_reports_count}`;
                document.getElementById('product-modal-rejection').textContent = product.rejection_reason;
                document.getElementById('product-modal-seller-avatar').textContent = product.avatar;
                document.getElementById('product-modal-seller-handle').textContent = product.seller;
                document.getElementById('product-modal-seller-name').textContent = product.seller_name;

                setImage(productModalHeroButton, productModalHeroImage, productModalHeroFallback, product.image_url,
                    product.name);
                renderThumbs(product);

                productModalApproveForm.action = product.approve_url;
                productModalApproveForm.hidden = !product.can_approve;
                productModalRejectButton.hidden = !product.can_reject;
                productModalRejectButton.dataset.rejectUrl = product.reject_url;
                productModalRejectButton.dataset.rejectName = product.name;

                openModal('product-approval-modal');
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

            document.querySelectorAll('[data-product-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    const product = byId[button.dataset.productView];
                    if (product) {
                        renderProductModal(product);
                    }
                });
            });

            document.querySelectorAll('[data-reject-url]').forEach((button) => {
                button.addEventListener('click', () => {
                    openRejectModal({
                        mode: 'single',
                        url: button.dataset.rejectUrl,
                        name: button.dataset.rejectName || 'Product',
                    });
                });
            });

            sellerTabButtons.forEach((button) => {
                button.addEventListener('click', () => activateSellerTab(button.dataset.sellerTab));
            });

            document.addEventListener('click', (event) => {
                const previewTrigger = event.target.closest('[data-image-preview]');
                if (!previewTrigger) return;

                const src = previewTrigger.dataset.imagePreview;
                const title = previewTrigger.dataset.imageTitle;
                if (src) {
                    openImagePreview(src, title);
                }
            });

            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    productCheckboxes.forEach((checkbox) => {
                        checkbox.checked = selectAll.checked;
                    });
                    updateBulkState();
                });
            }

            productCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateBulkState);
            });

            bulkApproveButton.addEventListener('click', () => {
                const ids = getSelectedIds();
                if (ids.length === 0) return;
                fillIds(bulkApproveIds, ids);
                bulkApproveForm.submit();
            });

            bulkRejectButton.addEventListener('click', () => {
                const ids = getSelectedIds();
                if (ids.length === 0) return;
                openRejectModal({
                    mode: 'bulk',
                    ids,
                });
            });

            productModalRejectButton.addEventListener('click', () => {
                if (!activeProduct) return;
                closeModal('product-approval-modal');
                openRejectModal({
                    mode: 'single',
                    url: activeProduct.reject_url,
                    name: activeProduct.name,
                });
            });

            productModalHeroButton.addEventListener('click', () => {
                if (!activeProduct) return;
                openImagePreview(activeProduct.image_url, activeProduct.name);
            });

            document.getElementById('product-modal-open-seller').addEventListener('click', () => {
                if (!activeProduct) return;

                activateSellerTab('shop');
                document.getElementById('product-seller-modal-handle').textContent = activeProduct.seller;
                document.getElementById('product-seller-modal-avatar').textContent = activeProduct.avatar;
                document.getElementById('product-seller-modal-username').textContent = activeProduct.shop_name;
                document.getElementById('product-seller-modal-fullname').textContent = activeProduct.seller_name;
                document.getElementById('product-seller-shop-name').textContent = activeProduct.shop_name;
                document.getElementById('product-seller-owner-name').textContent = activeProduct.seller_owner_name;
                document.getElementById('product-seller-email').textContent = activeProduct.seller_email;
                document.getElementById('product-seller-phone').textContent = activeProduct.seller_phone;
                document.getElementById('product-seller-address').textContent = activeProduct.seller_address;
                document.getElementById('product-seller-description').textContent = activeProduct.seller_description;
                document.getElementById('product-seller-status-text').textContent = activeProduct.seller_status_label;
                document.getElementById('product-seller-registered').textContent = activeProduct.seller_registered_at;
                document.getElementById('product-seller-verification').textContent = activeProduct.seller_verification_status;

                const sellerStatus = document.getElementById('product-seller-modal-status');
                sellerStatus.className = `status-pill ${activeProduct.seller_status_class}`;
                sellerStatus.textContent = activeProduct.seller_status_label;

                document.getElementById('product-seller-id-label').textContent = activeProduct.seller_id_type;
                document.getElementById('product-seller-id-meta').textContent =
                    `Uploaded ${activeProduct.seller_submitted_at}`;
                document.getElementById('product-seller-permit-meta').textContent =
                    activeProduct.seller_permit_url ? `Uploaded ${activeProduct.seller_submitted_at}` :
                    'Optional / Not uploaded';

                const idLink = document.getElementById('product-seller-id-link');
                const permitLink = document.getElementById('product-seller-permit-link');
                renderSellerProducts(activeProduct.seller_products);

                if (activeProduct.seller_id_url) {
                    idLink.disabled = false;
                } else {
                    idLink.disabled = true;
                }

                if (activeProduct.seller_permit_url) {
                    permitLink.disabled = false;
                } else {
                    permitLink.disabled = true;
                }

                openModal('product-seller-modal');
            });

            document.getElementById('product-seller-id-link').addEventListener('click', () => {
                if (!activeProduct || !activeProduct.seller_id_url) return;

                openSellerDocumentModal({
                    title: 'Document Preview',
                    type: activeProduct.seller_id_type,
                    sellerName: activeProduct.seller_owner_name,
                    uploadDate: activeProduct.seller_submitted_at,
                    status: activeProduct.seller_status_label,
                    url: activeProduct.seller_id_url,
                    filename: `${activeProduct.seller_owner_name || 'seller'}-${activeProduct.seller_id_type || 'document'}`,
                });
            });

            document.getElementById('product-seller-permit-link').addEventListener('click', () => {
                if (!activeProduct || !activeProduct.seller_permit_url) return;

                openSellerDocumentModal({
                    title: 'Document Preview',
                    type: 'Business License / Permit',
                    sellerName: activeProduct.seller_owner_name,
                    uploadDate: activeProduct.seller_submitted_at,
                    status: activeProduct.seller_status_label,
                    url: activeProduct.seller_permit_url,
                    filename: `${activeProduct.seller_owner_name || 'seller'}-business-license`,
                });
            });

            updateBulkState();
        })();
    </script>
@endpush
