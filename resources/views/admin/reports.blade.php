@extends('layouts.admin')

@section('title', 'Reports')
@section('eyebrow', 'Trust & Safety')
@section('page-title', 'Reports')
@section('page-description', 'Reports content, detail modal, and take-action modal rebuilt from the provided document.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/reports.css') }}">
@endpush

@section('content')
    @php
        $reports = [
            ['id' => 'earrings', 'product' => 'Handcrafted Clay Earrings', 'seller' => 'crafty_jenny', 'thumb' => 'earrings', 'type' => 'Inappropriate Product', 'type_class' => '', 'reporter' => 'student_sam', 'date' => 'Apr 15, 2024', 'status' => 'Investigating', 'status_class' => 'investigating'],
            ['id' => 'bear', 'product' => 'Knitted Mini Plush Bear', 'seller' => 'artisan_alex', 'thumb' => 'bear', 'type' => 'Suspicious Activity', 'type_class' => '', 'reporter' => 'shopkeeper_kate', 'date' => 'Apr 14, 2024', 'status' => 'Investigating', 'status_class' => 'investigating'],
            ['id' => 'macrame', 'product' => 'Boho Macrame Wall Hanging', 'seller' => 'macrame_mias', 'thumb' => 'macrame', 'type' => 'Trademark Violation', 'type_class' => '', 'reporter' => 'reporter123', 'date' => 'Apr 10, 2024', 'status' => 'Resolved', 'status_class' => 'resolved'],
            ['id' => 'mug', 'product' => 'Hand-Painted Pottery Mug', 'seller' => 'clayandcolor', 'thumb' => 'mug', 'type' => 'Inappropriate Product', 'type_class' => '', 'reporter' => 'artfan345', 'date' => 'Apr 5, 2024', 'status' => 'Investigating', 'status_class' => 'investigating'],
            ['id' => 'bracelet', 'product' => 'Handmade Bead Bracelet', 'seller' => 'crafty_emma', 'thumb' => 'bracelet', 'type' => 'Counterfeit Product', 'type_class' => '', 'reporter' => 'buyer_jess', 'date' => 'Apr 1, 2024', 'status' => 'Resolved', 'status_class' => 'resolved'],
            ['id' => 'wood', 'product' => 'Handmade Wood Coasters', 'seller' => 'woodworker_jake', 'thumb' => 'wood', 'type' => 'Suspicious Activity', 'type_class' => 'green', 'reporter' => 'beads_girl', 'date' => 'Mar 29, 2024', 'status' => 'Investigating', 'status_class' => 'investigating'],
        ];
    @endphp

    <div class="page-stack">
        <div class="filter-bar">
            <div class="chip is-active">All</div>
            <div class="inline-select">Last 30 Days <i class="fa-solid fa-chevron-down"></i></div>
            <div class="inline-select"><i class="fa-solid fa-xmark"></i></div>
            <div class="inline-select">All Statuses <i class="fa-solid fa-chevron-down"></i></div>
            <div class="inline-select">Category · Aly Riguences</div>
            <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search reports..." /></div>
        </div>

        <article class="table-card">
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Reported Product</th>
                            <th>Type</th>
                            <th>Reporter</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <div class="report-product-cell">
                                        <div class="mini-thumb mini-thumb--{{ $report['thumb'] }}"></div>
                                        <div class="report-product-cell__text">
                                            <div class="report-product-name">{{ $report['product'] }}</div>
                                            <div class="sub-line">{{ $report['seller'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="type-badge {{ $report['type_class'] ? 'type-badge--green' : '' }}">{{ $report['type'] }}</span></td>
                                <td><div class="muted-row"><i class="fa-solid fa-user"></i> {{ $report['reporter'] }}</div></td>
                                <td>{{ $report['date'] }}</td>
                                <td><span class="status-pill status-pill--{{ $report['status_class'] }}">{{ $report['status'] }}</span></td>
                                <td><button class="action-button action-button--primary" type="button" data-report-view="{{ $report['id'] }}"><i class="fa-solid fa-magnifying-glass"></i> View Details</button></td>
                            </tr>
                        @endforeach
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
                    <div class="thumb" id="report-detail-thumb"></div>
                    <div>
                        <div class="seller-name" id="report-detail-product"></div>
                        <div class="muted-row"><i class="fa-solid fa-user"></i> <span id="report-detail-seller"></span></div>
                    </div>
                </div>

                <div class="report-meta">
                    <div class="report-meta-row">
                        <div class="seller-name">Type: <span style="font-weight: 500; color: #c77628;" id="report-detail-type"></span></div>
                        <button class="secondary-link" type="button">View Product Listing <i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Reporter:</div>
                        <div class="muted-row"><div class="avatar-circle avatar-circle--xs">S</div><span id="report-detail-reporter"></span></div>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Submitted Date:</div>
                        <div id="report-detail-date"></div>
                    </div>
                    <div class="report-meta-row">
                        <div class="seller-name">Status:</div>
                        <span class="status-pill status-pill--investigating" id="report-detail-status"></span>
                    </div>
                </div>

                <div>
                    <h4 class="section-title">Reporter's Comment</h4>
                    <div class="report-comment spacer-top">
                        <div class="avatar-circle avatar-circle--sm">S</div>
                        <div>
                            <div class="seller-name" id="report-detail-commenter"></div>
                            <p style="margin: 0.5rem 0 0.75rem;" id="report-detail-comment"></p>
                            <div class="report-comment__meta" id="report-detail-comment-date"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="action-button action-button--danger" type="button">Dismiss as False Report</button>
                <div class="footer-actions">
                    <button class="action-button action-button--primary" type="button" id="open-take-action">Take Action</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-shell" id="take-action-modal" hidden>
        <div class="modal-card modal-card--wide">
            <div class="modal-card__header">
                <h3 class="modal-title">Take Action</h3>
                <button class="modal-close" type="button" data-close-modal="take-action-modal">&times;</button>
            </div>
            <div class="modal-card__body">
                <div class="report-summary">
                    <div class="thumb" id="take-action-thumb"></div>
                    <div>
                        <div class="seller-name" id="take-action-product"></div>
                        <div class="muted-row"><i class="fa-solid fa-user"></i> <span id="take-action-seller"></span></div>
                    </div>
                </div>

                <p class="section-title spacer-top">Select an action to resolve this report:</p>

                <div class="action-choice-list spacer-top">
                    <div class="action-choice is-selected">
                        <div class="action-choice__header">
                            <span class="choice-radio"></span>
                            <div class="seller-name">Mark as Resolved</div>
                        </div>
                        <div class="sub-line">Confirm the report is valid and take corrective action against the listing and/or seller.</div>
                        <input class="filter-input" value="Enter details of action taken." />
                    </div>

                    <div class="action-choice">
                        <div class="action-choice__header">
                            <span class="choice-radio"></span>
                            <div class="seller-name">Dismiss Report</div>
                        </div>
                        <div class="sub-line">Dismiss the report as invalid. No action will be taken against the listing or seller.</div>
                        <input class="filter-input" value="Enter reason for dismissing report." />
                        <label class="checkbox-line"><input type="checkbox" checked /> Notify the reporter via email that this report has been dismissed</label>
                    </div>

                    <div class="action-choice">
                        <div class="action-choice__header">
                            <span class="choice-radio"></span>
                            <div class="seller-name">Suspend Seller</div>
                        </div>
                        <div class="sub-line">Suspend this seller for violating marketplace rules.</div>
                        <input class="filter-input" value="Enter reason for suspending seller." />
                        <label class="checkbox-line"><input type="checkbox" checked /> Notify the seller via email about this action</label>
                    </div>
                </div>
            </div>
            <div class="modal-card__footer">
                <button class="button" type="button" data-close-modal="take-action-modal">Cancel</button>
                <div class="footer-actions">
                    <button class="action-button action-button--primary" type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
            const reports = @json($reports);
            const reportMap = Object.fromEntries(reports.map((report) => [report.id, report]));
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

            function setThumb(elementId, thumb) {
                const node = document.getElementById(elementId);
                if (node) node.className = `thumb thumb--${thumb}`;
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
                    const report = reportMap[button.dataset.reportView];
                    if (!report) return;
                    activeReport = report;
                    setThumb('report-detail-thumb', report.thumb);
                    document.getElementById('report-detail-product').textContent = report.product;
                    document.getElementById('report-detail-seller').textContent = report.seller;
                    document.getElementById('report-detail-type').textContent = report.type;
                    document.getElementById('report-detail-reporter').textContent = report.reporter;
                    document.getElementById('report-detail-date').textContent = report.date;
                    document.getElementById('report-detail-status').textContent = report.status;
                    document.getElementById('report-detail-commenter').textContent = report.reporter;
                    document.getElementById('report-detail-comment').textContent = 'I think this listing should be reviewed because it appears too similar to another artisan design and may violate marketplace rules.';
                    document.getElementById('report-detail-comment-date').textContent = `Reported on ${report.date}`;
                    openModal('report-detail-modal');
                });
            });

            const takeActionButton = document.getElementById('open-take-action');
            if (takeActionButton) {
                takeActionButton.addEventListener('click', () => {
                    if (!activeReport) return;
                    setThumb('take-action-thumb', activeReport.thumb);
                    document.getElementById('take-action-product').textContent = activeReport.product;
                    document.getElementById('take-action-seller').textContent = activeReport.seller;
                    closeModal('report-detail-modal');
                    openModal('take-action-modal');
                });
            }
        })();
    </script>
@endpush
