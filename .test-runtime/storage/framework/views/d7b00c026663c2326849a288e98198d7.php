<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/add_products.css')); ?>">

    <section class="dashboard-wrapper edit-product-page">
        <div class="container">
            <div class="dashboard-layout">
                <?php echo $__env->make('seller.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <main class="dashboard-main">
                    <?php
                        $descriptionValue = old('description');

                        if ($descriptionValue === null) {
                            $descriptionValue = html_entity_decode(
                                trim(
                                    preg_replace(
                                        "/\n{3,}/",
                                        "\n\n",
                                        strip_tags(
                                            str_ireplace(
                                                ['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'],
                                                ["\n", "\n", "\n", "\n", "\n", "\n"],
                                                $product->description ?? ''
                                            )
                                        )
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        }
                    ?>

                    <section class="seller-page-panel panel edit-product-panel">
                        <div class="edit-product-shell">
                            <div class="page-header edit-product-header">
                                <div>
                                    <span class="section-kicker">Catalog</span>
                                    <h2>Edit Product</h2>
                                    <p>Update your product details, pricing, stock, and shipping information.</p>
                                </div>

                                <a href="<?php echo e(route('seller.products.index')); ?>" class="table-action secondary edit-back-btn">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    Back
                                </a>
                            </div>

                            <form class="product-form edit-product-form" action="<?php echo e(route('seller.products.update', $product)); ?>"
                                data-enable-loading
                                method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Basic Information</h3>
                                        <p>Keep the product details clear and buyer-friendly.</p>
                                    </div>

                                    <div class="form-grid edit-main-grid">
                                        <div class="form-group form-group-wide">
                                            <label for="name">Product Name</label>
                                            <input type="text" id="name" name="name"
                                                value="<?php echo e(old('name', $product->name ?? '')); ?>" placeholder="Enter product name">
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="category_id">Category</label>
                                            <select id="category_id" name="category_id">
                                                <option value="">Select category</option>
                                                <?php if(isset($categories)): ?>
                                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($category->id); ?>"
                                                            <?php echo e(old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : ''); ?>>
                                                            <?php echo e($category->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="condition">Condition</label>
                                            <select id="condition" name="condition">
                                                <option value="new" <?php echo e(old('condition', $product->condition ?? '') === 'new' ? 'selected' : ''); ?>>New</option>
                                                <option value="used" <?php echo e(old('condition', $product->condition ?? '') === 'used' ? 'selected' : ''); ?>>Used</option>
                                            </select>
                                            <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group form-group-wide">
                                            <label for="description">Product Description</label>
                                            <textarea id="description" name="description" rows="7"
                                                placeholder="Describe your product"><?php echo e($descriptionValue); ?></textarea>
                                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Pricing and Stock</h3>
                                        <p>Keep pricing accurate and inventory up to date.</p>
                                    </div>

                                    <div class="form-grid edit-two-column-grid">
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="number" id="price" name="price"
                                                value="<?php echo e(old('price', $product->price ?? '')); ?>" placeholder="0.00" step="0.01">
                                            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="stock">Stock</label>
                                            <input type="number" id="stock" name="stock"
                                                value="<?php echo e(old('stock', $product->stock ?? '')); ?>" placeholder="0">
                                            <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Product Image</h3>
                                        <p>Replace the current image only if you want to update the listing preview.</p>
                                    </div>

                                    <div class="edit-image-layout">
                                        <?php if(!empty($product?->image)): ?>
                                            <div class="current-image-preview">
                                                <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>">
                                            </div>
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <label for="image">Change Product Image</label>
                                            <input type="file" id="image" name="image">
                                            <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Shipping Details</h3>
                                        <p>Package measurements are used to calculate the shipping fee.</p>
                                    </div>

                                    <div class="form-grid edit-two-column-grid">
                                        <div class="form-group">
                                            <label for="weight">Weight (kg)</label>
                                            <input type="number" id="weight" name="weight"
                                                value="<?php echo e(old('weight', $product->weight ?? '')); ?>" step="0.01">
                                            <?php $__errorArgs = ['weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="shipping_fee">Shipping Fee</label>
                                            <input type="number" id="shipping_fee" name="shipping_fee"
                                                value="<?php echo e(old('shipping_fee', $product->shipping_fee ?? '')); ?>" step="0.01" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="length_cm">Length (cm)</label>
                                            <input type="number" id="length_cm" name="length_cm"
                                                value="<?php echo e(old('length_cm', $product->length_cm ?? '')); ?>" step="0.01">
                                            <?php $__errorArgs = ['length_cm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="width_cm">Width (cm)</label>
                                            <input type="number" id="width_cm" name="width_cm"
                                                value="<?php echo e(old('width_cm', $product->width_cm ?? '')); ?>" step="0.01">
                                            <?php $__errorArgs = ['width_cm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="height_cm">Height (cm)</label>
                                            <input type="number" id="height_cm" name="height_cm"
                                                value="<?php echo e(old('height_cm', $product->height_cm ?? '')); ?>" step="0.01">
                                            <?php $__errorArgs = ['height_cm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="error-text"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </section>

                                <div class="form-actions edit-form-actions">
                                    <a href="<?php echo e(route('seller.products.index')); ?>" class="table-action secondary">Cancel</a>
                                    <button type="submit" class="page-action-btn" data-enable-loading
                                        data-loading-text="Saving...">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </section>

    <style>
        .edit-product-panel {
            padding: 28px;
        }

        .edit-product-shell {
            width: min(100%, 1000px);
            margin: 0 auto;
            display: grid;
            gap: 24px;
        }

        .edit-product-header {
            align-items: center;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.12);
        }

        .edit-product-header h2 {
            margin-bottom: 10px;
        }

        .edit-product-header p,
        .edit-section-heading p {
            margin: 0;
            color: #8fa7c4;
            line-height: 1.75;
        }

        .edit-back-btn {
            gap: 10px;
        }

        .edit-product-form {
            gap: 22px;
        }

        .edit-section-card {
            display: grid;
            gap: 18px;
            padding: 24px;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.03);
        }

        .edit-section-heading {
            display: grid;
            gap: 8px;
        }

        .edit-section-heading h3 {
            margin: 0;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .edit-main-grid,
        .edit-two-column-grid {
            gap: 18px;
        }

        .edit-product-form .form-group {
            gap: 10px;
        }

        .edit-product-form input,
        .edit-product-form select,
        .edit-product-form textarea {
            min-height: 54px;
            border-radius: 18px;
            background: rgba(10, 19, 34, 0.72);
        }

        .edit-product-form textarea {
            min-height: 180px;
            padding-top: 14px;
        }

        .edit-product-form input[readonly] {
            opacity: 0.9;
            cursor: not-allowed;
        }

        .edit-image-layout {
            display: grid;
            gap: 18px;
        }

        .current-image-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            max-height: 220px;
            padding: 18px;
            border: 1px dashed rgba(66, 165, 245, 0.3);
            border-radius: 22px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02)),
                rgba(4, 11, 22, 0.84);
            overflow: hidden;
        }

        .current-image-preview img {
            width: 100%;
            max-width: 420px;
            max-height: 220px;
            object-fit: contain;
            display: block;
            border-radius: 16px;
        }

        .edit-form-actions {
            justify-content: flex-end;
            padding-top: 4px;
        }

        @media (max-width: 980px) {
            .edit-product-header {
                align-items: flex-start;
            }
        }

        @media (max-width: 720px) {
            .edit-product-panel {
                padding: 20px 18px;
            }

            .edit-product-shell {
                gap: 20px;
            }

            .edit-section-card {
                padding: 18px;
                border-radius: 20px;
            }

            .edit-product-form .form-actions {
                width: 100%;
            }

            .edit-product-form .form-actions .table-action,
            .edit-product-form .form-actions .page-action-btn,
            .edit-back-btn {
                width: 100%;
            }

            .current-image-preview {
                min-height: 190px;
                max-height: 190px;
            }

            .current-image-preview img {
                max-height: 190px;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/edit.blade.php ENDPATH**/ ?>