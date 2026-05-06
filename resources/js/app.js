import './bootstrap';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
window.Pusher = Pusher;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const reverbPort = Number(import.meta.env.VITE_REVERB_PORT || 8080);
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (reverbKey) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: csrfToken ? {
                'X-CSRF-TOKEN': csrfToken,
            } : {},
        },
    });
}

const actionLoadingStyleId = 'locallift-action-loading-styles';
const formSubmitSelector = 'button[type="submit"], input[type="submit"], input[type="image"]';
const buttonLikeLinkSelector = [
    'a.action-btn',
    'a.page-action-btn',
    'a.table-action',
    'a.order-btn',
    'a.inline-link',
    'a.view-cart-btn',
    'a.button',
    'a.chip',
    'a.action-button',
    'a.notification-btn-action',
    'a.pagination-button',
    'a.action-link',
    'a.action-link-primary',
    'a.action-link-muted',
    'a[onclick*="requestSubmit"]',
    'a[onclick*=".submit()"]',
].join(', ');

const installActionLoadingStyles = () => {
    if (document.getElementById(actionLoadingStyleId)) {
        return;
    }

    const style = document.createElement('style');
    style.id = actionLoadingStyleId;
    style.textContent = `
        .ll-action-loading {
            pointer-events: none !important;
            user-select: none;
        }

        .ll-action-loading,
        .ll-action-loading-content {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
        }

        .ll-action-loading-spinner {
            width: 1em;
            height: 1em;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 999px;
            animation: ll-action-spin 0.7s linear infinite;
            flex-shrink: 0;
        }

        .ll-action-loading-label {
            white-space: nowrap;
        }

        .ll-action-disabled {
            pointer-events: none !important;
        }

        @keyframes ll-action-spin {
            to {
                transform: rotate(360deg);
            }
        }
    `;

    document.head.appendChild(style);
};

const getLoadingScope = (element) => {
    const root = element instanceof Node ? element : document.body;
    const body = root instanceof HTMLElement ? root.closest('body') : document.body;

    return body?.dataset.loadingScope || 'all';
};

const isLoadingExplicitlyEnabled = (element) => {
    if (!(element instanceof HTMLElement)) {
        return false;
    }

    return element.hasAttribute('data-enable-loading') || Boolean(element.closest('[data-enable-loading]'));
};

const canShowActionLoading = (element, { force = false } = {}) => {
    if (force) {
        return true;
    }

    return getLoadingScope(element) !== 'explicit' || isLoadingExplicitlyEnabled(element);
};

const isActionLoadingSkipped = (element) => (
    !element
    || element.dataset.skipLoading === 'true'
    || element.hasAttribute('data-skip-loading')
    || element.classList.contains('is-disabled')
    || element.getAttribute('aria-disabled') === 'true'
);

const isModifiedClick = (event) => (
    event.defaultPrevented
    || event.button !== 0
    || event.metaKey
    || event.ctrlKey
    || event.shiftKey
    || event.altKey
);

const resolveLoadingLabel = (element, overrideLabel) => {
    if (overrideLabel !== undefined) {
        return overrideLabel;
    }

    if (element?.dataset.loadingText !== undefined) {
        return element.dataset.loadingText;
    }

    const sourceText = element instanceof HTMLInputElement
        ? String(element.value || '').trim()
        : String(element?.textContent || '').trim();

    return sourceText ? 'Loading...' : '';
};

const buildLoadingContent = (label) => {
    const wrapper = document.createElement('span');
    wrapper.className = 'll-action-loading-content';

    const spinner = document.createElement('span');
    spinner.className = 'll-action-loading-spinner';
    spinner.setAttribute('aria-hidden', 'true');
    wrapper.appendChild(spinner);

    if (label) {
        const labelElement = document.createElement('span');
        labelElement.className = 'll-action-loading-label';
        labelElement.textContent = label;
        wrapper.appendChild(labelElement);
    }

    return wrapper;
};

const startActionLoading = (element, { label } = {}) => {
    if (
        !(element instanceof HTMLElement)
        || element.dataset.llLoadingActive === '1'
        || isActionLoadingSkipped(element)
        || !canShowActionLoading(element)
    ) {
        return false;
    }

    installActionLoadingStyles();

    const resolvedLabel = resolveLoadingLabel(element, label);
    const width = Math.ceil(element.getBoundingClientRect().width);

    element.dataset.llLoadingActive = '1';
    element.dataset.llLoadingWidth = element.style.width || '';
    element.dataset.llLoadingMinWidth = element.style.minWidth || '';
    if (width > 0) {
        element.style.width = `${width}px`;
        element.style.minWidth = `${width}px`;
    }

    if (element instanceof HTMLInputElement) {
        element.dataset.llLoadingOriginalValue = element.value;
    } else {
        element.dataset.llLoadingOriginalHtml = element.innerHTML;
    }

    if ('disabled' in element) {
        element.dataset.llLoadingOriginallyDisabled = element.disabled ? '1' : '0';
        element.disabled = true;
    }

    if (element instanceof HTMLAnchorElement) {
        element.dataset.llLoadingOriginalTabindex = element.getAttribute('tabindex') ?? '';
        element.setAttribute('aria-disabled', 'true');
        element.setAttribute('tabindex', '-1');
    }

    element.setAttribute('aria-busy', 'true');
    element.classList.add('ll-action-loading');

    if (element instanceof HTMLInputElement) {
        element.value = resolvedLabel || '...';
    } else {
        element.replaceChildren(buildLoadingContent(resolvedLabel));
    }

    return true;
};

