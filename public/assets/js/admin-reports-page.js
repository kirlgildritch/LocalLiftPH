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
        const reports = readJsonScript('admin-reports-modal-data', []);
        const byId = Object.fromEntries(reports.map(function (report) {
            return [String(report.id), report];
        }));
        let activeReport = null;

        if (!Array.isArray(reports)) {
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

            modal.hidden = true;

            if (![...document.querySelectorAll('.modal-shell')].some(function (item) { return !item.hidden; })) {
                document.body.classList.remove('is-modal-open');
            }
        };

        const fileExtension = function (url) {
            try {
                return new URL(url, window.location.origin).pathname.split('.').pop().toLowerCase();
            } catch (error) {
                return '';
            }
        };

        const openDocumentModal = function (title, label, url) {
            const stage = document.getElementById('report-document-stage');

            if (!url || !stage) {
                return;
            }

            document.getElementById('report-document-title').textContent = title;
            document.getElementById('report-document-label').textContent = label;
            stage.innerHTML = fileExtension(url) === 'pdf'
                ? '<iframe src="' + url + '" class="document-preview-frame" title="' + label + '"></iframe>'
                : '<img src="' + url + '" alt="' + label + '" class="document-preview-image">';

            openModal('report-document-modal');
        };

        const renderHistory = function (actions) {
            const list = document.getElementById('report-history-list');

            if (!list) {
                return;
            }

            list.innerHTML = '';

            if (!actions || actions.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'report-history-item';
                empty.innerHTML = '<strong>No actions yet.</strong>';
                list.appendChild(empty);
                return;
            }

            actions.forEach(function (action) {
                const item = document.createElement('div');
                item.className = 'report-history-item';
                item.innerHTML = [
                    '<strong>', action.label, '</strong>',
                    '<p>', action.notes, '</p>',
                    '<div class="report-history-meta">Handled by ', action.handled_by, ' on ', action.handled_at, '</div>',
                ].join('');
                list.appendChild(item);
            });
        };

        const submitReportAction = function (action, triggerButton) {
            const form = document.getElementById('report-action-form');
            const actionInput = document.getElementById('report-action-input');

            if (!form || !actionInput) {
                return;
            }

            actionInput.value = action;

            if (triggerButton) {
                window.LocalLiftActionLoading?.start(triggerButton, { label: 'Saving...' });
            }

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
                return;
            }

            form.submit();
        };

        const renderActionToolbar = function (report) {
            const toolbar = document.getElementById('report-action-toolbar');

            if (!toolbar) {
                return;
            }

            toolbar.innerHTML = '';

            const addButton = function (label, className, onClick) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = className;
                button.textContent = label;
                button.addEventListener('click', onClick);
                toolbar.appendChild(button);
            };

            if (report.target_type === 'Product') {
                addButton('View Product', 'action-button action-button--primary', function () {
                    openProductModal(report);
                });
            } else {
                addButton('View Seller', 'action-button action-button--primary', function () {
                    openSellerModal(report);
                });
            }

            if (report.is_final) {
                return;
            }

            addButton('Warn Seller', 'action-button action-button--warning', function (event) {
                submitReportAction('warn_seller', event.currentTarget);
            });

            if (report.target_type === 'Product') {
                addButton('Hide / Delist Product', 'action-button action-button--light', function (event) {
                    submitReportAction('delist_product', event.currentTarget);
                });
                addButton('Ban / Remove Product', 'action-button action-button--danger', function (event) {
                    submitReportAction('ban_product', event.currentTarget);
                });
            }

            addButton('Suspend Seller Account', 'action-button action-button--danger', function (event) {
                submitReportAction('suspend_seller', event.currentTarget);
            });
            addButton('Dismiss Report', 'button', function (event) {
                submitReportAction('dismiss_report', event.currentTarget);
            });
            addButton('Mark as Resolved', 'action-button action-button--success', function (event) {
                submitReportAction('mark_resolved', event.currentTarget);
            });
        };

        const openProductModal = function (report) {
            const product = report.product;
            const preview = document.getElementById('report-product-preview');

            if (!product || !preview) {
                return;
            }

            preview.innerHTML = product.image_url
                ? '<img src="' + product.image_url + '" alt="' + product.name + '" class="report-preview-image">'
                : '<div class="report-preview-fallback">' + String(product.name || '?').charAt(0).toUpperCase() + '</div>';

            document.getElementById('report-product-name').textContent = product.name;
            document.getElementById('report-product-category').textContent = product.category;
            document.getElementById('report-product-seller').textContent = product.seller_name;
            document.getElementById('report-product-price').textContent = product.price;
            document.getElementById('report-product-stock').textContent = product.stock;
            document.getElementById('report-product-condition').textContent = product.condition;
            document.getElementById('report-product-shipping').textContent = product.shipping_fee;
            document.getElementById('report-product-status').textContent = product.status_label;
            document.getElementById('report-product-reports').textContent = String(product.reports_count);
            document.getElementById('report-product-description').textContent = product.description;
            openModal('report-product-modal');
        };

        const openSellerModal = function (report) {
            const seller = report.seller;

            if (!seller) {
                return;
            }

            document.getElementById('report-seller-avatar').textContent = String(seller.shop_name || '?').charAt(0).toUpperCase();
            document.getElementById('report-seller-shop-name').textContent = seller.shop_name;
            document.getElementById('report-seller-owner-name').textContent = seller.owner_name;
            document.getElementById('report-seller-email').textContent = seller.email;
            document.getElementById('report-seller-phone').textContent = seller.phone;
            document.getElementById('report-seller-address').textContent = seller.address;
            document.getElementById('report-seller-status').textContent = seller.status_label;
            document.getElementById('report-seller-products-count').textContent = String(seller.products_count);
            document.getElementById('report-seller-description').textContent = seller.description;
            document.getElementById('report-seller-suspension-reason').textContent = seller.suspension_reason;
            document.getElementById('report-seller-id-type').textContent = seller.valid_id_type;

            const idView = document.getElementById('report-seller-id-view');
            const permitView = document.getElementById('report-seller-permit-view');
            idView.disabled = !seller.valid_id_url;
            permitView.disabled = !seller.business_permit_url;

            openModal('report-seller-modal');
        };

        document.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(button.dataset.closeModal);
            });
        });

        document.querySelectorAll('.modal-shell').forEach(function (shell) {
            shell.addEventListener('click', function (event) {
                if (event.target === shell) {
                    closeModal(shell.id);
                }
            });
        });

        document.querySelectorAll('[data-report-view]').forEach(function (button) {
            button.addEventListener('click', function () {
                const report = byId[button.dataset.reportView];

                if (!report) {
                    return;
                }

                activeReport = report;
                document.getElementById('report-detail-target').textContent = report.target_name;
                document.getElementById('report-detail-seller').textContent = report.seller_name;
                document.getElementById('report-detail-type').textContent = report.target_type;
                document.getElementById('report-detail-reporter').textContent = report.reporter_name;
                document.getElementById('report-detail-reason').textContent = report.reason_label;
                document.getElementById('report-detail-submitted-date').textContent = report.submitted_at;
                document.getElementById('report-detail-message').textContent = report.message;
                document.getElementById('report-admin-notes').value = '';

                const thumb = document.getElementById('report-detail-thumb');
                thumb.innerHTML = report.target_type === 'Seller'
                    ? '<i class="fa-solid fa-store"></i>'
                    : '<i class="fa-solid fa-box-open"></i>';

                const statusNode = document.getElementById('report-detail-status');
                statusNode.textContent = report.status_label;
                statusNode.className = 'status-pill status-pill--' + report.status_class;

                const form = document.getElementById('report-action-form');
                form.action = report.action_url;

                renderHistory(report.actions);
                renderActionToolbar(report);
                openModal('report-detail-modal');
            });
        });

        document.getElementById('report-seller-id-view')?.addEventListener('click', function () {
            if (!activeReport?.seller?.valid_id_url) {
                return;
            }

            openDocumentModal('Document Preview', activeReport.seller.valid_id_type, activeReport.seller.valid_id_url);
        });

        document.getElementById('report-seller-permit-view')?.addEventListener('click', function () {
            if (!activeReport?.seller?.business_permit_url) {
                return;
            }

            openDocumentModal('Document Preview', 'Business License', activeReport.seller.business_permit_url);
        });
    });
}());
