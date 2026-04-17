<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LocalLift PH')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    
</head>
<body>

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

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
        }, 3000); // visible for 3 seconds
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.add-to-cart-form');

    forms.forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault(); // ❗ stop page reload

            const url = form.action;
            const token = form.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                showToast(data.message || 'Added to cart!');
            } catch (error) {
                showToast('Something went wrong.', true);
            }
        });
    });

    function showToast(message, isError = false) {
        const toast = document.createElement('div');
        toast.className = 'toast-success';
        if (isError) toast.style.background = '#e74c3c';

        toast.innerHTML = `
            <i class="fa-solid fa-circle-check"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('toast-hide');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }
});
</script>
</body>
</html>