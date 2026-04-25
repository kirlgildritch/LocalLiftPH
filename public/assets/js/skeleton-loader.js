(function () {
    const DEFAULT_DELAY = 520;
    const STAGGER_DELAY = 55;
    const MAX_STAGGERED_ITEMS = 6;

    const revealItem = (item, delay) => {
        window.setTimeout(() => {
            item.classList.remove('is-loading');
            item.classList.add('is-loaded');
        }, Math.max(0, delay));
    };

    const revealCollection = (items, baseDelay = DEFAULT_DELAY) => {
        items.forEach((item, index) => {
            const stagger = Math.min(index, MAX_STAGGERED_ITEMS) * STAGGER_DELAY;
            revealItem(item, baseDelay + stagger);
        });
    };

    const initStaticSkeletons = () => {
        const groupedItems = new Set();

        document.querySelectorAll('[data-skeleton-group]').forEach((group) => {
            const items = Array.from(group.querySelectorAll('[data-skeleton-item].is-loading'));
            const delay = Number(group.dataset.skeletonDelay || DEFAULT_DELAY);

            if (!items.length) {
                return;
            }

            items.forEach((item) => groupedItems.add(item));
            revealCollection(items, delay);
        });

        const standaloneItems = Array.from(document.querySelectorAll('[data-skeleton-item].is-loading'))
            .filter((item) => !groupedItems.has(item));

        if (standaloneItems.length) {
            revealCollection(standaloneItems, DEFAULT_DELAY);
        }
    };

    window.LocalLiftSkeleton = {
        revealCollection,
        revealItem,
    };

    document.addEventListener('DOMContentLoaded', initStaticSkeletons, { once: true });
})();
