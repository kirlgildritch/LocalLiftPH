@extends('layouts.admin')

@section('title', 'Manage Sellers')
@section('eyebrow', 'Verification')
@section('page-title', 'Manage Sellers')


@section('content')
    @php
        $avatarClasses = ['gold', 'teal', 'rose', 'slate', 'olive'];
        $statusOptions = [
            '' => 'All Statuses',
            'active' => 'Active',
            'pending' => 'Pending Review',
            'rejected' => 'Rejected',
            'flagged' => 'Flagged',
        ];
    @endphp

    <div class="page-stack">
        @if (session('success'))
            <div class="alert-note">{{ session('success') }}</div>
        @endif

        <section class="seller-stats-grid">
            @foreach ($stats as $stat)
                <article class="seller-stat-card seller-stat-card--{{ $stat['tone'] }}">
                    <p>{{ $stat['label'] }}</p>
                    <strong>{{ $stat['value'] }}</strong>
                </article>
            @endforeach
        </section>

        <form method="GET" action="{{ route('admin.sellers') }}" class="filter-bar seller-filter-bar">
            <div class="search-box search-box--grow">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search sellers..." />
            </div>
            <label class="inline-select seller-inline-select">
                <i class="fa-solid fa-gear"></i>
                <select name="status" aria-label="Filter sellers by status">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <button class="action-button action-button--primary" type="submit">Filter</button>
            <a class="action-button action-button--light" href="{{ route('admin.sellers') }}">Reset</a>
        </form>

        <article class="table-card seller-table-card">
            <div class="seller-table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Top Category</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Date Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sellers as $index => $seller)
                            @php
                                $displayName = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');
                                $handle = '@' . \Illuminate\Support\Str::slug($displayName, '');
                                $products = $seller->user?->products ?? collect();
                                $productsCount = $products->count();
                                $categoryCounts = $products
                                    ->groupBy(fn($product) => $product->category?->name ?? 'Uncategorized')
                                    ->map
                                    ->count()
                                    ->sortDesc();
                                $distinctCategoryCount = $categoryCounts->count();
                                $topCategory = $categoryCounts->keys()->first();
                                $categoryLabel = match (true) {
                                    $productsCount === 0 => 'No products yet',
                                    $distinctCategoryCount === 1 => $topCategory,
                                    default => 'Multiple Categories',
                                };
                                $categoryDetail = $productsCount > 0 && $distinctCategoryCount > 1 ? 'Top: ' . $topCategory : null;
                                $hasFlaggedProducts = $products->contains(function ($product) {
                                    return $product->reports->where('status', \App\Models\Report::STATUS_PENDING)->isNotEmpty();
                                });
                                $statusLabel = $hasFlaggedProducts ? 'Flagged' : match ($seller->application_status) {
                                    'approved' => 'Active',
                                    'rejected' => 'Rejected',
                                    default => 'Pending Review',
                                };
                                $statusClass = $hasFlaggedProducts ? 'status-pill--danger' : match ($seller->application_status) {
                                    'approved' => 'status-pill--success',
                                    'rejected' => 'status-pill--danger',
                                    default => 'status-pill--pending',
                                };
                                $avatarClass = $avatarClasses[(($sellers->firstItem() ?? 1) + $index - 1) % count($avatarClasses)];
                            @endphp
                            <tr>
                                <td>
                                    <div class="seller-cell">
                                        <div class="avatar-photo avatar-photo--{{ $avatarClass }}">
                                            {{ strtoupper(substr($displayName, 0, 2)) }}
                                        </div>
                                        <div class="seller-cell__text">
                                            <div class="seller-name">{{ $displayName }}</div>
                                            <div class="sub-line">{{ $handle }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="seller-cell__text">
                                        <div class="seller-name">{{ $categoryLabel }}</div>
                                        @if ($categoryDetail)
                                            <div class="sub-line">{{ $categoryDetail }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $productsCount }}</td>
                                <td>
                                    <span class="status-pill {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td>{{ optional($seller->submitted_at ?? $seller->created_at)->format('m/d/Y') }}</td>
                                <td>
                                    <div class="table-actions__primary">
                                        <button class="action-button action-button--primary" type="button"
                                            data-seller-view="{{ $seller->id }}">
                                            <i class="fa-solid fa-magnifying-glass"></i> View
                                        </button>


                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="sub-line">No seller applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sellers->hasPages())
                @php
                    $startPage = max(1, $sellers->currentPage() - 1);
                    $endPage = min($sellers->lastPage(), $sellers->currentPage() + 1);
                @endphp
                <div class="pagination-bar">
                    @if ($sellers->onFirstPage())
                        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a class="pagination-button" href="{{ $sellers->previousPageUrl() }}"><i
                                class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach ($sellers->getUrlRange($startPage, $endPage) as $page => $url)
                        <a class="pagination-button {{ $page === $sellers->currentPage() ? 'is-active' : '' }}"
                            href="{{ $url }}">{{ $page }}</a>
                    @endforeach

                    @if ($sellers->hasMorePages())
                        <a class="pagination-button" href="{{ $sellers->nextPageUrl() }}"><i
                                class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            @endif
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
                    <div class="avatar-photo avatar-photo--teal" id="seller-detail-avatar"></div>
                    <div>
                        <div class="seller-name" id="seller-detail-name"></div>
                        <div class="sub-line" id="seller-detail-handle"></div>
                    </div>
                </div>

                <div class="modal-meta-bar spacer-top">
                    <strong id="seller-detail-products"></strong>
                    <span>Joined <span id="seller-detail-date"></span></span>
                    <span>Email <span id="seller-detail-email"></span></span>
                </div>

                <div class="spacer-top">
                    <h4 class="section-title">Uploaded Documents</h4>
                    <div class="doc-card-grid spacer-top">
                        <div class="doc-card">
                            <div class="doc-thumb doc-thumb--id"></div>
                            <div class="doc-card__content">
                                <div class="seller-name" id="seller-id-label">ID / Passport</div>
                                <div class="doc-card__status" id="seller-id-status">Uploaded</div>
                                <button class="button" type="button" id="seller-id-link">View</button>
                            </div>
                        </div>

                        <div class="doc-card">
                            <div class="doc-thumb doc-thumb--license"></div>
                            <div class="doc-card__content">
                                <div class="seller-name">Business License</div>
                                <div class="doc-card__status" id="seller-permit-status">Optional / Not uploaded</div>
                                <button class="button" type="button" id="seller-permit-link">View</button>
                            </div>
                        </div>

                        <div class="doc-card">
                            <div class="doc-thumb doc-thumb--address"></div>
                            <div class="doc-card__content">
                                <div class="seller-name" id="seller-requested-document-label">Requested Document</div>
                                <div class="doc-card__status" id="seller-requested-document-status">Not uploaded</div>
                                <button class="button" type="button" id="seller-requested-document-link">View</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="spacer-top">
                    <h4 class="section-title">Request More Documents</h4>
                    <form method="POST" id="seller-review-form" class="page-stack spacer-top">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="application_status" value="pending" id="seller-review-status">
                        <input type="hidden" name="request_more_documents" value="0" id="seller-request-more-documents">

                        <div class="info-card">
                            <div class="detail-line"><span>Reason</span><strong id="seller-request-current-reason">None</strong></div>
                            <div class="detail-line"><span>Notes</span><strong id="seller-request-current-notes">None</strong></div>
                            <div class="detail-line"><span>Requested</span><strong id="seller-request-current-date">N/A</strong></div>
                            <div class="detail-line"><span>Status</span><strong id="seller-request-current-status">None</strong></div>
                        </div>

                        <div class="form-row spacer-top">
                            <select class="field-select" id="seller-review-reason" name="document_request_reason">
                                <option value="">Select Reason</option>
                                @foreach ($documentRequestReasons as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="action-button action-button--warning" type="button"
                                id="request-documents-button">Request Documents</button>
                        </div>

                        <textarea class="field-textarea" name="review_notes" id="seller-review-notes" rows="4"
                            placeholder="Add admin review notes or required document instructions..."></textarea>

                        <div class="alert-note">Additional document requests will notify the seller through the dashboard
                            review state.</div>
                    </form>
                </div>
            </div>

            <div class="modal-card__footer">
                <div class="footer-actions">
                    <button class="action-button action-button--success" type="button" data-status-submit="approved">Verify
                        Seller</button>
                    <button class="action-button action-button--danger" type="button" data-status-submit="rejected">Reject
                        Seller</button>
                    <button class="button" type="button" data-status-submit="pending">Save as Pending</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="seller-document-modal" hidden>
        <div class="modal-card modal-card--document">
            <div class="modal-card__header">
                <h3 class="modal-title" id="seller-document-modal-title">Uploaded Document</h3>
                <button class="modal-close" type="button" data-close-modal="seller-document-modal">&times;</button>
            </div>

            <div class="modal-card__body">
                <div class="document-preview-shell">
                    <div class="seller-name" id="seller-document-modal-label">Business License</div>
                    <div class="document-preview-stage" id="seller-document-preview-stage"></div>
                </div>
            </div>

            <div class="modal-card__footer">
                <div class="footer-actions">
                    <a class="action-button action-button--success" href="#" target="_blank" id="seller-document-open-link">
                        <i class="fa-solid fa-up-right-from-square"></i> Open File
                    </a>
                    <button class="button" type="button" data-close-modal="seller-document-modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    @php
        $sellerModalData = $sellers->getCollection()->values()->map(function ($seller, $index) use ($avatarClasses, $sellers) {
            $displayName = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');
            $handle = '@' . \Illuminate\Support\Str::slug($displayName, '');
            $productsCount = $seller->user?->products->count() ?? 0;
            $latestRequest = $seller->latestDocumentRequest;
            $requestReasonLabel = match ($latestRequest?->reason) {
                'proof_of_address' => 'Proof of Address',
                'tax_identification_number' => 'Tax Identification Number',
                'bank_statement' => 'Bank Statement',
                default => $latestRequest?->reason ? ucfirst(str_replace('_', ' ', $latestRequest->reason)) : null,
            };
            $requestStatusLabel = match ($latestRequest?->status) {
                \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED => 'Resubmitted',
                \App\Models\SellerDocumentRequest::STATUS_RESOLVED => 'Resolved',
                \App\Models\SellerDocumentRequest::STATUS_PENDING => 'Pending',
                default => 'None',
            };

            return [
                'id' => $seller->id,
                'name' => $displayName,
                'handle' => $handle,
                'email' => $seller->email ?? $seller->user?->email,
                'date' => optional($seller->submitted_at ?? $seller->created_at)->format('m/d/Y'),
                'products' => $productsCount . ' product' . ($productsCount === 1 ? '' : 's'),
                'valid_id_type' => $seller->valid_id_type ?: 'ID / Passport',
                'valid_id_url' => $seller->valid_id_path ? asset('storage/' . $seller->valid_id_path) : null,
                'business_permit_url' => $seller->business_permit_path ? asset('storage/' . $seller->business_permit_path) : null,
                'review_notes' => $seller->review_notes,
                'status' => $seller->application_status,
                'update_url' => route('admin.sellers.status', $seller),
                'avatar' => strtoupper(substr($displayName, 0, 2)),
                'avatar_class' => $avatarClasses[(($sellers->firstItem() ?? 1) + $index - 1) % count($avatarClasses)],
                'latest_request_reason' => $latestRequest?->reason,
                'latest_request_reason_label' => $requestReasonLabel,
                'latest_request_notes' => $latestRequest?->admin_notes,
                'latest_request_status' => $latestRequest?->status,
                'latest_request_status_label' => $requestStatusLabel,
                'latest_request_date' => optional($latestRequest?->requested_at)->format('m/d/Y h:i A'),
                'latest_request_document_url' => $latestRequest?->response_document_path ? asset('storage/' . $latestRequest->response_document_path) : null,
            ];
        })->values();
    @endphp

    <script>
        (() => {
            const sellers = @json($sellerModalData);

            const sellerMap = Object.fromEntries(sellers.map((seller) => [String(seller.id), seller]));
            const documentModalTitle = document.getElementById('seller-document-modal-title');
            const documentModalLabel = document.getElementById('seller-document-modal-label');
            const documentPreviewStage = document.getElementById('seller-document-preview-stage');
            const documentOpenLink = document.getElementById('seller-document-open-link');
            const sellerRequestMoreDocuments = document.getElementById('seller-request-more-documents');

            const openModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.hidden = false;
                document.body.classList.add('is-modal-open');
            };

            const closeModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.hidden = true;
                if (![...document.querySelectorAll('.modal-shell')].some((item) => !item.hidden)) {
                    document.body.classList.remove('is-modal-open');
                }
            };

            const fileExtension = (url) => {
                try {
                    return new URL(url, window.location.origin).pathname.split('.').pop().toLowerCase();
                } catch (error) {
                    return '';
                }
            };

            const openDocumentModal = (title, label, url) => {
                if (!url || !documentPreviewStage || !documentOpenLink) return;

                documentModalTitle.textContent = title;
                documentModalLabel.textContent = label;
                documentOpenLink.href = url;

                const extension = fileExtension(url);
                const isPdf = extension === 'pdf';

                documentPreviewStage.innerHTML = isPdf
                    ? `<iframe src="${url}" class="document-preview-frame" title="${label}"></iframe>`
                    : `<img src="${url}" alt="${label}" class="document-preview-image">`;

                openModal('seller-document-modal');
            };

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
                    document.getElementById('seller-detail-products').textContent = seller.products;
                    document.getElementById('seller-detail-date').textContent = seller.date || 'N/A';
                    document.getElementById('seller-detail-email').textContent = seller.email || 'N/A';
                    document.getElementById('seller-id-label').textContent = seller.valid_id_type || 'ID / Passport';

                    const idLink = document.getElementById('seller-id-link');
                    const permitLink = document.getElementById('seller-permit-link');
                    const requestedDocumentLink = document.getElementById('seller-requested-document-link');
                    const idStatus = document.getElementById('seller-id-status');
                    const permitStatus = document.getElementById('seller-permit-status');
                    const requestedDocumentStatus = document.getElementById('seller-requested-document-status');
                    const form = document.getElementById('seller-review-form');
                    const notes = document.getElementById('seller-review-notes');
                    const statusInput = document.getElementById('seller-review-status');
                    const reasonSelect = document.getElementById('seller-review-reason');

                    form.action = seller.update_url;
                    notes.value = seller.review_notes || '';
                    statusInput.value = seller.status || 'pending';
                    sellerRequestMoreDocuments.value = '0';
                    reasonSelect.value = '';
                    idLink.onclick = null;
                    permitLink.onclick = null;
                    requestedDocumentLink.onclick = null;
                    idLink.disabled = !seller.valid_id_url;
                    permitLink.disabled = !seller.business_permit_url;
                    requestedDocumentLink.disabled = !seller.latest_request_document_url;

                    if (seller.valid_id_url) {
                        idStatus.textContent = 'Uploaded';
                        idLink.addEventListener('click', function handleIdClick() {
                            openDocumentModal('Uploaded Document', seller.valid_id_type || 'ID / Passport', seller.valid_id_url);
                        }, { once: true });
                    } else {
                        idStatus.textContent = 'Not uploaded';
                    }

                    if (seller.business_permit_url) {
                        permitStatus.textContent = 'Uploaded';
                        permitLink.addEventListener('click', function handlePermitClick() {
                            openDocumentModal('Uploaded Document', 'Business License', seller.business_permit_url);
                        }, { once: true });
                    } else {
                        permitStatus.textContent = 'Optional / Not uploaded';
                    }

                    document.getElementById('seller-request-current-reason').textContent = seller
                        .latest_request_reason_label || 'None';
                    document.getElementById('seller-request-current-notes').textContent = seller
                        .latest_request_notes || 'None';
                    document.getElementById('seller-request-current-date').textContent = seller
                        .latest_request_date || 'N/A';
                    document.getElementById('seller-request-current-status').textContent = seller
                        .latest_request_status_label || 'None';
                    document.getElementById('seller-requested-document-label').textContent = seller
                        .latest_request_reason_label || 'Requested Document';

                    if (seller.latest_request_document_url) {
                        requestedDocumentStatus.textContent = seller.latest_request_status_label || 'Uploaded';
                        requestedDocumentLink.addEventListener('click', function handleRequestedDocumentClick() {
                            openDocumentModal('Requested Document', seller.latest_request_reason_label ||
                                'Requested Document', seller.latest_request_document_url);
                        }, {
                            once: true
                        });
                    } else {
                        requestedDocumentStatus.textContent = seller.latest_request_status === 'pending' ? 'Awaiting upload' :
                            'Not uploaded';
                    }

                    openModal('seller-detail-modal');
                });
            });

            const requestDocumentsButton = document.getElementById('request-documents-button');
            const sellerReviewReason = document.getElementById('seller-review-reason');
            const sellerReviewNotes = document.getElementById('seller-review-notes');
            const sellerReviewStatus = document.getElementById('seller-review-status');
            const sellerReviewForm = document.getElementById('seller-review-form');

            if (requestDocumentsButton && sellerReviewReason && sellerReviewNotes) {
                requestDocumentsButton.addEventListener('click', () => {
                    if (!sellerReviewReason.value) {
                        sellerReviewReason.focus();
                        return;
                    }
                    sellerRequestMoreDocuments.value = '1';
                    sellerReviewStatus.value = 'pending';
                    sellerReviewForm.submit();
                });
            }

            document.querySelectorAll('[data-status-submit]').forEach((button) => {
                button.addEventListener('click', () => {
                    sellerRequestMoreDocuments.value = '0';
                    sellerReviewStatus.value = button.dataset.statusSubmit;
                    sellerReviewForm.submit();
                });
            });
        })();
    </script>
