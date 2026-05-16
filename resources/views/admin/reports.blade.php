@extends('layouts.admin')

@section('title', 'Reports')
@section('eyebrow', 'Trust & Safety')
@section('page-title', 'Reports')
@php
    $adminReportsScript = asset('assets/js/admin-reports-page.js') . '?v=' . @filemtime(public_path('assets/js/admin-reports-page.js'));
@endphp

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
                        'image_url' => \App\Support\PublicAssetUrl::for($product->image),
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
                        'valid_id_url' => \App\Support\PublicAssetUrl::for($sellerProfile?->valid_id_path),
                        'business_permit_url' => \App\Support\PublicAssetUrl::for($sellerProfile?->business_permit_path),
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
        @include('admin.reports.partials.filter-bar', [
            'reports' => $reports,
            'pendingCount' => $pendingCount,
            'resolvedCount' => $resolvedCount,
            'dismissedCount' => $dismissedCount,
        ])

        @include('admin.reports.partials.table', [
            'reports' => $reports,
            'reportStatusClass' => $reportStatusClass,
        ])
    </div>
@endsection

@push('modals')
    @include('admin.reports.partials.modals.report-detail')
    @include('admin.reports.partials.modals.report-product')
    @include('admin.reports.partials.modals.report-seller')
    @include('admin.reports.partials.modals.report-document')
@endpush

@push('scripts')
    <script id="admin-reports-modal-data" type="application/json">@json($reportModalData)</script>
    <script src="{{ $adminReportsScript }}" defer></script>
@endpush
