<?php
    $sellerHeaderNotifications = $sellerHeaderNotifications ?? collect();
    $sellerUnreadNotificationCount = (int) ($sellerUnreadNotificationCount ?? 0);
    $sellerHeaderScript = asset('assets/js/seller-header.js') . '?v=' . @filemtime(public_path('assets/js/seller-header.js'));
?>

<header
    class="seller-header-shell"
    data-seller-header-shell
    data-seller-suggestions-url="<?php echo e(route('seller.search.suggestions')); ?>"
    data-seller-notification-user-id="<?php echo e((int) auth('seller')->id()); ?>"
    data-seller-notification-feed-url="<?php echo e(route('seller.notifications.feed')); ?>"
    data-seller-notification-base-url="<?php echo e(url('/seller-notifications')); ?>"
    data-seller-unread-count="<?php echo e($sellerUnreadNotificationCount); ?>">
    <div class="container">
        <div class="seller-header panel">
            <button class="seller-menu-toggle" type="button" id="sellerMenuToggle" aria-label="Open seller navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="<?php echo e(route('seller.dashboard')); ?>" class="seller-brand">
                <span class="seller-brand-icon">
                    <img src="<?php echo e(asset('assets/image/Logo.png')); ?>" alt="Logo">
                </span>
                <span class="seller-brand-copy">
                    <strong>LocalLift</strong>
                    <small>Seller Hub</small>
                </span>
            </a>

            <form class="seller-search" action="<?php echo e(route('seller.search')); ?>" method="GET">

                <input type="text" id="sellerSearchInput" name="q" value="<?php echo e(request('q')); ?>"
                    placeholder="Search products, orders, messages, and tools..." aria-label="Search seller dashboard"
                    autocomplete="off">
                <button type="button" id="sellerSearchClearButton" class="seller-search__clear is-hidden"
                    title="Clear search" aria-label="Clear search">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <button type="submit" class="seller-search__submit" title="Search" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <div id="sellerSearchSuggestions" class="seller-search-suggestions"></div>
            </form>

            <div class="seller-header-actions">
                <div class="notification-dropdown">
                    <button class="notification-btn" id="notificationToggle" type="button" data-seller-notification-button
                        aria-label="View notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notif-badge <?php echo e($sellerUnreadNotificationCount > 0 ? '' : 'is-hidden'); ?>"
                            data-seller-notification-badge>
                            <?php echo e($sellerUnreadNotificationCount > 99 ? '99+' : $sellerUnreadNotificationCount); ?>

                        </span>
                    </button>

                    <div class="notification-menu" id="notificationMenu" data-seller-notification-menu>
                        <div class="notification-header">
                            <div>
                                <h4>Notifications</h4>
                                <p class="notification-header__meta" data-seller-notification-meta>
                                    <?php echo e($sellerUnreadNotificationCount > 0
                                        ? $sellerUnreadNotificationCount . ' unread notification' . ($sellerUnreadNotificationCount === 1 ? '' : 's')
                                        : 'You are all caught up.'); ?>

                                </p>
                            </div>
                        </div>

                        <?php $__empty_1 = true; $__currentLoopData = $sellerHeaderNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $data = $notification->data ?? [];
                                $type = $data['type'] ?? $data['category'] ?? 'admin';
                                $action = $data['action'] ?? 'notification';
                                $title = $data['title'] ?? 'Notification';
                                $message = $data['message'] ?? 'You have a new notification.';
                                $icon = match ($action) {
                                    'new_order', 'order_completed', 'order_cancelled', 'buyer_confirmed_receipt', 'pending_order_not_shipped' => 'fa-bag-shopping',
                                    'buyer_message' => 'fa-envelope',
                                    'buyer_review' => 'fa-star',
                                    'product_low_stock', 'product_out_of_stock', 'product_edited' => 'fa-box',
                                    'product_approved', 'product_rejected', 'product_reported', 'shop_verified', 'shop_flagged', 'warn_seller', 'delist_product', 'ban_product', 'suspend_seller', 'dismiss_report', 'shop_documents_requested' => 'fa-triangle-exclamation',
                                    default => ($type === 'messages' ? 'fa-envelope' : ($type === 'orders' ? 'fa-bag-shopping' : ($type === 'reviews' ? 'fa-star' : 'fa-bell'))),
                                };
                            ?>

                            <div class="notification-item <?php echo e($notification->read_at ? '' : 'unread'); ?>"
                                data-seller-notification-item
                                data-seller-notification-id="<?php echo e($notification->id); ?>"
                                data-seller-notification-read="<?php echo e($notification->read_at ? '1' : '0'); ?>">
                                <div class="notif-icon"><i class="fa-solid <?php echo e($icon); ?>"></i></div>
                                <a href="<?php echo e(route('seller.notifications.open', $notification)); ?>" class="notif-content"
                                    <?php if($action === 'buyer_message' && !empty($data['related_id'])): ?>
                                        data-chat-notification-link
                                        data-chat-conversation-id="<?php echo e((int) $data['related_id']); ?>"
                                    <?php endif; ?>>
                                    <p><strong><?php echo e($title); ?></strong></p>
                                    <span><?php echo e($message); ?></span>
                                    <small><?php echo e($notification->created_at?->diffForHumans() ?? 'Just now'); ?></small>
                                </a>

                                <?php if(! $notification->read_at): ?>
                                    <form method="POST" action="<?php echo e(route('seller.notifications.read', $notification)); ?>"
                                        class="notification-read-form" data-seller-notification-read-form>
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="notification-read-btn" title="Mark as read"
                                            aria-label="Mark as read">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="notification-item notification-item--empty" data-seller-notification-empty>
                                <div class="notif-icon"><i class="fa-regular fa-bell-slash"></i></div>
                                <div class="notif-content">
                                    <p><strong>No notifications</strong></p>
                                    <span>You're all caught up.</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="notification-footer">
                            <a href="<?php echo e(route('seller.notifications.index')); ?>">View all notifications</a>
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown">
                    <button class="profile-btn" id="profileToggle" type="button">
                        <?php if(auth()->user()->profile_image): ?>
                            <img src="<?php echo e(asset('storage/' . auth()->user()->profile_image)); ?>" alt="Profile"
                                class="header-profile-img">
                        <?php else: ?>
                            <i class="fa-regular fa-circle-user profile-icon"></i>
                        <?php endif; ?>

                        <span>Hi, <?php echo e(auth()->user()->name); ?>!</span>
                    </button>

                    <div class="profile-menu" id="profileMenu">
                        <a href="<?php echo e(route('seller.profile')); ?>">My Profile</a>
                        <a href="<?php echo e(route('seller.settings')); ?>">Settings</a>

                        <form action="<?php echo e(route('seller.logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="logout">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script src="<?php echo e($sellerHeaderScript); ?>" defer></script>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/partials/seller-header.blade.php ENDPATH**/ ?>