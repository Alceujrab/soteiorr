<script>
window.DrawCeremonyUI = (function () {
    function reelElements(root) {
        return Array.from(root.querySelectorAll('.draw-reel'));
    }

    function stopReel(reel, digit, animate) {
        const strip = reel.querySelector('[data-strip]');
        const windowEl = reel.querySelector('.draw-reel__window');
        if (!strip || !windowEl) return;

        const alphabet = Math.max(1, Number(reel.dataset.alphabet || 10));
        const safeDigit = ((Number(digit) % alphabet) + alphabet) % alphabet;

        reel.classList.remove('is-spinning');
        const height = windowEl.clientHeight || 1;
        // Land in the second cycle so the strip travels visibly before stopping.
        const targetIndex = alphabet + safeDigit;
        const y = -(targetIndex * height);

        if (!animate) {
            strip.style.transition = 'none';
            strip.style.transform = `translateY(${y}px)`;
            void strip.offsetHeight;
            strip.style.transition = '';
        } else {
            strip.style.transition = '';
            strip.style.transform = `translateY(${y}px)`;
        }

        reel.classList.add('is-locked');
        reel.dataset.lockedDigit = String(safeDigit);
    }

    function spinReel(reel) {
        const strip = reel.querySelector('[data-strip]');
        reel.classList.remove('is-locked');
        delete reel.dataset.lockedDigit;
        if (strip) {
            strip.style.transition = 'none';
            strip.style.transform = 'translateY(0)';
            void strip.offsetHeight;
        }
        reel.classList.add('is-spinning');
    }

    function spinUnrevealed(root, revealedCount = 0) {
        reelElements(root).forEach((reel, index) => {
            if (index >= Number(revealedCount || 0) && !reel.classList.contains('is-locked')) {
                spinReel(reel);
            }
        });
    }

    function applyState(root, state, options = {}) {
        const animate = options.animate !== false;
        const reels = reelElements(root);
        const targets = state.target_digits || [];
        const revealed = Number(state.revealed_digits || 0);

        if (state.auto_running) {
            spinUnrevealed(root, revealed);
        }

        reels.forEach((reel, index) => {
            const digit = targets[index];
            const already = reel.dataset.lockedDigit;

            if (index < revealed && digit !== null && digit !== undefined) {
                if (already === String(digit)) {
                    return;
                }

                if (animate) {
                    if (reel.classList.contains('is-spinning')) {
                        stopReel(reel, digit, true);
                    } else {
                        spinReel(reel);
                        setTimeout(() => stopReel(reel, digit, true), 220);
                    }
                } else {
                    stopReel(reel, digit, false);
                }
            } else if (state.auto_running && index >= revealed) {
                if (!reel.classList.contains('is-spinning') && !reel.classList.contains('is-locked')) {
                    spinReel(reel);
                }
            } else if (state.status === 'live' && index === revealed && options.previewNext) {
                spinReel(reel);
            }
        });
    }

    function reset(root) {
        reelElements(root).forEach((reel) => {
            const strip = reel.querySelector('[data-strip]');
            reel.classList.remove('is-spinning', 'is-locked');
            delete reel.dataset.lockedDigit;
            if (strip) {
                strip.style.transition = 'none';
                strip.style.transform = 'translateY(0)';
                void strip.offsetHeight;
                strip.style.transition = '';
            }
        });
    }

    function sleep(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }

    async function runAutoReveal(options) {
        const {
            startUrl,
            revealUrl,
            csrfToken,
            reelsRoot,
            onState,
            onError,
            intervalMs = 5000,
            digitLength = 6,
            initialRevealed = 0,
        } = options;

        if (startUrl) {
            const startRes = await fetch(startUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const startData = await startRes.json().catch(() => ({}));
            if (!startRes.ok) {
                throw new Error(startData.message || startData.errors?.draw?.[0] || 'Não foi possível iniciar o sorteio automático.');
            }
            onState?.(startData);
        }

        spinUnrevealed(reelsRoot, initialRevealed);

        let state = null;
        const remaining = Math.max(0, digitLength - Number(initialRevealed || 0));

        for (let i = 0; i < remaining; i++) {
            await sleep(intervalMs);
            const res = await fetch(revealUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            state = await res.json().catch(() => ({}));
            if (!res.ok) {
                const message = state.message || state.errors?.draw?.[0] || 'Falha ao revelar dígito.';
                onError?.(message);
                throw new Error(message);
            }
            onState?.(state);
            if (state.status === 'completed') {
                break;
            }
        }

        return state;
    }

    return { applyState, reset, stopReel, spinReel, spinUnrevealed, runAutoReveal, sleep };
})();
</script>
