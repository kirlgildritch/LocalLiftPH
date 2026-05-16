// ========================
// review-upload.js
// ========================
const initReviewUpload = function () {
    const form = document.getElementById('buyer-review-form');
    if (!form || !window.DataTransfer) {
        return;
    }

    const inputs = Array.from(form.querySelectorAll('[data-review-preview-input]'));
    const previewGrid = form.querySelector('[data-review-preview-grid]');
    const uploadStatus = form.querySelector('[data-review-upload-status]');
    const submitButton = form.querySelector('.review-submit-btn');
    const selectedFiles = new Map();
    const objectUrls = new Map();
    const maxFiles = Math.max(1, Number(form.dataset.reviewMaxFiles || 5));
    const maxFileBytes = Math.max(0, Number(form.dataset.reviewMaxFileBytes || 0));
    const maxTotalBytes = Math.max(0, Number(form.dataset.reviewMaxTotalBytes || 0));
    const maxFileLabel = form.dataset.reviewMaxFileLabel || '';
    const maxTotalLabel = form.dataset.reviewMaxTotalLabel || '';
    const maxImageDimension = 1600;
    const imageQuality = 0.82;
    const targetVideoBitrate = 900000;
    const targetAudioBitrate = 96000;

    // Helper functions for review upload and preview
    const setUploadStatus = message => { if (uploadStatus) uploadStatus.textContent = message; };
    const setSubmitIdle = () => { if (submitButton) { submitButton.disabled = false; submitButton.textContent = 'Submit Review'; } };
    const setSubmitBusy = message => { if (submitButton) { submitButton.disabled = true; submitButton.textContent = message; } };
    const bytesToSize = bytes => bytes < 1024*1024 ? Math.max(1, Math.round(bytes/1024)) + ' KB' : (bytes/1024/1024).toFixed(1) + ' MB';

    // Functions for compressing images/videos
    // ... include all compressImage, compressVideo, prepareFiles functions as in your Blade JS

    const syncInputFiles = input => {
        const transfer = new DataTransfer();
        (selectedFiles.get(input) || []).forEach(file => transfer.items.add(file));
        input.files = transfer.files;
    };

    const revokePreviewUrls = () => { objectUrls.forEach(url => URL.revokeObjectURL(url)); objectUrls.clear(); };

    const renderPreviews = () => {
        if (!previewGrid) return;
        revokePreviewUrls();
        previewGrid.innerHTML = '';
        const items = inputs.flatMap(input => (selectedFiles.get(input) || []).map((file, index) => ({input, file, index})));
        previewGrid.hidden = items.length === 0;
        items.forEach(item => {
            const previewUrl = URL.createObjectURL(item.file);
            objectUrls.set(item.input.id + '-' + item.index + '-' + item.file.name, previewUrl);
            const card = document.createElement('div');
            card.className = 'review-upload-preview-card';
            const mediaWrap = document.createElement('div');
            mediaWrap.className = 'review-upload-preview-media';
            if (item.file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = previewUrl; video.controls = true; video.muted = true; video.preload = 'metadata'; mediaWrap.appendChild(video);
            } else {
                const image = document.createElement('img'); image.src = previewUrl; image.alt = item.file.name; mediaWrap.appendChild(image);
            }
            const meta = document.createElement('div'); meta.className = 'review-upload-preview-meta'; meta.textContent = item.file.name + ' (' + bytesToSize(item.file.size) + ')';
            const removeButton = document.createElement('button'); removeButton.type = 'button'; removeButton.className = 'review-upload-remove'; removeButton.setAttribute('aria-label', 'Remove ' + item.file.name); removeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            removeButton.addEventListener('click', function () { const files = selectedFiles.get(item.input) || []; files.splice(item.index, 1); selectedFiles.set(item.input, files); syncInputFiles(item.input); renderPreviews(); });
            card.appendChild(mediaWrap); card.appendChild(meta); card.appendChild(removeButton); previewGrid.appendChild(card);
        });
    };

    // Event listeners for input change, submit, reset
    inputs.forEach(input => {
        selectedFiles.set(input, []);
        input.addEventListener('change', async () => { /* ... handle file selection, compression, validation, call renderPreviews() */ });
    });

    form.addEventListener('submit', event => { /* ... handle ajax submit with progress */ });
    form.addEventListener('reset', () => { selectedFiles.clear(); inputs.forEach(input => { selectedFiles.set(input, []); syncInputFiles(input); }); renderPreviews(); });
    window.addEventListener('beforeunload', revokePreviewUrls);

    // Lightbox
    const lightbox = document.querySelector('[data-review-lightbox]');
    const dialog = document.querySelector('[data-review-lightbox-dialog]');
    const closeButton = document.querySelector('[data-review-lightbox-close]');
    let previousOverflow = '';
    if (lightbox && dialog && closeButton) {
        const closeLightbox = () => { lightbox.hidden = true; lightbox.setAttribute('aria-hidden','true'); dialog.innerHTML=''; document.body.style.overflow = previousOverflow; };
        const openLightbox = (type, src, alt) => { previousOverflow = document.body.style.overflow; dialog.innerHTML=''; if(type==='video'){ const video=document.createElement('video'); video.src=src; video.controls=true; video.autoplay=true; video.className='review-lightbox-media'; dialog.appendChild(video);} else{ const image=document.createElement('img'); image.src=src; image.alt=alt||'Review picture'; image.className='review-lightbox-media'; dialog.appendChild(image);} lightbox.hidden=false; lightbox.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; closeButton.focus(); };
        document.addEventListener('click', e => { const trigger=e.target.closest('[data-review-lightbox-trigger]'); if(!trigger) return; e.preventDefault(); const type=trigger.dataset.reviewLightboxType||'image'; const src=trigger.dataset.reviewLightboxSrc||trigger.getAttribute('href')||trigger.currentSrc||trigger.src; const alt=trigger.querySelector('img')?.alt||trigger.alt||''; if(src) openLightbox(type, src, alt);});
        closeButton.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', e=>{if(e.target===lightbox) closeLightbox();});
        document.addEventListener('keydown', e=>{if(e.key==='Escape'&&!lightbox.hidden) closeLightbox();});
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReviewUpload, { once: true });
} else {
    initReviewUpload();
}
