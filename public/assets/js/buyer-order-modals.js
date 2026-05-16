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
        const body = document.body;

        const bindCancelOrderModal = function () {
            const modal = document.getElementById('cancelOrderModal');
            const form = document.getElementById('cancelOrderForm');
            const otherWrap = document.getElementById('otherReasonWrap');

            if (!modal || !form || !otherWrap) {
                return;
            }

            const openModal = function () {
                modal.classList.add('show');
                body.classList.add('modal-open');
            };

            const closeModal = function () {
                modal.classList.remove('show');
                body.classList.remove('modal-open');
            };

            const syncOtherReason = function () {
                const otherInput = form.querySelector('input[value="Other"]');
                const isChecked = Boolean(otherInput && otherInput.checked);
                otherWrap.classList.toggle('is-visible', isChecked);
            };

            document.querySelectorAll('.open-cancel-order').forEach(function (button) {
                button.addEventListener('click', function () {
                    form.action = button.dataset.orderAction;
                    openModal();
                    syncOtherReason();
                });
            });

            modal.querySelectorAll('[data-close-cancel-modal]').forEach(function (button) {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            form.querySelectorAll('input[name="reasons[]"]').forEach(function (input) {
                input.addEventListener('change', syncOtherReason);
            });

            syncOtherReason();
        };

        const bindReturnRequestModal = function () {
            const modal = document.getElementById('returnRequestModal');
            const form = document.getElementById('returnRequestForm');

            if (!modal || !form) {
                return;
            }

            const openModal = function () {
                modal.classList.add('show');
                body.classList.add('modal-open');
            };

            const closeModal = function () {
                modal.classList.remove('show');
                body.classList.remove('modal-open');
            };

            document.querySelectorAll('.open-return-request').forEach(function (button) {
                button.addEventListener('click', function () {
                    form.action = button.dataset.orderAction;
                    openModal();
                });
            });

            modal.querySelectorAll('[data-close-return-modal]').forEach(function (button) {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
        };

        bindCancelOrderModal();
        bindReturnRequestModal();
    });
}());
