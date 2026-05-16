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
        const toast = document.getElementById('toast-success');

        if (toast) {
            window.setTimeout(function () {
                toast.classList.add('toast-hide');

                window.setTimeout(function () {
                    toast.remove();
                }, 400);
            }, 3000);
        }

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
