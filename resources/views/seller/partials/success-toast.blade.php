@if(session('success'))
    <div id="toast-success" class="toast-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>

    <style>
        .toast-success {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, rgba(46, 204, 113, 0.95), rgba(39, 174, 96, 0.95));
            color: #fff;
            padding: 14px 18px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            opacity: 0;
            transform: translateY(-20px);
            animation: toastFadeIn 0.4s ease forwards;
        }

        .toast-success i {
            font-size: 16px;
        }

        .toast-hide {
            animation: toastFadeOut 0.4s ease forwards;
        }

        @keyframes toastFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes toastFadeOut {
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('toast-success');

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
@endif
