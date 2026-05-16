(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    onReady(function () {
        const toast = document.getElementById('seller-toast');

        if (toast) {
            window.setTimeout(function () {
                toast.classList.add('toast-hide');

                window.setTimeout(function () {
                    toast.remove();
                }, 400);
            }, 3000);
        }

        const AudioContextClass = window.AudioContext || window.webkitAudioContext;

        if (!AudioContextClass) {
            return;
        }

        let audioContext = null;
        let audioUnlocked = false;
        const interactionEvents = ['pointerdown', 'keydown', 'touchstart'];

        const removeInteractionListeners = function () {
            interactionEvents.forEach(function (eventName) {
                document.removeEventListener(eventName, unlockAudioContext);
            });
        };

        const ensureAudioContext = async function () {
            try {
                if (!audioContext) {
                    audioContext = new AudioContextClass();
                }

                if (audioContext.state === 'suspended') {
                    await audioContext.resume();
                }

                return audioContext.state === 'running';
            } catch (error) {
                return false;
            }
        };

        const unlockAudioContext = async function () {
            audioUnlocked = await ensureAudioContext();

            if (audioUnlocked) {
                removeInteractionListeners();
            }
        };

        const playNotificationChime = async function () {
            if (!audioUnlocked) {
                return;
            }

            const isReady = await ensureAudioContext();

            if (!isReady || !audioContext) {
                return;
            }

            const now = audioContext.currentTime;
            const masterGain = audioContext.createGain();
            masterGain.connect(audioContext.destination);
            masterGain.gain.setValueAtTime(0.0001, now);
            masterGain.gain.exponentialRampToValueAtTime(0.045, now + 0.02);
            masterGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.62);

            const firstTone = audioContext.createOscillator();
            firstTone.type = 'sine';
            firstTone.frequency.setValueAtTime(880, now);
            firstTone.frequency.exponentialRampToValueAtTime(1046.5, now + 0.18);
            firstTone.connect(masterGain);
            firstTone.start(now);
            firstTone.stop(now + 0.22);

            const secondTone = audioContext.createOscillator();
            secondTone.type = 'sine';
            secondTone.frequency.setValueAtTime(1174.7, now + 0.16);
            secondTone.frequency.exponentialRampToValueAtTime(1318.5, now + 0.34);
            secondTone.connect(masterGain);
            secondTone.start(now + 0.16);
            secondTone.stop(now + 0.4);

            window.setTimeout(function () {
                masterGain.disconnect();
            }, 900);
        };

        interactionEvents.forEach(function (eventName) {
            document.addEventListener(eventName, unlockAudioContext, { passive: true });
        });

        document.addEventListener('seller:notification-received', function (event) {
            if (event.detail?.read_at) {
                return;
            }

            void playNotificationChime();
        });
    });
}());
