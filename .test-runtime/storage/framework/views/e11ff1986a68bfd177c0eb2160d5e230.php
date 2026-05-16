<?php $__env->startPush('head'); ?>
  <?php
    $homeCssVersion = @filemtime(public_path('assets/css/home.css'));
  ?>
  <link rel="preload" as="image" href="<?php echo e(asset('assets/image/hero-carousel/hero-slide-1.webp')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/css/home.css')); ?>?v=<?php echo e($homeCssVersion); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="hero-background-carousel" aria-hidden="true">
          <span class="hero-slide"></span>
          <span class="hero-slide"></span>
          <span class="hero-slide"></span>
        </div>

        <div class="hero-overlay"></div>

        <div class="hero-content">
          <div class="hero-pill">
            <span class="status-dot"></span>
            Independent marketplace for local products and trusted sellers
          </div>

          <h1>
            <span class="hero-accent">LocalLift</span>
            for buyers discovering
            standout local products
          </h1>

          <p>
            Explore curated categories, reliable shops, and product collections designed to help local businesses look
            more premium online.
          </p>

          <div class="hero-actions">
            <a href="<?php echo e(url('/products')); ?>" class="btn btn-primary">
              <i class="fa-solid fa-bag-shopping"></i>
              Explore Products
            </a>

            <?php if(auth()->guard()->check()): ?>
              <a href="<?php echo e(route('seller.center')); ?>" class="btn btn-outline">
                <i class="fa-solid fa-store"></i>
                Become a Seller
              </a>
            <?php else: ?>
              <a href="<?php echo e(route('seller.center')); ?>" class="btn btn-outline">
                <i class="fa-solid fa-store"></i>
                Become a Seller
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php if(($recentlyViewedProducts ?? collect())->isNotEmpty()): ?>
    <section class="section">
      <div class="container">
        <div class="section-header">
          <div>
            <span class="section-kicker">Recently Viewed</span>

          </div>
        </div>

        <div class="products product-card-grid" data-skeleton-group data-skeleton-delay="420">
          <?php $__currentLoopData = $recentlyViewedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product,'fallbackImage' => asset('assets/image/heroBanner.png')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'fallback-image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(asset('assets/image/heroBanner.png'))]); ?>
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
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <div>
          <span class="section-kicker">Browse</span>
          <h2 class="section-title">Featured Categories</h2>
        </div>
        <a href="<?php echo e(route('categories.index')); ?>" class="view-all">View all categories <i
            class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="categories">

        <?php $__empty_1 = true; $__currentLoopData = $featuredCategories ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <a href="<?php echo e(route('products.index', ['category' => $category->slug])); ?>" class="category-card">
            <div class="cat-icon"><i class="fa-solid <?php echo e($category->icon); ?>"></i></div>
            <h4><?php echo e($category->name); ?></h4>
            <p><?php echo e($category->count); ?> products available in this category.</p>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <a href="<?php echo e(route('categories.index')); ?>" class="category-card">
            <div class="cat-icon"><i class="fa-solid fa-grid-2"></i></div>
            <h4>Browse Categories</h4>
            <p>Explore available categories once sellers publish active products.</p>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <div>
          <span class="section-kicker">Products</span>
          <h2 class="section-title">Featured Products</h2>
        </div>
        <div class="featured-products-header-actions">
          <div class="featured-products-nav" aria-label="Featured products navigation">
            <button type="button" class="featured-products-arrow" data-featured-products-prev
              aria-label="Scroll featured products left">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="featured-products-arrow" data-featured-products-next
              aria-label="Scroll featured products right">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
          <a href="<?php echo e(url('/products')); ?>" class="view-all">View all products <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>

      <div class="featured-products-shell">
        <div class="products-carousel" data-featured-products-track>
          <div class="products product-card-grid">
            <?php $__empty_1 = true; $__currentLoopData = $featuredProducts ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product,'fallbackImage' => asset('assets/image/heroBanner.png'),'cardClass' => 'featured-product-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'fallback-image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(asset('assets/image/heroBanner.png')),'card-class' => 'featured-product-card']); ?>
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
              <div class="market-product-card market-product-card--empty featured-product-card">
                <div class="market-product-card__body">
                  <span class="market-product-card__badge">No products yet</span>
                  <h4 class="market-product-card__title">Featured products will appear here</h4>
                  <p class="market-product-card__subtitle">Active seller listings will automatically populate this section.
                  </p>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const track = document.querySelector('[data-featured-products-track]');
      const prevButton = document.querySelector('[data-featured-products-prev]');
      const nextButton = document.querySelector('[data-featured-products-next]');

      if (!track || !prevButton || !nextButton) {
        return;
      }

      const getScrollAmount = () => {
        const firstCard = track.querySelector('.featured-product-card');
        const list = track.querySelector('.products');
        if (!firstCard) {
          return Math.max(track.clientWidth * 0.85, 240);
        }

        const cardWidth = firstCard.getBoundingClientRect().width;
        const listStyles = list ? window.getComputedStyle(list) : null;
        const gap = listStyles ? parseFloat(listStyles.columnGap || listStyles.gap) || 16 : 16;
        return Math.round(cardWidth + gap);
      };

      const updateButtons = () => {
        const maxScrollLeft = track.scrollWidth - track.clientWidth;
        const atStart = track.scrollLeft <= 4;
        const atEnd = track.scrollLeft >= maxScrollLeft - 4;

        prevButton.disabled = atStart;
        nextButton.disabled = atEnd;
      };

      prevButton.addEventListener('click', function () {
        track.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
      });

      nextButton.addEventListener('click', function () {
        track.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
      });

      track.addEventListener('scroll', updateButtons, { passive: true });
      window.addEventListener('resize', updateButtons);
      updateButtons();
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/home.blade.php ENDPATH**/ ?>