<?php
    $isSellerWidget = auth('seller')->check();
    $initialConversation = $chatWidgetConversationId ?? optional(request()->route('conversation'))->id;
    $autoOpenWidget = ($chatWidgetAutoOpen ?? false) || request()->routeIs('messages.*') || request()->routeIs('seller.messages*');
    $widgetFetchUrl = $isSellerWidget ? route('seller.chat.widget') : route('chat.widget');
    $floatingChatScript = asset('assets/js/floating-chat.js') . '?v=' . @filemtime(public_path('assets/js/floating-chat.js'));
?>

<div class="chat-widget-shell" data-chat-widget data-fetch-url="<?php echo e($widgetFetchUrl); ?>"
    data-initial-conversation="<?php echo e((int) $initialConversation); ?>" data-auto-open="<?php echo e($autoOpenWidget ? '1' : '0'); ?>">
    <button type="button" class="chat-widget-fab" data-chat-toggle aria-label="Open chat">
        <i class="fa-regular fa-comments"></i>

        <strong class="chat-widget-count is-hidden" data-chat-count></strong>
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

                <div class="chat-widget-conversations skeleton-swap is-content-ready" data-chat-conversations></div>
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

                <div class="chat-widget-messages skeleton-swap is-content-ready" data-chat-messages></div>

                <form class="chat-widget-form" data-chat-form enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="file" name="image" accept="image/*,video/*" data-chat-image-input hidden>
                    <button type="button" class="chat-widget-attach" data-chat-attach aria-label="Attach media">
                        <i class="fa-solid fa-paperclip"></i>
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

<script src="<?php echo e($floatingChatScript); ?>" defer></script>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/partials/floating-chat.blade.php ENDPATH**/ ?>