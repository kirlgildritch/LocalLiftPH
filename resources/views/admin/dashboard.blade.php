@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Dashboard')
@section('page-title', 'Welcome, Admin!')
@section('page-description', 'Review sellers, products, orders, and reports from one workspace.')

@push('styles')
    @include('admin.dashboard.partials.styles')
@endpush

@section('content')
    <div class="page-stack">
        @include('admin.dashboard.partials.summary')

        <section class="dashboard-section-grid admin-dashboard-sections">
            <div class="stack">
                @include('admin.dashboard.partials.sales-overview')
                @include('admin.dashboard.partials.order-monitoring')
                @include('admin.dashboard.partials.product-moderation')
                @include('admin.dashboard.partials.recent-activity')
            </div>

            <div class="stack">
                @include('admin.dashboard.partials.user-management')
                @include('admin.dashboard.partials.shop-verification')
            </div>
        </section>
    </div>
@endsection