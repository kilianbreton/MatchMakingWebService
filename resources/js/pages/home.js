document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('[data-match-id][data-gamemode]');

    if (cards.length === 0 || !window.Echo) {
        return;
    }

    const FINISHED_VISIBLE_DURATION_MS = 5 * 60 * 1000;
    const removalTimers = new Map();
    const subscribedGamemodes = new Set();

    cards.forEach((card) => {
        scheduleRemovalIfNeeded(card);

        const gamemode = card.dataset.gamemode;

        if (!gamemode || subscribedGamemodes.has(gamemode)) {
            return;
        }

        subscribedGamemodes.add(gamemode);

        window.Echo.channel(`livematch.${gamemode}`)
            .listen('.livematch.updated', (event) => {
                const match = event.match || {};
                const matchId = String(match.matchId ?? '');

                if (!matchId) {
                    return;
                }

                const targetCard = document.querySelector(`[data-match-id="${matchId}"]`);
                if (!targetCard) {
                    return;
                }

                updateMatchCard(targetCard, match);
            });
    });

    function updateMatchCard(card, match) {
        const score = String(match.score || '0-0');
        const [blueScore = '0', redScore = '0'] = score.split('-');

        const blueEl = card.querySelector('.js-score-blue');
        const redEl = card.querySelector('.js-score-red');
        const statusEl = card.querySelector('.js-status-live');

        if (blueEl) {
            blueEl.textContent = blueScore;
        }

        if (redEl) {
            redEl.textContent = redScore;
        }

        if (statusEl) {
            statusEl.textContent = match.finished ? 'FINISHED' : 'IN PROGRESS';
            statusEl.classList.toggle('status-finished', !!match.finished);
            statusEl.classList.toggle('status-live', !match.finished);
        }

        card.dataset.finished = match.finished ? '1' : '0';
        card.dataset.updatedAt = match.updated_at || new Date().toISOString();

        scheduleRemovalIfNeeded(card);
    }

    function scheduleRemovalIfNeeded(card) {
        const matchId = card.dataset.matchId;
        clearRemovalTimer(matchId);

        const isFinished = card.dataset.finished === '1';
        if (!isFinished) {
            return;
        }

        const updatedAt = card.dataset.updatedAt;
        if (!updatedAt) {
            return;
        }

        const updatedTime = new Date(updatedAt).getTime();
        if (Number.isNaN(updatedTime)) {
            return;
        }

        const removeAt = updatedTime + FINISHED_VISIBLE_DURATION_MS;
        const delay = removeAt - Date.now();

        if (delay <= 0) {
            card.remove();
            return;
        }

        const timer = setTimeout(() => {
            card.remove();
            removalTimers.delete(matchId);
        }, delay);

        removalTimers.set(matchId, timer);
    }

    function clearRemovalTimer(matchId) {
        if (!removalTimers.has(matchId)) {
            return;
        }

        clearTimeout(removalTimers.get(matchId));
        removalTimers.delete(matchId);
    }
});