@endpush

@push('styles')
    <style>
        .seller-filter-bar {
            align-items: stretch;
        }

        .seller-inline-select {
            min-width: 12rem;
        }

        .seller-inline-select select {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text);
            cursor: pointer;
        }

        .seller-table-card {
            display: flex;
            flex-direction: column;
            min-height: 34rem;
        }

        .seller-table-scroll {
            flex: 1 1 auto;
            overflow-x: auto;
        }

        .seller-table-card .pagination-bar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: auto;
            padding-top: 18px;
            align-self: center;
            flex-wrap: wrap;
        }

        .pagination-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            min-width: 42px;
            height: 42px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            color: #8fa7c4;
            font-weight: 700;
            text-decoration: none;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
            transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
        }

        .pagination-button:hover {
            background: rgba(66, 165, 245, 0.1);
            border-color: rgba(66, 165, 245, 0.28);
            color: #dfeaff;
        }

        .pagination-button.is-active {
            background: linear-gradient(135deg, #4f8df0, #3e6fdb);
            border-color: rgba(96, 165, 250, 0.4);
            color: #fff;
            box-shadow: 0 12px 24px rgba(62, 111, 219, 0.24);
        }

        .pagination-button.is-disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        @media (max-width: 720px) {
            .seller-filter-bar > * {
                flex: 1 1 100%;
            }

            .seller-table-card {
                min-height: 0;
            }

            .seller-table-card .pagination-bar {
                width: 100%;
            }
        }
    </style>
@endpush
