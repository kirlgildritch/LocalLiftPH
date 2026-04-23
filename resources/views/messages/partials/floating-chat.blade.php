@php
    $isSellerWidget = auth('seller')->check();
    $initialConversation = $chatWidgetConversationId ?? optional(request()->route('conversation'))->id;
    $autoOpenWidget = ($chatWidgetAutoOpen ?? false) || request()->routeIs('messages.*') || request()->routeIs('seller.messages*');
    $widgetFetchUrl = $isSellerWidget ? route('seller.chat.widget') : route('chat.widget');
@endphp

<div
    class="chat-widget-shell"
    data-chat-widget
    data-fetch-url="{{ $widgetFetchUrl }}"
    data-initial-conversation="{{ (int) $initialConversation }}"
    data-auto-open="{{ $autoOpenWidget ? '1' : '0' }}"
>
    <button type="button" class="chat-widget-fab" data-chat-toggle aria-label="Open chat">
        <i class="fa-regular fa-comments"></i>
        <span>Chat</span>
        <strong class="chat-widget-count" data-chat-count>0</strong>
    </button>

    <section class="chat-widget-panel panel" data-chat-panel aria-hidden="true">
        <div class="chat-widget-header">
            <div class="chat-widget-heading">
                <span class="section-kicker">Messages</span>
                <h3>Marketplace Chat</h3>
            </div>

            <div class="chat-widget-controls">
                <button type="button" class="chat-control-btn" data-chat-minimize aria-label="Minimize chat">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <button type="button" class="chat-control-btn" data-chat-close aria-label="Close chat">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="chat-widget-body">
            <aside class="chat-widget-sidebar">
                <div class="chat-widget-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search conversations..." data-chat-search>
                </div>

                <div class="chat-widget-conversations" data-chat-conversations></div>
            </aside>

            <div class="chat-widget-main">
                <div class="chat-widget-main-header" data-chat-main-header>
                    <button type="button" class="chat-widget-back" data-chat-back aria-label="Back to conversations">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <div class="chat-widget-empty-inline">
                        <h4>Select a conversation</h4>
                        <p>Choose a seller or buyer conversation to continue chatting.</p>
                    </div>
                </div>

                <div class="chat-widget-messages" data-chat-messages></div>

                <form class="chat-widget-form" data-chat-form enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="image" accept="image/*" data-chat-image-input hidden>
                    <button type="button" class="chat-widget-attach" data-chat-attach aria-label="Attach image">
                        <i class="fa-regular fa-image"></i>
                    </button>
                    <input type="text" name="message" placeholder="Type a message..." data-chat-input>
                    <span class="chat-widget-file-name" data-chat-file-name></span>
                    <div class="chat-widget-preview" data-chat-preview hidden></div>
                    <button type="submit" class="chat-widget-send">Send</button>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-chat-widget]').forEach(function (widget) {
            const fetchUrl = widget.dataset.fetchUrl;
            const initialConversationId = Number(widget.dataset.initialConversation || 0);
            const autoOpen = widget.dataset.autoOpen === '1';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const fab = widget.querySelector('[data-chat-toggle]');
            const panel = widget.querySelector('[data-chat-panel]');
            const minimizeBtn = widget.querySelector('[data-chat-minimize]');
            const closeBtn = widget.querySelector('[data-chat-close]');
            const countBadge = widget.querySelector('[data-chat-count]');
            const searchInput = widget.querySelector('[data-chat-search]');
            const conversationsEl = widget.querySelector('[data-chat-conversations]');
            const headerEl = widget.querySelector('[data-chat-main-header]');
            const messagesEl = widget.querySelector('[data-chat-messages]');
            const form = widget.querySelector('[data-chat-form]');
            const input = widget.querySelector('[data-chat-input]');
            const imageInput = widget.querySelector('[data-chat-image-input]');
            const attachBtn = widget.querySelector('[data-chat-attach]');
            const fileNameEl = widget.querySelector('[data-chat-file-name]');
            const previewEl = widget.querySelector('[data-chat-preview]');
            const backBtn = widget.querySelector('[data-chat-back]');
            let pollTimer = null;
            let typingTimer = null;
            let typingResetTimer = null;
            let imagePreviewUrl = null;
            let pendingScrollBehavior = 'auto';

            const state = {
                open: autoOpen,
                minimized: false,
                loading: false,
                activeConversationId: initialConversationId || null,
                conversations: [],
                activeConversation: null,
                mobileView: initialConversationId ? 'thread' : 'list',
            };

            const isMobileChatViewport = () => window.matchMedia('(max-width: 640px)').matches;
            const openForLiveUpdates = () => state.open && !state.minimized;
            const isNearMessagesBottom = () => {
                if (!messagesEl) {
                    return true;
                }

                const distanceFromBottom = messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight;

                return distanceFromBottom <= 48;
            };

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const updateShellState = () => {
                widget.classList.toggle('is-open', state.open && !state.minimized);
                widget.classList.toggle('is-minimized', state.minimized);
                panel.setAttribute('aria-hidden', state.open && !state.minimized ? 'false' : 'true');
                widget.classList.toggle(
                    'show-thread',
                    isMobileChatViewport() && state.mobileView === 'thread' && !!state.activeConversation
                );
            };

            const resetSelectedFile = () => {
                if (imageInput) {
                    imageInput.value = '';
                }

                if (fileNameEl) {
                    fileNameEl.textContent = '';
                    fileNameEl.classList.remove('is-visible');
                }

                if (imagePreviewUrl) {
                    URL.revokeObjectURL(imagePreviewUrl);
                    imagePreviewUrl = null;
                }

                if (previewEl) {
                    previewEl.innerHTML = '';
                    previewEl.hidden = true;
                }
            };

            const renderSelectedFilePreview = (file) => {
                if (!file || !previewEl) {
                    resetSelectedFile();
                    return;
                }

                if (fileNameEl) {
                    fileNameEl.textContent = file.name;
                    fileNameEl.classList.add('is-visible');
                }

                if (imagePreviewUrl) {
                    URL.revokeObjectURL(imagePreviewUrl);
                }

                imagePreviewUrl = URL.createObjectURL(file);
                previewEl.hidden = false;
                previewEl.innerHTML = `
                    <div class="chat-widget-preview-card">
                        <img src="${escapeHtml(imagePreviewUrl)}" alt="Selected preview" class="chat-widget-preview-image">
                        <div class="chat-widget-preview-copy">
                            <strong>${escapeHtml(file.name)}</strong>
                            <span>Ready to send</span>
                        </div>
                        <button type="button" class="chat-widget-preview-remove" data-chat-preview-remove aria-label="Remove image">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;

                previewEl.querySelector('[data-chat-preview-remove]')?.addEventListener('click', function () {
                    resetSelectedFile();
                });
            };

            const syncTyping = async (typing) => {
                const typingUrl = state.activeConversation?.typing_url;
                if (!typingUrl) {
                    return;
                }

                try {
                    await fetch(typingUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: new URLSearchParams({ typing: typing ? '1' : '0' }),
                        credentials: 'same-origin',
                    });
                } catch (error) {
                    console.error(error);
                }
            };

            const queueTypingSync = () => {
                if (!state.activeConversation || input.disabled) {
                    return;
                }

                window.clearTimeout(typingTimer);
                window.clearTimeout(typingResetTimer);

                typingTimer = window.setTimeout(() => {
                    syncTyping(true);
                }, 120);

                typingResetTimer = window.setTimeout(() => {
                    syncTyping(false);
                }, 1800);
            };

            const startPolling = () => {
                if (pollTimer) {
                    return;
                }

                pollTimer = window.setInterval(() => {
                    if (!state.loading) {
                        loadWidget(state.activeConversationId);
                    }
                }, openForLiveUpdates() ? 1500 : 4000);
            };

            const restartPolling = () => {
                if (pollTimer) {
                    window.clearInterval(pollTimer);
                    pollTimer = null;
                }

                startPolling();
            };

            const renderConversations = () => {
                const keyword = (searchInput.value || '').trim().toLowerCase();
                const filtered = state.conversations.filter((conversation) => {
                    if (!keyword) {
                        return true;
                    }

                    return conversation.name.toLowerCase().includes(keyword)
                        || conversation.preview.toLowerCase().includes(keyword);
                });

                countBadge.textContent = state.conversations.length;
                countBadge.classList.toggle('is-hidden', state.conversations.length < 1);

                if (!filtered.length) {
                    conversationsEl.innerHTML = `
                        <div class="chat-widget-empty">
                            <h4>No conversations found</h4>
                            <p>Try a different keyword or start a conversation from a product page.</p>
                        </div>
                    `;
                    return;
                }

                conversationsEl.innerHTML = filtered.map((conversation) => `
                    <button type="button" class="chat-widget-conversation ${conversation.id === state.activeConversationId ? 'is-active' : ''}" data-conversation-id="${conversation.id}">
                        ${conversation.avatar_url
                            ? `<img src="${escapeHtml(conversation.avatar_url)}" alt="${escapeHtml(conversation.name)}">`
                            : `<span class="chat-widget-avatar">${escapeHtml(conversation.avatar_initials)}</span>`}
                        <span class="chat-widget-conversation-copy">
                            <span class="chat-widget-conversation-topline">
                                <strong>${escapeHtml(conversation.name)}</strong>
                                ${conversation.unread_count > 0 ? `<span class="chat-widget-unread-badge">${conversation.unread_count}</span>` : ''}
                            </span>
                            <p>${escapeHtml(conversation.preview)}</p>
                            <small>${escapeHtml(conversation.updated_at)}</small>
                        </span>
                    </button>
                `).join('');

                conversationsEl.querySelectorAll('[data-conversation-id]').forEach((button) => {
                    button.addEventListener('click', function () {
                        const conversationId = Number(button.dataset.conversationId || 0);
                        if (!conversationId || conversationId === state.activeConversationId) {
                            return;
                        }

                        if (isMobileChatViewport()) {
                            state.mobileView = 'thread';
                        }

                        loadWidget(conversationId);
                    });
                });
            };

            const renderMessages = () => {
                const active = state.activeConversation;
                const shouldStickToBottom = pendingScrollBehavior === 'force' || isNearMessagesBottom();

                if (!active) {
                    resetSelectedFile();
                    headerEl.innerHTML = `
                        <div class="chat-widget-empty-inline">
                            <h4>No active chat</h4>
                            <p>Choose a conversation from the left to continue.</p>
                        </div>
                    `;
                    messagesEl.innerHTML = `
                        <div class="chat-widget-empty chat-widget-empty-body">
                            <h4>No messages yet</h4>
                            <p>Select a conversation or start one from a product or shop page.</p>
                        </div>
                    `;
                    form.classList.add('is-disabled');
                    input.disabled = true;
                    if (attachBtn) {
                        attachBtn.disabled = true;
                    }
                    updateShellState();
                    return;
                }

                headerEl.innerHTML = `
                    ${active.shop_url
                        ? `<a href="${escapeHtml(active.shop_url)}" class="chat-widget-profile-link">
                                ${active.avatar_url
                                    ? `<img src="${escapeHtml(active.avatar_url)}" alt="${escapeHtml(active.name)}" class="chat-widget-profile-image">`
                                    : `<span class="chat-widget-profile-avatar">${escapeHtml(active.avatar_initials)}</span>`}
                                <span class="chat-widget-active-copy">
                                    <h4>${escapeHtml(active.name)}</h4>
                                    <span>${escapeHtml(active.role_label)}</span>
                                </span>
                           </a>`
                        : `<div class="chat-widget-active-copy">
                                <h4>${escapeHtml(active.name)}</h4>
                                <span>${escapeHtml(active.role_label)}</span>
                           </div>`}
                `;

                messagesEl.innerHTML = active.messages.length
                    ? active.messages.map((message) => `
                        <div class="chat-widget-row ${message.is_current_user ? 'is-right' : 'is-left'}">
                            <div class="chat-widget-bubble">
                                <strong>${escapeHtml(message.sender_label)}</strong>
                                ${message.has_text ? `<p>${escapeHtml(message.message)}</p>` : ''}
                                ${message.has_image ? `<img src="${escapeHtml(message.image_url)}" alt="Shared image" class="chat-widget-image">` : ''}
                            </div>
                            <span class="chat-widget-time">
                                ${escapeHtml(message.time)}
                                ${message.status_label ? `<em class="chat-widget-status">${escapeHtml(message.status_label)}</em>` : ''}
                            </span>
                        </div>
                    `).join('')
                    : `
                        <div class="chat-widget-empty chat-widget-empty-body">
                            <h4>No messages yet</h4>
                            <p>Send the first message in this conversation.</p>
                        </div>
                    `;

                if (active.typing_text) {
                    messagesEl.insertAdjacentHTML('beforeend', `
                        <div class="chat-widget-row is-left chat-widget-typing-row">
                            <div class="chat-widget-bubble chat-widget-typing-bubble">
                                <strong>${escapeHtml(active.name)}</strong>
                                <p>${escapeHtml(active.typing_text)}</p>
                            </div>
                        </div>
                    `);
                }

                form.classList.remove('is-disabled');
                input.disabled = false;
                if (attachBtn) {
                    attachBtn.disabled = false;
                }
                form.dataset.sendUrl = active.send_url;

                if (shouldStickToBottom) {
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }

                pendingScrollBehavior = 'auto';
                updateShellState();
            };

            const renderAll = () => {
                renderConversations();
                renderMessages();
            };

            const loadWidget = async (conversationId = state.activeConversationId) => {
                if (state.loading) {
                    return;
                }

                state.loading = true;
                const requestedConversationId = conversationId || null;
                const previousConversationId = state.activeConversationId || null;
                const preserveBottom = isNearMessagesBottom();

                const url = new URL(fetchUrl, window.location.origin);
                if (conversationId) {
                    url.searchParams.set('conversation', String(conversationId));
                }

                try {
                    const response = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load chat widget.');
                    }

                    const payload = await response.json();
                    state.conversations = Array.isArray(payload.conversations) ? payload.conversations : [];
                    state.activeConversation = payload.active_conversation || null;
                    state.activeConversationId = state.activeConversation?.id || (state.conversations[0]?.id ?? null);
                    pendingScrollBehavior = requestedConversationId !== previousConversationId
                        ? 'force'
                        : (preserveBottom ? 'force' : 'preserve');

                    if (isMobileChatViewport()) {
                        state.mobileView = state.activeConversation ? 'thread' : 'list';
                    }

                    renderAll();
                    restartPolling();
                } catch (error) {
                    console.error(error);
                } finally {
                    state.loading = false;
                }
            };

            const openWidget = () => {
                state.open = true;
                state.minimized = false;
                updateShellState();
                restartPolling();

                if (!state.conversations.length) {
                    loadWidget();
                } else {
                    renderAll();
                }
            };

            const openConversation = async (conversationId) => {
                if (!conversationId) {
                    openWidget();
                    return;
                }

                state.activeConversationId = Number(conversationId);
                if (isMobileChatViewport()) {
                    state.mobileView = 'thread';
                }

                openWidget();
                await loadWidget(state.activeConversationId);
            };

            fab.addEventListener('click', function () {
                if (state.open && !state.minimized) {
                    state.minimized = true;
                    updateShellState();
                    restartPolling();
                    return;
                }

                openWidget();
            });

            minimizeBtn.addEventListener('click', function () {
                state.minimized = true;
                updateShellState();
                restartPolling();
            });

            closeBtn.addEventListener('click', function () {
                state.open = false;
                state.minimized = false;
                updateShellState();
                restartPolling();
            });

            if (backBtn) {
                backBtn.addEventListener('click', function () {
                    state.mobileView = 'list';
                    updateShellState();
                });
            }

            searchInput.addEventListener('input', renderConversations);

            document.querySelectorAll('[data-chat-start-form]').forEach((startForm) => {
                startForm.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const action = startForm.getAttribute('action');
                    if (!action) {
                        return;
                    }

                    try {
                        const response = await fetch(action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: new FormData(startForm),
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error('Unable to start conversation.');
                        }

                        const payload = await response.json();
                        const widgetPayload = payload.widget || null;
                        const conversationId = Number(payload.conversation_id || 0);

                        if (widgetPayload) {
                            state.conversations = widgetPayload.conversations || [];
                            state.activeConversation = widgetPayload.active_conversation || null;
                            state.activeConversationId = state.activeConversation?.id || conversationId || null;
                            pendingScrollBehavior = 'force';
                            resetSelectedFile();
                            openWidget();
                            renderAll();
                            return;
                        }

                        await openConversation(conversationId);
                    } catch (error) {
                        console.error(error);
                        startForm.submit();
                    }
                });
            });

            if (attachBtn && imageInput) {
                attachBtn.addEventListener('click', function () {
                    imageInput.click();
                });

                imageInput.addEventListener('change', function () {
                    const file = imageInput.files?.[0];
                    if (!file) {
                        resetSelectedFile();
                        return;
                    }

                    renderSelectedFilePreview(file);
                });
            }

            [messagesEl, form].forEach((dropTarget) => {
                if (!dropTarget) {
                    return;
                }

                ['dragenter', 'dragover'].forEach((eventName) => {
                    dropTarget.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        if (!state.activeConversation || input.disabled) {
                            return;
                        }

                        widget.classList.add('is-dragging-file');
                    });
                });

                ['dragleave', 'dragend', 'drop'].forEach((eventName) => {
                    dropTarget.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        if (eventName !== 'dragleave' || dropTarget.contains(event.relatedTarget) === false) {
                            widget.classList.remove('is-dragging-file');
                        }
                    });
                });

                dropTarget.addEventListener('drop', function (event) {
                    const file = event.dataTransfer?.files?.[0];
                    if (!file || !file.type.startsWith('image/')) {
                        return;
                    }

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    imageInput.files = transfer.files;
                    renderSelectedFilePreview(file);
                });
            });

            input.addEventListener('input', queueTypingSync);

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const sendUrl = form.dataset.sendUrl;
                if (!sendUrl || input.disabled) {
                    return;
                }

                const message = input.value.trim();
                const selectedImage = imageInput?.files?.[0] || null;
                if (!message && !selectedImage) {
                    return;
                }

                const formData = new FormData(form);
                formData.set('message', message);

                try {
                    const response = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('Unable to send message.');
                    }

                    const payload = await response.json();
                    const widgetPayload = payload.widget || null;

                    if (widgetPayload) {
                        state.conversations = widgetPayload.conversations || [];
                        state.activeConversation = widgetPayload.active_conversation || null;
                        state.activeConversationId = state.activeConversation?.id || state.activeConversationId;
                        pendingScrollBehavior = 'force';
                        input.value = '';
                        window.clearTimeout(typingResetTimer);
                        syncTyping(false);
                        resetSelectedFile();
                        renderAll();
                    } else {
                        await loadWidget(state.activeConversationId);
                        input.value = '';
                        resetSelectedFile();
                    }
                } catch (error) {
                    console.error(error);
                }
            });

            updateShellState();

            window.addEventListener('resize', function () {
                if (!isMobileChatViewport()) {
                    state.mobileView = 'thread';
                } else if (!state.activeConversation) {
                    state.mobileView = 'list';
                }

                updateShellState();
                restartPolling();
            });

            if (autoOpen) {
                openWidget();
            } else {
                loadWidget();
            }

            startPolling();
        });
    });
</script>
