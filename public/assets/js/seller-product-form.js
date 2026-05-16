(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    const formatCurrency = function (value) {
        return '\u20B1 ' + Number(value || 0).toFixed(2);
    };

    const formatFileSize = function (bytes) {
        if (bytes < 1024 * 1024) {
            return Math.max(1, Math.round(bytes / 1024)) + ' KB';
        }

        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    };

    const initDescriptionEditor = function () {
        const editor = document.getElementById('editor');
        const hiddenDescription = document.getElementById('description');
        const form = document.querySelector('.product-form');

        if (!editor || !hiddenDescription || !form || typeof window.Quill !== 'function') {
            return;
        }

        const quill = new window.Quill('#editor', {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['link'],
                ],
            },
            theme: 'snow',
        });

        form.addEventListener('submit', function () {
            hiddenDescription.value = quill.root.innerHTML;
        });
    };

    const initShippingModal = function () {
        const modal = document.getElementById('shippingModal');
        const openButton = document.getElementById('openShippingModal');
        const closeButton = document.getElementById('closeShippingModal');
        const cancelButton = document.getElementById('cancelShippingModal');
        const saveButton = document.getElementById('saveShippingSetup');
        const feePreview = document.getElementById('shippingFeePreview');
        const summaryFee = document.getElementById('shippingSummaryFee');
        const summaryMeta = document.getElementById('shippingSummaryMeta');
        const modalWeight = document.getElementById('modal_weight');
        const modalWidth = document.getElementById('modal_width');
        const modalLength = document.getElementById('modal_length');
        const modalHeight = document.getElementById('modal_height');
        const hiddenWeight = document.getElementById('shipping_weight');
        const hiddenWidth = document.getElementById('shipping_width');
        const hiddenLength = document.getElementById('shipping_length');
        const hiddenHeight = document.getElementById('shipping_height');
        const hiddenFee = document.getElementById('shipping_fee');

        if (!modal || !openButton || !closeButton || !cancelButton || !saveButton || !feePreview || !summaryFee || !summaryMeta
            || !modalWeight || !modalWidth || !modalLength || !modalHeight || !hiddenWeight || !hiddenWidth
            || !hiddenLength || !hiddenHeight || !hiddenFee) {
            return;
        }

        const calculateFee = function () {
            const weight = parseFloat(modalWeight.value) || 0;
            const width = parseFloat(modalWidth.value) || 0;
            const length = parseFloat(modalLength.value) || 0;
            const height = parseFloat(modalHeight.value) || 0;
            const volumetricWeight = (width * length * height) / 5000;
            const billableWeight = Math.max(weight, volumetricWeight);
            const fee = billableWeight > 0 ? 60 + (billableWeight * 35) : 0;

            feePreview.textContent = formatCurrency(fee);

            return fee;
        };

        const updateShippingSummary = function () {
            const weight = hiddenWeight.value;
            const width = hiddenWidth.value;
            const length = hiddenLength.value;
            const height = hiddenHeight.value;
            const fee = parseFloat(hiddenFee.value || 0);

            if (!weight || !width || !length || !height || !fee) {
                summaryFee.textContent = 'Shipping fee not set';
                summaryMeta.textContent = 'Add package size and weight to calculate shipping.';
                return;
            }

            summaryFee.textContent = formatCurrency(fee);
            summaryMeta.textContent = weight + ' kg | ' + width + 'cm x ' + length + 'cm x ' + height + 'cm';
        };

        const openModal = function () {
            modal.classList.add('show');
        };

        const closeModal = function () {
            modal.classList.remove('show');
        };

        [modalWeight, modalWidth, modalLength, modalHeight].forEach(function (input) {
            input.addEventListener('input', calculateFee);
        });

        openButton.addEventListener('click', openModal);
        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        saveButton.addEventListener('click', function () {
            const fee = calculateFee();

            hiddenWeight.value = modalWeight.value;
            hiddenWidth.value = modalWidth.value;
            hiddenLength.value = modalLength.value;
            hiddenHeight.value = modalHeight.value;
            hiddenFee.value = fee.toFixed(2);

            updateShippingSummary();
            closeModal();
        });

        updateShippingSummary();
        calculateFee();
    };

    const initMediaPicker = function () {
        const mediaInput = document.querySelector('[data-product-media-input]');
        const previewGrid = document.querySelector('[data-product-media-preview]');
        const existingGallery = document.querySelector('[data-existing-media-gallery]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        const selectedFiles = [];
        const objectUrls = new Map();

        const syncExistingGalleryVisibility = function () {
            if (!existingGallery) {
                return;
            }

            const hasVisibleCards = Array.from(existingGallery.querySelectorAll('[data-existing-media-card]')).some(function (card) {
                return !card.hidden;
            });

            existingGallery.hidden = !hasVisibleCards;
        };

        if (existingGallery) {
            existingGallery.addEventListener('click', async function (event) {
                const removeButton = event.target.closest('[data-remove-existing-media]');
                const deleteUrl = existingGallery.dataset.existingMediaDeleteUrl || '';

                if (!removeButton || !deleteUrl || typeof window.fetch !== 'function') {
                    return;
                }

                const card = removeButton.closest('[data-existing-media-card]');
                const mediaPath = card ? card.dataset.mediaPath || '' : '';

                if (!card || !mediaPath) {
                    return;
                }

                removeButton.disabled = true;

                try {
                    const response = await fetch(deleteUrl, {
                        body: JSON.stringify({ path: mediaPath }),
                        headers: Object.assign({
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }, csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                        method: 'DELETE',
                    });

                    if (!response.ok) {
                        throw new Error('Unable to remove saved media.');
                    }

                    card.remove();
                    syncExistingGalleryVisibility();
                } catch (error) {
                    removeButton.disabled = false;
                    window.alert('Unable to remove this saved media right now. Please try again.');
                }
            });

            syncExistingGalleryVisibility();
        }

        if (!mediaInput || !previewGrid || !window.URL || typeof window.URL.createObjectURL !== 'function' || !window.DataTransfer) {
            return;
        }

        const revokeObjectUrls = function () {
            objectUrls.forEach(function (url) {
                URL.revokeObjectURL(url);
            });
            objectUrls.clear();
        };

        const syncInputFiles = function () {
            const transfer = new DataTransfer();

            selectedFiles.forEach(function (file) {
                transfer.items.add(file);
            });

            mediaInput.files = transfer.files;
        };

        const renderPreview = function () {
            revokeObjectUrls();
            previewGrid.innerHTML = '';
            previewGrid.hidden = selectedFiles.length === 0;

            selectedFiles.forEach(function (file, index) {
                const previewUrl = URL.createObjectURL(file);
                objectUrls.set(file.name + '-' + index + '-' + file.lastModified, previewUrl);

                const card = document.createElement('div');
                card.className = 'product-media-preview-card';

                const mediaWrap = document.createElement('div');
                mediaWrap.className = 'product-media-preview-media';

                if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = previewUrl;
                    video.controls = true;
                    video.muted = true;
                    video.preload = 'metadata';
                    mediaWrap.appendChild(video);
                } else {
                    const image = document.createElement('img');
                    image.src = previewUrl;
                    image.alt = file.name;
                    mediaWrap.appendChild(image);
                }

                const meta = document.createElement('div');
                meta.className = 'product-media-preview-meta';
                meta.textContent = file.name + ' (' + formatFileSize(file.size) + ')';

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'product-media-preview-remove';
                removeButton.setAttribute('aria-label', 'Remove ' + file.name);
                removeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                removeButton.addEventListener('click', function () {
                    selectedFiles.splice(index, 1);
                    syncInputFiles();
                    renderPreview();
                });

                card.appendChild(mediaWrap);
                card.appendChild(meta);
                card.appendChild(removeButton);
                previewGrid.appendChild(card);
            });
        };

        mediaInput.addEventListener('change', function () {
            const nextFiles = Array.from(mediaInput.files || []);

            if (nextFiles.length === 0) {
                syncInputFiles();
                return;
            }

            nextFiles.forEach(function (file) {
                const alreadySelected = selectedFiles.some(function (currentFile) {
                    return currentFile.name === file.name
                        && currentFile.size === file.size
                        && currentFile.lastModified === file.lastModified;
                });

                if (!alreadySelected) {
                    selectedFiles.push(file);
                }
            });

            syncInputFiles();
            renderPreview();
        });

        window.addEventListener('beforeunload', revokeObjectUrls, { once: true });
    };

    const initVariantBuilder = function () {
        const builder = document.querySelector('[data-variant-builder]');

        if (!builder) {
            return;
        }

        const toggle = builder.querySelector('[data-variant-toggle]');
        const list = builder.querySelector('[data-variant-list]');
        const addButton = builder.querySelector('[data-add-variant]');
        let nextIndex = Number(builder.dataset.nextIndex || 0);

        if (!toggle || !list || !addButton) {
            return;
        }

        const variantTemplate = function (index) {
            return [
                '<div class="variant-row" data-variant-row>',
                '<div class="form-group"><label>Variant Name</label><input type="text" name="variants[', index, '][name]" placeholder="e.g. Small / Red"></div>',
                '<div class="form-group"><label>SKU</label><input type="text" name="variants[', index, '][sku]" placeholder="Optional"></div>',
                '<div class="form-group"><label>Price</label><input type="number" name="variants[', index, '][price]" step="0.01" min="0" placeholder="0.00"></div>',
                '<div class="form-group"><label>Stock</label><input type="number" name="variants[', index, '][stock]" min="0" placeholder="0"></div>',
                '<div class="form-group"><label>Image</label><input type="file" name="variants[', index, '][image]" accept="image/*"></div>',
                '<div class="variant-row-actions">',
                '<input type="hidden" name="variants[', index, '][is_active]" value="0">',
                '<label class="variant-active-toggle"><input type="checkbox" name="variants[', index, '][is_active]" value="1" checked>Active</label>',
                '<button type="button" class="table-action danger" data-remove-variant>Remove</button>',
                '</div>',
                '</div>',
            ].join('');
        };

        const syncVisibility = function () {
            const enabled = toggle.checked;
            list.hidden = !enabled;
            addButton.hidden = !enabled;

            if (enabled && !list.querySelector('[data-variant-row]')) {
                list.insertAdjacentHTML('beforeend', variantTemplate(nextIndex++));
            }
        };

        toggle.addEventListener('change', syncVisibility);
        addButton.addEventListener('click', function () {
            list.insertAdjacentHTML('beforeend', variantTemplate(nextIndex++));
        });

        list.addEventListener('click', function (event) {
            const removeButton = event.target.closest('[data-remove-variant]');

            if (!removeButton) {
                return;
            }

            const rows = list.querySelectorAll('[data-variant-row]');

            if (rows.length <= 1) {
                const firstRow = rows[0];
                if (firstRow) {
                    firstRow.querySelectorAll('input[type="text"], input[type="number"]').forEach(function (input) {
                        input.value = '';
                    });
                }
                return;
            }

            const row = removeButton.closest('[data-variant-row]');
            if (row) {
                row.remove();
            }
        });

        syncVisibility();
    };

    onReady(function () {
        initDescriptionEditor();
        initShippingModal();
        initMediaPicker();
        initVariantBuilder();
    });
}());
