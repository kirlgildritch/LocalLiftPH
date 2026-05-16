(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    const escapeHtml = function (value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const formatPeso = function (value) {
        return '\u20B1 ' + Number(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

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

        try {
            mediaItems = JSON.parse(gallery.dataset.productGalleryItems || '[]');
        } catch (error) {
            mediaItems = [];
        }

        if (!viewport || mediaItems.length === 0) {
            return;
        }

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        let currentIndex = 0;
        let animationTimer = null;
        let isAnimating = false;

        const setButtonsDisabled = function () {
            const disabled = mediaItems.length < 2 || isAnimating;

            if (prevButton) {
                prevButton.disabled = disabled;
            }

            if (nextButton) {
                nextButton.disabled = disabled;
            }
        };

        const updateCounter = function () {
            if (counter) {
                counter.textContent = (currentIndex + 1) + ' / ' + mediaItems.length;
            }
        };

        const buildSlide = function (item, index) {
            const slide = document.createElement('div');
            slide.className = 'product-media-slide is-entering';
            slide.dataset.productGallerySlide = '1';

            if (item.type === 'video') {
                slide.innerHTML = [
                    '<div class="product-media-video-shell" data-product-media-shell>',
                    '<video src="', escapeHtml(item.url), '" preload="metadata" playsinline class="product-media-content" data-product-media-video></video>',
                    '<button type="button" class="product-media-play-button" data-product-media-play aria-label="Play video">',
                    '<i class="fa-solid fa-play"></i>',
                    '</button>',
                    '</div>',
                ].join('');
            } else {
                slide.innerHTML = [
                    '<img src="', escapeHtml(item.url),
                    '" alt="', escapeHtml(galleryName),
                    '" loading="', index === 0 ? 'eager' : 'lazy',
                    '" decoding="async" class="product-media-content">',
                ].join('');
            }

            return slide;
        };

        const setupVideoSlide = function (slide) {
            const shell = slide ? slide.querySelector('[data-product-media-shell]') : null;
            const video = slide ? slide.querySelector('[data-product-media-video]') : null;
            const playButton = slide ? slide.querySelector('[data-product-media-play]') : null;

            if (!shell || !video || !playButton) {
                return;
            }

            const syncState = function () {
                shell.classList.toggle('is-playing', !video.paused && !video.ended);
            };

            video.controls = false;

            if (!video.dataset.galleryVideoBound) {
                video.dataset.galleryVideoBound = '1';

                playButton.addEventListener('click', async function () {
                    try {
                        await video.play();
                    } catch (error) {
                        // The user can tap again if playback is blocked.
                    }
                });

                video.addEventListener('play', syncState);
                video.addEventListener('pause', syncState);
                video.addEventListener('ended', syncState);
            }

            syncState();
        };

        const swapSlide = function (nextIndex, direction) {
            if (isAnimating || nextIndex === currentIndex || !mediaItems[nextIndex]) {
                return;
            }

            const currentSlide = viewport.querySelector('[data-product-gallery-slide].is-active');
            const currentVideo = currentSlide ? currentSlide.querySelector('video') : null;

            if (currentVideo) {
                currentVideo.pause();
            }

            const nextSlide = buildSlide(mediaItems[nextIndex], nextIndex);

            if (currentSlide) {
                currentSlide.classList.remove('is-active');
                currentSlide.classList.add('is-leaving');
                currentSlide.classList.add(direction === 'next' ? 'from-left' : 'from-right');
            }

            viewport.appendChild(nextSlide);
            setupVideoSlide(nextSlide);
            isAnimating = !prefersReducedMotion;
            setButtonsDisabled();

            requestAnimationFrame(function () {
                nextSlide.classList.add('is-active');
            });

            animationTimer = window.setTimeout(function () {
                if (currentSlide) {
                    currentSlide.remove();
                }

                nextSlide.classList.remove('is-entering');
                currentIndex = nextIndex;
                updateCounter();
                isAnimating = false;
                setButtonsDisabled();
            }, prefersReducedMotion ? 0 : 280);
        };

        const go = function (delta) {
            if (mediaItems.length < 2) {
                return;
            }

            const nextIndex = (currentIndex + delta + mediaItems.length) % mediaItems.length;
            swapSlide(nextIndex, delta > 0 ? 'next' : 'prev');
        };

        if (prevButton) {
            prevButton.addEventListener('click', function () {
                go(-1);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                go(1);
            });
        }

        viewport.querySelectorAll('[data-product-media-shell]').forEach(function (shell) {
            setupVideoSlide(shell.closest('[data-product-gallery-slide]'));
        });

        setButtonsDisabled();
        updateCounter();

        window.addEventListener('beforeunload', function () {
            window.clearTimeout(animationTimer);
        }, { once: true });
    };

    const initReviewForm = function () {
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

        const setUploadStatus = function (message) {
            if (uploadStatus) {
                uploadStatus.textContent = message;
            }
        };

        const setSubmitIdle = function () {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Submit Review';
            }
        };

        const setSubmitBusy = function (message) {
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = message;
            }
        };

        const bytesToSize = function (bytes) {
            if (bytes < 1024 * 1024) {
                return Math.max(1, Math.round(bytes / 1024)) + ' KB';
            }

            return (bytes / 1024 / 1024).toFixed(1) + ' MB';
        };

        const totalSelectedBytes = function () {
            return inputs.reduce(function (total, input) {
                return total + (selectedFiles.get(input) || []).reduce(function (size, file) {
                    return size + (file.size || 0);
                }, 0);
            }, 0);
        };

        const batchTotalBytes = function (files) {
            return files.reduce(function (total, file) {
                return total + (file.size || 0);
            }, 0);
        };

        const syncInputFiles = function (input) {
            const transfer = new DataTransfer();
            const files = selectedFiles.get(input) || [];

            files.forEach(function (file) {
                transfer.items.add(file);
            });

            input.files = transfer.files;
        };

        const revokePreviewUrls = function () {
            objectUrls.forEach(function (url) {
                URL.revokeObjectURL(url);
            });
            objectUrls.clear();
        };

        const renderPreviews = function () {
            if (!previewGrid) {
                return;
            }

            revokePreviewUrls();
            previewGrid.innerHTML = '';

            const items = inputs.flatMap(function (input) {
                return (selectedFiles.get(input) || []).map(function (file, index) {
                    return { file: file, index: index, input: input };
                });
            });

            previewGrid.hidden = items.length === 0;

            items.forEach(function (item) {
                const previewUrl = URL.createObjectURL(item.file);
                objectUrls.set(item.input.id + '-' + item.index + '-' + item.file.name, previewUrl);

                const card = document.createElement('div');
                card.className = 'review-upload-preview-card';

                const mediaWrap = document.createElement('div');
                mediaWrap.className = 'review-upload-preview-media';

                if (item.file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = previewUrl;
                    video.controls = true;
                    video.muted = true;
                    video.preload = 'metadata';
                    mediaWrap.appendChild(video);
                } else {
                    const image = document.createElement('img');
                    image.src = previewUrl;
                    image.alt = item.file.name;
                    mediaWrap.appendChild(image);
                }

                const meta = document.createElement('div');
                meta.className = 'review-upload-preview-meta';
                meta.textContent = item.file.name + ' (' + bytesToSize(item.file.size) + ')';

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'review-upload-remove';
                removeButton.setAttribute('aria-label', 'Remove ' + item.file.name);
                removeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                removeButton.addEventListener('click', function () {
                    const files = selectedFiles.get(item.input) || [];
                    files.splice(item.index, 1);
                    selectedFiles.set(item.input, files);
                    syncInputFiles(item.input);
                    renderPreviews();
                });

                card.appendChild(mediaWrap);
                card.appendChild(meta);
                card.appendChild(removeButton);
                previewGrid.appendChild(card);
            });
        };

        const clearSelectedFiles = function () {
            selectedFiles.clear();
            inputs.forEach(function (input) {
                selectedFiles.set(input, []);
                syncInputFiles(input);
            });
            renderPreviews();
        };

        const compressedFileName = function (file, mimeType) {
            const extension = mimeType === 'image/webp' ? 'webp' : 'jpg';
            return file.name.replace(/\.[^.]+$/, '') + '.' + extension;
        };

        const loadImage = function (file) {
            return new Promise(function (resolve, reject) {
                const url = URL.createObjectURL(file);
                const image = new Image();

                image.onload = function () {
                    URL.revokeObjectURL(url);
                    resolve(image);
                };

                image.onerror = function () {
                    URL.revokeObjectURL(url);
                    reject(new Error('Unable to read selected image.'));
                };

                image.src = url;
            });
        };

        const compressImage = async function (file) {
            if (!file.type.startsWith('image/') || file.type === 'image/gif') {
                return file;
            }

            if (maxFileBytes > 0 && file.size <= maxFileBytes) {
                return file;
            }

            try {
                const image = await loadImage(file);
                const scale = Math.min(maxImageDimension / image.width, maxImageDimension / image.height, 1);
                const width = Math.max(1, Math.round(image.width * scale));
                const height = Math.max(1, Math.round(image.height * scale));
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                if (!context) {
                    return file;
                }

                canvas.width = width;
                canvas.height = height;
                context.drawImage(image, 0, 0, width, height);

                const outputType = file.type === 'image/png' || file.type === 'image/webp'
                    ? 'image/webp'
                    : 'image/jpeg';

                const blob = await new Promise(function (resolve) {
                    canvas.toBlob(resolve, outputType, imageQuality);
                });

                if (!blob || blob.size >= file.size) {
                    return file;
                }

                return new File([blob], compressedFileName(file, outputType), {
                    lastModified: Date.now(),
                    type: outputType,
                });
            } catch (error) {
                return file;
            }
        };

        const getCompressedVideoName = function (file, mimeType) {
            const extension = mimeType.includes('webm') ? 'webm' : 'mp4';
            const baseName = file.name.replace(/\.[^.]+$/, '') || 'review-video';

            return baseName + '.' + extension;
        };

        const getSupportedVideoMimeType = function () {
            if (!window.MediaRecorder || typeof MediaRecorder.isTypeSupported !== 'function') {
                return null;
            }

            const candidates = [
                'video/webm;codecs=vp9,opus',
                'video/webm;codecs=vp8,opus',
                'video/webm',
            ];

            return candidates.find(function (candidate) {
                return MediaRecorder.isTypeSupported(candidate);
            }) || null;
        };

        const compressVideo = async function (file) {
            if (!file.type.startsWith('video/')) {
                return file;
            }

            if (maxFileBytes > 0 && file.size <= maxFileBytes) {
                return file;
            }

            const mimeType = getSupportedVideoMimeType();
            const canCapture = typeof HTMLVideoElement !== 'undefined'
                && (HTMLVideoElement.prototype.captureStream || HTMLVideoElement.prototype.mozCaptureStream);

            if (!mimeType || !canCapture) {
                return file;
            }

            const objectUrl = URL.createObjectURL(file);
            const video = document.createElement('video');
            video.src = objectUrl;
            video.preload = 'metadata';
            video.muted = true;
            video.playsInline = true;
            video.crossOrigin = 'anonymous';

            const cleanup = function () {
                URL.revokeObjectURL(objectUrl);
                video.pause();
                video.removeAttribute('src');
                video.load();
            };

            try {
                await new Promise(function (resolve, reject) {
                    video.onloadedmetadata = function () {
                        resolve();
                    };

                    video.onerror = function () {
                        reject(new Error('Unable to read selected video.'));
                    };
                });

                const stream = video.captureStream ? video.captureStream() : video.mozCaptureStream();

                if (!stream) {
                    cleanup();
                    return file;
                }

                const chunks = [];
                const compressedBlob = await new Promise(async function (resolve, reject) {
                    let resolved = false;
                    const recorder = new MediaRecorder(stream, {
                        audioBitsPerSecond: targetAudioBitrate,
                        mimeType: mimeType,
                        videoBitsPerSecond: targetVideoBitrate,
                    });

                    const finish = function (value, isError) {
                        if (resolved) {
                            return;
                        }

                        resolved = true;

                        if (recorder.state !== 'inactive') {
                            recorder.stop();
                        }

                        stream.getTracks().forEach(function (track) {
                            track.stop();
                        });

                        cleanup();

                        if (isError) {
                            reject(value);
                            return;
                        }

                        resolve(value);
                    };

                    recorder.ondataavailable = function (event) {
                        if (event.data && event.data.size > 0) {
                            chunks.push(event.data);
                        }
                    };

                    recorder.onerror = function () {
                        finish(new Error('Unable to compress selected video.'), true);
                    };

                    recorder.onstop = function () {
                        if (!resolved) {
                            finish(new Blob(chunks, { type: mimeType.split(';')[0] || 'video/webm' }), false);
                        }
                    };

                    video.onended = function () {
                        if (recorder.state !== 'inactive') {
                            recorder.stop();
                        }
                    };

                    try {
                        recorder.start(250);
                        await video.play();
                    } catch (error) {
                        finish(error, true);
                    }
                });

                if (!(compressedBlob instanceof Blob) || compressedBlob.size === 0 || compressedBlob.size >= file.size) {
                    return file;
                }

                return new File([compressedBlob], getCompressedVideoName(file, compressedBlob.type || mimeType), {
                    lastModified: Date.now(),
                    type: compressedBlob.type || mimeType.split(';')[0] || 'video/webm',
                });
            } catch (error) {
                cleanup();
                return file;
            }
        };

        const prepareFiles = async function (files) {
            const preparedFiles = [];

            for (const file of files) {
                if (file.type.startsWith('video/')) {
                    preparedFiles.push(await compressVideo(file));
                    continue;
                }

                preparedFiles.push(await compressImage(file));
            }

            return preparedFiles;
        };

        const firstErrorMessage = function (payload) {
            if (!payload || typeof payload !== 'object') {
                return 'Unable to submit your review right now.';
            }

            if (payload.message) {
                return payload.message;
            }

            const errors = payload.errors || {};
            const firstKey = Object.keys(errors)[0];

            if (firstKey && Array.isArray(errors[firstKey]) && errors[firstKey][0]) {
                return errors[firstKey][0];
            }

            return 'Unable to submit your review right now.';
        };

        inputs.forEach(function (input) {
            selectedFiles.set(input, []);

            input.addEventListener('change', async function () {
                const currentFiles = selectedFiles.get(input) || [];
                const pickedFiles = Array.from(input.files || []);
                const totalSelected = inputs.reduce(function (total, previewInput) {
                    return total + (selectedFiles.get(previewInput) || []).length;
                }, 0);
                const remainingSlots = Math.max(maxFiles - totalSelected, 0);
                const selectedBatch = pickedFiles.slice(0, remainingSlots);

                if (pickedFiles.length === 0 || remainingSlots === 0) {
                    syncInputFiles(input);
                    return;
                }

                const baseTotalBytes = totalSelectedBytes();
                const rawBatchTotalBytes = batchTotalBytes(selectedBatch);
                const batchNeedsOptimization = selectedBatch.some(function (file) {
                    return maxFileBytes > 0 && file.size > maxFileBytes;
                }) || (maxTotalBytes > 0 && baseTotalBytes + rawBatchTotalBytes > maxTotalBytes);

                input.disabled = true;

                try {
                    let preparedFiles = selectedBatch;

                    if (batchNeedsOptimization) {
                        setUploadStatus('Optimizing selected media before upload...');
                        preparedFiles = await prepareFiles(selectedBatch);
                    }

                    let runningTotalBytes = baseTotalBytes;
                    const rejectedMessages = [];
                    const nextFiles = preparedFiles.filter(function (newFile) {
                        if (maxFileBytes > 0 && newFile.size > maxFileBytes) {
                            rejectedMessages.push(newFile.name + ' exceeds the current file limit of ' + maxFileLabel + '.');
                            return false;
                        }

                        return !currentFiles.some(function (currentFile) {
                            const isDuplicate = currentFile.name === newFile.name
                                && currentFile.size === newFile.size
                                && currentFile.lastModified === newFile.lastModified;

                            if (isDuplicate) {
                                rejectedMessages.push(newFile.name + ' is already selected.');
                            }

                            return isDuplicate;
                        });
                    }).filter(function (newFile) {
                        if (maxTotalBytes > 0 && runningTotalBytes + newFile.size > maxTotalBytes) {
                            rejectedMessages.push('The selected files exceed the current upload limit of ' + maxTotalLabel + ' per submission.');
                            return false;
                        }

                        runningTotalBytes += newFile.size;
                        return true;
                    });

                    selectedFiles.set(input, currentFiles.concat(nextFiles));
                    syncInputFiles(input);
                    renderPreviews();

                    if (rejectedMessages.length > 0) {
                        setUploadStatus(rejectedMessages[0]);
                        return;
                    }

                    setUploadStatus(batchNeedsOptimization
                        ? 'Ready to submit. Media is optimized for faster upload.'
                        : 'Ready to submit. Files are already within the current upload limit.');
                } catch (error) {
                    setUploadStatus('Unable to prepare selected media right now. Please try again.');
                } finally {
                    input.disabled = false;
                }
            });
        });

        form.addEventListener('submit', function (event) {
            const currentTotalBytes = totalSelectedBytes();

            if (maxTotalBytes > 0 && currentTotalBytes > maxTotalBytes) {
                event.preventDefault();
                setSubmitIdle();
                setUploadStatus('The selected files exceed the current upload limit of ' + maxTotalLabel + ' per submission.');
                return;
            }

            event.preventDefault();
            setSubmitBusy('Uploading...');
            setUploadStatus('Uploading your review media... 0%');

            const formData = new FormData(form);
            const request = new XMLHttpRequest();

            request.open('POST', form.action, true);
            request.setRequestHeader('Accept', 'application/json');
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            request.upload.addEventListener('progress', function (progressEvent) {
                if (!progressEvent.lengthComputable) {
                    return;
                }

                const percent = Math.max(0, Math.min(100, Math.round((progressEvent.loaded / progressEvent.total) * 100)));
                setUploadStatus('Uploading your review media... ' + percent + '%');
                setSubmitBusy('Uploading ' + percent + '%');
            });

            request.addEventListener('load', function () {
                let payload = null;

                try {
                    payload = JSON.parse(request.responseText || '{}');
                } catch (error) {
                    payload = null;
                }

                if (request.status >= 200 && request.status < 300) {
                    setSubmitBusy('Refreshing...');
                    setUploadStatus((payload && payload.message ? payload.message : 'Review submitted successfully.') + ' Refreshing page...');
                    window.setTimeout(function () {
                        window.location.reload();
                    }, 250);
                    return;
                }

                setSubmitIdle();
                setUploadStatus(firstErrorMessage(payload));
            });

            request.addEventListener('error', function () {
                setSubmitIdle();
                setUploadStatus('Upload failed. Please check your connection and try again.');
            });

            request.addEventListener('abort', function () {
                setSubmitIdle();
                setUploadStatus('Upload canceled.');
            });

            request.send(formData);
        });

        form.addEventListener('reset', function () {
            clearSelectedFiles();
        });

        window.addEventListener('beforeunload', revokePreviewUrls, { once: true });
    };

    const initReviewLightbox = function () {
        const lightbox = document.querySelector('[data-review-lightbox]');
        const dialog = document.querySelector('[data-review-lightbox-dialog]');
        const closeButton = document.querySelector('[data-review-lightbox-close]');
        let previousOverflow = '';

        if (!lightbox || !dialog || !closeButton) {
            return;
        }

        const closeLightbox = function () {
            lightbox.hidden = true;
            lightbox.setAttribute('aria-hidden', 'true');
            dialog.innerHTML = '';
            document.body.style.overflow = previousOverflow;
        };

        const openLightbox = function (type, src, alt) {
            previousOverflow = document.body.style.overflow;
            dialog.innerHTML = '';

            if (type === 'video') {
                const video = document.createElement('video');
                video.src = src;
                video.controls = true;
                video.autoplay = true;
                video.className = 'review-lightbox-media';
                dialog.appendChild(video);
            } else {
                const image = document.createElement('img');
                image.src = src;
                image.alt = alt || 'Review picture';
                image.className = 'review-lightbox-media';
                dialog.appendChild(image);
            }

            lightbox.hidden = false;
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            closeButton.focus();
        };

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-review-lightbox-trigger]');

            if (!trigger) {
                return;
            }

            event.preventDefault();

            const type = trigger.dataset.reviewLightboxType || 'image';
            const src = trigger.dataset.reviewLightboxSrc || trigger.getAttribute('href') || trigger.currentSrc || trigger.src;
            const alt = trigger.querySelector('img') ? trigger.querySelector('img').alt : (trigger.alt || '');

            if (src) {
                openLightbox(type, src, alt);
            }
        });

        closeButton.addEventListener('click', closeLightbox);

        lightbox.addEventListener('click', function (event) {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !lightbox.hidden) {
                closeLightbox();
            }
        });
    };

    const initPurchaseBox = function () {
        const quantityBox = document.querySelector('[data-purchase-quantity-box]');

        if (!quantityBox) {
            return;
        }

        let maxStock = Math.max(0, Number(quantityBox.dataset.maxStock || 0));
        let minQuantity = maxStock > 0 ? 1 : 0;
        let unitPrice = Number(quantityBox.dataset.unitPrice || 0);
        const hasVariants = quantityBox.dataset.hasVariants === 'true';
        const display = quantityBox.querySelector('[data-quantity-display]');
        const decrementButton = quantityBox.querySelector('[data-quantity-decrement]');
        const incrementButton = quantityBox.querySelector('[data-quantity-increment]');
        const note = quantityBox.querySelector('[data-quantity-note]');
        const totalDisplay = document.querySelector('[data-purchase-total]');
        const quantityInputs = Array.from(document.querySelectorAll('[data-purchase-quantity]'));
        const variantInputs = Array.from(document.querySelectorAll('[data-purchase-variant-input]'));
        const submitButtons = Array.from(document.querySelectorAll('[data-purchase-submit]'));
        const variantButtons = Array.from(document.querySelectorAll('[data-variant-choice]'));
        const variantNote = document.querySelector('[data-variant-note]');
        const productDisplayPrice = document.querySelector('[data-product-display-price]');
        const variantModal = document.querySelector('[data-variant-modal]');
        const openVariantModalButtons = Array.from(document.querySelectorAll('[data-open-variant-modal]'));
        const closeVariantModalButtons = Array.from(document.querySelectorAll('[data-close-variant-modal]'));

        if (!display || !decrementButton || !incrementButton || !note || !totalDisplay || quantityInputs.length === 0) {
            return;
        }

        const clampQuantity = function (value) {
            if (maxStock <= 0) {
                return 0;
            }

            return Math.min(maxStock, Math.max(minQuantity, value));
        };

        const setPurchasingEnabled = function (enabled) {
            submitButtons.forEach(function (button) {
                button.disabled = !enabled;
            });
        };

        const updateQuantity = function (nextQuantity) {
            const quantity = clampQuantity(nextQuantity);

            display.value = quantity;
            totalDisplay.textContent = formatPeso(unitPrice * quantity);
            quantityInputs.forEach(function (input) {
                input.value = String(quantity);
            });

            decrementButton.disabled = quantity <= minQuantity;
            incrementButton.disabled = maxStock <= 0 || quantity >= maxStock;

            if (maxStock <= 0) {
                note.hidden = false;
                note.textContent = hasVariants ? 'Choose an available variant.' : 'Out of stock.';
                return;
            }

            if (quantity >= maxStock) {
                note.hidden = false;
                note.textContent = 'Max stock reached.';
                return;
            }

            note.hidden = true;
            note.textContent = '';
        };

        const openVariantModal = function () {
            if (!variantModal) {
                return;
            }

            variantModal.hidden = false;
            variantModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        };

        const closeVariantModal = function () {
            if (!variantModal) {
                return;
            }

            variantModal.hidden = true;
            variantModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
        };

        const selectVariant = function (button) {
            const selectedVariantId = button.dataset.variantId || '';

            variantButtons.forEach(function (variantButton) {
                variantButton.classList.toggle('is-selected', variantButton.dataset.variantId === selectedVariantId);
            });

            maxStock = Math.max(0, Number(button.dataset.variantStock || 0));
            minQuantity = maxStock > 0 ? 1 : 0;
            unitPrice = Number(button.dataset.variantPrice || 0);

            variantInputs.forEach(function (input) {
                input.value = selectedVariantId;
            });

            if (variantNote) {
                variantNote.hidden = true;
                variantNote.textContent = '';
            }

            if (productDisplayPrice) {
                productDisplayPrice.textContent = formatPeso(unitPrice);
            }

            setPurchasingEnabled(maxStock > 0);
            updateQuantity(maxStock > 0 ? 1 : 0);

            if (button.closest('[data-variant-modal]')) {
                closeVariantModal();
            }
        };

        decrementButton.addEventListener('click', function () {
            updateQuantity(Number(display.value || minQuantity) - 1);
        });

        incrementButton.addEventListener('click', function () {
            updateQuantity(Number(display.value || minQuantity) + 1);
        });

        variantButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (!button.disabled) {
                    selectVariant(button);
                }
            });
        });

        openVariantModalButtons.forEach(function (button) {
            button.addEventListener('click', openVariantModal);
        });

        closeVariantModalButtons.forEach(function (button) {
            button.addEventListener('click', closeVariantModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && variantModal && !variantModal.hidden) {
                closeVariantModal();
            }
        });

        if (hasVariants) {
            setPurchasingEnabled(false);
        }

        updateQuantity(Number(display.value || minQuantity));
    };

    onReady(function () {
        initProductGallery();
        initReviewForm();
        initReviewLightbox();
        initPurchaseBox();
    });
}());
