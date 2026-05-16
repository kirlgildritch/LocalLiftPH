@extends('layouts.admin')

@section('title', 'Payouts')
@section('eyebrow', 'Finance')
@section('page-title', 'Payouts')

@section('content')
    <div class="page-stack">
        <article class="table-card admin-payouts-card">
            <div class="admin-payouts-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Account</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payouts as $payout)
                            @php
                                $statusClass = match ($payout->toneClass()) {
                                    'delivered' => 'delivered',
                                    'cancelled' => 'cancelled',
                                    default => 'pending',
                                };
                                $sellerName = $payout->seller->store_name
                                    ?? $payout->seller->user?->name
                                    ?? 'Seller';
                            @endphp
                            <tr>
                                <td class="product-title">{{ $sellerName }}</td>
                                <td>&#8369; {{ number_format((float) $payout->amount, 2) }}</td>
                                <td>{{ strtoupper($payout->method) }}</td>
                                <td>{{ $payout->account_name }}<br><span class="admin-payouts-muted">{{ $payout->account_number }}</span></td>
                                <td>{{ $payout->requested_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                <td><span class="status-pill status-pill--{{ $statusClass }}">{{ $payout->statusLabel() }}</span></td>
                                <td>{{ $payout->reference_number ?: '—' }}</td>
                                <td>
                                    @if ($payout->status === \App\Models\SellerPayout::STATUS_PENDING)
                                        <div class="admin-payouts-actions">
                                            <form method="POST" action="{{ route('admin.payouts.paid', $payout) }}" class="admin-payouts-inline-form">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="Reference number">
                                                <button type="submit" class="action-button action-button--success admin-payouts-submit">Mark Paid</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.payouts.reject', $payout) }}" class="admin-payouts-reject-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-button action-button--danger admin-payouts-submit">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="admin-payouts-muted">Processed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-text">No payout requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payouts->hasPages())
                @php
                    $startPage = max(1, $payouts->currentPage() - 1);
                    $endPage = min($payouts->lastPage(), $payouts->currentPage() + 1);
                @endphp
                <div class="pagination-bar">
                    @if ($payouts->onFirstPage())
                        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a class="pagination-button" href="{{ $payouts->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach ($payouts->getUrlRange($startPage, $endPage) as $page => $url)
                        <a class="pagination-button {{ $page === $payouts->currentPage() ? 'is-active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach

                    @if ($payouts->hasMorePages())
                        <a class="pagination-button" href="{{ $payouts->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            @endif
        </article>
    </div>
@endsection

@push('styles')
    <style>
        .admin-payouts-card {
            display: flex;
            flex-direction: column;
            min-height: 34rem;
        }

        .admin-payouts-scroll {
            flex: 1 1 auto;
            overflow-x: auto;
        }

        .admin-payouts-actions {
            display: grid;
            gap: 0.75rem;
            min-width: 272px;
        }

        .admin-payouts-inline-form {
            display: grid;
            gap: 0.5rem;
        }

        .admin-payouts-inline-form input {
            width: 100%;
            min-height: 2.8rem;
            padding: 0 0.95rem;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            color: var(--text);
            font: inherit;
        }

        .admin-payouts-inline-form input:focus {
            outline: none;
            border-color: rgba(59, 111, 214, 0.4);
            box-shadow: 0 0 0 3px rgba(59, 111, 214, 0.1);
        }

        .admin-payouts-submit {
            width: 100%;
            justify-content: center;
            min-height: 2.8rem;
        }

        .admin-payouts-reject-form {
            display: block;
        }

        .admin-payouts-muted {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .admin-payouts-card .pagination-bar {
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
            transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
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
            .admin-payouts-actions {
                min-width: 220px;
            }
        }
    </style>
@endpush
