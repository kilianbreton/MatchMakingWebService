import TmNick from '../utils/TmNick';

document.addEventListener('DOMContentLoaded', () => {

    const cards = document.querySelectorAll('[data-queue]');
    if (cards.length === 0 || !window.Echo) return;

    function renderPlayers(players) {
        if (!players || players.length === 0) {
            return `<div class="queue-empty">No players in queue.</div>`;
        }

        return players.map(player => `
            <div class="queue-player">
                <div class="queue-player-name">
                    ${TmNick.toHtml(player.nickname || player.login)}
                </div>
                <div class="queue-player-login">
                    ${escapeHtml(player.login)}
                </div>
            </div>
        `).join('');
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    cards.forEach(card => {
        const queueName = card.dataset.queue;

        window.Echo.channel(`queue.${queueName}`)
            .listen('.queue.updated', (event) => {

                const playersEl = card.querySelector('.queue-players');
                const countEl = card.querySelector('.queue-count');

                const players = event.players || [];

                countEl.textContent = players.length;
                playersEl.innerHTML = renderPlayers(players);
            });
    });
});