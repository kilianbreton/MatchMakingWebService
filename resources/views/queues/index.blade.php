@extends('layouts.app')

@section('title', 'Queues')

@section('content')
    <div class="page-header">
        <h1>Queues</h1>
    </div>

    <div class="queues-grid" id="queuesGrid">
        @forelse($queues as $queue)
            <div class="queue-card" data-queue="{{ strtolower($queue['name']) }}">
                <div class="queue-card-header">
                    <div>
                        <h2>{{ $queue['name'] }}</h2>
                        @if(!empty($queue['titlepack']))
                            <div class="queue-subtitle">{{ $queue['titlepack'] }}</div>
                        @endif
                    </div>

                    <div class="queue-count">{{ $queue['count'] }}</div>
                </div>

                <div class="queue-card-body">
                    <div class="queue-players">
                        @forelse($queue['players'] as $player)
                            <div class="queue-player">
                                <div class="queue-player-name">{{ $player['nickname'] ?: $player['login'] }}</div>
                                <div class="queue-player-login">{{ $player['login'] }}</div>
                            </div>
                        @empty
                            <div class="queue-empty">No players in queue.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">No queues available.</div>
        @endforelse
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const queues = @json($queues->pluck('name')->map(fn ($name) => strtolower($name))->values());
        
            function renderPlayers(players) {
                if (!players || players.length === 0) {
                    return `<div class="queue-empty">No players in queue.</div>`;
                }
        
                return players.map(player => `
                    <div class="queue-player">
                        <div class="queue-player-name">${escapeHtml(player.nickname || player.login)}</div>
                        <div class="queue-player-login">${escapeHtml(player.login)}</div>
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
        
            queues.forEach(queueName => {
                window.Echo.channel(`queue.${queueName}`)
                    .listen('.queue.updated', (event) => {
                        const card = document.querySelector(`[data-queue="${queueName}"]`);
                        if (!card) return;
        
                        const countEl = card.querySelector('.queue-count');
                        const playersEl = card.querySelector('.queue-players');
        
                        const players = event.players || [];
        
                        countEl.textContent = players.length;
                        playersEl.innerHTML = renderPlayers(players);
                    });
            });
        });
        </script>
@endsection