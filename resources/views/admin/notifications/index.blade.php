@extends('layouts.admin')

@section('title', 'Admin Notifications')
@section('eyebrow', 'Notifications')
@section('page-title', 'Admin Notifications')
@php
    $adminNotificationsScript = asset('assets/js/admin-notifications-page.js') . '?v=' . @filemtime(public_path('assets/js/admin-notifications-page.js'));
    $adminNotificationsStyles = asset('assets/css/admin-notifications-page.css') . '?v=' . @filemtime(public_path('assets/css/admin-notifications-page.css'));
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ $adminNotificationsStyles }}">
@endpush

@section('content')
    <div class="admin-notifications-page"
        data-notification-read-count="{{ $readCount }}"
        data-notification-base-url="{{ url('/admin/notifications') }}">
        @include('admin.notifications.partials.summary')
        @include('admin.notifications.partials.panel')
    </div>
@endsection

@push('scripts')
    <script src="{{ $adminNotificationsScript }}" defer></script>
@endpush
