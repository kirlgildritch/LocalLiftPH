// ========================
// product-gallery.js
// ========================
const initProductGallery = function () {
    const gallery = document.querySelector('[data-product-gallery]');
    if (!gallery) {
        return;
    }

    const viewport = gallery.querySelector('[data-product-gallery-viewport]');
    const prevButton = gallery.querySelector('[data-product-gallery-prev]');
    const nextButton = gallery.querySelector('[data-product-gallery-next]');
    const counter = gallery.querySelector('[data-product-gallery-counter]');
    const galleryName = gallery.dataset.productName || 'Product media';

    let mediaItems = [];
    try { mediaItems = JSON.parse(gallery.dataset.productGalleryItems || '[]'); } catch { mediaItems = []; }
    if (!viewport || !mediaItems.length) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let currentIndex = 0;
    let animationTimer = null;
    let isAnimating = false;

    const escapeHtml = value => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

    const setButtonsDisabled = () => {
        const disabled = mediaItems.length < 2 || isAnimating;
        if (prevButton) prevButton.disabled = disabled;
        if (nextButton) nextButton.disabled = disabled;
    };

    const updateCounter = () => { if (counter) counter.textContent = `${currentIndex + 1} / ${mediaItems.length}`; };

    const buildSlide = (item, index) => {
        const slide = document.createElement('div');
        slide.className = 'product-media-slide is-entering';
        slide.dataset.productGallerySlide = '1';

        if (item.type === 'video') {
            slide.innerHTML = `<div class="product-media-video-shell" data-product-media-shell>
                <video src="${escapeHtml(item.url)}" preload="metadata" playsinline class="product-media-content" data-product-media-video></video>
                <button type="button" class="product-media-play-button" data-product-media-play aria-label="Play video"><i class="fa-solid fa-play"></i></button>
            </div>`;
        } else {
            slide.innerHTML = `<img src="${escapeHtml(item.url)}" alt="${escapeHtml(galleryName)}" loading="${index === 0 ? 'eager' : 'lazy'}" class="product-media-content">`;
        }
        return slide;
    };

    const setupVideoSlide = slide => {
        const shell = slide?.querySelector('[data-product-media-shell]');
        const video = slide?.querySelector('[data-product-media-video]');
        const playButton = slide?.querySelector('[data-product-media-play]');
        if (!shell || !video || !playButton) return;

        video.controls = false;
        shell.classList.toggle('is-playing', !video.paused && !video.ended);

        if (!video.dataset.galleryVideoBound) {
            video.dataset.galleryVideoBound = '1';
            playButton.addEventListener('click', async () => { try { await video.play(); } catch {} });
            ['play','pause','ended'].forEach(evt => video.addEventListener(evt, () => shell.classList.toggle('is-playing', !video.paused && !video.ended)));
        }
    };

    const swapSlide = (nextIndex, direction) => {
        if (isAnimating || nextIndex === currentIndex || !mediaItems[nextIndex]) return;
        const currentSlide = viewport.querySelector('[data-product-gallery-slide].is-active');
        const currentVideo = currentSlide?.querySelector('video');
        if (currentVideo) currentVideo.pause();

        const slide = buildSlide(mediaItems[nextIndex], nextIndex);
        if (currentSlide) {
            currentSlide.classList.remove('is-active');
            currentSlide.classList.add('is-leaving');
            currentSlide.classList.add(direction === 'next' ? 'from-left' : 'from-right');
        }
        viewport.appendChild(slide);
        setupVideoSlide(slide);
        isAnimating = !prefersReducedMotion;
        setButtonsDisabled();

        requestAnimationFrame(() => slide.classList.add('is-active'));
        animationTimer = setTimeout(() => {
            currentSlide?.remove();
            slide.classList.remove('is-entering');
            currentIndex = nextIndex;
            updateCounter();
            isAnimating = false;
            setButtonsDisabled();
        }, prefersReducedMotion ? 0 : 280);
    };

    const go = delta => {
        if (mediaItems.length < 2) return;
        swapSlide((currentIndex + delta + mediaItems.length) % mediaItems.length, delta > 0 ? 'next' : 'prev');
    };

    if (prevButton) prevButton.addEventListener('click', () => go(-1));
    if (nextButton) nextButton.addEventListener('click', () => go(1));
    viewport.querySelectorAll('[data-product-media-shell]').forEach(slide => setupVideoSlide(slide.closest('[data-product-gallery-slide]')));
    setButtonsDisabled();
    updateCounter();
    window.addEventListener('beforeunload', () => clearTimeout(animationTimer));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductGallery, { once: true });
} else {
    initProductGallery();
}
