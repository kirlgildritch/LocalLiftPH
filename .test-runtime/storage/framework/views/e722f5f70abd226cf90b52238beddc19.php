<?php
    $helpbotName = config('helpbot.name', 'LocalLift HelpBot');
    $helpbotIntro = config('helpbot.intro', '');
    $helpbotEmptyState = config('helpbot.empty_state', '');
    $helpbotFallback = config('helpbot.fallback', '');
    $helpbotFaqs = config('helpbot.faqs', []);
    $helpbotQuickTopics = config('helpbot.quick_questions', []);
    $helpbotMode = $helpbotMode ?? 'floating';
    $isInlineHelpbot = $helpbotMode === 'auth-inline';
    $helpbotInstance = 'helpbot-' . uniqid();
    $helpbotPanelId = $helpbotInstance . '-panel';
    $helpbotInputId = $helpbotInstance . '-query';

    $helpbotPayload = [];
    $helpbotScript = asset('assets/js/helpbot.js') . '?v=' . @filemtime(public_path('assets/js/helpbot.js'));

    foreach ($helpbotFaqs as $key => $faq) {
        $helpbotPayload[] = [
            'key' => $key,
            'question' => $faq['question'] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $key)),
            'answer' => $faq['answer'] ?? '',
            'keywords' => array_values($faq['keywords'] ?? []),
        ];
    }

    $helpbotQuickButtons = [];

    foreach ($helpbotQuickTopics as $topicKey) {
        if (!isset($helpbotFaqs[$topicKey])) {
            continue;
        }

        $defaultLabel = match ($topicKey) {
            'message_seller' => 'Message Seller',
            'order_tracking' => 'Track Order',
            'seller_registration' => 'Seller Signup',
            default => \Illuminate\Support\Str::headline(str_replace('_', ' ', $topicKey)),
        };

        $helpbotQuickButtons[] = [
            'key' => $topicKey,
            'label' => $defaultLabel,
            'prompt' => $helpbotFaqs[$topicKey]['question'] ?? $defaultLabel,
        ];
    }
?>

<div class="helpbot-shell<?php echo e($isInlineHelpbot ? ' helpbot-shell--inline' : ''); ?>" data-helpbot
    data-helpbot-name="<?php echo e($helpbotName); ?>">
    <button type="button" class="helpbot-fab<?php echo e($isInlineHelpbot ? ' helpbot-fab--hidden' : ''); ?>" data-helpbot-toggle
        aria-controls="<?php echo e($helpbotPanelId); ?>" aria-expanded="false"
        aria-label="Open <?php echo e($helpbotName); ?>">
        <span>FAQ</span>
    </button>

    <section id="<?php echo e($helpbotPanelId); ?>" class="helpbot-panel<?php echo e($isInlineHelpbot ? ' helpbot-panel--inline' : ''); ?>"
        data-helpbot-panel hidden>
        <header class="helpbot-header">
            <div class="helpbot-header-copy">
                <span class="helpbot-kicker">FAQ</span>
                <h3><?php echo e($helpbotName); ?></h3>
                <?php if($helpbotIntro): ?>
                    <p><?php echo e($helpbotIntro); ?></p>
                <?php endif; ?>
            </div>

            <button type="button" class="helpbot-close" data-helpbot-close aria-label="Close help bot">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <div class="helpbot-body">
            <div class="helpbot-messages" data-helpbot-messages>
                <article class="helpbot-message is-bot">
                    <div class="helpbot-bubble">
                        <strong>Hey!</strong>
                        <p><?php echo e($helpbotEmptyState); ?></p>
                    </div>
                </article>
            </div>

            <div class="helpbot-quick-actions">
                <?php $__currentLoopData = $helpbotQuickButtons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quickButton): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="helpbot-chip" data-helpbot-question="<?php echo e($quickButton['key']); ?>"
                        data-helpbot-label="<?php echo e($quickButton['label']); ?>" data-helpbot-prompt="<?php echo e($quickButton['prompt']); ?>">
                        <?php echo e($quickButton['label']); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <form class="helpbot-form" data-helpbot-form>
                <label class="sr-only" for="<?php echo e($helpbotInputId); ?>">Ask <?php echo e($helpbotName); ?></label>
                <input id="<?php echo e($helpbotInputId); ?>" type="text" name="query" data-helpbot-input
                    placeholder="Ask a quick question" autocomplete="off" maxlength="120">
                <button type="submit" class="helpbot-submit">Send</button>
            </form>


        </div>

        <script type="application/json" data-helpbot-config><?php echo json_encode($helpbotPayload, 15, 512) ?></script>
        <script type="application/json" data-helpbot-fallback><?php echo json_encode($helpbotFallback, 15, 512) ?></script>
    </section>
</div>

<?php if (! $__env->hasRenderedOnce('cb16e428-3f22-40be-a73e-8ccb3c05a40a')): $__env->markAsRenderedOnce('cb16e428-3f22-40be-a73e-8ccb3c05a40a'); ?>
    <script src="<?php echo e($helpbotScript); ?>" defer></script>
<?php endif; ?>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/partials/helpbot.blade.php ENDPATH**/ ?>