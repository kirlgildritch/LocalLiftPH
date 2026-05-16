(function () {
    const ROOT_SELECTOR = '[data-market-pagination-root]';
    const LINK_SELECTOR = '[data-market-pagination-link]';
    const DEFAULT_CARD_COUNT = 12;
    const DEFAULT_SCROLL_OFFSET = 24;

    const shouldIgnoreClick = function (event, link) {
        return event.defaultPrevented
            || event.button !== 0
            || event.metaKey
            || event.ctrlKey
            || event.shiftKey
            || event.altKey
            || (link.getAttribute('target') && link.getAttribute('target') !== '_self');
    };

    const buildProductSkeleton = function () {
        return `
            <article class="market-product-card product-card-link skeleton-shell is-loading market-product-card--placeholder" aria-hidden="true">
                <div class="market-product-card__image skeleton skeleton-image"></div>
                <div class="market-product-card__body">
                    <span class="market-product-card__badge skeleton skeleton-text">Category</span>
                    <h4 class="market-product-card__title skeleton skeleton-text">Loading product</h4>
                    <div class="ratings skeleton skeleton-text">
                        <span>Loading rating</span>
                    </div>
                    <div class="market-product-card__seller-line skeleton skeleton-text">
                        <span>Loading seller</span>
                    </div>
                    <div class="market-product-card__price skeleton skeleton-text">
                        <span class="market-product-card__currency">&#8369;</span> 0.00
                    </div>
                </div>
            </article>
        `;
    };

    const buildSkeletonMarkup = function (count) {
        const safeCount = Math.max(1, Number(count) || DEFAULT_CARD_COUNT);
        const cards = Array.from({ length: safeCount }, buildProductSkeleton).join('');

        return `
            <div class="product-grid product-card-grid" data-market-pagination-grid>${cards}</div>
            <div class="panel" data-market-pagination-nav style="padding: 16px 20px; margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;" aria-hidden="true">
                <p class="skeleton skeleton-text" style="margin: 0; color: #9fb3c8; font-size: 14px;">Loading products</p>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="action-btn secondary-btn skeleton skeleton-button" style="pointer-events: none;">Previous</span>
                    <span class="skeleton skeleton-text" style="color: #dbeafe; font-size: 14px;">Loading page</span>
                    <span class="action-btn secondary-btn skeleton skeleton-button" style="pointer-events: none;">Next</span>
                </div>
            </div>
        `;
    };

    const scrollRootIntoView = function (root) {
        const selector = root.dataset.marketPaginationScrollTarget;
        const target = selector ? document.querySelector(selector) : root;

        if (!target) {
            return;
        }

        const top = target.getBoundingClientRect().top + window.scrollY - DEFAULT_SCROLL_OFFSET;
        window.scrollTo({
            top: Math.max(0, top),
            behavior: 'smooth',
        });
    };

    const initRoot = function (root) {
        let activeRequest = null;
        let activeUrl = window.location.href;

        if (!history.state || history.state.marketPaginationUrl !== window.location.href) {
            history.replaceState(
                Object.assign({}, history.state || {}, { marketPaginationUrl: window.location.href }),
                '',
                window.location.href
            );
        }

        const updateSkeletonCount = function () {
            const renderedCards = root.querySelectorAll('.market-product-card.product-card-link').length;
            if (renderedCards > 0) {
                root.dataset.marketPaginationCount = String(renderedCards);
            }
        };

        const renderLoadingState = function () {
            const currentHeight = root.offsetHeight;
            if (currentHeight > 0) {
                root.style.minHeight = `${currentHeight}px`;
            }

            root.setAttribute('aria-busy', 'true');
            root.innerHTML = buildSkeletonMarkup(root.dataset.marketPaginationCount);
        };

        const clearLoadingState = function () {
            root.removeAttribute('aria-busy');
            root.style.minHeight = '';
        };

        const loadPage = async function (url, options) {
            const settings = Object.assign({ pushHistory: true, shouldScroll: true }, options || {});

            if (activeRequest) {
                activeRequest.abort();
            }

            const requestController = new AbortController();
            activeRequest = requestController;
            renderLoadingState();

            try {
                const response = await fetch(url, {
                    signal: requestController.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                });

                if (!response.ok) {
                    throw new Error(`Pagination request failed with status ${response.status}`);
                }

                const html = await response.text();
                root.innerHTML = html;
                updateSkeletonCount();
                clearLoadingState();
                activeUrl = url;

                if (settings.pushHistory) {
                    history.pushState(
                        Object.assign({}, history.state || {}, { marketPaginationUrl: url }),
                        '',
                        url
                    );
                }

                if (settings.shouldScroll) {
                    scrollRootIntoView(root);
                }
            } catch (error) {
                clearLoadingState();

                if (error.name === 'AbortError') {
                    return;
                }

                window.location.href = url;
            } finally {
                if (activeRequest === requestController) {
                    activeRequest = null;
                }
            }
        };

        updateSkeletonCount();

        root.addEventListener('click', function (event) {
            const link = event.target.closest(LINK_SELECTOR);

            if (!link || !root.contains(link) || shouldIgnoreClick(event, link)) {
                return;
            }

            event.preventDefault();

            if (link.href === activeUrl) {
                return;
            }

            void loadPage(link.href);
        });

        window.addEventListener('popstate', function () {
            if (window.location.pathname !== new URL(activeUrl, window.location.origin).pathname) {
                return;
            }

            if (window.location.href === activeUrl) {
                return;
            }

            void loadPage(window.location.href, { pushHistory: false, shouldScroll: false });
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll(ROOT_SELECTOR).forEach(initRoot);
    }, { once: true });
})();
