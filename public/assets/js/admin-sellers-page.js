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
        const sellers = readJsonScript('admin-sellers-modal-data', []);
        const sellerMap = Object.fromEntries(sellers.map(function (seller) {
            return [String(seller.id), seller];
        }));
        const documentModalTitle = document.getElementById('seller-document-modal-title');
        const documentModalLabel = document.getElementById('seller-document-modal-label');
        const documentPreviewStage = document.getElementById('seller-document-preview-stage');
        const documentOpenLink = document.getElementById('seller-document-open-link');
        const sellerRequestMoreDocuments = document.getElementById('seller-request-more-documents');
        const sellerRequestEmptyState = document.getElementById('seller-request-empty-state');
        const sellerRequestDetails = document.getElementById('seller-request-details');
        const sellerRequestCurrentStatus = document.getElementById('seller-request-current-status');
        const sellerApproveButton = document.getElementById('seller-approve-button');
        const sellerRejectButton = document.getElementById('seller-reject-button');
        const sellerPendingButton = document.getElementById('seller-pending-button');

        if (!Array.isArray(sellers) || !documentPreviewStage || !documentOpenLink) {
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

        const hasDocumentRequest = function (seller) {
            return Boolean(
                seller?.latest_request_reason
                || seller?.latest_request_status
                || seller?.latest_request_date
                || seller?.latest_request_notes
            );
        };

        const requestStatusBadgeClass = function (status) {
            switch (status) {
                case 'pending':
                    return 'is-pending';
                case 'resubmitted':
                    return 'is-resubmitted';
                case 'resolved':
                    return 'is-resolved';
                default:
                    return 'is-none';
            }
        };

        const setText = function (id, value, fallback) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value || fallback || 'None';
            }
        };

        const renderAvatar = function (element, seller) {
            if (!element || !seller) {
                return;
            }

            element.className = 'avatar-photo avatar-photo--' + seller.avatar_class;

            if (seller.avatar_url) {
                element.textContent = '';
                const image = document.createElement('img');
                image.src = seller.avatar_url;
                image.alt = seller.name;
                element.replaceChildren(image);
                return;
            }

            element.textContent = seller.avatar;
        };

        const resetButtonState = function (button) {
            if (!(button instanceof HTMLElement)) {
                return;
            }

            window.LocalLiftActionLoading?.stop(button);
            button.disabled = false;
            button.classList.remove('is-static');
        };

        const syncSellerActionButtons = function (seller) {
            resetButtonState(sellerApproveButton);
            resetButtonState(sellerRejectButton);
            resetButtonState(sellerPendingButton);

            if (sellerApproveButton) {
                sellerApproveButton.textContent = seller.status === 'approved' ? 'Verified' : 'Verify Seller';
                sellerApproveButton.disabled = seller.status === 'approved';
                sellerApproveButton.classList.toggle('is-static', seller.status === 'approved');
            }

            if (sellerRejectButton) {
                sellerRejectButton.textContent = seller.status === 'rejected' ? 'Rejected' : 'Reject Seller';
                sellerRejectButton.disabled = seller.status === 'rejected';
                sellerRejectButton.classList.toggle('is-static', seller.status === 'rejected');
            }

            if (sellerPendingButton) {
                sellerPendingButton.textContent = seller.status === 'pending' ? 'Pending Review' : 'Save as Pending';
                sellerPendingButton.disabled = seller.status === 'pending';
                sellerPendingButton.classList.toggle('is-static', seller.status === 'pending');
            }
        };

        const openDocumentModal = function (title, label, url) {
            if (!url) {
                return;
            }

            documentModalTitle.textContent = title;
            documentModalLabel.textContent = label;
            documentOpenLink.href = url;
            documentPreviewStage.innerHTML = fileExtension(url) === 'pdf'
                ? '<iframe src="' + url + '" class="document-preview-frame" title="' + label + '"></iframe>'
                : '<img src="' + url + '" alt="' + label + '" class="document-preview-image">';

            openModal('seller-document-modal');
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

        document.querySelectorAll('[data-seller-view]').forEach(function (button) {
            button.addEventListener('click', function () {
                const seller = sellerMap[button.dataset.sellerView];

                if (!seller) {
                    return;
                }

                syncSellerActionButtons(seller);
                renderAvatar(document.getElementById('seller-detail-avatar'), seller);

                document.getElementById('seller-detail-name').textContent = seller.name;
                document.getElementById('seller-detail-handle').textContent = seller.handle;
                document.getElementById('seller-detail-products').textContent = seller.products;
                document.getElementById('seller-detail-date').textContent = seller.date || 'N/A';
                document.getElementById('seller-detail-email').textContent = seller.email || 'N/A';
                document.getElementById('seller-id-label').textContent = seller.valid_id_type || 'ID / Passport';

                const idLink = document.getElementById('seller-id-link');
                const permitLink = document.getElementById('seller-permit-link');
                const requestedDocumentLink = document.getElementById('seller-requested-document-link');
                const idStatus = document.getElementById('seller-id-status');
                const permitStatus = document.getElementById('seller-permit-status');
                const requestedDocumentStatus = document.getElementById('seller-requested-document-status');
                const form = document.getElementById('seller-review-form');
                const notes = document.getElementById('seller-review-notes');
                const statusInput = document.getElementById('seller-review-status');
                const reasonSelect = document.getElementById('seller-review-reason');

                form.action = seller.update_url;
                notes.value = seller.review_notes || '';
                statusInput.value = seller.status || 'pending';
                sellerRequestMoreDocuments.value = '0';
                reasonSelect.value = '';
                idLink.onclick = null;
                permitLink.onclick = null;
                requestedDocumentLink.onclick = null;
                idLink.disabled = !seller.valid_id_url;
                permitLink.disabled = !seller.business_permit_url;
                requestedDocumentLink.disabled = !seller.latest_request_document_url;

                if (seller.valid_id_url) {
                    idStatus.textContent = 'Uploaded';
                    idLink.onclick = function () {
                        openDocumentModal('Uploaded Document', seller.valid_id_type || 'ID / Passport', seller.valid_id_url);
                    };
                } else {
                    idStatus.textContent = 'Not uploaded';
                }

                if (seller.business_permit_url) {
                    permitStatus.textContent = 'Uploaded';
                    permitLink.onclick = function () {
                        openDocumentModal('Uploaded Document', 'Business License', seller.business_permit_url);
                    };
                } else {
                    permitStatus.textContent = 'Optional / Not uploaded';
                }

                setText('seller-request-current-reason', seller.latest_request_reason_label);
                setText('seller-request-current-notes', seller.latest_request_notes);
                setText('seller-request-current-date', seller.latest_request_date, 'N/A');
                setText('seller-request-current-status', seller.latest_request_status_label);

                if (sellerRequestCurrentStatus) {
                    sellerRequestCurrentStatus.className = 'seller-request-status-badge ' + requestStatusBadgeClass(seller.latest_request_status);
                }

                const requestExists = hasDocumentRequest(seller);

                if (sellerRequestEmptyState) {
                    sellerRequestEmptyState.hidden = requestExists;
                }

                if (sellerRequestDetails) {
                    sellerRequestDetails.hidden = !requestExists;
                }

                document.getElementById('seller-requested-document-label').textContent =
                    seller.latest_request_reason_label || 'Requested Document';

                if (seller.latest_request_document_url) {
                    requestedDocumentStatus.textContent = seller.latest_request_status_label || 'Uploaded';
                    requestedDocumentLink.onclick = function () {
                        openDocumentModal(
                            'Requested Document',
                            seller.latest_request_reason_label || 'Requested Document',
                            seller.latest_request_document_url
                        );
                    };
                } else {
                    requestedDocumentStatus.textContent = seller.latest_request_status === 'pending'
                        ? 'Awaiting upload'
                        : 'Not uploaded';
                }

                openModal('seller-detail-modal');
            });
        });

        const requestDocumentsButton = document.getElementById('request-documents-button');
        const sellerReviewReason = document.getElementById('seller-review-reason');
        const sellerReviewForm = document.getElementById('seller-review-form');
        const sellerReviewStatus = document.getElementById('seller-review-status');

        if (requestDocumentsButton && sellerReviewReason && sellerReviewForm && sellerRequestMoreDocuments && sellerReviewStatus) {
            requestDocumentsButton.addEventListener('click', function () {
                if (!sellerReviewReason.value) {
                    sellerReviewReason.focus();
                    return;
                }

                sellerRequestMoreDocuments.value = '1';
                sellerReviewStatus.value = 'pending';
                window.LocalLiftActionLoading?.start(requestDocumentsButton, { label: 'Sending...' });

                if (typeof sellerReviewForm.requestSubmit === 'function') {
                    sellerReviewForm.requestSubmit();
                    return;
                }

                sellerReviewForm.submit();
            });
        }

        document.querySelectorAll('[data-status-submit]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!sellerRequestMoreDocuments || !sellerReviewStatus || !sellerReviewForm) {
                    return;
                }

                sellerRequestMoreDocuments.value = '0';
                sellerReviewStatus.value = button.dataset.statusSubmit;
                window.LocalLiftActionLoading?.start(button, { label: 'Saving...' });

                if (typeof sellerReviewForm.requestSubmit === 'function') {
                    sellerReviewForm.requestSubmit();
                    return;
                }

                sellerReviewForm.submit();
            });
        });
    });
}());
