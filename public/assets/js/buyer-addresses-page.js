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
        const modal = document.getElementById('editAddressModal');
        const closeBtn = document.getElementById('closeEditAddressModal');
        const cancelBtn = document.getElementById('cancelEditAddressModal');
        const form = document.getElementById('editAddressForm');
        const body = document.body;

        if (!modal || !closeBtn || !cancelBtn || !form) {
            return;
        }

        const baseUpdateUrl = form.dataset.baseUpdateUrl || '';
        const shouldAutoOpen = form.dataset.autoOpen === '1';

        const openModal = function () {
            modal.classList.add('show');
            body.classList.add('modal-open');
        };

        const closeModal = function () {
            modal.classList.remove('show');
            body.classList.remove('modal-open');
        };

        const setLabel = function (label) {
            const home = document.getElementById('edit_label_home');
            const work = document.getElementById('edit_label_work');
            const other = document.getElementById('edit_label_other');

            if (home) {
                home.checked = label === 'Home';
            }

            if (work) {
                work.checked = label === 'Work';
            }

            if (other) {
                other.checked = label === 'Other';
            }
        };

        document.querySelectorAll('.open-edit-address').forEach(function (button) {
            button.addEventListener('click', async function () {
                const id = button.dataset.id;
                const locationValues = {
                    region: button.dataset.region || '',
                    province: button.dataset.province || '',
                    city: button.dataset.city || '',
                    barangay: button.dataset.barangay || '',
                };

                form.action = baseUpdateUrl ? baseUpdateUrl + '/' + id : form.action;
                document.getElementById('edit_address_id').value = id;
                document.getElementById('edit_full_name').value = button.dataset.full_name || '';
                document.getElementById('edit_phone').value = button.dataset.phone || '';
                document.getElementById('edit_street_address').value = button.dataset.street_address || '';
                document.getElementById('edit_postal_code').value = button.dataset.postal_code || '';
                document.getElementById('edit_landmark').value = button.dataset.landmark || '';
                document.getElementById('edit_is_default').checked = button.dataset.is_default === '1';
                setLabel(button.dataset.label || '');

                if (window.LocalLiftAddressForm?.init) {
                    await window.LocalLiftAddressForm.init(form, locationValues);
                }

                openModal();
            });
        });

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        if (shouldAutoOpen) {
            const initPromise = window.LocalLiftAddressForm?.init
                ? window.LocalLiftAddressForm.init(form)
                : Promise.resolve();

            Promise.resolve(initPromise).finally(openModal);
        }
    });
}());
