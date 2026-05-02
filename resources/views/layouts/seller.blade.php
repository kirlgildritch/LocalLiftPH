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
    @php
        $sellerToast = null;

        foreach (['success', 'error', 'warning', 'info'] as $type) {
            if (session()->has($type)) {
                $sellerToast = [
                    'type' => $type,
                    'message' => session($type),
                ];
                break;
            }
        }

        if (! $sellerToast && $errors->any()) {
            $sellerToast = [
                'type' => 'error',
                'message' => $errors->first(),
            ];
        }

        $sellerToastIcon = $sellerToast
            ? match ($sellerToast['type']) {
                'error' => 'fa-circle-xmark',
                'warning' => 'fa-triangle-exclamation',
                'info' => 'fa-circle-info',
                default => 'fa-circle-check',
            }
            : null;
    @endphp

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

    @if ($sellerToast)
        <div
            id="seller-toast"
            class="toast-message toast-message--{{ $sellerToast['type'] }}"
            role="status"
            aria-live="polite"
        >
            <i class="fa-solid {{ $sellerToastIcon }}"></i>
            <span>{{ $sellerToast['message'] }}</span>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('seller-toast');

            if (!toast) {
                return;
            }

            window.setTimeout(() => {
                toast.classList.add('toast-hide');

                window.setTimeout(() => {
                    toast.remove();
                }, 400);
            }, 3000);
        });
    </script>
    <script src="{{ asset('assets/js/skeleton-loader.js') }}" defer></script>
</body>

</html>
