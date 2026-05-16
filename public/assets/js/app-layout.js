(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    onReady(function () {
        const toast = document.getElementById('app-toast') || document.getElementById('toast-success');

        if (toast) {
            window.setTimeout(function () {
                toast.classList.add('toast-hide');

                window.setTimeout(function () {
                    toast.remove();
                }, 400);
            }, 3000);
        }

        document.querySelectorAll('[data-copy-voucher-code]').forEach(function (button) {
            button.addEventListener('click', async function () {
                const code = button.getAttribute('data-copy-voucher-code') || '';
                const originalText = button.textContent;

                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(code);
                    } else {
                        const input = document.createElement('input');
                        input.value = code;
                        input.setAttribute('readonly', 'readonly');
                        input.style.position = 'fixed';
                        input.style.opacity = '0';
                        document.body.appendChild(input);
                        input.select();
                        document.execCommand('copy');
                        input.remove();
                    }

                    button.textContent = 'Copied';
                    button.classList.add('is-copied');

                    window.setTimeout(function () {
                        button.textContent = originalText;
                        button.classList.remove('is-copied');
                    }, 1800);
                } catch (error) {
                    button.textContent = code;
                }
            });
        });

        const reportModals = Array.from(document.querySelectorAll('.report-modal-shell[id]'));

        if (!reportModals.length) {
            return;
        }

        const syncBodyClass = function () {
            const hasVisibleModal = reportModals.some(function (modal) {
                return !modal.hidden;
            });

            document.body.classList.toggle('report-modal-open', hasVisibleModal);
        };

        reportModals.forEach(function (modal) {
            const modalId = modal.id;
            const openButtons = document.querySelectorAll('[data-report-open="' + modalId + '"]');
            const closeButtons = document.querySelectorAll('[data-report-close="' + modalId + '"]');

            const openModal = function () {
                modal.hidden = false;
                syncBodyClass();
            };

            const closeModal = function () {
                modal.hidden = true;
                syncBodyClass();
            };

            openButtons.forEach(function (button) {
                button.addEventListener('click', openModal);
            });

            closeButtons.forEach(function (button) {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
        });

        syncBodyClass();
    });
}());
