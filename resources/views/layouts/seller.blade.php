<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Seller Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/seller_dashboard.css') }}">
    @if(empty($disableFloatingChatWidget))
        <link rel="stylesheet" href="{{ asset('assets/css/messages.css') }}">
    @endif
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/image/favicon.png') }}">
    @vite(['resources/js/app.js'])
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }
    </style>

</head>

<body>
    <div class="page-wrapper">
        @include('partials.seller-header')

        <main class="page-content">
            @yield('content')
        </main>

        @include('partials.seller-footer')

        @if(auth('seller')->check() && empty($disableFloatingChatWidget))
            @include('messages.partials.floating-chat')
        @endif
    </div>

    <script src="{{ asset('assets/js/skeleton-loader.js') }}" defer></script>
</body>

</html>
