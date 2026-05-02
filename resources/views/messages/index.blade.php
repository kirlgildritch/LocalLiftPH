@php
    $isSellerInbox = $isSellerInbox ?? auth('seller')->check();
    $chatData = $chatData ?? ['conversations' => [], 'active_conversation' => null];
    $conversations = $chatData['conversations'] ?? [];
    $activeConversation = $chatData['active_conversation'] ?? null;
    $pageTitle = $isSellerInbox ? 'Seller Messages' : 'Messages';
@endphp

@extends($isSellerInbox ? 'layouts.seller' : 'layouts.app')

@section('content')
    @if($isSellerInbox)
        <section class="dashboard-wrapper seller-messages-page">
            <div class="container">
                <div class="dashboard-layout">
                    @include('seller.partials.sidebar')

                    <main class="dashboard-main">
                        <section class="seller-page-panel panel inbox-page-panel">
                            <div class="page-header inbox-page-header">
                                <div>
                                    <span class="section-kicker">Inbox</span>
                                    <h2>{{ $pageTitle }}</h2>
                                    <p>Reply to buyers from one steady inbox view.</p>
                                </div>
                            </div>

                            @include('messages.partials.page-inbox', ['conversations' => $conversations, 'activeConversation' => $activeConversation, 'isSellerInbox' => true])
                        </section>
                    </main>
                </div>
            </div>
        </section>
    @else
        <section class="buyer-messages-page">
            <div class="container">
                <div class="inbox-page-shell panel">
                    <div class="buyer-messages-heading">
                        <div class="buyer-messages-intro">
                            <span class="section-kicker">Inbox</span>
                            <h2>{{ $pageTitle }}</h2>
                            <p class="floating-chat-page-note">Stay connected with sellers without the floating widget flicker.</p>
                        </div>
                    </div>

                    @include('messages.partials.page-inbox', ['conversations' => $conversations, 'activeConversation' => $activeConversation, 'isSellerInbox' => false])
                </div>
            </div>
        </section>
    @endif

    <style>
        .buyer-messages-page {
            padding: 22px 0 14px;
        }

        .buyer-messages-heading {
            display: grid;
            gap: 10px;
            margin-bottom: 14px;
        }

        .buyer-messages-intro {
            display: grid;
            gap: 6px;
        }

        .buyer-messages-intro h2 {
            margin: 0;
            font-size: 1.45rem;
        }

        .floating-chat-page-note {
            margin: 6px 0 0;
            max-width: 580px;
            color: #8fa7c4;
            line-height: 1.55;
            font-size: 13px;
        }

        .inbox-page-panel,
        .inbox-page-shell {
            display: grid;
            gap: 22px;
            padding: 24px;
        }

        .inbox-page-header p {
            margin: 10px 0 0;
            color: #8fa7c4;
            line-height: 1.75;
        }

        .inbox-layout {
            display: grid;
            grid-template-columns: minmax(300px, 0.9fr) minmax(0, 1.5fr);
            gap: 18px;
            min-height: 620px;
        }

        .inbox-sidebar,
        .inbox-thread {
            display: grid;
            min-height: 0;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.03);
            overflow: hidden;
        }

        .inbox-sidebar {
            grid-template-rows: auto minmax(0, 1fr);
        }

        .inbox-search {
            position: relative;
            padding: 14px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.08);
        }

        .inbox-search i {
            position: absolute;
            top: 50%;
            left: 28px;
            transform: translateY(-50%);
            color: #42a5f5;
            pointer-events: none;
        }

        .inbox-search input {
            width: 100%;
            min-height: 46px;
            padding: 0 14px 0 40px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 14px;
            background: rgba(10, 19, 34, 0.72);
            color: #f5f9ff;
            outline: none;
        }

        .inbox-search input:focus,
        .inbox-reply-form input[type="text"]:focus,
        .inbox-reply-form input[type="file"]:focus {
            border-color: rgba(66, 165, 245, 0.42);
            box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.14);
        }

        .inbox-conversation-list,
        .inbox-thread-messages {
            min-height: 0;
            overflow-y: auto;
            scrollbar-gutter: stable;
        }

        .inbox-conversation-list::-webkit-scrollbar,
        .inbox-thread-messages::-webkit-scrollbar {
            width: 6px;
        }

        .inbox-conversation-list::-webkit-scrollbar-thumb,
        .inbox-thread-messages::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(144, 202, 249, 0.36);
        }

        .inbox-conversation-item {
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr);
            gap: 12px;
            padding: 14px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.08);
            color: inherit;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .inbox-conversation-item:hover,
        .inbox-conversation-item.is-active {
            background: rgba(66, 165, 245, 0.08);
        }

        .inbox-conversation-avatar,
        .inbox-conversation-avatar img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
        }

        .inbox-conversation-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(66, 165, 245, 0.16);
            color: #bbdefb;
            font-size: 14px;
            font-weight: 700;
            overflow: hidden;
        }

        .inbox-conversation-avatar-wrap {
            position: relative;
            display: inline-flex;
            width: 46px;
            height: 46px;
            line-height: 0;
            flex-shrink: 0;
        }

        .inbox-conversation-avatar img {
            object-fit: cover;
            display: block;
        }

        .inbox-conversation-copy {
            min-width: 0;
            display: grid;
            gap: 5px;
        }

        .inbox-conversation-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .inbox-conversation-identity,
        .inbox-thread-identity {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .inbox-presence-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #6b7280;
            box-shadow: 0 0 0 2px rgba(10, 19, 34, 0.7);
            flex-shrink: 0;
        }

        .inbox-presence-dot.is-online {
            background: #22c55e;
        }

        .inbox-presence-dot.is-offline {
            background: #64748b;
        }

        .inbox-conversation-topline strong,
        .inbox-thread-heading h3,
        .inbox-empty-state h3,
        .inbox-message-bubble strong {
            margin: 0;
            color: #f5f9ff;
        }

        .inbox-conversation-copy p,
        .inbox-conversation-copy small,
        .inbox-thread-heading span,
        .inbox-empty-state p,
        .inbox-message-bubble p,
        .inbox-message-meta {
            margin: 0;
            color: #8fa7c4;
        }

        .inbox-conversation-copy p {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .inbox-unread-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, #42a5f5, #1565c0);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .inbox-thread {
            grid-template-rows: auto minmax(0, 1fr) auto;
        }

        .inbox-thread.is-empty-thread {
            grid-template-rows: auto minmax(0, 1fr);
        }

        .inbox-thread-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.08);
            background: rgba(10, 19, 34, 0.45);
        }

        .inbox-thread-back {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 12px;
            background: rgba(19, 32, 51, 0.88);
            color: #bbdefb;
            cursor: pointer;
            flex-shrink: 0;
        }

        .inbox-thread-avatar,
        .inbox-thread-avatar img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
        }

        .inbox-thread-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: rgba(66, 165, 245, 0.16);
            color: #bbdefb;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .inbox-thread-avatar-wrap {
            position: relative;
            display: inline-flex;
            flex-shrink: 0;
        }

        .inbox-thread-avatar img {
            object-fit: cover;
            display: block;
        }

        .inbox-presence-dot--avatar {
            position: absolute;
            right: 0;
            bottom: 0;
            transform: translate(14%, 14%);
        }

        .inbox-thread-heading {
            display: grid;
            gap: 4px;
            min-width: 0;
        }

        .inbox-thread-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .inbox-thread-messages {
            display: grid;
            gap: 12px;
            align-content: start;
            padding: 18px;
            background: rgba(7, 15, 27, 0.28);
        }

        .inbox-thread-messages.is-empty-pane {
            align-content: stretch;
        }

        .inbox-message-row {
            display: grid;
            gap: 6px;
            justify-items: start;
        }

        .inbox-message-row.is-current-user {
            justify-items: end;
        }

        .inbox-message-row.is-pending {
            opacity: 0.78;
        }

        .inbox-message-row.is-failed .inbox-message-bubble {
            border-color: rgba(239, 68, 68, 0.35);
        }

        .inbox-message-bubble {
            max-width: min(70%, 420px);
            padding: 12px 14px;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 16px 16px 16px 8px;
            background: rgba(17, 29, 45, 0.92);
        }

        .inbox-message-row.is-current-user .inbox-message-bubble {
            border-radius: 16px 16px 8px 16px;
            background: linear-gradient(135deg, rgba(66, 165, 245, 0.18), rgba(21, 101, 192, 0.22));
            border-color: rgba(66, 165, 245, 0.28);
        }

        .inbox-message-bubble p {
            margin-top: 4px;
            line-height: 1.6;
            color: #d6e4f5;
            overflow-wrap: anywhere;
        }

        .inbox-message-image {
            display: block;
            width: min(100%, 240px);
            margin-top: 10px;
            border-radius: 12px;
            object-fit: cover;
        }

        .inbox-product-card {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            gap: 12px;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid rgba(66, 165, 245, 0.18);
            border-radius: 14px;
            background: rgba(10, 19, 34, 0.5);
            color: inherit;
            text-decoration: none;
        }

        .inbox-product-card:hover {
            border-color: rgba(66, 165, 245, 0.34);
            background: rgba(15, 27, 43, 0.86);
        }

        .inbox-product-card-image {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            object-fit: cover;
            display: block;
        }

        .inbox-product-card-copy {
            min-width: 0;
            display: grid;
            gap: 3px;
        }

        .inbox-product-card-copy strong {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .inbox-product-card-copy span {
            color: #8fa7c4;
            font-size: 12px;
            line-height: 1.45;
        }

        .inbox-product-card-label {
            color: #bbdefb;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .inbox-message-meta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }

        .inbox-message-meta em {
            font-style: normal;
            color: #bbdefb;
        }

        .inbox-typing-row .inbox-message-bubble {
            background: rgba(66, 165, 245, 0.08);
            border-color: rgba(66, 165, 245, 0.2);
        }

        .inbox-reply-form {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            padding: 16px 18px;
            border-top: 1px solid rgba(187, 222, 251, 0.08);
            background: rgba(10, 19, 34, 0.45);
        }

        .inbox-reply-form input[type="text"] {
            flex: 1 1 280px;
            min-height: 46px;
            padding: 0 14px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 14px;
            background: rgba(10, 19, 34, 0.72);
            color: #f5f9ff;
            outline: none;
        }

        .inbox-reply-form input[type="file"] {
            min-height: 46px;
            padding: 10px 12px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 14px;
            background: rgba(10, 19, 34, 0.72);
            color: #f5f9ff;
            outline: none;
        }

        .inbox-reply-form .page-action-btn {
            min-height: 46px;
        }

        .inbox-empty-state {
            display: grid;
            gap: 8px;
            place-items: center;
            align-content: center;
            min-height: 100%;
            padding: 32px;
            text-align: center;
        }

        @media (max-width: 960px) {
            .inbox-layout {
                grid-template-columns: 1fr;
            }

            .inbox-sidebar {
                max-height: none;
            }
        }

        @media (max-width: 860px) {
            .inbox-layout {
                grid-template-columns: 1fr;
                min-height: min(76vh, 760px);
            }

            .inbox-sidebar,
            .inbox-thread {
                min-height: 0;
                height: 100%;
            }

            .inbox-layout.is-mobile-thread .inbox-sidebar {
                display: none;
            }

            .inbox-layout:not(.is-mobile-thread) .inbox-thread {
                display: none;
            }

            .inbox-thread-back {
                display: inline-flex;
            }
        }

        @media (max-width: 640px) {
            .inbox-page-panel,
            .inbox-page-shell {
                padding: 18px;
            }

            .inbox-layout {
                min-height: min(78vh, 820px);
            }

            .inbox-thread-header,
            .inbox-thread-messages,
            .inbox-reply-form {
                padding-left: 14px;
                padding-right: 14px;
            }

            .inbox-message-bubble {
                max-width: 100%;
            }

            .inbox-reply-form input[type="text"],
            .inbox-reply-form input[type="file"],
            .inbox-reply-form .page-action-btn {
                width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatPage = document.querySelector('[data-chat-page]');
            if (!chatPage) {
                const messageContainer = document.querySelector('[data-inbox-messages]');
                if (messageContainer) {
                    messageContainer.scrollTop = messageContainer.scrollHeight;
                }

                return;
            }

            const stateScript = chatPage.querySelector('[data-chat-page-state]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const fetchUrl = chatPage.dataset.fetchUrl || '';
            const listUrl = chatPage.dataset.listUrl || '';
            const initialMobileView = chatPage.dataset.mobileView === 'thread' ? 'thread' : 'list';
            const conversationListEl = chatPage.querySelector('[data-inbox-conversation-list]');
            const threadEl = chatPage.querySelector('[data-inbox-thread]');
            const subscriptions = new Map();
            const presenceSubscriptions = new Map();
            const presenceMembers = new Map();
            let state = stateScript ? JSON.parse(stateScript.textContent || '{}') : {};
            let typingTimer = null;
            let typingResetTimer = null;
            let typingExpiryTimer = null;
            let loading = false;
            let mobileView = initialMobileView;

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const renderProductCard = (message) => {
                if (!message?.has_product || !message?.product) {
                    return '';
                }

                return `
                    <a href="${escapeHtml(message.product.url)}" class="inbox-product-card">
                        <img src="${escapeHtml(message.product.image_url)}" alt="${escapeHtml(message.product.name)}" class="inbox-product-card-image">
                        <span class="inbox-product-card-copy">
                            <span class="inbox-product-card-label">Product</span>
                            <strong>${escapeHtml(message.product.name)}</strong>
                            <span>${escapeHtml(message.product.price_label)}</span>
                            <span>${escapeHtml(message.product.shop_name)}</span>
                        </span>
                    </a>
                `;
            };

            const socketId = () => window.Echo?.socketId?.() || '';
            const isCompactViewport = () => window.matchMedia('(max-width: 860px)').matches;

            const applyPageLayoutMode = () => {
                chatPage.classList.toggle('is-mobile-thread', isCompactViewport() && mobileView === 'thread');
            };

            const requestHeaders = (includeSocket = true) => {
                const headers = {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                };

                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }

                if (includeSocket && socketId()) {
                    headers['X-Socket-ID'] = socketId();
                }

                return headers;
            };

            const isNearBottom = () => {
                const messageContainer = chatPage.querySelector('[data-inbox-messages]');
                if (!messageContainer) {
                    return true;
                }

                const distanceFromBottom = messageContainer.scrollHeight - messageContainer.scrollTop - messageContainer.clientHeight;

                return distanceFromBottom <= 48;
            };

            const scrollMessagesToBottom = () => {
                const messageContainer = chatPage.querySelector('[data-inbox-messages]');
                if (messageContainer) {
                    messageContainer.scrollTop = messageContainer.scrollHeight;
                }
            };

            const currentUserId = () => Number(state.meta?.current_user_id || 0);
            const createClientMessageId = () => `chat-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

            const conversationById = (conversationId) => {
                return (state.conversations || []).find((conversation) => Number(conversation.id) === Number(conversationId)) || null;
            };

            const activeConversation = () => state.active_conversation || null;

            const setConversationLastSeen = (conversationId, isoValue, label) => {
                const conversation = conversationById(conversationId);
                if (conversation) {
                    conversation.last_seen_at = isoValue;
                    conversation.last_seen_label = label;
                }

                if (Number(activeConversation()?.id || 0) === Number(conversationId)) {
                    state.active_conversation.last_seen_at = isoValue;
                    state.active_conversation.last_seen_label = label;
                }
            };

            const participantOnline = (conversation) => {
                const participantId = Number(conversation?.participant_id || 0);
                const members = presenceMembers.get(Number(conversation?.id || 0));

                return Boolean(participantId && members?.has(participantId));
            };

            const statusText = (conversation) => {
                if (participantOnline(conversation)) {
                    return 'Online';
                }

                if (conversation?.last_seen_label) {
                    return `Last seen ${conversation.last_seen_label}`;
                }

                return 'Offline';
            };

            const messagePreviewText = (message) => {
                if (message.has_product && message.product?.name) {
                    return `Product: ${String(message.product.name).slice(0, 40)}`;
                }

                if (message.has_image && !message.has_text) {
                    return 'Sent an image';
                }

                if (message.has_image && message.has_text) {
                    return `Image: ${String(message.message || '').slice(0, 40)}`;
                }

                return String(message.message || 'Start chatting from a product or shop page.').slice(0, 52);
            };

            const syncConversationFromMessage = (message, fallbackIso = null) => {
                const conversation = conversationById(activeConversation()?.id);
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
                    ...(state.conversations || []).filter((item) => Number(item.id) !== Number(conversation.id)),
                ];
            };

            const createOptimisticMessage = ({ clientMessageId, text, file }) => ({
                id: `temp-${clientMessageId}`,
                client_message_id: clientMessageId,
                sender_label: 'You',
                message: text,
                image_url: file ? URL.createObjectURL(file) : null,
                has_image: Boolean(file),
                has_text: Boolean(text),
                has_product: false,
                product: null,
                time: 'Sending...',
                is_current_user: true,
                is_seen: false,
                status_label: 'Sending...',
                is_pending: true,
                is_failed: false,
            });

            const appendOptimisticMessage = (message) => {
                if (!activeConversation()) {
                    return;
                }

                state.active_conversation.messages = [
                    ...(Array.isArray(state.active_conversation.messages) ? state.active_conversation.messages : []),
                    message,
                ];

                syncConversationFromMessage(message);
            };

            const markOptimisticMessageFailed = (clientMessageId) => {
                if (!activeConversation()) {
                    return;
                }

                state.active_conversation.messages = (state.active_conversation.messages || []).map((message) => {
                    if (message.client_message_id !== clientMessageId) {
                        return message;
                    }

                    return {
                        ...message,
                        time: 'Not sent',
                        status_label: 'Failed',
                        is_pending: false,
                        is_failed: true,
                    };
                });

                const failedMessage = (state.active_conversation.messages || []).find((message) => message.client_message_id === clientMessageId);
                if (failedMessage) {
                    syncConversationFromMessage(failedMessage);
                }
            };

            const applyPresenceIndicators = () => {
                chatPage.querySelectorAll('[data-presence-dot]').forEach((dot) => {
                    const conversationId = Number(dot.dataset.conversationId || 0);
                    const conversation = conversationById(conversationId) || (Number(activeConversation()?.id || 0) === conversationId ? activeConversation() : null);
                    const online = participantOnline(conversation);

                    dot.classList.toggle('is-online', online);
                    dot.classList.toggle('is-offline', !online);
                });

                chatPage.querySelectorAll('[data-presence-label]').forEach((label) => {
                    const conversationId = Number(label.dataset.conversationId || 0);
                    const conversation = conversationById(conversationId) || (Number(activeConversation()?.id || 0) === conversationId ? activeConversation() : null);
                    const baseLabel = label.dataset.baseLabel || '';
                    const presenceLabel = statusText(conversation);

                    label.textContent = baseLabel ? `${baseLabel} • ${presenceLabel}` : presenceLabel;
                });
            };

            const syncPresenceSubscriptions = () => {
                if (!window.Echo) {
                    return;
                }

                const nextConversations = Array.isArray(state.conversations) ? state.conversations : [];
                const nextIds = new Set(nextConversations.map((conversation) => Number(conversation.id)));

                nextConversations.forEach((conversation) => {
                    const conversationId = Number(conversation.id || 0);
                    const channelName = conversation.presence_channel;

                    if (!conversationId || !channelName || presenceSubscriptions.has(conversationId)) {
                        return;
                    }

                    const channel = window.Echo.join(channelName)
                        .here((members) => {
                            presenceMembers.set(conversationId, new Set(members.map((member) => Number(member.id))));
                            applyPresenceIndicators();
                        })
                        .joining((member) => {
                            const members = presenceMembers.get(conversationId) || new Set();
                            members.add(Number(member.id));
                            presenceMembers.set(conversationId, members);
                            applyPresenceIndicators();
                        })
                        .leaving((member) => {
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

                Array.from(presenceSubscriptions.keys()).forEach((conversationId) => {
                    if (nextIds.has(conversationId)) {
                        return;
                    }

                    const conversation = conversationById(conversationId);
                    if (conversation?.presence_channel) {
                        window.Echo.leave(conversation.presence_channel);
                    }

                    presenceSubscriptions.delete(conversationId);
                    presenceMembers.delete(conversationId);
                });
            };

            const syncSubscriptions = () => {
                if (!window.Echo) {
                    return;
                }

                const nextIds = new Set((state.conversations || []).map((conversation) => Number(conversation.id)));

                nextIds.forEach((conversationId) => {
                    if (!conversationId || subscriptions.has(conversationId)) {
                        return;
                    }

                    const channelName = `chat.conversation.${conversationId}`;
                    const channel = window.Echo.private(channelName)
                        .listen('.message.sent', (event) => {
                            refreshState({
                                conversationId: state.active_conversation?.id || event.conversation_id || conversationId,
                                forceScroll: event.conversation_id === state.active_conversation?.id,
                            });
                        })
                        .listen('.typing.updated', (event) => {
                            window.clearTimeout(typingExpiryTimer);

                            if (event.typing && event.conversation_id === state.active_conversation?.id) {
                                typingExpiryTimer = window.setTimeout(() => {
                                    refreshState({
                                        conversationId: state.active_conversation?.id,
                                        preserveScroll: true,
                                    });
                                }, 5500);
                            }

                            if (event.conversation_id !== state.active_conversation?.id) {
                                refreshState({
                                    conversationId: state.active_conversation?.id || event.conversation_id,
                                });
                                return;
                            }

                            refreshState({
                                conversationId: state.active_conversation?.id,
                                preserveScroll: true,
                            });
                        })
                        .listen('.messages.read', (event) => {
                            if (event.conversation_id !== state.active_conversation?.id) {
                                return;
                            }

                            refreshState({
                                conversationId: state.active_conversation?.id,
                                preserveScroll: true,
                            });
                        });

                    subscriptions.set(conversationId, channel);
                });

                Array.from(subscriptions.keys()).forEach((conversationId) => {
                    if (nextIds.has(conversationId)) {
                        return;
                    }

                    window.Echo.leave(`chat.conversation.${conversationId}`);
                    subscriptions.delete(conversationId);
                });
            };

            const renderConversations = () => {
                if (!conversationListEl) {
                    return;
                }

                const conversations = Array.isArray(state.conversations) ? state.conversations : [];

                if (!conversations.length) {
                    conversationListEl.innerHTML = `
                        <div class="inbox-empty-state">
                            <h3>No conversations yet</h3>
                            <p>Start a conversation from a product or shop page.</p>
                        </div>
                    `;
                    return;
                }

                conversationListEl.innerHTML = conversations.map((conversation) => `
                    <a href="${escapeHtml(conversation.show_url)}" class="inbox-conversation-item ${conversation.active ? 'is-active' : ''}" data-conversation-link data-conversation-id="${conversation.id}">
                        <span class="inbox-conversation-avatar-wrap">
                            <span class="inbox-conversation-avatar">
                                ${conversation.avatar_url
                                ? `<img src="${escapeHtml(conversation.avatar_url)}" alt="${escapeHtml(conversation.name)}">`
                                : escapeHtml(conversation.avatar_initials)}
                            </span>
                            <span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot data-conversation-id="${conversation.id}"></span>
                        </span>

                        <span class="inbox-conversation-copy">
                            <span class="inbox-conversation-topline">
                                <span class="inbox-conversation-identity">
                                    <strong>${escapeHtml(conversation.name)}</strong>
                                </span>
                                ${Number(conversation.unread_count || 0) > 0
                                    ? `<span class="inbox-unread-badge">${conversation.unread_count}</span>`
                                    : ''}
                            </span>

                            <p>${escapeHtml(conversation.preview)}</p>
                            <small>${escapeHtml(conversation.updated_at)}</small>
                        </span>
                    </a>
                `).join('');
            };

            const renderThread = () => {
                if (!threadEl) {
                    return;
                }

                const activeConversation = state.active_conversation || null;

                if (!activeConversation) {
                    threadEl.classList.add('is-empty-thread');
                    threadEl.innerHTML = `
                        <div class="inbox-thread-header">
                            <button type="button" class="inbox-thread-back" data-inbox-back aria-label="Back to conversations">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <div class="inbox-thread-heading">
                                <h3>No active chat</h3>
                                <span>Choose a conversation to continue.</span>
                            </div>
                        </div>

                        <div class="inbox-thread-messages is-empty-pane">
                            <div class="inbox-empty-state">
                                <h3>No active chat</h3>
                                <p>Select a conversation from the left to continue.</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                threadEl.classList.remove('is-empty-thread');
                threadEl.innerHTML = `
                    <div class="inbox-thread-header">
                        <button type="button" class="inbox-thread-back" data-inbox-back aria-label="Back to conversations">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <span class="inbox-thread-avatar-wrap">
                            <span class="inbox-thread-avatar">
                                ${activeConversation.avatar_url
                                ? `<img src="${escapeHtml(activeConversation.avatar_url)}" alt="${escapeHtml(activeConversation.name)}">`
                                : escapeHtml(activeConversation.avatar_initials)}
                            </span>
                            <span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot data-conversation-id="${activeConversation.id}"></span>
                        </span>

                        <div class="inbox-thread-heading">
                            <span class="inbox-thread-identity">
                                <h3>${escapeHtml(activeConversation.name)}</h3>
                            </span>
                            <span class="inbox-thread-status" data-presence-label data-conversation-id="${activeConversation.id}" data-base-label="${escapeHtml(activeConversation.role_label)}">${escapeHtml(activeConversation.role_label)}</span>
                        </div>
                    </div>

                    <div class="inbox-thread-messages" data-inbox-messages>
                        ${activeConversation.messages.length
                            ? activeConversation.messages.map((message) => `
                                <div class="inbox-message-row ${message.is_current_user ? 'is-current-user' : ''} ${message.is_pending ? 'is-pending' : ''} ${message.is_failed ? 'is-failed' : ''}">
                                    <div class="inbox-message-bubble">
                                        <strong>${escapeHtml(message.sender_label)}</strong>
                                        ${renderProductCard(message)}
                                        ${message.has_text ? `<p>${escapeHtml(message.message)}</p>` : ''}
                                        ${message.has_image ? `<img src="${escapeHtml(message.image_url)}" alt="Shared image" class="inbox-message-image">` : ''}
                                    </div>
                                    <span class="inbox-message-meta">
                                        ${escapeHtml(message.time)}
                                        ${message.status_label ? `<em>${escapeHtml(message.status_label)}</em>` : ''}
                                    </span>
                                </div>
                            `).join('')
                            : `
                                <div class="inbox-empty-state">
                                    <h3>No messages yet</h3>
                                    <p>Send the first message in this conversation.</p>
                                </div>
                            `}
                        ${activeConversation.typing_text ? `
                            <div class="inbox-message-row inbox-typing-row">
                                <div class="inbox-message-bubble">
                                    <strong>${escapeHtml(activeConversation.name)}</strong>
                                    <p>${escapeHtml(activeConversation.typing_text)}</p>
                                </div>
                            </div>
                        ` : ''}
                    </div>

                    <form action="${escapeHtml(activeConversation.send_url)}" method="POST" enctype="multipart/form-data" class="inbox-reply-form" data-inbox-form>
                        <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                        <input type="text" name="message" placeholder="Type a message..." value="">
                        <input type="file" name="image" accept="image/*">
                        <button type="submit" class="page-action-btn">Send</button>
                    </form>
                `;
            };

            const bindConversationLinks = () => {
                chatPage.querySelectorAll('[data-conversation-link]').forEach((link) => {
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
                            conversationId,
                            forceScroll: true,
                        });
                    });
                });
            };

            const bindBackButton = () => {
                chatPage.querySelectorAll('[data-inbox-back]').forEach((button) => {
                    button.addEventListener('click', function () {
                        mobileView = 'list';
                        applyPageLayoutMode();

                        if (listUrl) {
                            history.pushState({}, '', listUrl);
                        }
                    });
                });
            };

            const syncTyping = async (typing) => {
                const typingUrl = state.active_conversation?.typing_url;
                if (!typingUrl) {
                    return;
                }

                try {
                    await fetch(typingUrl, {
                        method: 'POST',
                        headers: requestHeaders(),
                        body: new URLSearchParams({ typing: typing ? '1' : '0' }),
                        credentials: 'same-origin',
                    });
                } catch (error) {
                    console.error(error);
                }
            };

            const queueTypingSync = () => {
                const input = chatPage.querySelector('[data-inbox-form] input[name="message"]');
                if (!state.active_conversation || !input) {
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

            const bindForm = () => {
                const form = chatPage.querySelector('[data-inbox-form]');
                if (!form) {
                    return;
                }

                const input = form.querySelector('input[name="message"]');
                input?.addEventListener('input', queueTypingSync);

                form.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    if (!state.active_conversation) {
                        return;
                    }

                    const formData = new FormData(form);
                    const message = String(formData.get('message') || '').trim();
                    const selectedImage = formData.get('image');

                    if (!message && (!selectedImage || !selectedImage.name)) {
                        return;
                    }

                    formData.set('message', message);
                    const clientMessageId = createClientMessageId();
                    formData.set('client_message_id', clientMessageId);

                    const optimisticMessage = createOptimisticMessage({
                        clientMessageId,
                        text: message,
                        file: selectedImage && selectedImage.name ? selectedImage : null,
                    });

                    input.value = '';
                    appendOptimisticMessage(optimisticMessage);
                    renderAll({ forceScroll: true });

                    try {
                        const response = await fetch(state.active_conversation.send_url, {
                            method: 'POST',
                            headers: requestHeaders(),
                            body: formData,
                            credentials: 'same-origin',
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
                    }
                });
            };

            const renderAll = ({ forceScroll = false, preserveScroll = false } = {}) => {
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

            const refreshState = async ({ conversationId = null, forceScroll = false, preserveScroll = true } = {}) => {
                if (!fetchUrl || loading) {
                    return;
                }

                loading = true;
                const existingContainer = chatPage.querySelector('[data-inbox-messages]');
                const shouldStickToBottom = preserveScroll ? isNearBottom() : forceScroll;
                const previousScrollTop = existingContainer?.scrollTop ?? 0;
                const previousScrollHeight = existingContainer?.scrollHeight ?? 0;
                const url = new URL(fetchUrl, window.location.origin);

                if (conversationId) {
                    url.searchParams.set('conversation', String(conversationId));
                }

                try {
                    const response = await fetch(url.toString(), {
                        headers: requestHeaders(),
                        credentials: 'same-origin',
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
                        const nextContainer = chatPage.querySelector('[data-inbox-messages]');
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

            window.addEventListener('resize', applyPageLayoutMode);
            window.addEventListener('popstate', function () {
                const normalizedListPath = listUrl ? new URL(listUrl, window.location.origin).pathname : '';
                const matchedConversation = (state.conversations || []).find((conversation) => {
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
    </script>
@endsection
