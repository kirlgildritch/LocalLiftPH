@php
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
@endphp

<div class="helpbot-shell{{ $isInlineHelpbot ? ' helpbot-shell--inline' : '' }}" data-helpbot
    data-helpbot-name="{{ $helpbotName }}">
    <button type="button" class="helpbot-fab{{ $isInlineHelpbot ? ' helpbot-fab--hidden' : '' }}" data-helpbot-toggle
        aria-controls="{{ $helpbotPanelId }}" aria-expanded="false"
        aria-label="Open {{ $helpbotName }}">
        <span>FAQ</span>
    </button>

    <section id="{{ $helpbotPanelId }}" class="helpbot-panel{{ $isInlineHelpbot ? ' helpbot-panel--inline' : '' }}"
        data-helpbot-panel hidden>
        <header class="helpbot-header">
            <div class="helpbot-header-copy">
                <span class="helpbot-kicker">FAQ</span>
                <h3>{{ $helpbotName }}</h3>
                @if($helpbotIntro)
                    <p>{{ $helpbotIntro }}</p>
                @endif
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
                        <p>{{ $helpbotEmptyState }}</p>
                    </div>
                </article>
            </div>

            <div class="helpbot-quick-actions">
                @foreach($helpbotQuickButtons as $quickButton)
                    <button type="button" class="helpbot-chip" data-helpbot-question="{{ $quickButton['key'] }}"
                        data-helpbot-label="{{ $quickButton['label'] }}" data-helpbot-prompt="{{ $quickButton['prompt'] }}">
                        {{ $quickButton['label'] }}
                    </button>
                @endforeach
            </div>

            <form class="helpbot-form" data-helpbot-form>
                <label class="sr-only" for="{{ $helpbotInputId }}">Ask {{ $helpbotName }}</label>
                <input id="{{ $helpbotInputId }}" type="text" name="query" data-helpbot-input
                    placeholder="Ask a quick question" autocomplete="off" maxlength="120">
                <button type="submit" class="helpbot-submit">Send</button>
            </form>


        </div>

        <script type="application/json" data-helpbot-config>@json($helpbotPayload)</script>
        <script type="application/json" data-helpbot-fallback>@json($helpbotFallback)</script>
    </section>
</div>

@once
    <script src="{{ $helpbotScript }}" defer></script>
@endonce
