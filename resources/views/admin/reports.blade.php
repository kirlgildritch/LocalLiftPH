@extends('layouts.admin')

@section('title', 'Reports')
@section('eyebrow', 'Trust & Safety')
@section('page-title', 'Reports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/reports.css') }}">
    <style>
        .report-detail-grid {
            display: grid;
            gap: 1.25rem;
        }

        .report-history-list,
        .report-action-stack {
            display: grid;
            gap: 0.85rem;
        }

        .report-history-item {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.95rem 1rem;
            background: var(--surface-soft);
        }

        .report-history-item strong,
        .report-history-item p {
            margin: 0;
        }

        .report-history-meta {
            margin-top: 0.45rem;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .report-admin-notes {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.95rem 1rem;
            resize: vertical;
            min-height: 6.5rem;
            background: var(--surface);
            color: var(--text);
        }

        .report-action-toolbar {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .report-action-toolbar .action-button,
        .report-action-toolbar .button {
            justify-content: center;
        }

        .report-summary-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem;
            display: grid;
            gap: 1rem;
        }

        .report-target-modal-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }

        .report-preview-image {
            width: 100%;
            min-height: 17rem;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, #eef4ff, #f8faff);
        }

        .report-preview-fallback {
            width: 100%;
            min-height: 17rem;
            border-radius: 18px;
            display: grid;
            place-items: center;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, #eef4ff, #f8faff);
            color: #3b6fd6;
            font-size: 3rem;
            font-weight: 700;
        }

        .report-seller-summary {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem;
            display: grid;
            gap: 1rem;
        }

        .report-doc-grid {
            display: grid;
            gap: 0.8rem;
        }

        .report-doc-row {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
        }

        .status-pill--dismissed {
            background: #f1f5f9;
            color: #475569;
        }

        @media (max-width: 900px) {
            .report-target-modal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $pendingCount = $reports->where('status', \App\Models\Report::STATUS_PENDING)->count();
        $resolvedCount = $reports->where('status', \App\Models\Report::STATUS_RESOLVED)->count();
        $dismissedCount = $reports->where('status', \App\Models\Report::STATUS_DISMISSED)->count();

        $reportStatusClass = function ($report) {
            return match ($report->status) {
                \App\Models\Report::STATUS_RESOLVED => 'resolved',
                \App\Models\Report::STATUS_DISMISSED => 'dismissed',
                default => 'pending',
            };
        };

        $productStatusClass = function ($product) {
            if ($product->status === \App\Models\Product::STATUS_APPROVED && ! $product->is_active) {
                return ['label' => 'Delisted', 'class' => 'status-pill--delivered'];
            }

            return match ($product->status) {
                \App\Models\Product::STATUS_APPROVED => ['label' => 'Approved', 'class' => 'status-pill--success'],
                \App\Models\Product::STATUS_REJECTED => ['label' => 'Violation', 'class' => 'status-pill--cancelled'],
                default => ['label' => 'Pending', 'class' => 'status-pill--pending'],
            };
        };

        $sellerStatusClass = function ($sellerProfile, $flagged = false) {
            if ($sellerProfile?->suspended_at) {
                return ['label' => 'Suspended', 'class' => 'status-pill--cancelled'];
            }

            if ($flagged) {
                return ['label' => 'Flagged', 'class' => 'status-pill--danger'];
            }

            return match ($sellerProfile?->application_status) {
                \App\Models\Seller::STATUS_APPROVED => ['label' => 'Active', 'class' => 'status-pill--success'],
                \App\Models\Seller::STATUS_REJECTED => ['label' => 'Rejected', 'class' => 'status-pill--cancelled'],
                default => ['label' => 'Pending Review', 'class' => 'status-pill--pending'],
            };
        };

        $money = fn ($value) => 'PHP ' . number_format((float) $value, 2);

        $reportModalData = $reports
            ->map(function ($report) use ($reportStatusClass, $productStatusClass, $sellerStatusClass, $money) {
                $product = $report->product;
                $sellerUser = $report->seller ?: $product?->user;
                $sellerProfile = $sellerUser?->sellerProfile;
                $sellerName = $sellerProfile?->store_name ?: $sellerUser?->name ?: 'Seller unavailable';
                $reportStatus = $reportStatusClass($report);
                $flagged = $sellerUser?->sellerReports?->where('status', \App\Models\Report::STATUS_PENDING)->isNotEmpty() ?? false;

                $productData = null;
                if ($product) {
                    $productStatus = $productStatusClass($product);
                    $productData = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                        'category' => $product->category->name ?? 'Uncategorized',
                        'price' => $money($product->price),
                        'stock' => (string) $product->stock,
                        'condition' => $product->condition ? ucfirst((string) $product->condition) : 'Not set',
                        'shipping_fee' => $money($product->shipping_fee),
                        'description' => $product->description ?: 'No description provided.',
                        'status_label' => $productStatus['label'],
                        'status_class' => $productStatus['class'],
                        'seller_name' => $sellerName,
                        'reports_count' => $product->reports->count(),
                    ];
                }

                $sellerData = null;
                if ($sellerUser) {
                    $sellerState = $sellerStatusClass($sellerProfile, $flagged);
                    $sellerData = [
                        'id' => $sellerUser->id,
                        'shop_name' => $sellerProfile?->store_name ?: $sellerUser->name,
                        'owner_name' => $sellerProfile?->full_name ?: $sellerUser->name,
                        'email' => $sellerProfile?->email ?: $sellerUser->email ?: 'N/A',
                        'phone' => $sellerProfile?->contact_number ?: $sellerUser->phone ?: 'N/A',
                        'address' => $sellerProfile?->address ?: $sellerUser->address ?: 'N/A',
                        'description' => $sellerProfile?->store_description ?: 'No description provided.',
                        'status_label' => $sellerState['label'],
                        'status_class' => $sellerState['class'],
                        'products_count' => $sellerUser->products->count(),
                        'suspension_reason' => $sellerProfile?->suspension_reason ?: 'None',
                        'valid_id_type' => $sellerProfile?->valid_id_type ?: 'ID / Passport',
                        'valid_id_url' => $sellerProfile?->valid_id_path ? asset('storage/' . $sellerProfile->valid_id_path) : null,
                        'business_permit_url' => $sellerProfile?->business_permit_path ? asset('storage/' . $sellerProfile->business_permit_path) : null,
                    ];
                }

                return [
                    'id' => $report->id,
                    'target_name' => $report->targetLabel(),
                    'target_type' => $product ? 'Product' : 'Seller',
                    'seller_name' => $sellerName,
                    'reason_label' => $report->reasonLabel(),
                    'reporter_name' => $report->user?->name ?? 'Deleted user',
                    'submitted_at' => $report->created_at?->format('M d, Y h:i A') ?? 'Unknown',
                    'status_label' => $report->statusLabel(),
                    'status_class' => $reportStatus,
                    'message' => $report->message ?: 'No additional details were provided by the reporter.',
                    'is_final' => $report->status !== \App\Models\Report::STATUS_PENDING,
                    'product' => $productData,
                    'seller' => $sellerData,
                    'actions' => $report->actions->map(fn ($action) => [
                        'label' => $action->actionLabel(),
                        'notes' => $action->admin_notes ?: 'No notes added.',
                        'handled_by' => $action->admin?->name ?? 'Admin',
                        'handled_at' => $action->handled_at?->format('M d, Y h:i A') ?? 'Unknown',
                    ])->values(),
                    'action_url' => route('admin.reports.action', $report),
                ];
            })
            ->values();
    @endphp

    <div class="page-stack">
        @if (session('success'))
            <div class="admin-report-feedback">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="admin-report-feedback" style="background:#fff0f0;border-color:#f0c9c9;color:#b12f2f;">
                {{ $errors->first() }}</div>
        @endif

        <div class="filter-bar">
            <div class="chip is-active">All {{ $reports->count() }}</div>
            <div class="chip">Pending {{ $pendingCount }}</div>
            <div class="chip">Resolved {{ $resolvedCount }}</div>
            <div class="chip">Dismissed {{ $dismissedCount }}</div>
        </div>

        <article class="table-card">
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reported Item</th>
                            <th>Reason</th>
                            <th>Reporter</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            @php
                                $targetName = $report->targetLabel();
                                $sellerName = $report->seller?->sellerProfile?->store_name
                                    ?: $report->seller?->name
                                    ?: $report->product?->user?->sellerProfile?->store_name
                                    ?: $report->product?->user?->name
                                    ?: 'Seller unavailable';
                                $statusClass = $reportStatusClass($report);
                                $targetType = $report->product ? 'Product' : 'Seller';
                            @endphp
                            <tr>
                                <td>
                                    <div class="report-product-cell">
                                        <div class="report-thumb-icon">
                                            <i class="fa-solid fa-{{ $report->product ? 'box-open' : 'store' }}"></i>
                                        </div>
                                        <div class="report-product-cell__text">
                                            <div class="report-product-name">{{ $targetName }}</div>
                                            <div class="sub-line">{{ $targetType }} | {{ $sellerName }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="type-badge">{{ $report->reasonLabel() }}</span></td>
                                <td>
                                    <div class="muted-row"><i class="fa-solid fa-user"></i>
                                        {{ $report->user?->name ?? 'Deleted user' }}</div>
                                </td>
                                <td>{{ $report->created_at?->format('M d, Y') }}</td>
                                <td><span class="status-pill status-pill--{{ $statusClass }}">{{ $report->statusLabel() }}</span>
                                </td>
                                <td>
                                    <button class="action-button action-button--primary" type="button"
                                        data-report-view="{{ $report->id }}">
                                        <i class="fa-solid fa-magnifying-glass"></i> View Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-text">No reports submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
@endsection

@push('modals')
    <div class="modal-shell" id="report-detail-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title">Report Details</h3>
                <button class="modal-close" type="button" data-close-modal="report-detail-modal">&times;</button>
            </div>
            <div class="modal-card__body report-detail-grid">
                <div class="report-summary">
                    <div class="report-thumb-icon report-thumb-icon--large" id="report-detail-thumb">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div>
                        <div class="seller-name" id="report-detail-target"></div>
                        <div class="muted-row"><i class="fa-solid fa-user"></i> <span id="report-detail-seller"></span></div>
                    </div>
                </div>

                <div class="report-meta">
                    <div class="report-meta-row">
                        <div class="seller-name">Target:</div>
                        <div id="report-detail-type"></div>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Reporter:</div>
                        <div id="report-detail-reporter"></div>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Reason:</div>
                        <div id="report-detail-reason"></div>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Submitted Date:</div>
                        <div id="report-detail-submitted-date"></div>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Status:</div>
                        <span class="status-pill status-pill--pending" id="report-detail-status"></span>
                    </div>
                </div>

                <div class="report-summary-card">
                    <h4 class="section-title">Reporter Message</h4>
                    <p style="margin: 0;" id="report-detail-message"></p>
                </div>

                <div class="report-summary-card">
                    <h4 class="section-title">Action History</h4>
                    <div class="report-history-list" id="report-history-list"></div>
                </div>

                <form method="POST" id="report-action-form" class="report-action-stack">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" id="report-action-input">

                    <label>
                        <span class="section-title" style="display:block;margin-bottom:0.75rem;">Admin Notes</span>
                        <textarea class="report-admin-notes" name="admin_notes" id="report-admin-notes"
                            placeholder="Add admin notes"></textarea>
                    </label>
                </form>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="report-detail-modal">Close</button>
                <div class="report-action-toolbar" id="report-action-toolbar"></div>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="report-product-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title">Reported Product</h3>
                <button class="modal-close" type="button" data-close-modal="report-product-modal">&times;</button>
            </div>
            <div class="modal-card__body report-target-modal-grid">
                <div id="report-product-preview"></div>
                <div class="detail-list">
                    <div class="detail-list__item"><span>Name</span><strong id="report-product-name"></strong></div>
                    <div class="detail-list__item"><span>Category</span><strong id="report-product-category"></strong></div>
                    <div class="detail-list__item"><span>Seller</span><strong id="report-product-seller"></strong></div>
                    <div class="detail-list__item"><span>Price</span><strong id="report-product-price"></strong></div>
                    <div class="detail-list__item"><span>Stock</span><strong id="report-product-stock"></strong></div>
                    <div class="detail-list__item"><span>Condition</span><strong id="report-product-condition"></strong></div>
                    <div class="detail-list__item"><span>Shipping Fee</span><strong id="report-product-shipping"></strong></div>
                    <div class="detail-list__item"><span>Status</span><strong id="report-product-status"></strong></div>
                    <div class="detail-list__item"><span>Reports</span><strong id="report-product-reports"></strong></div>
                    <div class="detail-list__item detail-list__item--top"><span>Description</span><strong
                            id="report-product-description"></strong></div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="report-product-modal">Close</button>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="report-seller-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title">Reported Seller</h3>
                <button class="modal-close" type="button" data-close-modal="report-seller-modal">&times;</button>
            </div>
            <div class="modal-card__body report-target-modal-grid">
                <div class="report-seller-summary">
                    <div class="seller-box__profile">
                        <div class="avatar-circle" id="report-seller-avatar"></div>
                        <div>
                            <div class="seller-name" id="report-seller-shop-name"></div>
                            <div class="sub-line" id="report-seller-owner-name"></div>
                        </div>
                    </div>
                    <div class="report-doc-grid">
                        <div class="report-doc-row">
                            <div>
                                <div class="seller-name" id="report-seller-id-type">ID / Passport</div>
                                <div class="sub-line">Verification document</div>
                            </div>
                            <button class="action-button action-button--primary" type="button"
                                id="report-seller-id-view">View</button>
                        </div>
                        <div class="report-doc-row">
                            <div>
                                <div class="seller-name">Business License</div>
                                <div class="sub-line">Business permit</div>
                            </div>
                            <button class="action-button action-button--primary" type="button"
                                id="report-seller-permit-view">View</button>
                        </div>
                    </div>
                </div>
                <div class="detail-list">
                    <div class="detail-list__item"><span>Email</span><strong id="report-seller-email"></strong></div>
                    <div class="detail-list__item"><span>Phone</span><strong id="report-seller-phone"></strong></div>
                    <div class="detail-list__item detail-list__item--top"><span>Address</span><strong
                            id="report-seller-address"></strong></div>
                    <div class="detail-list__item"><span>Status</span><strong id="report-seller-status"></strong></div>
                    <div class="detail-list__item"><span>Products</span><strong id="report-seller-products-count"></strong>
                    </div>
                    <div class="detail-list__item detail-list__item--top"><span>Description</span><strong
                            id="report-seller-description"></strong></div>
                    <div class="detail-list__item detail-list__item--top"><span>Suspension Reason</span><strong
                            id="report-seller-suspension-reason"></strong></div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="report-seller-modal">Close</button>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="report-document-modal" hidden>
        <div class="modal-card modal-card--document">
            <div class="modal-card__header">
                <h3 class="modal-title" id="report-document-title">Document Preview</h3>
                <button class="modal-close" type="button" data-close-modal="report-document-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="document-preview-shell">
                    <div class="seller-name" id="report-document-label">Document</div>
                    <div class="document-preview-stage" id="report-document-stage"></div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="report-document-modal">Close</button>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
            const reports = @json($reportModalData);
            const byId = Object.fromEntries(reports.map((report) => [String(report.id), report]));
            let activeReport = null;

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

            function fileExtension(url) {
                try {
                    return new URL(url, window.location.origin).pathname.split('.').pop().toLowerCase();
                } catch (error) {
                    return '';
                }
            }

            function openDocumentModal(title, label, url) {
                if (!url) return;

                const stage = document.getElementById('report-document-stage');
                const isPdf = fileExtension(url) === 'pdf';
                document.getElementById('report-document-title').textContent = title;
                document.getElementById('report-document-label').textContent = label;
                stage.innerHTML = isPdf ?
                    `<iframe src="${url}" class="document-preview-frame" title="${label}"></iframe>` :
                    `<img src="${url}" alt="${label}" class="document-preview-image">`;

                openModal('report-document-modal');
            }

            function renderHistory(actions) {
                const list = document.getElementById('report-history-list');
                list.innerHTML = '';

                if (!actions || actions.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'report-history-item';
                    empty.innerHTML = '<strong>No actions yet.</strong>';
                    list.appendChild(empty);
                    return;
                }

                actions.forEach((action) => {
                    const item = document.createElement('div');
                    item.className = 'report-history-item';
                    item.innerHTML = `
                        <strong>${action.label}</strong>
                        <p>${action.notes}</p>
                        <div class="report-history-meta">Handled by ${action.handled_by} on ${action.handled_at}</div>
                    `;
                    list.appendChild(item);
                });
            }

            function submitReportAction(action) {
                const form = document.getElementById('report-action-form');
                document.getElementById('report-action-input').value = action;
                form.submit();
            }

            function renderActionToolbar(report) {
                const toolbar = document.getElementById('report-action-toolbar');
                toolbar.innerHTML = '';

                const addButton = (label, className, onClick) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = className;
                    button.textContent = label;
                    button.addEventListener('click', onClick);
                    toolbar.appendChild(button);
                };

                if (report.target_type === 'Product') {
                    addButton('View Product', 'action-button action-button--primary', () => openProductModal(report));
                } else {
                    addButton('View Seller', 'action-button action-button--primary', () => openSellerModal(report));
                }

                if (report.is_final) {
                    return;
                }

                addButton('Warn Seller', 'action-button action-button--warning', () => submitReportAction('warn_seller'));

                if (report.target_type === 'Product') {
                    addButton('Hide / Delist Product', 'action-button action-button--light', () => submitReportAction(
                        'delist_product'));
                    addButton('Ban / Remove Product', 'action-button action-button--danger', () => submitReportAction(
                        'ban_product'));
                }

                addButton('Suspend Seller Account', 'action-button action-button--danger', () => submitReportAction(
                    'suspend_seller'));
                addButton('Dismiss Report', 'button', () => submitReportAction('dismiss_report'));
                addButton('Mark as Resolved', 'action-button action-button--success', () => submitReportAction(
                    'mark_resolved'));
            }

            function openProductModal(report) {
                const product = report.product;
                if (!product) return;

                const preview = document.getElementById('report-product-preview');
                preview.innerHTML = product.image_url ?
                    `<img src="${product.image_url}" alt="${product.name}" class="report-preview-image">` :
                    `<div class="report-preview-fallback">${product.name.charAt(0).toUpperCase()}</div>`;

                document.getElementById('report-product-name').textContent = product.name;
                document.getElementById('report-product-category').textContent = product.category;
                document.getElementById('report-product-seller').textContent = product.seller_name;
                document.getElementById('report-product-price').textContent = product.price;
                document.getElementById('report-product-stock').textContent = product.stock;
                document.getElementById('report-product-condition').textContent = product.condition;
                document.getElementById('report-product-shipping').textContent = product.shipping_fee;
                document.getElementById('report-product-status').textContent = product.status_label;
                document.getElementById('report-product-reports').textContent = `${product.reports_count}`;
                document.getElementById('report-product-description').textContent = product.description;
                openModal('report-product-modal');
            }

            function openSellerModal(report) {
                const seller = report.seller;
                if (!seller) return;

                document.getElementById('report-seller-avatar').textContent = seller.shop_name.charAt(0).toUpperCase();
                document.getElementById('report-seller-shop-name').textContent = seller.shop_name;
                document.getElementById('report-seller-owner-name').textContent = seller.owner_name;
                document.getElementById('report-seller-email').textContent = seller.email;
                document.getElementById('report-seller-phone').textContent = seller.phone;
                document.getElementById('report-seller-address').textContent = seller.address;
                document.getElementById('report-seller-status').textContent = seller.status_label;
                document.getElementById('report-seller-products-count').textContent = `${seller.products_count}`;
                document.getElementById('report-seller-description').textContent = seller.description;
                document.getElementById('report-seller-suspension-reason').textContent = seller.suspension_reason;
                document.getElementById('report-seller-id-type').textContent = seller.valid_id_type;

                const idView = document.getElementById('report-seller-id-view');
                const permitView = document.getElementById('report-seller-permit-view');
                idView.disabled = !seller.valid_id_url;
                permitView.disabled = !seller.business_permit_url;

                openModal('report-seller-modal');
            }

            document.querySelectorAll('[data-close-modal]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.dataset.closeModal));
            });

            document.querySelectorAll('.modal-shell').forEach((shell) => {
                shell.addEventListener('click', (event) => {
                    if (event.target === shell) closeModal(shell.id);
                });
            });

            document.querySelectorAll('[data-report-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    const report = byId[button.dataset.reportView];
                    if (!report) return;
                    activeReport = report;

                    document.getElementById('report-detail-target').textContent = report.target_name;
                    document.getElementById('report-detail-seller').textContent = report.seller_name;
                    document.getElementById('report-detail-type').textContent = report.target_type;
                    document.getElementById('report-detail-reporter').textContent = report.reporter_name;
                    document.getElementById('report-detail-reason').textContent = report.reason_label;
                    document.getElementById('report-detail-submitted-date').textContent = report.submitted_at;
                    document.getElementById('report-detail-message').textContent = report.message;
                    document.getElementById('report-admin-notes').value = '';

                    const thumb = document.getElementById('report-detail-thumb');
                    thumb.innerHTML = report.target_type === 'Seller' ?
                        '<i class="fa-solid fa-store"></i>' :
                        '<i class="fa-solid fa-box-open"></i>';

                    const statusNode = document.getElementById('report-detail-status');
                    statusNode.textContent = report.status_label;
                    statusNode.className = `status-pill status-pill--${report.status_class}`;

                    const form = document.getElementById('report-action-form');
                    form.action = report.action_url;

                    renderHistory(report.actions);
                    renderActionToolbar(report);
                    openModal('report-detail-modal');
                });
            });

            document.getElementById('report-seller-id-view').addEventListener('click', () => {
                if (!activeReport?.seller?.valid_id_url) return;
                openDocumentModal('Document Preview', activeReport.seller.valid_id_type, activeReport.seller.valid_id_url);
            });

            document.getElementById('report-seller-permit-view').addEventListener('click', () => {
                if (!activeReport?.seller?.business_permit_url) return;
                openDocumentModal('Document Preview', 'Business License', activeReport.seller.business_permit_url);
            });
        })();
    </script>
@endpush
