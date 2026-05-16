(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    const safeParseJson = function (rawValue, fallbackValue) {
        try {
            return JSON.parse(rawValue || '');
        } catch (error) {
            return fallbackValue;
        }
    };

    const normalize = function (value) {
        return String(value || '')
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    };

    const initHelpbots = function () {
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
            const faqs = safeParseJson(configNode ? configNode.textContent : '[]', []);
            const fallbackMessage = safeParseJson(fallbackNode ? fallbackNode.textContent : '""', '');
            const faqIndex = faqs.map(function (faq) {
                return {
                    answer: faq.answer,
                    key: faq.key,
                    question: faq.question,
                    tokens: [faq.key, faq.question].concat(faq.keywords || []).map(normalize),
                };
            });
            let closeTimer = null;

            if (!panel || !form || !input || !messageList) {
                return;
            }

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
                    if (toggleButton) {
                        toggleButton.setAttribute('aria-expanded', 'true');
                    }
                    input.focus();
                });
            };

            const closePanel = function () {
                shell.classList.remove('is-open');

                if (toggleButton) {
                    toggleButton.setAttribute('aria-expanded', 'false');
                }

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

            if (toggleButton) {
                toggleButton.addEventListener('click', function () {
                    if (shell.classList.contains('is-open')) {
                        closePanel();
                        return;
                    }

                    openPanel();
                });
            }

            if (closeButton) {
                closeButton.addEventListener('click', closePanel);
            }

            questionButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    openPanel();
                    answerPrompt(
                        button.dataset.helpbotPrompt || button.dataset.helpbotLabel,
                        button.dataset.helpbotPrompt || button.dataset.helpbotLabel,
                        button.dataset.helpbotQuestion
                    );
                });
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const loadingHelper = window.LocalLiftActionLoading;
                const submitButton = form.querySelector('button[type="submit"]');
                const query = input.value.trim();

                if (!query) {
                    input.focus();
                    return;
                }

                if (loadingHelper && submitButton) {
                    loadingHelper.start(submitButton, { label: 'Sending...' });
                }

                answerPrompt(query, query);
                form.reset();
                input.focus();

                window.setTimeout(function () {
                    if (submitButton && submitButton.isConnected && loadingHelper) {
                        loadingHelper.stop(submitButton);
                    }
                }, 180);
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
    };

    const initAuthHelp = function () {
        document.querySelectorAll('[data-auth-help]').forEach(function (shell) {
            if (shell.dataset.authHelpReady === 'true') {
                return;
            }

            shell.dataset.authHelpReady = 'true';

            const toggle = shell.querySelector('[data-auth-help-toggle]');
            const panel = shell.querySelector('[data-auth-help-panel]');
            const faqCard = shell.querySelector('[data-auth-help-openbot]');
            const helpbotShell = shell.querySelector('[data-helpbot]');
            const helpbotToggle = helpbotShell ? helpbotShell.querySelector('[data-helpbot-toggle]') : null;
            const helpbotInput = helpbotShell ? helpbotShell.querySelector('[data-helpbot-input]') : null;

            const closePopover = function () {
                if (!panel || panel.hidden) {
                    return;
                }

                panel.hidden = true;

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }

                if (helpbotShell && helpbotShell.classList.contains('is-open') && helpbotToggle) {
                    helpbotToggle.click();
                }
            };

            const openPopover = function () {
                if (!panel) {
                    return;
                }

                panel.hidden = false;

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'true');
                }
            };

            if (toggle) {
                toggle.addEventListener('click', function () {
                    if (panel && !panel.hidden) {
                        closePopover();
                        return;
                    }

                    openPopover();
                });
            }

            if (faqCard) {
                faqCard.addEventListener('click', function () {
                    openPopover();

                    if (helpbotToggle && helpbotToggle.getAttribute('aria-expanded') !== 'true') {
                        helpbotToggle.click();
                        return;
                    }

                    if (helpbotInput) {
                        helpbotInput.focus();
                    }
                });
            }

            document.addEventListener('click', function (event) {
                if (!shell.contains(event.target)) {
                    closePopover();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closePopover();
                }
            });
        });
    };

    onReady(function () {
        initHelpbots();
        initAuthHelp();
    });
}());
