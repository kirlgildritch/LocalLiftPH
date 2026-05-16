@extends('layouts.seller')

@section('content')
    @php
        $requestStatusLabel = match ($latestDocumentRequest?->status) {
            \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED => 'Resubmitted',
            \App\Models\SellerDocumentRequest::STATUS_RESOLVED => 'Resolved',
            default => 'Pending',
        };

        $requestReasonLabel = match ($latestDocumentRequest?->reason) {
            'proof_of_address' => 'Proof of Address',
            'tax_identification_number' => 'Tax Identification Number',
            'bank_statement' => 'Bank Statement',
            default => $latestDocumentRequest?->reason ? ucfirst(str_replace('_', ' ', $latestDocumentRequest->reason)) : null,
        };
    @endphp

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main panel">
                    @if ($dashboardState === 'not_started')
                        @include('seller.dashboard.partials.not-started')
                    @elseif ($dashboardState === 'filling_form')
                        @include('seller.dashboard.partials.application-form')
                    @elseif ($dashboardState === 'documents_requested')
                        @include('seller.dashboard.partials.documents-requested')
                    @elseif ($dashboardState === 'suspended')
                        @include('seller.dashboard.partials.suspended')
                    @elseif ($dashboardState === 'pending')
                        @include('seller.dashboard.partials.pending')
                    @elseif ($dashboardState === 'rejected')
                        @include('seller.dashboard.partials.rejected')
                    @else
                        @include('seller.dashboard.partials.active-overview')
                    @endif
                </main>
            </div>
        </div>
    </section>
    <script src="{{ asset('assets/js/buyer-address-form.js') }}"></script>
@endsection