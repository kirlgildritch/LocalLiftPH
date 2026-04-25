@extends('layouts.admin')

@section('title', 'Reports')
@section('eyebrow', 'Trust & Safety')
@section('page-title', 'Reports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/reports.css') }}">
@endpush

@section('content')
    @php
        $pendingCount = $reports->where('status', \App\Models\Report::STATUS_PENDING)->count();
        $resolvedCount = $reports->where('status', \App\Models\Report::STATUS_RESOLVED)->count();
    @endphp

    <div class="page-stack">
        @if(session('success'))
            <div class="admin-report-feedback">{{ session('success') }}</div>
        @endif

        <div class="filter-bar">
            <div class="chip is-active">All {{ $reports->count() }}</div>
            <div class="chip">Pending {{ $pendingCount }}</div>
            <div class="chip">Resolved {{ $resolvedCount }}</div>
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
                                $statusClass = $report->status === \App\Models\Report::STATUS_RESOLVED ? 'resolved' : 'pending';
                                $targetType = $report->product ? 'Product' : 'Seller';
                                $viewUrl = $report->product
                                    ? route('products.show', $report->product)
                                    : ($report->seller ? route('shops.show', $report->seller) : null);
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
                                <td><span
                                        class="status-pill status-pill--{{ $statusClass }}">{{ $report->statusLabel() }}</span>
                                </td>
                                <td>
                                    <button class="action-button action-button--primary" type="button"
                                        data-report-view="{{ $report->id }}" data-report-target="{{ e($targetName) }}"
                                        data-report-seller="{{ e($sellerName) }}" data-report-type="{{ e($targetType) }}"
                                        data-report-reason="{{ e($report->reasonLabel()) }}"
                                        data-report-reporter="{{ e($report->user?->name ?? 'Deleted user') }}"
                                        data-report-date="{{ e($report->created_at?->format('M d, Y h:i A') ?? 'Unknown') }}"
                                        data-report-status="{{ e($report->statusLabel()) }}"
                                        data-report-status-class="{{ $statusClass }}"
                                        data-report-message="{{ e($report->message ?: 'No additional details were provided by the reporter.') }}"
                                        data-report-link="{{ $viewUrl ? e($viewUrl) : '' }}"
                                        data-report-link-label="{{ $report->product ? 'View Product Listing' : 'View Seller Shop' }}"
                                        data-report-resolve-url="{{ route('admin.reports.resolve', $report) }}"
                                        data-report-is-resolved="{{ $report->status === \App\Models\Report::STATUS_RESOLVED ? '1' : '0' }}">
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
                        <div class="seller-name" id="report-detail-product"></div>
                        <div class="muted-row"><i class="fa-solid fa-user"></i> <span id="report-detail-seller"></span>
                        </div>
                    </div>
                </div>

                <div class="report-meta">
                    <div class="report-meta-row">
                        <div class="seller-name">Target: <span class="admin-report-inline-value"
                                id="report-detail-type"></span></div>
                        <a class="secondary-link view-link-inline" href="#" id="report-detail-link" target="_blank"
                            rel="noopener noreferrer" hidden>View Listing <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Reporter:</div>
                        <div class="muted-row">
                            <div class="avatar-circle avatar-circle--xs">R</div><span id="report-detail-reporter"></span>
                        </div>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Reason:</div>
                        <div id="report-detail-date"></div>
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

                <div>
                    <h4 class="section-title">Reporter Message</h4>
                    <div class="report-comment spacer-top">
                        <div class="avatar-circle avatar-circle--sm">R</div>
                        <div>
                            <div class="seller-name" id="report-detail-commenter"></div>
                            <p style="margin: 0.5rem 0 0.75rem;" id="report-detail-comment"></p>
                            <div class="report-comment__meta" id="report-detail-comment-date"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="report-detail-modal">Close</button>
                <div class="footer-actions" id="report-detail-actions">
                    <form method="POST" action="#" id="report-resolve-form">
                        @csrf
                        @method('PATCH')
                        <button class="action-button action-button--primary" type="submit">Mark as Resolved</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
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

            document.querySelectorAll('[data-report-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    const thumb = document.getElementById('report-detail-thumb');
                    const detailLink = document.getElementById('report-detail-link');
                    const resolveForm = document.getElementById('report-resolve-form');
                    const detailActions = document.getElementById('report-detail-actions');
                    const isResolved = button.dataset.reportIsResolved === '1';
                    const targetType = button.dataset.reportType || 'Listing';

                    document.getElementById('report-detail-product').textContent = button.dataset.reportTarget || 'Unavailable';
                    document.getElementById('report-detail-seller').textContent = button.dataset.reportSeller || 'Seller unavailable';
                    document.getElementById('report-detail-type').textContent = targetType;
                    document.getElementById('report-detail-reporter').textContent = button.dataset.reportReporter || 'Deleted user';
                    document.getElementById('report-detail-date').textContent = button.dataset.reportReason || 'Other';
                    document.getElementById('report-detail-submitted-date').textContent = button.dataset.reportDate || 'Unknown';
                    document.getElementById('report-detail-commenter').textContent = button.dataset.reportReporter || 'Deleted user';
                    document.getElementById('report-detail-comment').textContent = button.dataset.reportMessage || 'No additional details were provided by the reporter.';
                    document.getElementById('report-detail-comment-date').textContent = `Reported on ${button.dataset.reportDate || 'Unknown'}`;

                    const statusNode = document.getElementById('report-detail-status');
                    statusNode.textContent = button.dataset.reportStatus || 'Pending';
                    statusNode.className = `status-pill status-pill--${button.dataset.reportStatusClass || 'pending'}`;

                    thumb.innerHTML = targetType === 'Seller'
                        ? '<i class="fa-solid fa-store"></i>'
                        : '<i class="fa-solid fa-box-open"></i>';

                    if (button.dataset.reportLink) {
                        detailLink.hidden = false;
                        detailLink.href = button.dataset.reportLink;
                        detailLink.innerHTML = `${button.dataset.reportLinkLabel || 'View Listing'} <i class="fa-solid fa-chevron-right"></i>`;
                    } else {
                        detailLink.hidden = true;
                        detailLink.removeAttribute('href');
                    }

                    if (resolveForm) {
                        resolveForm.action = button.dataset.reportResolveUrl || '#';
                    }

                    if (detailActions) {
                        detailActions.hidden = isResolved;
                    }

                    openModal('report-detail-modal');
                });
            });
        })();
    </script>
@endpush