(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    const safeParseJson = function (rawValue, fallbackValue) {
        try {
            return JSON.parse(rawValue || '');
        } catch (error) {
            return fallbackValue;
        }
    };

    const escapeHtml = function (value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    onReady(function () {
        const chatPage = document.querySelector('[data-chat-page]');

        if (!chatPage) {
            const messageContainer = document.querySelector('[data-inbox-messages]');
            if (messageContainer) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
            return;
        }

        const stateScript = chatPage.querySelector('[data-chat-page-state]');
        const csrfTokenNode = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenNode ? csrfTokenNode.getAttribute('content') || '' : '';
        const fetchUrl = chatPage.dataset.fetchUrl || '';
        const listUrl = chatPage.dataset.listUrl || '';
        const initialMobileView = chatPage.dataset.mobileView === 'thread' ? 'thread' : 'list';
        const conversationListEl = chatPage.querySelector('[data-inbox-conversation-list]');
        const threadEl = chatPage.querySelector('[data-inbox-thread]');
        const subscriptions = new Map();
        const presenceSubscriptions = new Map();
        const presenceMembers = new Map();
        let state = safeParseJson(stateScript ? stateScript.textContent : '{}', {});
        let typingTimer = null;
        let typingResetTimer = null;
        let typingExpiryTimer = null;
        let loading = false;
        let mobileView = initialMobileView;

        const getMessageMediaType = function (message) {
            return message.media_type || (message.has_video ? 'video' : message.has_image ? 'image' : null);
        };

        const getMessageMediaUrl = function (message) {
            return message.media_url || message.video_url || message.image_url || '';
        };

        const renderMessageMedia = function (message, className) {
            const mediaType = getMessageMediaType(message);
            const mediaUrl = getMessageMediaUrl(message);

            if (!mediaType || !mediaUrl) {
                return '';
            }

            if (mediaType === 'video') {
                return '<video src="' + escapeHtml(mediaUrl) + '" controls preload="metadata" class="' + className + ' ' + className + '--video"></video>';
            }

            return '<img src="' + escapeHtml(mediaUrl) + '" alt="Shared image" class="' + className + '">';
        };

        const renderProductCard = function (message) {
            if (!message || !message.has_product || !message.product) {
                return '';
            }

            return [
                '<a href="', escapeHtml(message.product.url), '" class="inbox-product-card">',
                '<img src="', escapeHtml(message.product.image_url), '" alt="', escapeHtml(message.product.name), '" class="inbox-product-card-image">',
                '<span class="inbox-product-card-copy">',
                '<span class="inbox-product-card-label">Product</span>',
                '<strong>', escapeHtml(message.product.name), '</strong>',
                '<span>', escapeHtml(message.product.price_label), '</span>',
                '<span>', escapeHtml(message.product.shop_name), '</span>',
                '</span>',
                '</a>',
            ].join('');
        };

        const socketId = function () {
            return window.Echo && typeof window.Echo.socketId === 'function' ? window.Echo.socketId() || '' : '';
        };

        const isCompactViewport = function () {
            return window.matchMedia('(max-width: 860px)').matches;
        };

        const applyPageLayoutMode = function () {
            chatPage.classList.toggle('is-mobile-thread', isCompactViewport() && mobileView === 'thread');
        };

        const requestHeaders = function (includeSocket) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            if (includeSocket !== false && socketId()) {
                headers['X-Socket-ID'] = socketId();
            }

            return headers;
        };

        const currentMessagesContainer = function () {
            return chatPage.querySelector('[data-inbox-messages]');
        };

        const isNearBottom = function () {
            const messageContainer = currentMessagesContainer();
            if (!messageContainer) {
                return true;
            }

            const distanceFromBottom = messageContainer.scrollHeight - messageContainer.scrollTop - messageContainer.clientHeight;
            return distanceFromBottom <= 48;
        };

        const scrollMessagesToBottom = function () {
            const messageContainer = currentMessagesContainer();
            if (messageContainer) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        };

        const currentUserId = function () {
            return Number(state.meta && state.meta.current_user_id || 0);
        };

        const createClientMessageId = function () {
            return 'chat-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);
        };

        const conversationById = function (conversationId) {
            return (state.conversations || []).find(function (conversation) {
                return Number(conversation.id) === Number(conversationId);
            }) || null;
        };

        const activeConversation = function () {
            return state.active_conversation || null;
        };

        const setConversationLastSeen = function (conversationId, isoValue, label) {
            const conversation = conversationById(conversationId);
            if (conversation) {
                conversation.last_seen_at = isoValue;
                conversation.last_seen_label = label;
            }

            if (Number(activeConversation() && activeConversation().id || 0) === Number(conversationId) && state.active_conversation) {
                state.active_conversation.last_seen_at = isoValue;
                state.active_conversation.last_seen_label = label;
            }
        };

        const participantOnline = function (conversation) {
            const participantId = Number(conversation && conversation.participant_id || 0);
            const members = presenceMembers.get(Number(conversation && conversation.id || 0));
            return Boolean(participantId && members && members.has(participantId));
        };

        const statusText = function (conversation) {
            if (participantOnline(conversation)) {
                return 'Online';
            }

            if (conversation && conversation.last_seen_label) {
                return 'Last seen ' + conversation.last_seen_label;
            }

            return 'Offline';
        };

        const messagePreviewText = function (message) {
            if (message.has_product && message.product && message.product.name) {
                return 'Product: ' + String(message.product.name).slice(0, 40);
            }

            const mediaType = message.media_type || (message.has_video ? 'video' : message.has_image ? 'image' : null);

            if (mediaType && !message.has_text) {
                return mediaType === 'video' ? 'Sent a video' : 'Sent an image';
            }

            if (mediaType && message.has_text) {
                const mediaLabel = mediaType === 'video' ? 'Video' : 'Image';
                return mediaLabel + ': ' + String(message.message || '').slice(0, 40);
            }

            return String(message.message || 'Start chatting from a product or shop page.').slice(0, 52);
        };

        const syncConversationFromMessage = function (message, fallbackIso) {
            const conversation = conversationById(activeConversation() && activeConversation().id);
            if (!conversation) {
                return;
            }

            const updatedAtIso = fallbackIso || new Date().toISOString();
            conversation.preview = messagePreviewText(message);
            conversation.updated_at = message.is_failed ? 'Failed to send' : 'just now';
            conversation.updated_at_iso = updatedAtIso;
            conversation.unread_count = 0;

            state.conversations = [
                conversation,
            ].concat((state.conversations || []).filter(function (item) {
                return Number(item.id) !== Number(conversation.id);
            }));
        };

        const createOptimisticMessage = function (options) {
            const clientMessageId = options.clientMessageId;
            const text = options.text;
            const file = options.file;
            const mediaType = file ? (file.type.startsWith('video/') ? 'video' : 'image') : null;
            const mediaUrl = file ? URL.createObjectURL(file) : null;

            return {
                id: 'temp-' + clientMessageId,
                client_message_id: clientMessageId,
                sender_label: 'You',
                message: text,
                media_type: mediaType,
                media_url: mediaUrl,
                image_url: mediaType === 'image' ? mediaUrl : null,
                video_url: mediaType === 'video' ? mediaUrl : null,
                has_image: mediaType === 'image',
                has_video: mediaType === 'video',
                has_media: Boolean(file),
                has_text: Boolean(text),
                has_product: false,
                product: null,
                time: 'Sending...',
                is_current_user: true,
                is_seen: false,
                status_label: 'Sending...',
                is_pending: true,
                is_failed: false,
            };
        };

        const appendOptimisticMessage = function (message) {
            if (!activeConversation()) {
                return;
            }

            state.active_conversation.messages = (Array.isArray(state.active_conversation.messages) ? state.active_conversation.messages : []).concat([message]);
            syncConversationFromMessage(message);
        };

        const markOptimisticMessageFailed = function (clientMessageId) {
            if (!activeConversation()) {
                return;
            }

            state.active_conversation.messages = (state.active_conversation.messages || []).map(function (message) {
                if (message.client_message_id !== clientMessageId) {
                    return message;
                }

                return Object.assign({}, message, {
                    time: 'Not sent',
                    status_label: 'Failed',
                    is_pending: false,
                    is_failed: true,
                });
            });

            const failedMessage = (state.active_conversation.messages || []).find(function (message) {
                return message.client_message_id === clientMessageId;
            });

            if (failedMessage) {
                syncConversationFromMessage(failedMessage);
            }
        };

        const applyPresenceIndicators = function () {
            chatPage.querySelectorAll('[data-presence-dot]').forEach(function (dot) {
                const conversationId = Number(dot.dataset.conversationId || 0);
                const conversation = conversationById(conversationId) || (Number(activeConversation() && activeConversation().id || 0) === conversationId ? activeConversation() : null);
                const online = participantOnline(conversation);

                dot.classList.toggle('is-online', online);
                dot.classList.toggle('is-offline', !online);
            });

            chatPage.querySelectorAll('[data-presence-label]').forEach(function (label) {
                const conversationId = Number(label.dataset.conversationId || 0);
                const conversation = conversationById(conversationId) || (Number(activeConversation() && activeConversation().id || 0) === conversationId ? activeConversation() : null);
                const baseLabel = label.dataset.baseLabel || '';
                const presenceLabel = statusText(conversation);

                label.textContent = baseLabel ? (baseLabel + ' \u2022 ' + presenceLabel) : presenceLabel;
            });
        };

        const syncPresenceSubscriptions = function () {
            if (!window.Echo) {
                return;
            }

            const nextConversations = Array.isArray(state.conversations) ? state.conversations : [];
            const nextIds = new Set(nextConversations.map(function (conversation) {
                return Number(conversation.id);
            }));

            nextConversations.forEach(function (conversation) {
                const conversationId = Number(conversation.id || 0);
                const channelName = conversation.presence_channel;

                if (!conversationId || !channelName || presenceSubscriptions.has(conversationId)) {
                    return;
                }

                const channel = window.Echo.join(channelName)
                    .here(function (members) {
                        presenceMembers.set(conversationId, new Set(members.map(function (member) {
                            return Number(member.id);
                        })));
                        applyPresenceIndicators();
                    })
                    .joining(function (member) {
                        const members = presenceMembers.get(conversationId) || new Set();
                        members.add(Number(member.id));
                        presenceMembers.set(conversationId, members);
                        applyPresenceIndicators();
                    })
                    .leaving(function (member) {
                        const members = presenceMembers.get(conversationId) || new Set();
                        members.delete(Number(member.id));
                        presenceMembers.set(conversationId, members);

                        if (Number(member.id) !== currentUserId()) {
                            setConversationLastSeen(conversationId, new Date().toISOString(), 'just now');
                        }

                        applyPresenceIndicators();
                    });

                presenceSubscriptions.set(conversationId, channel);
            });

            Array.from(presenceSubscriptions.keys()).forEach(function (conversationId) {
                if (nextIds.has(conversationId)) {
                    return;
                }

                const conversation = conversationById(conversationId);
                if (conversation && conversation.presence_channel) {
                    window.Echo.leave(conversation.presence_channel);
                }

                presenceSubscriptions.delete(conversationId);
                presenceMembers.delete(conversationId);
            });
        };

        const refreshState = async function (options) {
            const conversationId = options && options.conversationId || null;
            const forceScroll = options && options.forceScroll || false;
            const preserveScroll = options && Object.prototype.hasOwnProperty.call(options, 'preserveScroll') ? options.preserveScroll : true;

            if (!fetchUrl || loading) {
                return;
            }

            loading = true;
            const existingContainer = currentMessagesContainer();
            const shouldStickToBottom = preserveScroll ? isNearBottom() : forceScroll;
            const previousScrollTop = existingContainer ? existingContainer.scrollTop : 0;
            const previousScrollHeight = existingContainer ? existingContainer.scrollHeight : 0;
            const url = new URL(fetchUrl, window.location.origin);

            if (conversationId) {
                url.searchParams.set('conversation', String(conversationId));
            }

            try {
                const response = await fetch(url.toString(), {
                    credentials: 'same-origin',
                    headers: requestHeaders(),
                });

                if (!response.ok) {
                    throw new Error('Unable to refresh conversation.');
                }

                state = await response.json();
                renderAll({
                    forceScroll: forceScroll || shouldStickToBottom,
                    preserveScroll: shouldStickToBottom,
                });

                if (!forceScroll && !shouldStickToBottom) {
                    const nextContainer = currentMessagesContainer();
                    if (nextContainer) {
                        nextContainer.scrollTop = previousScrollTop + (nextContainer.scrollHeight - previousScrollHeight);
                    }
                }
            } catch (error) {
                console.error(error);
            } finally {
                loading = false;
            }
        };

        const syncSubscriptions = function () {
            if (!window.Echo) {
                return;
            }

            const nextIds = new Set((state.conversations || []).map(function (conversation) {
                return Number(conversation.id);
            }));

            nextIds.forEach(function (conversationId) {
                if (!conversationId || subscriptions.has(conversationId)) {
                    return;
                }

                const channelName = 'chat.conversation.' + conversationId;
                const channel = window.Echo.private(channelName)
                    .listen('.message.sent', function (event) {
                        refreshState({
                            conversationId: state.active_conversation && state.active_conversation.id || event.conversation_id || conversationId,
                            forceScroll: event.conversation_id === (state.active_conversation && state.active_conversation.id),
                        });
                    })
                    .listen('.typing.updated', function (event) {
                        window.clearTimeout(typingExpiryTimer);

                        if (event.typing && event.conversation_id === (state.active_conversation && state.active_conversation.id)) {
                            typingExpiryTimer = window.setTimeout(function () {
                                refreshState({
                                    conversationId: state.active_conversation && state.active_conversation.id,
                                    preserveScroll: true,
                                });
                            }, 5500);
                        }

                        if (event.conversation_id !== (state.active_conversation && state.active_conversation.id)) {
                            refreshState({
                                conversationId: state.active_conversation && state.active_conversation.id || event.conversation_id,
                            });
                            return;
                        }

                        refreshState({
                            conversationId: state.active_conversation && state.active_conversation.id,
                            preserveScroll: true,
                        });
                    })
                    .listen('.messages.read', function (event) {
                        if (event.conversation_id !== (state.active_conversation && state.active_conversation.id)) {
                            return;
                        }

                        refreshState({
                            conversationId: state.active_conversation && state.active_conversation.id,
                            preserveScroll: true,
                        });
                    });

                subscriptions.set(conversationId, channel);
            });

            Array.from(subscriptions.keys()).forEach(function (conversationId) {
                if (nextIds.has(conversationId)) {
                    return;
                }

                window.Echo.leave('chat.conversation.' + conversationId);
                subscriptions.delete(conversationId);
            });
        };

        const renderConversations = function () {
            if (!conversationListEl) {
                return;
            }

            const conversations = Array.isArray(state.conversations) ? state.conversations : [];

            if (!conversations.length) {
                conversationListEl.innerHTML = [
                    '<div class="inbox-empty-state">',
                    '<h3>No conversations yet</h3>',
                    '<p>Start a conversation from a product or shop page.</p>',
                    '</div>',
                ].join('');
                return;
            }

            conversationListEl.innerHTML = conversations.map(function (conversation) {
                return [
                    '<a href="', escapeHtml(conversation.show_url), '" class="inbox-conversation-item ', (conversation.active ? 'is-active' : ''), '" data-conversation-link data-conversation-id="', conversation.id, '">',
                    '<span class="inbox-conversation-avatar-wrap">',
                    '<span class="inbox-conversation-avatar">',
                    conversation.avatar_url
                        ? '<img src="' + escapeHtml(conversation.avatar_url) + '" alt="' + escapeHtml(conversation.name) + '">'
                        : escapeHtml(conversation.avatar_initials),
                    '</span>',
                    '<span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot data-conversation-id="', conversation.id, '"></span>',
                    '</span>',
                    '<span class="inbox-conversation-copy">',
                    '<span class="inbox-conversation-topline">',
                    '<span class="inbox-conversation-identity"><strong>', escapeHtml(conversation.name), '</strong></span>',
                    Number(conversation.unread_count || 0) > 0 ? '<span class="inbox-unread-badge">' + conversation.unread_count + '</span>' : '',
                    '</span>',
                    '<p>', escapeHtml(conversation.preview), '</p>',
                    '<small>', escapeHtml(conversation.updated_at), '</small>',
                    '</span>',
                    '</a>',
                ].join('');
            }).join('');
        };

        const renderThread = function () {
            if (!threadEl) {
                return;
            }

            const active = state.active_conversation || null;

            if (!active) {
                threadEl.classList.add('is-empty-thread');
                threadEl.innerHTML = [
                    '<div class="inbox-thread-header">',
                    '<button type="button" class="inbox-thread-back" data-inbox-back aria-label="Back to conversations"><i class="fa-solid fa-arrow-left"></i></button>',
                    '<div class="inbox-thread-heading"><h3>No active chat</h3><span>Choose a conversation to continue.</span></div>',
                    '</div>',
                    '<div class="inbox-thread-messages is-empty-pane">',
                    '<div class="inbox-empty-state"><h3>No active chat</h3><p>Select a conversation from the left to continue.</p></div>',
                    '</div>',
                ].join('');
                return;
            }

            threadEl.classList.remove('is-empty-thread');
            threadEl.innerHTML = [
                '<div class="inbox-thread-header">',
                '<button type="button" class="inbox-thread-back" data-inbox-back aria-label="Back to conversations"><i class="fa-solid fa-arrow-left"></i></button>',
                '<span class="inbox-thread-avatar-wrap">',
                '<span class="inbox-thread-avatar">',
                active.avatar_url
                    ? '<img src="' + escapeHtml(active.avatar_url) + '" alt="' + escapeHtml(active.name) + '">'
                    : escapeHtml(active.avatar_initials),
                '</span>',
                '<span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot data-conversation-id="', active.id, '"></span>',
                '</span>',
                '<div class="inbox-thread-heading">',
                '<span class="inbox-thread-identity"><h3>', escapeHtml(active.name), '</h3></span>',
                '<span class="inbox-thread-status" data-presence-label data-conversation-id="', active.id, '" data-base-label="', escapeHtml(active.role_label), '">', escapeHtml(active.role_label), '</span>',
                '</div>',
                '</div>',
                '<div class="inbox-thread-messages" data-inbox-messages>',
                active.messages.length
                    ? active.messages.map(function (message) {
                        return [
                            '<div class="inbox-message-row ', (message.is_current_user ? 'is-current-user ' : ''), (message.is_pending ? 'is-pending ' : ''), (message.is_failed ? 'is-failed' : ''), '">',
                            '<div class="inbox-message-bubble">',
                            '<strong>', escapeHtml(message.sender_label), '</strong>',
                            renderProductCard(message),
                            message.has_text ? '<p>' + escapeHtml(message.message) + '</p>' : '',
                            renderMessageMedia(message, 'inbox-message-media'),
                            '</div>',
                            '<span class="inbox-message-meta">',
                            escapeHtml(message.time),
                            message.status_label ? '<em>' + escapeHtml(message.status_label) + '</em>' : '',
                            '</span>',
                            '</div>',
                        ].join('');
                    }).join('')
                    : '<div class="inbox-empty-state"><h3>No messages yet</h3><p>Send the first message in this conversation.</p></div>',
                active.typing_text
                    ? '<div class="inbox-message-row inbox-typing-row"><div class="inbox-message-bubble"><strong>' + escapeHtml(active.name) + '</strong><p>' + escapeHtml(active.typing_text) + '</p></div></div>'
                    : '',
                '</div>',
                '<form action="', escapeHtml(active.send_url), '" method="POST" enctype="multipart/form-data" class="inbox-reply-form" data-inbox-form>',
                '<input type="hidden" name="_token" value="', escapeHtml(csrfToken), '">',
                '<input type="text" name="message" placeholder="Type a message..." value="">',
                '<input type="file" name="image" accept="image/*,video/*">',
                '<button type="submit" class="page-action-btn">Send</button>',
                '</form>',
            ].join('');
        };

        const bindConversationLinks = function () {
            chatPage.querySelectorAll('[data-conversation-link]').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();

                    const conversationId = Number(link.dataset.conversationId || 0);
                    if (!conversationId) {
                        return;
                    }

                    mobileView = 'thread';
                    applyPageLayoutMode();
                    history.pushState({}, '', link.getAttribute('href') || window.location.href);
                    refreshState({
                        conversationId: conversationId,
                        forceScroll: true,
                    });
                });
            });
        };

        const bindBackButton = function () {
            chatPage.querySelectorAll('[data-inbox-back]').forEach(function (button) {
                button.addEventListener('click', function () {
                    mobileView = 'list';
                    applyPageLayoutMode();

                    if (listUrl) {
                        history.pushState({}, '', listUrl);
                    }
                });
            });
        };

        const syncTyping = async function (typing) {
            const typingUrl = state.active_conversation && state.active_conversation.typing_url;
            if (!typingUrl) {
                return;
            }

            try {
                await fetch(typingUrl, {
                    body: new URLSearchParams({ typing: typing ? '1' : '0' }),
                    credentials: 'same-origin',
                    headers: requestHeaders(),
                    method: 'POST',
                });
            } catch (error) {
                console.error(error);
            }
        };

        const queueTypingSync = function () {
            const form = chatPage.querySelector('[data-inbox-form]');
            const input = form ? form.querySelector('input[name="message"]') : null;
            if (!state.active_conversation || !input) {
                return;
            }

            window.clearTimeout(typingTimer);
            window.clearTimeout(typingResetTimer);

            typingTimer = window.setTimeout(function () {
                syncTyping(true);
            }, 120);

            typingResetTimer = window.setTimeout(function () {
                syncTyping(false);
            }, 1800);
        };

        const bindForm = function () {
            const form = chatPage.querySelector('[data-inbox-form]');
            if (!form) {
                return;
            }

            const input = form.querySelector('input[name="message"]');
            if (input) {
                input.addEventListener('input', queueTypingSync);
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                if (!state.active_conversation) {
                    return;
                }

                const loadingHelper = window.LocalLiftActionLoading;
                const submitButton = form.querySelector('button[type="submit"]');
                const formData = new FormData(form);
                const message = String(formData.get('message') || '').trim();
                const selectedImage = formData.get('image');

                if (!message && (!selectedImage || !selectedImage.name)) {
                    return;
                }

                if (loadingHelper && submitButton) {
                    loadingHelper.start(submitButton, { label: 'Sending...' });
                }

                formData.set('message', message);
                const clientMessageId = createClientMessageId();
                formData.set('client_message_id', clientMessageId);

                const optimisticMessage = createOptimisticMessage({
                    clientMessageId: clientMessageId,
                    text: message,
                    file: selectedImage && selectedImage.name ? selectedImage : null,
                });

                if (input) {
                    input.value = '';
                }

                appendOptimisticMessage(optimisticMessage);
                renderAll({ forceScroll: true });

                try {
                    const response = await fetch(state.active_conversation.send_url, {
                        body: formData,
                        credentials: 'same-origin',
                        headers: requestHeaders(),
                        method: 'POST',
                    });

                    if (!response.ok) {
                        throw new Error('Unable to send message.');
                    }

                    const payload = await response.json();
                    if (payload.widget) {
                        state = payload.widget;
                        renderAll({ forceScroll: true });
                    }

                    window.clearTimeout(typingResetTimer);
                    syncTyping(false);
                } catch (error) {
                    console.error(error);
                    markOptimisticMessageFailed(clientMessageId);
                    renderAll({ forceScroll: true });
                } finally {
                    if (submitButton && submitButton.isConnected && loadingHelper) {
                        loadingHelper.stop(submitButton);
                    }
                }
            });
        };

        const renderAll = function (options) {
            const forceScroll = options && options.forceScroll || false;
            const preserveScroll = options && options.preserveScroll || false;

            renderConversations();
            renderThread();
            bindConversationLinks();
            bindBackButton();
            bindForm();
            syncSubscriptions();
            syncPresenceSubscriptions();
            applyPresenceIndicators();
            applyPageLayoutMode();

            if (forceScroll || preserveScroll || isNearBottom()) {
                scrollMessagesToBottom();
            }
        };

        window.addEventListener('resize', applyPageLayoutMode);
        window.addEventListener('popstate', function () {
            const normalizedListPath = listUrl ? new URL(listUrl, window.location.origin).pathname : '';
            const matchedConversation = (state.conversations || []).find(function (conversation) {
                try {
                    return new URL(conversation.show_url, window.location.origin).pathname === window.location.pathname;
                } catch (error) {
                    return false;
                }
            });

            if (matchedConversation) {
                mobileView = 'thread';
                refreshState({
                    conversationId: Number(matchedConversation.id),
                    preserveScroll: true,
                });
                return;
            }

            mobileView = window.location.pathname === normalizedListPath ? 'list' : 'thread';
            applyPageLayoutMode();
        });

        renderAll({ forceScroll: true });
    });
}());
