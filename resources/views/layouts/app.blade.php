<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LocalLift PH')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/messages.css') }}">
    <link rel="icon" href="{{ asset('assets/image/Logo.png') }}">
</head>

<body>

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @if(auth('web')->check())
        @include('messages.partials.floating-chat')
    @endif

    @if(session('success'))
        <div id="toast-success" class="toast-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('toast-success');

            if (toast) {
                setTimeout(() => {
                    toast.classList.add('toast-hide');

                    setTimeout(() => {
                        toast.remove();
                    }, 400);
                }, 3000);
            }
        });
    </script>
</body>

</html>