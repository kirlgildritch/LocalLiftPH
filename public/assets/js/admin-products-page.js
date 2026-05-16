(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    const readJsonScript = function (id, fallback) {
        const node = document.getElementById(id);

        if (!node) {
            return fallback;
        }

        try {
            return JSON.parse(node.textContent || '');
        } catch (error) {
            return fallback;
        }
    };

    onReady(function () {
        const products = readJsonScript('admin-products-modal-data', []);
        const byId = Object.fromEntries(products.map(function (product) {
            return [String(product.id), product];
        }));
        const selectAll = document.getElementById('select-all-products');
        const productCheckboxes = Array.from(document.querySelectorAll('.product-select'));
        const bulkSelectionCount = document.getElementById('bulk-selection-count');
        const bulkApproveButton = document.getElementById('bulk-approve-button');
        const bulkRejectButton = document.getElementById('bulk-reject-button');
        const bulkApproveForm = document.getElementById('bulk-approve-form');
        const bulkApproveIds = document.getElementById('bulk-approve-ids');
        const rejectModalForm = document.getElementById('reject-modal-form');
        const rejectModalAction = document.getElementById('reject-modal-action');
        const rejectModalProductIds = document.getElementById('reject-modal-product-ids');
        const rejectModalTitle = document.getElementById('reject-modal-title');
        const productModalApproveForm = document.getElementById('product-modal-approve-form');
        const productModalApproveButton = document.getElementById('product-modal-approve-button');
        const productModalRejectButton = document.getElementById('product-modal-reject-button');
        const productModalStage = document.getElementById('product-modal-stage');
        const productModalThumbs = document.getElementById('product-modal-thumbs');
        const productModalCounter = document.getElementById('product-modal-counter');
        const productModalPrev = document.getElementById('product-modal-prev');
        const productModalNext = document.getElementById('product-modal-next');
        const imagePreviewTitle = document.getElementById('image-preview-title');
        const imagePreviewImage = document.getElementById('image-preview-image');
        const sellerTabButtons = Array.from(document.querySelectorAll('[data-seller-tab]'));
        const sellerTabPanels = Array.from(document.querySelectorAll('[data-seller-panel]'));
        const sellerProductsList = document.getElementById('product-seller-products-list');
        const sellerDocumentTitle = document.getElementById('product-seller-document-title');
        const sellerDocumentType = document.getElementById('product-seller-document-type');
        const sellerDocumentSeller = document.getElementById('product-seller-document-seller');
        const sellerDocumentDate = document.getElementById('product-seller-document-date');
        const sellerDocumentStatus = document.getElementById('product-seller-document-status');
        const sellerDocumentPreviewStage = document.getElementById('product-seller-document-preview-stage');
        const sellerDocumentDownload = document.getElementById('product-seller-document-download');
        let activeProduct = null;
        let activeProductMediaIndex = 0;

        if (!bulkApproveForm || !productModalStage || !productModalThumbs || !productModalPrev || !productModalNext) {
            return;
        }

        const openModal = function (id) {
            const modal = document.getElementById(id);

            if (!modal) {
                return;
            }

            modal.hidden = false;
            document.body.classList.add('is-modal-open');
        };

        const closeModal = function (id) {
            const modal = document.getElementById(id);

            if (!modal) {
                return;
            }

            if (id === 'product-approval-modal') {
                pauseActiveProductVideo();
            }

            modal.hidden = true;

            if (![...document.querySelectorAll('.modal-shell')].some(function (item) { return !item.hidden; })) {
                document.body.classList.remove('is-modal-open');
            }
        };

        const getSelectedIds = function () {
            return productCheckboxes.filter(function (checkbox) {
                return checkbox.checked;
            }).map(function (checkbox) {
                return checkbox.value;
            });
        };

        const fillIds = function (container, ids) {
            if (!container) {
                return;
            }

            container.innerHTML = '';

            ids.forEach(function (id) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'product_ids[]';
                input.value = id;
                container.appendChild(input);
            });
        };

        const updateBulkState = function () {
            const count = getSelectedIds().length;

            if (bulkSelectionCount) {
                bulkSelectionCount.textContent = count + ' selected';
            }

            if (bulkApproveButton) {
                bulkApproveButton.disabled = count === 0;
            }

            if (bulkRejectButton) {
                bulkRejectButton.disabled = count === 0;
            }

            if (selectAll) {
                selectAll.checked = count > 0 && count === productCheckboxes.length;
                selectAll.indeterminate = count > 0 && count < productCheckboxes.length;
            }
        };

        const openImagePreview = function (src, title) {
            if (!src || !imagePreviewImage || !imagePreviewTitle) {
                return;
            }

            imagePreviewTitle.textContent = title || 'Product Image';
            imagePreviewImage.src = src;
            imagePreviewImage.alt = title || 'Product Image';
            openModal('image-preview-modal');
        };

        const fileExtension = function (url) {
            try {
                return new URL(url, window.location.origin).pathname.split('.').pop().toLowerCase();
            } catch (error) {
                return '';
            }
        };

        const activateSellerTab = function (tab) {
            sellerTabButtons.forEach(function (button) {
                button.classList.toggle('is-active', button.dataset.sellerTab === tab);
            });

            sellerTabPanels.forEach(function (panel) {
                panel.hidden = panel.dataset.sellerPanel !== tab;
            });
        };

        const escapeHtml = function (value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const getProductMediaItems = function (product) {
            const items = Array.isArray(product?.media_items) && product.media_items.length
                ? product.media_items
                : [{ type: 'image', url: product?.image_url || '', is_fallback: !product?.image_url }];

            return items.map(function (item) {
                return {
                    type: item?.type === 'video' ? 'video' : 'image',
                    url: item?.url || '',
                    is_fallback: Boolean(item?.is_fallback),
                };
            });
        };

        const setAdminVideoState = function (shell, video) {
            shell.classList.toggle('is-playing', !video.paused && !video.ended);
        };

        const renderCircleAvatar = function (element, label, imageUrl) {
            if (!element) {
                return;
            }

            if (imageUrl) {
                element.textContent = '';
                const image = document.createElement('img');
                image.src = imageUrl;
                image.alt = label;
                element.replaceChildren(image);
                return;
            }

            element.textContent = label;
        };

        const resetActionButtonState = function (button) {
            if (!(button instanceof HTMLElement)) {
                return;
            }

            window.LocalLiftActionLoading?.stop(button);
            button.disabled = false;
            button.classList.remove('is-static');
        };

        const buildProductMediaSlide = function (item, productName) {
            const slide = document.createElement('div');
            slide.className = 'admin-product-gallery__slide';

            if (item.type === 'video' && item.url) {
                slide.innerHTML = [
                    '<div class="admin-product-gallery__video" data-admin-product-video-shell>',
                    '<video src="', escapeHtml(item.url), '" preload="metadata" playsinline class="admin-product-gallery__media" data-admin-product-video></video>',
                    '<button type="button" class="admin-product-gallery__play" data-admin-product-play aria-label="Play video">',
                    '<i class="fa-solid fa-play"></i>',
                    '</button>',
                    '</div>',
                ].join('');

                const videoShell = slide.querySelector('[data-admin-product-video-shell]');
                const video = slide.querySelector('[data-admin-product-video]');
                const playButton = slide.querySelector('[data-admin-product-play]');

                playButton?.addEventListener('click', async function () {
                    try {
                        await video.play();
                    } catch (error) {
                        // Ignore and let the admin try again.
                    }
                });

                video?.addEventListener('play', function () {
                    setAdminVideoState(videoShell, video);
                });
                video?.addEventListener('pause', function () {
                    setAdminVideoState(videoShell, video);
                });
                video?.addEventListener('ended', function () {
                    setAdminVideoState(videoShell, video);
                });

                if (videoShell && video) {
                    setAdminVideoState(videoShell, video);
                }

                return slide;
            }

            if (item.url) {
                slide.innerHTML = '<img src="' + escapeHtml(item.url) + '" alt="' + escapeHtml(productName) + '" class="admin-product-gallery__media" data-image-preview="' + escapeHtml(item.url) + '" data-image-title="' + escapeHtml(productName) + '">';
                return slide;
            }

            const fallback = document.createElement('span');
            fallback.className = 'product-image-fallback';
            fallback.textContent = (productName || '?').trim().charAt(0).toUpperCase();
            slide.appendChild(fallback);
            return slide;
        };

        const pauseActiveProductVideo = function () {
            const currentVideo = productModalStage.querySelector('[data-admin-product-video]');

            if (currentVideo) {
                currentVideo.pause();
            }
        };

        const renderProductThumbs = function (product, items) {
            productModalThumbs.innerHTML = '';

            items.forEach(function (item, index) {
                const thumb = document.createElement('button');
                thumb.type = 'button';
                thumb.className = 'thumb-image-button' + (index === activeProductMediaIndex ? ' is-active' : '');
                thumb.dataset.mediaIndex = String(index);

                if (item.type === 'video' && item.url) {
                    const video = document.createElement('video');
                    video.src = item.url;
                    video.muted = true;
                    video.playsInline = true;
                    video.preload = 'metadata';
                    thumb.appendChild(video);

                    const badge = document.createElement('span');
                    badge.className = 'thumb-image-button__badge';
                    badge.innerHTML = '<i class="fa-solid fa-play"></i>';
                    thumb.appendChild(badge);
                } else if (item.url) {
                    const image = document.createElement('img');
                    image.src = item.url;
                    image.alt = product.name;
                    thumb.appendChild(image);
                } else {
                    const fallback = document.createElement('span');
                    fallback.className = 'product-image-fallback';
                    fallback.textContent = String(product.name || '?').charAt(0).toUpperCase();
                    thumb.appendChild(fallback);
                }

                thumb.addEventListener('click', function () {
                    setActiveProductMedia(product, index);
                });

                productModalThumbs.appendChild(thumb);
            });
        };

        const setActiveProductMedia = function (product, index) {
            const items = getProductMediaItems(product);

            if (!items[index]) {
                return;
            }

            pauseActiveProductVideo();
            activeProductMediaIndex = index;
            productModalStage.innerHTML = '';
            productModalStage.appendChild(buildProductMediaSlide(items[index], product.name));
            productModalCounter.textContent = (index + 1) + ' / ' + items.length;
            productModalPrev.disabled = items.length < 2;
            productModalNext.disabled = items.length < 2;
            renderProductThumbs(product, items);
        };

        const renderProductGallery = function (product) {
            const items = getProductMediaItems(product);
            activeProductMediaIndex = 0;
            setActiveProductMedia(product, 0);

            productModalPrev.onclick = function () {
                const nextIndex = (activeProductMediaIndex - 1 + items.length) % items.length;
                setActiveProductMedia(product, nextIndex);
            };

            productModalNext.onclick = function () {
                const nextIndex = (activeProductMediaIndex + 1) % items.length;
                setActiveProductMedia(product, nextIndex);
            };
        };

        const renderSellerProducts = function (sellerProducts) {
            if (!sellerProductsList) {
                return;
            }

            sellerProductsList.innerHTML = '';

            if (!sellerProducts || sellerProducts.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'sub-line empty-table';
                empty.textContent = 'No products yet.';
                sellerProductsList.appendChild(empty);
                return;
            }

            sellerProducts.forEach(function (sellerProduct) {
                const row = document.createElement('div');
                row.className = 'seller-product-row';

                const thumb = document.createElement('button');
                thumb.type = 'button';
                thumb.className = 'seller-product-thumb';
                thumb.dataset.imagePreview = sellerProduct.image_url || '';
                thumb.dataset.imageTitle = sellerProduct.name;

                if (sellerProduct.image_url) {
                    const image = document.createElement('img');
                    image.src = sellerProduct.image_url;
                    image.alt = sellerProduct.name;
                    thumb.appendChild(image);
                } else {
                    thumb.textContent = String(sellerProduct.name || '?').charAt(0).toUpperCase();
                }

                const body = document.createElement('div');
                body.className = 'seller-product-meta';
                body.innerHTML = [
                    '<div class="product-title">', escapeHtml(sellerProduct.name), '</div>',
                    '<div class="seller-product-meta-row">',
                    '<span>', escapeHtml(sellerProduct.category), '</span>',
                    '<strong>', escapeHtml(sellerProduct.price), '</strong>',
                    '<span>Stock ', escapeHtml(sellerProduct.stock), '</span>',
                    '</div>',
                    '<div class="seller-product-meta-row">',
                    '<span class="status-pill ', escapeHtml(sellerProduct.status_class), '">', escapeHtml(sellerProduct.status_label), '</span>',
                    '<span>', escapeHtml(sellerProduct.date_added), '</span>',
                    '</div>',
                ].join('');

                row.appendChild(thumb);
                row.appendChild(body);
                sellerProductsList.appendChild(row);
            });
        };

        const openSellerDocumentModal = function (config) {
            if (!config.url || !sellerDocumentPreviewStage || !sellerDocumentDownload) {
                return;
            }

            sellerDocumentTitle.textContent = config.title;
            sellerDocumentType.textContent = config.type;
            sellerDocumentSeller.textContent = config.sellerName;
            sellerDocumentDate.textContent = config.uploadDate;
            sellerDocumentStatus.textContent = config.status;
            sellerDocumentDownload.href = config.url;
            sellerDocumentDownload.setAttribute('download', config.filename || config.type || 'document');

            const isPdf = fileExtension(config.url) === 'pdf';
            sellerDocumentPreviewStage.innerHTML = isPdf
                ? '<iframe src="' + config.url + '" title="' + escapeHtml(config.type) + '"></iframe>'
                : '<img src="' + config.url + '" alt="' + escapeHtml(config.type) + '">';

            openModal('product-seller-document-modal');
        };

        const openRejectModal = function (config) {
            if (!rejectModalForm || !rejectModalAction || !rejectModalProductIds || !rejectModalTitle) {
                return;
            }

            rejectModalForm.reset();
            rejectModalProductIds.innerHTML = '';
            rejectModalAction.value = '';

            if (config.mode === 'bulk') {
                rejectModalForm.action = bulkApproveForm.action;
                rejectModalAction.value = 'reject';
                fillIds(rejectModalProductIds, config.ids);
                rejectModalTitle.textContent = 'Reject Selected Products';
            } else {
                rejectModalForm.action = config.url;
                rejectModalTitle.textContent = 'Reject ' + config.name;
            }

            openModal('reject-reason-modal');
        };

        const renderProductModal = function (product) {
            activeProduct = product;

            document.getElementById('product-modal-title').textContent = product.name;
            document.getElementById('product-modal-category').textContent = product.category;
            document.getElementById('product-modal-shop').textContent = product.shop_name;
            document.getElementById('product-modal-status').textContent = product.status_label;
            document.getElementById('product-modal-submitted').textContent = product.submitted_at;
            document.getElementById('product-modal-price').textContent = product.price;
            document.getElementById('product-modal-shipping').textContent = product.shipping_fee;
            document.getElementById('product-modal-stock').textContent = product.stock;
            document.getElementById('product-modal-condition').textContent = product.condition;
            document.getElementById('product-modal-dimensions').textContent = product.dimensions;
            document.getElementById('product-modal-weight').textContent = product.weight;
            document.getElementById('product-modal-description').textContent = product.description;
            document.getElementById('product-modal-reports').textContent = String(product.pending_reports_count);
            document.getElementById('product-modal-rejection').textContent = product.rejection_reason;
            renderCircleAvatar(
                document.getElementById('product-modal-seller-avatar'),
                product.avatar,
                product.seller_avatar_url
            );
            document.getElementById('product-modal-seller-handle').textContent = product.seller;
            document.getElementById('product-modal-seller-name').textContent = product.seller_name;

            renderProductGallery(product);

            productModalApproveForm.action = product.approve_url;
            productModalApproveForm.hidden = false;
            resetActionButtonState(productModalApproveButton);
            resetActionButtonState(productModalRejectButton);

            productModalApproveButton.textContent = product.can_approve ? 'Approve' : 'Approved';
            productModalApproveButton.disabled = !product.can_approve;
            productModalApproveButton.classList.toggle('is-static', !product.can_approve);

            productModalRejectButton.hidden = false;
            productModalRejectButton.textContent = product.can_reject ? 'Reject' : 'Rejected';
            productModalRejectButton.disabled = !product.can_reject;
            productModalRejectButton.classList.toggle('is-static', !product.can_reject);
            productModalRejectButton.dataset.rejectUrl = product.reject_url;
            productModalRejectButton.dataset.rejectName = product.name;

            openModal('product-approval-modal');
        };

        document.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(button.dataset.closeModal);
            });
        });

        document.querySelectorAll('.modal-shell').forEach(function (modalShell) {
            modalShell.addEventListener('click', function (event) {
                if (event.target === modalShell) {
                    closeModal(modalShell.id);
                }
            });
        });

        document.querySelectorAll('[data-product-view]').forEach(function (button) {
            button.addEventListener('click', function () {
                const product = byId[button.dataset.productView];

                if (product) {
                    renderProductModal(product);
                }
            });
        });

        document.querySelectorAll('[data-reject-url]').forEach(function (button) {
            button.addEventListener('click', function () {
                openRejectModal({
                    mode: 'single',
                    url: button.dataset.rejectUrl,
                    name: button.dataset.rejectName || 'Product',
                });
            });
        });

        sellerTabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activateSellerTab(button.dataset.sellerTab);
            });
        });

        document.addEventListener('click', function (event) {
            const previewTrigger = event.target.closest('[data-image-preview]');

            if (!previewTrigger) {
                return;
            }

            const src = previewTrigger.dataset.imagePreview;
            const title = previewTrigger.dataset.imageTitle;

            if (src) {
                openImagePreview(src, title);
            }
        });

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                productCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
                updateBulkState();
            });
        }

        productCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateBulkState);
        });

        bulkApproveButton?.addEventListener('click', function () {
            const ids = getSelectedIds();

            if (ids.length === 0) {
                return;
            }

            fillIds(bulkApproveIds, ids);
            window.LocalLiftActionLoading?.start(bulkApproveButton, { label: 'Approving...' });

            if (typeof bulkApproveForm.requestSubmit === 'function') {
                bulkApproveForm.requestSubmit();
                return;
            }

            bulkApproveForm.submit();
        });

        bulkRejectButton?.addEventListener('click', function () {
            const ids = getSelectedIds();

            if (ids.length === 0) {
                return;
            }

            openRejectModal({
                mode: 'bulk',
                ids: ids,
            });
        });

        productModalRejectButton?.addEventListener('click', function () {
            if (!activeProduct) {
                return;
            }

            closeModal('product-approval-modal');
            openRejectModal({
                mode: 'single',
                url: activeProduct.reject_url,
                name: activeProduct.name,
            });
        });

        document.getElementById('product-modal-open-seller')?.addEventListener('click', function () {
            if (!activeProduct) {
                return;
            }

            activateSellerTab('shop');
            document.getElementById('product-seller-modal-handle').textContent = activeProduct.seller;
            renderCircleAvatar(
                document.getElementById('product-seller-modal-avatar'),
                activeProduct.avatar,
                activeProduct.seller_avatar_url
            );
            document.getElementById('product-seller-modal-username').textContent = activeProduct.shop_name;
            document.getElementById('product-seller-modal-fullname').textContent = activeProduct.seller_name;
            document.getElementById('product-seller-shop-name').textContent = activeProduct.shop_name;
            document.getElementById('product-seller-owner-name').textContent = activeProduct.seller_owner_name;
            document.getElementById('product-seller-email').textContent = activeProduct.seller_email;
            document.getElementById('product-seller-phone').textContent = activeProduct.seller_phone;
            document.getElementById('product-seller-address').textContent = activeProduct.seller_address;
            document.getElementById('product-seller-description').textContent = activeProduct.seller_description;
            document.getElementById('product-seller-status-text').textContent = activeProduct.seller_status_label;
            document.getElementById('product-seller-registered').textContent = activeProduct.seller_registered_at;
            document.getElementById('product-seller-verification').textContent = activeProduct.seller_verification_status;

            const sellerStatus = document.getElementById('product-seller-modal-status');
            sellerStatus.className = 'status-pill ' + activeProduct.seller_status_class;
            sellerStatus.textContent = activeProduct.seller_status_label;

            document.getElementById('product-seller-id-label').textContent = activeProduct.seller_id_type;
            document.getElementById('product-seller-id-meta').textContent = 'Uploaded ' + activeProduct.seller_submitted_at;
            document.getElementById('product-seller-permit-meta').textContent = activeProduct.seller_permit_url
                ? 'Uploaded ' + activeProduct.seller_submitted_at
                : 'Optional / Not uploaded';

            const idLink = document.getElementById('product-seller-id-link');
            const permitLink = document.getElementById('product-seller-permit-link');
            renderSellerProducts(activeProduct.seller_products);

            idLink.disabled = !activeProduct.seller_id_url;
            permitLink.disabled = !activeProduct.seller_permit_url;

            openModal('product-seller-modal');
        });

        document.getElementById('product-seller-id-link')?.addEventListener('click', function () {
            if (!activeProduct || !activeProduct.seller_id_url) {
                return;
            }

            openSellerDocumentModal({
                title: 'Document Preview',
                type: activeProduct.seller_id_type,
                sellerName: activeProduct.seller_owner_name,
                uploadDate: activeProduct.seller_submitted_at,
                status: activeProduct.seller_status_label,
                url: activeProduct.seller_id_url,
                filename: (activeProduct.seller_owner_name || 'seller') + '-' + (activeProduct.seller_id_type || 'document'),
            });
        });

        document.getElementById('product-seller-permit-link')?.addEventListener('click', function () {
            if (!activeProduct || !activeProduct.seller_permit_url) {
                return;
            }

            openSellerDocumentModal({
                title: 'Document Preview',
                type: 'Business License / Permit',
                sellerName: activeProduct.seller_owner_name,
                uploadDate: activeProduct.seller_submitted_at,
                status: activeProduct.seller_status_label,
                url: activeProduct.seller_permit_url,
                filename: (activeProduct.seller_owner_name || 'seller') + '-business-license',
            });
        });

        updateBulkState();
    });
}());
