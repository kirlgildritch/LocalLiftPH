@extends('layouts.admin')

@section('title', 'Manage Sellers')
@section('eyebrow', 'Verification')
@section('page-title', 'Manage Sellers')


@section('content')
    @php
        $avatarClasses = ['gold', 'teal', 'rose', 'slate', 'olive'];
    @endphp

    <div class="page-stack">
        <p class="sub-line" style="font-size: 1.05rem; margin: 0;">View and manage all registered sellers.</p>

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

        <div class="filter-bar">
            <div class="search-box search-box--grow">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search sellers..." />
            </div>
            <div class="inline-select"><i class="fa-solid fa-gear"></i> Filter <i class="fa-solid fa-chevron-down"></i>
            </div>
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
                        @forelse ($sellers as $index => $seller)
                            @php
                                $displayName = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');
                                $handle = '@' . \Illuminate\Support\Str::slug($displayName, '');
                                $category = optional($seller->user?->products->first()?->category)->name
                                    ?? ($seller->seller_type === 'registered_business' ? 'Business' : 'Individual');
                                $productsCount = $seller->user?->products->count() ?? 0;
                                $statusLabel = match ($seller->application_status) {
                                    'approved' => 'Active',
                                    'rejected' => 'Rejected',
                                    default => 'Pending',
                                };
                                $statusClass = match ($seller->application_status) {
                                    'approved' => 'status-pill--success',
                                    'rejected' => 'status-pill--danger',
                                    default => 'status-pill--pending',
                                };
                                $avatarClass = $avatarClasses[$index % count($avatarClasses)];
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
                                <td>{{ $category }}</td>
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

            <div class="pagination-bar">
                <button class="pagination-button"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="pagination-button is-active">1</button>
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
                    </div>
                </div>

                <div class="spacer-top">
                    <h4 class="section-title">Request More Documents</h4>
                    <form method="POST" id="seller-review-form" class="page-stack spacer-top">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="application_status" value="pending" id="seller-review-status">

                        <div class="form-row spacer-top">
                            <select class="field-select" id="seller-review-reason">
                                <option value="">Select Reason</option>
                                <option value="Please upload proof of address for verification.">Proof of Address</option>
                                <option value="Please upload your tax identification number document.">Tax Identification
                                    Number</option>
                                <option value="Please upload a recent bank statement.">Bank Statement</option>
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
        $sellerModalData = $sellers->map(function ($seller, $index) use ($avatarClasses) {
            $displayName = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');
            $handle = '@' . \Illuminate\Support\Str::slug($displayName, '');
            $productsCount = $seller->user?->products->count() ?? 0;

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
                'avatar_class' => $avatarClasses[$index % count($avatarClasses)],
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
                    const idStatus = document.getElementById('seller-id-status');
                    const permitStatus = document.getElementById('seller-permit-status');
                    const form = document.getElementById('seller-review-form');
                    const notes = document.getElementById('seller-review-notes');
                    const statusInput = document.getElementById('seller-review-status');

                    form.action = seller.update_url;
                    notes.value = seller.review_notes || '';
                    statusInput.value = seller.status || 'pending';
                    idLink.onclick = null;
                    permitLink.onclick = null;
                    idLink.disabled = !seller.valid_id_url;
                    permitLink.disabled = !seller.business_permit_url;

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
                    if (sellerReviewReason.value) {
                        sellerReviewNotes.value = sellerReviewReason.value;
                    }
                    sellerReviewStatus.value = 'pending';
                    sellerReviewForm.submit();
                });
            }

            document.querySelectorAll('[data-status-submit]').forEach((button) => {
                button.addEventListener('click', () => {
                    sellerReviewStatus.value = button.dataset.statusSubmit;
                    sellerReviewForm.submit();
                });
            });
        })();
    </script>
@endpush