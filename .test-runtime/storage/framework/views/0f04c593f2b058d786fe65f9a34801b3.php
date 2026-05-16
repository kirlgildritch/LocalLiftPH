<?php $__env->startSection('title', 'LocalLift PH - Shop'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/shop_details.css')); ?>">
<?php
    $ownsShop = auth()->check() && (int) $user->id === (int) auth()->id();
    $shopCategories = $products->groupBy(fn ($product) => $product->category?->name ?? 'Uncategorized');
    $canReportSeller = auth('web')->check() && ! $ownsShop;
    $shopReviewsToggleUrl = $showAllReviews
        ? route('shops.show', $user) . '#shop-reviews'
        : route('shops.show', array_merge(request()->query(), ['user' => $user->getRouteKey(), 'show_reviews' => 'all'])) . '#shop-reviews';
?>

<section class="shop-detail-page">
    <div class="container">
        <div class="checkout-breadcrumb">
            <a href="<?php echo e(route('home')); ?>">Home</a>
            <span>&gt;</span>
            <a href="<?php echo e(route('shops.index')); ?>">Shops</a>
            <span>&gt;</span>
            <span><?php echo e($user->sellerProfile?->store_name ?? $user->name); ?></span>
        </div>

        <div class="shop-hero panel">
            <div class="shop-hero-top">
                <div class="shop-hero-brand">
                    <div class="shop-hero-logo">
                        <?php if(!empty($user->sellerProfile?->shop_logo)): ?>
                            <img src="<?php echo e(asset('storage/' . $user->sellerProfile->shop_logo)); ?>" alt="Shop Logo">
                        <?php else: ?>
                            <div class="shop-hero-logo-placeholder">
                                <i class="fa-solid fa-store"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="shop-hero-copy">
                    <div class="shop-hero-copy-top">
                        <div class="shop-kicker-row">
                            <span class="section-kicker">Local Seller</span>
                        </div>
                        <?php if($canReportSeller): ?>
                            <?php echo $__env->make('partials.report-modal', [
                                'modalId' => 'report-seller-modal',
                                'modalContext' => 'seller',
                                'triggerLabel' => 'Report seller',
                                'sellerId' => $user->id,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php elseif(!auth('seller')->check() && !auth('admin')->check()): ?>
                            <a href="<?php echo e(route('login')); ?>" class="report-trigger-button" aria-label="Log in to report seller">
                                <i class="fa-solid fa-flag"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="shop-hero-title-row">
                        <h1><?php echo e($user->sellerProfile?->store_name ?? 'My Shop'); ?></h1>
                        <?php if (isset($component)) { $__componentOriginalfe2859e26b7d777b13c0d03a650c7378 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seller-trust-badge','data' => ['seller' => $user->sellerProfile,'iconOnly' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seller-trust-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['seller' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->sellerProfile),'icon-only' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $attributes = $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $component = $__componentOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
                    </div>

                        <?php if(filled($user->sellerProfile?->store_description)): ?>
                        <p class="shop-description">
                            <?php echo e($user->sellerProfile?->store_description); ?>

                        </p>
                        <?php endif; ?>

                    <div class="shop-meta">
                        <span>
                            <i class="fa-solid fa-phone"></i>
                            <?php echo e($user->sellerProfile?->contact_number ?? 'No contact number'); ?>

                        </span>
                        <span>
                            <i class="fa-solid fa-location-dot"></i>
                            <?php echo e($user->sellerProfile?->address ?? 'No address provided'); ?>

                        </span>
                    </div>

                    <div class="shop-hero-actions">
                        <a href="#shop-products" class="action-btn primary-btn">
                            <i class="fa-solid fa-bag-shopping"></i>&nbsp; Browse Products
                        </a>

                        <?php if(auth()->guard()->check()): ?>
                            <?php if(!$ownsShop): ?>
                                <form action="<?php echo e($isFollowing ? route('shops.unfollow', $user) : route('shops.follow', $user)); ?>" method="POST" data-shop-follow-form data-follow-url="<?php echo e(route('shops.follow', $user)); ?>" data-unfollow-url="<?php echo e(route('shops.unfollow', $user)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php if($isFollowing): ?>
                                        <?php echo method_field('DELETE'); ?>
                                    <?php endif; ?>
                                    <button type="submit" class="action-btn secondary-btn" data-shop-follow-button>
                                        <i class="fa-<?php echo e($isFollowing ? 'solid' : 'regular'); ?> fa-heart" data-shop-follow-icon></i>&nbsp;
                                        <span data-shop-follow-label><?php echo e($isFollowing ? 'Following' : 'Follow Shop'); ?></span>
                                    </button>
                                </form>
                                <form action="<?php echo e(route('messages.start', $user)); ?>" method="POST" data-chat-start-form>
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="action-btn secondary-btn">
                                        <i class="fa-regular fa-message"></i>&nbsp; Message Seller
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="action-btn secondary-btn" aria-disabled="true">This is your shop</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" class="action-btn secondary-btn">
                                <i class="fa-regular fa-heart"></i>&nbsp; Follow Shop
                            </a>
                            <a href="<?php echo e(route('login')); ?>" class="action-btn secondary-btn">
                                <i class="fa-regular fa-message"></i>&nbsp; Message Seller
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="shop-hero-info-grid">
                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>
                    <div>
                        <strong>Member Seller</strong>
                        <span>Part of the LocalLift marketplace community.</span>
                    </div>
                </div>

                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div>
                        <strong><?php echo e($products->count()); ?> Active Products</strong>
                        <span>Browse available items from this shop.</span>
                    </div>
                </div>

                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div>
                        <strong><span data-shop-follower-count><?php echo e($followerCount ?? 0); ?></span> Follower<?php echo e(($followerCount ?? 0) === 1 ? '' : 's'); ?></strong>
                        <span>Buyers following updates from this shop.</span>
                    </div>
                </div>

                <div class="shop-highlight">
                    <div class="shop-highlight-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <strong>Shop trust</strong>
                        <span><?php echo e($user->sellerProfile?->hasVerifiedSellerBadge() ? 'Verified shops show a check beside their name.' : 'Committed to quality products and excellent service.'); ?></span>
                    </div>
                </div>
            </div>

            <?php echo $__env->make('vouchers.partials.buyer-voucher-list', [
                'vouchers' => $sellerVouchers ?? collect(),
                'title' => 'Shop Vouchers',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <div class="shop-detail-layout">
            <aside class="shop-sidebar">
                <div class="panel sidebar-panel">
                    <div class="shop-sidebar-brand">
                        <span class="shop-avatar">
                            <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                        </span>
                        <div>
                            <h2><?php echo e($user->name); ?></h2>
                            <p>
                                LocalLift seller
                                <?php if (isset($component)) { $__componentOriginalfe2859e26b7d777b13c0d03a650c7378 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seller-trust-badge','data' => ['seller' => $user->sellerProfile,'compact' => true,'iconOnly' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seller-trust-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['seller' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->sellerProfile),'compact' => true,'icon-only' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $attributes = $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $component = $__componentOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="shop-sidebar-stats">
                        <div class="stat-chip">
                            <strong><?php echo e($products->count()); ?></strong>
                            <span>Active products</span>
                        </div>
                        <div class="stat-chip">
                            <strong data-shop-follower-count><?php echo e($followerCount ?? 0); ?></strong>
                            <span>Followers</span>
                        </div>
                    </div>
                </div>

                <div class="panel sidebar-panel">
                    <h3>Categories</h3>

                    <div class="mobile-category-dropdown">
                        <select onchange="if(this.value) window.location.href=this.value">
                            <option value="#shop-products" selected>
                                All Products (<?php echo e($products->count()); ?>)
                            </option>

                            <?php $__currentLoopData = $shopCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $categoryProducts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="#category-<?php echo e(\Illuminate\Support\Str::slug($category)); ?>">
                                    <?php echo e($category); ?> (<?php echo e($categoryProducts->count()); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="filter-list">
                        <a href="#shop-products" class="filter-item active">
                            <div class="filter-label"><span class="dot"></span> All Products</div>
                            <span class="count"><?php echo e($products->count()); ?></span>
                        </a>

                        <?php $__currentLoopData = $shopCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $categoryProducts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="#category-<?php echo e(\Illuminate\Support\Str::slug($category)); ?>" class="filter-item">
                                <div class="filter-label">
                                    <span class="dot"></span> <?php echo e($category); ?>

                                </div>
                                <span class="count"><?php echo e($categoryProducts->count()); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </aside>

            <div class="shop-main">
                <div class="panel content-panel" id="shop-reviews">
                    <div class="content-header content-header--split">
                        <div>
                            <h2>Shop Reviews</h2>
                            <p class="shop-section-copy">Recent buyer feedback from this seller’s visible products.</p>
                        </div>

                        <div class="shop-review-summary">
                            <strong><?php echo e($sellerReviewAverage > 0 ? number_format($sellerReviewAverage, 1) : '0.0'); ?></strong>
                            <span><?php echo e($sellerReviewCount); ?> review<?php echo e($sellerReviewCount !== 1 ? 's' : ''); ?></span>
                        </div>
                    </div>

                    <?php if($sellerReviewCount > $initialReviewsLimit): ?>
                        <div class="shop-review-toggle">
                            <a href="<?php echo e($shopReviewsToggleUrl); ?>" class="action-btn secondary-btn">
                                <?php echo e($showAllReviews ? 'Show Fewer Reviews' : 'View All Reviews'); ?>

                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($sellerReviews->isEmpty()): ?>
                        <div class="shop-review-empty">
                            <h3>No reviews yet</h3>
                            <p>This shop has not received buyer feedback yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="shop-review-list">
                            <?php $__currentLoopData = $sellerReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('shops.partials.review-card', ['review' => $review], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="panel content-panel">
                    <div class="content-header" id="shop-products">
                        <div>
                            <h2>Available products</h2>
                        </div>
                    </div>

                    <div class="product-grid product-card-grid">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p>This shop has no products yet.</p>
                        <?php endif; ?>
                    </div>

                    <?php $__currentLoopData = $shopCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $categoryProducts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span id="category-<?php echo e(\Illuminate\Support\Str::slug($category)); ?>"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-shop-follow-form]');

    if (!form) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const button = form.querySelector('[data-shop-follow-button]');
    const icon = form.querySelector('[data-shop-follow-icon]');
    const label = form.querySelector('[data-shop-follow-label]');
    const countTargets = Array.from(document.querySelectorAll('[data-shop-follower-count]'));

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (button) {
            button.disabled = true;
        }

        const isUnfollow = form.querySelector('input[name="_method"]')?.value === 'DELETE';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: new FormData(form),
                credentials: 'same-origin',
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Unable to update shop follow status.');
            }

            form.action = payload.is_following ? form.dataset.unfollowUrl : form.dataset.followUrl;
            form.querySelector('input[name="_method"]')?.remove();

            if (payload.is_following) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
            }

            if (icon) {
                icon.className = 'fa-' + (payload.is_following ? 'solid' : 'regular') + ' fa-heart';
            }

            if (label) {
                label.textContent = payload.is_following ? 'Following' : 'Follow Shop';
            }

            countTargets.forEach((target) => {
                target.textContent = String(payload.follower_count ?? 0);
            });
        } catch (error) {
            window.alert(error.message || 'Unable to update shop follow status.');
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/shops/show.blade.php ENDPATH**/ ?>