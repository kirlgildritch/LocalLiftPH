@php
    $helpbotName = config('helpbot.name', 'LocalLift HelpBot');
    $helpbotIntro = config('helpbot.intro', '');
    $helpbotEmptyState = config('helpbot.empty_state', '');
    $helpbotFallback = config('helpbot.fallback', '');
    $helpbotFaqs = config('helpbot.faqs', []);
    $helpbotQuickTopics = config('helpbot.quick_questions', []);

    $helpbotPayload = [];

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

<div class="helpbot-shell" data-helpbot data-helpbot-name="{{ $helpbotName }}">
    <button type="button" class="helpbot-fab" data-helpbot-toggle aria-controls="helpbot-panel" aria-expanded="false"
        aria-label="Open {{ $helpbotName }}">
        <span>FAQ</span>
    </button>

    <section id="helpbot-panel" class="helpbot-panel" data-helpbot-panel hidden>
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
                <label class="sr-only" for="helpbot-query">Ask {{ $helpbotName }}</label>
                <input id="helpbot-query" type="text" name="query" data-helpbot-input
                    placeholder="Ask a quick question" autocomplete="off" maxlength="120">
                <button type="submit" class="helpbot-submit">Send</button>
            </form>


        </div>

        <script type="application/json" data-helpbot-config>@json($helpbotPayload)</script>
        <script type="application/json" data-helpbot-fallback>@json($helpbotFallback)</script>
    </section>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-helpbot]').forEach(function (shell) {
                if (shell.dataset.helpbotReady === 'true') {
                    return;
                }

                shell.dataset.helpbotReady = 'true';

                const panel = shell.querySelector('[data-helpbot-panel]');
                const toggleButton = shell.querySelector('[data-helpbot-toggle]');
                const closeButton = shell.querySelector('[data-helpbot-close]');
                const form = shell.querySelector('[data-helpbot-form]');
                const input = shell.querySelector('[data-helpbot-input]');
                const messageList = shell.querySelector('[data-helpbot-messages]');
                const questionButtons = shell.querySelectorAll('[data-helpbot-question]');
                const configNode = shell.querySelector('[data-helpbot-config]');
                const fallbackNode = shell.querySelector('[data-helpbot-fallback]');
                const botName = shell.dataset.helpbotName || 'LocalLift HelpBot';
                const faqs = JSON.parse(configNode ? configNode.textContent : '[]');
                const fallbackMessage = JSON.parse(fallbackNode ? fallbackNode.textContent : '""');
                let closeTimer = null;

                const normalize = function (value) {
                    return String(value || '')
                        .toLowerCase()
                        .replace(/[^a-z0-9\s]/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                };

                const faqIndex = faqs.map(function (faq) {
                    return {
                        key: faq.key,
                        question: faq.question,
                        answer: faq.answer,
                        tokens: [faq.key, faq.question].concat(faq.keywords || []).map(normalize),
                    };
                });

                const appendMessage = function (type, text, title) {
                    const item = document.createElement('article');
                    const bubble = document.createElement('div');

                    item.className = 'helpbot-message ' + (type === 'user' ? 'is-user' : 'is-bot');
                    bubble.className = 'helpbot-bubble';

                    if (title) {
                        const strong = document.createElement('strong');
                        strong.textContent = title;
                        bubble.appendChild(strong);
                    }

                    const paragraph = document.createElement('p');
                    paragraph.textContent = text;
                    bubble.appendChild(paragraph);
                    item.appendChild(bubble);
                    messageList.appendChild(item);
                    messageList.scrollTop = messageList.scrollHeight;
                };

                const findMatch = function (query, preferredKey) {
                    if (preferredKey) {
                        return faqIndex.find(function (faq) {
                            return faq.key === preferredKey;
                        }) || null;
                    }

                    const normalizedQuery = normalize(query);

                    if (!normalizedQuery) {
                        return null;
                    }

                    let bestMatch = null;
                    let bestScore = 0;

                    faqIndex.forEach(function (faq) {
                        faq.tokens.forEach(function (token) {
                            if (!token) {
                                return;
                            }

                            let score = 0;

                            if (normalizedQuery === token) {
                                score = 120 + token.length;
                            } else if (normalizedQuery.includes(token)) {
                                score = 80 + token.length;
                            } else if (token.includes(normalizedQuery)) {
                                score = 48 + normalizedQuery.length;
                            }

                            if (score > bestScore) {
                                bestScore = score;
                                bestMatch = faq;
                            }
                        });
                    });

                    return bestMatch;
                };

                const openPanel = function () {
                    window.clearTimeout(closeTimer);
                    panel.hidden = false;
                    requestAnimationFrame(function () {
                        shell.classList.add('is-open');
                        toggleButton.setAttribute('aria-expanded', 'true');
                        input.focus();
                    });
                };

                const closePanel = function () {
                    shell.classList.remove('is-open');
                    toggleButton.setAttribute('aria-expanded', 'false');
                    window.clearTimeout(closeTimer);
                    closeTimer = window.setTimeout(function () {
                        panel.hidden = true;
                    }, 220);
                };

                const answerPrompt = function (label, query, preferredKey) {
                    const trimmedQuery = String(query || '').trim();

                    if (!trimmedQuery) {
                        return;
                    }

                    appendMessage('user', label || trimmedQuery);

                    const match = findMatch(trimmedQuery, preferredKey);
                    appendMessage('bot', match ? match.answer : fallbackMessage, botName);
                };

                toggleButton.addEventListener('click', function () {
                    if (shell.classList.contains('is-open')) {
                        closePanel();
                        return;
                    }

                    openPanel();
                });

                closeButton.addEventListener('click', closePanel);

                questionButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        openPanel();
                        answerPrompt(button.dataset.helpbotPrompt || button.dataset.helpbotLabel, button.dataset.helpbotPrompt || button.dataset.helpbotLabel, button.dataset.helpbotQuestion);
                    });
                });

                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const query = input.value.trim();

                    if (!query) {
                        input.focus();
                        return;
                    }

                    answerPrompt(query, query);
                    form.reset();
                    input.focus();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && shell.classList.contains('is-open')) {
                        closePanel();
                    }
                });

                document.addEventListener('click', function (event) {
                    if (!shell.classList.contains('is-open')) {
                        return;
                    }

                    if (!shell.contains(event.target)) {
                        closePanel();
                    }
                });
            });
        });
    </script>
@endonce
