<?php
    $isSellerInbox = $isSellerInbox ?? auth('seller')->check();
    $chatData = $chatData ?? ['conversations' => [], 'active_conversation' => null];
    $conversations = $chatData['conversations'] ?? [];
    $activeConversation = $chatData['active_conversation'] ?? null;
    $pageTitle = $isSellerInbox ? 'Seller Messages' : 'Messages';
    $messagesPageScript = asset('assets/js/messages-page.js') . '?v=' . @filemtime(public_path('assets/js/messages-page.js'));
?>


<?php $__env->startSection('title', $isSellerInbox ? 'Seller Messages' : 'LocalLift PH - Messages'); ?>

<?php $__env->startSection('content'); ?>
    <?php if($isSellerInbox): ?>
        <section class="dashboard-wrapper seller-messages-page">
            <div class="container">
                <div class="dashboard-layout">
                    <?php echo $__env->make('seller.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <main class="dashboard-main">
                        <section class="seller-page-panel panel inbox-page-panel">
                            <div class="page-header inbox-page-header">
                                <div>
                                    <span class="section-kicker">Inbox</span>
                                    <h2><?php echo e($pageTitle); ?></h2>
                                    <p>Reply to buyers from one steady inbox view.</p>
                                </div>
                            </div>

                            <?php echo $__env->make('messages.partials.page-inbox', ['conversations' => $conversations, 'activeConversation' => $activeConversation, 'isSellerInbox' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </section>
                    </main>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="buyer-messages-page">
            <div class="container">
                <div class="inbox-page-shell panel">
                    <div class="buyer-messages-heading">
                        <div class="buyer-messages-intro">
                            <span class="section-kicker">Inbox</span>
                            <h2><?php echo e($pageTitle); ?></h2>
                            <p class="floating-chat-page-note">Stay connected with sellers without the floating widget flicker.</p>
                        </div>
                    </div>

                    <?php echo $__env->make('messages.partials.page-inbox', ['conversations' => $conversations, 'activeConversation' => $activeConversation, 'isSellerInbox' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

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

        .inbox-message-media {
            display: block;
            width: min(100%, 240px);
            margin-top: 10px;
            border-radius: 12px;
            object-fit: cover;
            background: #000;
            max-height: 320px;
        }

        .inbox-message-media--video {
            object-fit: contain;
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
    <script src="<?php echo e($messagesPageScript); ?>" defer></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($isSellerInbox ? 'layouts.seller' : 'layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/index.blade.php ENDPATH**/ ?>