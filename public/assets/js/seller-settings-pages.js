(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    window.showSettingsTab = function (event, tabId) {
        const tabContents = document.querySelectorAll('.settings-tab-content');
        const tabButtons = document.querySelectorAll('.tab-btn');
        const targetContent = document.getElementById(tabId);

        if (!targetContent) {
            return;
        }

        tabContents.forEach(function (content) {
            content.classList.remove('active');
        });

        tabButtons.forEach(function (button) {
            button.classList.remove('active');
        });

        targetContent.classList.add('active');

        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        } else {
            const fallbackButton = document.querySelector(".tab-btn[onclick*=\"'" + tabId + "'\"]");
            if (fallbackButton) {
                fallbackButton.classList.add('active');
            }
        }

        if (window.location.hash !== '#' + tabId) {
            history.replaceState(null, '', '#' + tabId);
        }
    };

    window.nextStep = function (stepNumber) {
        document.querySelectorAll('.form-step').forEach(function (step) {
            step.classList.remove('active');
        });

        document.querySelectorAll('.step-item').forEach(function (item) {
            item.classList.remove('active', 'completed');
        });

        const targetStep = document.getElementById('step-' + stepNumber);
        if (targetStep) {
            targetStep.classList.add('active');
        }

        document.querySelectorAll('.step-item').forEach(function (item) {
            const itemStep = parseInt(item.getAttribute('data-step'), 10);

            if (itemStep < stepNumber) {
                item.classList.add('completed');
            } else if (itemStep === stepNumber) {
                item.classList.add('active');
            }
        });
    };

    onReady(function () {
        const tabId = window.location.hash ? window.location.hash.substring(1) : null;
        const statusInputs = document.querySelectorAll('input[name="shop_status"]');
        const statusUntilGroup = document.querySelector('[data-status-until-group]');
        const statusUntilInput = document.getElementById('shop_status_until');

        const syncStatusUntilVisibility = function () {
            const selectedStatusNode = document.querySelector('input[name="shop_status"]:checked');
            const selectedStatus = selectedStatusNode ? selectedStatusNode.value : '';
            const showUntil = selectedStatus === 'temporarily_closed';

            if (statusUntilGroup) {
                statusUntilGroup.classList.toggle('is-hidden', !showUntil);
            }

            if (statusUntilInput) {
                statusUntilInput.disabled = !showUntil;

                if (!showUntil) {
                    statusUntilInput.value = '';
                }
            }
        };

        statusInputs.forEach(function (input) {
            input.addEventListener('change', syncStatusUntilVisibility);
        });

        if (tabId && document.getElementById(tabId)) {
            window.showSettingsTab(null, tabId);
        }

        syncStatusUntilVisibility();
    });
}());