const stopActionLoading = (element) => {
    if (!(element instanceof HTMLElement) || element.dataset.llLoadingActive !== '1') {
        return;
    }

    if (element instanceof HTMLInputElement) {
        element.value = element.dataset.llLoadingOriginalValue ?? element.value;
    } else {
        element.innerHTML = element.dataset.llLoadingOriginalHtml ?? element.innerHTML;
    }

    if ('disabled' in element) {
        element.disabled = element.dataset.llLoadingOriginallyDisabled === '1';
    }

    if (element instanceof HTMLAnchorElement) {
        element.removeAttribute('aria-disabled');

        if (element.dataset.llLoadingOriginalTabindex === '') {
            element.removeAttribute('tabindex');
        } else {
            element.setAttribute('tabindex', element.dataset.llLoadingOriginalTabindex);
        }
    }

    element.removeAttribute('aria-busy');
    element.classList.remove('ll-action-loading');
    element.style.width = element.dataset.llLoadingWidth || '';
    element.style.minWidth = element.dataset.llLoadingMinWidth || '';

    [
        'llLoadingActive',
        'llLoadingOriginalHtml',
        'llLoadingOriginalValue',
        'llLoadingOriginallyDisabled',
        'llLoadingOriginalTabindex',
        'llLoadingWidth',
        'llLoadingMinWidth',
    ].forEach((key) => {
        delete element.dataset[key];
    });
};

const markFormPending = (form, submitter, options = {}) => {
    if (
        !(form instanceof HTMLFormElement)
        || form.dataset.llFormPending === '1'
        || !canShowActionLoading(submitter instanceof HTMLElement ? submitter : form)
    ) {
        return false;
    }

    form.dataset.llFormPending = '1';

    const submitControls = Array.from(form.querySelectorAll(formSubmitSelector))
        .filter((control) => control instanceof HTMLElement);

    submitControls.forEach((control) => {
        if (control === submitter) {
            return;
        }

        if ('disabled' in control) {
            control.dataset.llTempDisabled = control.disabled ? '1' : '0';
            control.disabled = true;
            control.classList.add('ll-action-disabled');
        }
    });

    if (submitter instanceof HTMLElement) {
        startActionLoading(submitter, options);
    }

    return true;
};

const releaseFormPending = (form, submitter) => {
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    delete form.dataset.llFormPending;

    form.querySelectorAll('[data-ll-temp-disabled]').forEach((control) => {
        if (!(control instanceof HTMLElement) || !('disabled' in control)) {
            return;
        }

        control.disabled = control.dataset.llTempDisabled === '1';
        control.classList.remove('ll-action-disabled');
        delete control.dataset.llTempDisabled;
    });

    if (submitter instanceof HTMLElement) {
        stopActionLoading(submitter);
    }
};

const findFormSubmitter = (form, submitter) => {
    if (submitter instanceof HTMLElement) {
        return submitter;
    }

    return form.querySelector(formSubmitSelector);
};

const installActionLoadingHandlers = () => {
    window.LocalLiftActionLoading = {
        start: startActionLoading,
        stop: stopActionLoading,
        markFormPending,
        releaseFormPending,
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || form.hasAttribute('data-skip-loading')) {
            return;
        }

        const submitter = findFormSubmitter(form, event.submitter);

        queueMicrotask(() => {
            if (event.defaultPrevented || !form.isConnected || form.dataset.llFormPending === '1') {
                return;
            }

            if (submitter instanceof HTMLElement && isActionLoadingSkipped(submitter)) {
                return;
            }

            if (!canShowActionLoading(submitter instanceof HTMLElement ? submitter : form)) {
                return;
            }

            markFormPending(form, submitter);
        });
    }, true);

    document.addEventListener('click', (event) => {
        const link = event.target instanceof Element
            ? event.target.closest(buttonLikeLinkSelector)
            : null;

        if (
            !(link instanceof HTMLAnchorElement)
            || isModifiedClick(event)
            || isActionLoadingSkipped(link)
            || !canShowActionLoading(link)
        ) {
            return;
        }

        const href = (link.getAttribute('href') || '').trim();

        if (
            !href
            || href === '#'
            || href.startsWith('#')
            || href.startsWith('javascript:')
            || link.hasAttribute('download')
            || link.target === '_blank'
        ) {
            return;
        }

        if (link.dataset.llLoadingActive === '1') {
            event.preventDefault();
            return;
        }

        startActionLoading(link);
    }, true);
};

installActionLoadingHandlers();

Alpine.start();
