<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $currentRoute = request()->route()?->getName();
        $sellerRouteTitle = match (true) {
            $currentRoute === 'seller.dashboard' => 'Seller Dashboard',
            $currentRoute === 'seller.products.index' => 'Manage Products',
            $currentRoute === 'seller.products.create' => 'Add Product',
            $currentRoute === 'seller.products.edit' => 'Edit Product',
            $currentRoute === 'seller.products.reviews' => 'Product Reviews',
            $currentRoute === 'seller.orders' => 'Seller Orders',
            $currentRoute === 'seller.earnings' => 'Earnings',
            $currentRoute === 'seller.messages',
            $currentRoute === 'seller.messages.show' => 'Seller Messages',
            $currentRoute === 'seller.profile' => 'Seller Profile',
            $currentRoute === 'seller.settings' => 'Seller Settings',
            $currentRoute === 'seller.search' => 'Seller Search',
            $currentRoute === 'seller.shop.preview' => 'Shop Preview',
            default => 'Seller Dashboard',
        };

        $sellerDocumentTitle = trim($__env->yieldContent('title')) ?: ($title ?? $sellerRouteTitle);
        $sellerDashboardCss = asset('assets/css/seller_dashboard.css') . '?v=' . @filemtime(public_path('assets/css/seller_dashboard.css'));
        $sellerMessagesCss = asset('assets/css/messages.css') . '?v=' . @filemtime(public_path('assets/css/messages.css'));
    @endphp
    <title>{{ $sellerDocumentTitle }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ $sellerDashboardCss }}">
    @if(empty($disableFloatingChatWidget))
        <link rel="stylesheet" href="{{ $sellerMessagesCss }}">
    @endif
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/image/favicon.png') }}">
    @vite(['resources/js/app.js'])
    @stack('styles')
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }
    </style>

</head>

<body data-loading-scope="explicit">
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;

            if (!AudioContextClass) {
                return;
            }

            let audioContext = null;
            let audioUnlocked = false;

            const interactionEvents = ['pointerdown', 'keydown', 'touchstart'];

            const removeInteractionListeners = () => {
                interactionEvents.forEach((eventName) => {
                    document.removeEventListener(eventName, unlockAudioContext);
                });
            };

            const ensureAudioContext = async () => {
                try {
                    if (!audioContext) {
                        audioContext = new AudioContextClass();
                    }

                    if (audioContext.state === 'suspended') {
                        await audioContext.resume();
                    }

                    return audioContext.state === 'running';
                } catch (error) {
                    return false;
                }
            };

            const unlockAudioContext = async () => {
                audioUnlocked = await ensureAudioContext();

                if (audioUnlocked) {
                    removeInteractionListeners();
                }
            };

            const playNotificationChime = async () => {
                if (!audioUnlocked) {
                    return;
                }

                const isReady = await ensureAudioContext();

                if (!isReady || !audioContext) {
                    return;
                }

                const now = audioContext.currentTime;
                const masterGain = audioContext.createGain();
                masterGain.connect(audioContext.destination);
                masterGain.gain.setValueAtTime(0.0001, now);
                masterGain.gain.exponentialRampToValueAtTime(0.045, now + 0.02);
                masterGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.62);

                const firstTone = audioContext.createOscillator();
                firstTone.type = 'sine';
                firstTone.frequency.setValueAtTime(880, now);
                firstTone.frequency.exponentialRampToValueAtTime(1046.5, now + 0.18);
                firstTone.connect(masterGain);
                firstTone.start(now);
                firstTone.stop(now + 0.22);

                const secondTone = audioContext.createOscillator();
                secondTone.type = 'sine';
                secondTone.frequency.setValueAtTime(1174.7, now + 0.16);
                secondTone.frequency.exponentialRampToValueAtTime(1318.5, now + 0.34);
                secondTone.connect(masterGain);
                secondTone.start(now + 0.16);
                secondTone.stop(now + 0.4);

                window.setTimeout(() => {
                    masterGain.disconnect();
                }, 900);
            };

            interactionEvents.forEach((eventName) => {
                document.addEventListener(eventName, unlockAudioContext, { passive: true });
            });

            document.addEventListener('seller:notification-received', (event) => {
                if (event.detail?.read_at) {
                    return;
                }

                void playNotificationChime();
            });
        });
    </script>
    <script src="{{ asset('assets/js/skeleton-loader.js') }}" defer></script>
    @stack('scripts')
</body>

</html>
