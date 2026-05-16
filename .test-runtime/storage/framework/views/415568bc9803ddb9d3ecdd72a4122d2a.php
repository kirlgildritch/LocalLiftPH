<?php $__env->startSection('title', 'LocalLift PH - Login'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/auth.css')); ?>">

    <style>
        .google-auth-btn {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.12);
            background: #ffffff;
            color: #1f2937;
            font-weight: 700;
            text-decoration: none;
            transition: 0.2s ease;
            margin-bottom: 16px;
        }

        .google-auth-btn:hover {
            background: #f9fafb;
            color: #111827;
            transform: translateY(-1px);
        }

        .google-auth-btn i {
            font-size: 18px;
            color: #ea4335;
        }

        .auth-alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
        }
    </style>

    <section class="auth-page">
        <div class="auth-topbar">
            <div class="container auth-topbar-inner">
                <a href="<?php echo e(route('home')); ?>" class="auth-brand">
                    <img src="<?php echo e(asset('assets/image/Logo.png')); ?>" alt="LocalLift Logo">
                    <div>
                        <strong>LocalLift</strong>
                        <span>PH</span>
                    </div>
                </a>

                <div class="auth-help-shell" data-auth-help>
                    <button type="button" class="auth-help auth-help-toggle" data-auth-help-toggle aria-expanded="false"
                        aria-controls="auth-help-panel">
                        Need help?
                    </button>

                    <div id="auth-help-panel" class="auth-help-popover panel" data-auth-help-panel hidden>
                        <button type="button" class="auth-faq-card" data-auth-help-openbot>
                            <span class="auth-faq-card-kicker">FAQ</span>
                            <strong>Open HelpBot</strong>
                            <small>Login, orders, seller signup, and more.</small>
                        </button>

                        <?php echo $__env->make('partials.helpbot', ['helpbotMode' => 'auth-inline'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-hero">
            <div class="container auth-shell">
                <div class="auth-promo">
                    <span class="promo-badge">Local marketplace</span>
                    <h1>Shop local products with ease.</h1>
                    <p>Discover trusted sellers, support local businesses, and enjoy a cleaner marketplace experience.</p>

                    <div class="promo-highlights">
                        <span><i class="fa-solid fa-store"></i> Local sellers</span>
                        <span><i class="fa-solid fa-shield-heart"></i> Trusted shops</span>
                        <span><i class="fa-solid fa-bag-shopping"></i> Easy shopping</span>
                    </div>
                </div>

                <div class="auth-card panel">
                    <div class="auth-card-header">
                        <h2>Log In</h2>
                    </div>

                    <?php if(session('error')): ?>
                        <div class="auth-alert">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('login')); ?>" class="auth-form" data-enable-loading>
                        <?php echo csrf_field(); ?>

                        <div class="input-group">
                            <div class="input-wrap">
                                <i class="fa-solid fa-envelope"></i>
                                <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>"
                                    placeholder="Email Address" required autofocus>
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="error-text"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="input-group">
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input id="password" type="password" name="password" placeholder="Password" required>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="error-text"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <button type="submit" class="auth-btn" data-enable-loading data-loading-text="Signing In...">Log
                            In</button>

                        <div class="forgot">
                            <?php if(Route::has('password.request')): ?>
                                <a href="<?php echo e(route('password.request')); ?>">Forgot password?</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="auth-divider">
                        <span></span>
                        <small>OR</small>
                        <span></span>
                    </div>

                    <a href="<?php echo e(route('google.login')); ?>" class="google-auth-btn">
                        <i class="fa-brands fa-google"></i>
                        Continue with Google
                    </a>

                    <div class="auth-footer">
                        New to LocalLift?
                        <a href="<?php echo e(route('register')); ?>">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.log', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/auth/login.blade.php ENDPATH**/ ?>