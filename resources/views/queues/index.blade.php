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
                                @php
                                    $nickname = App\Services\TmNick::toHtml($player['nickname'] ?? $player['login']);
                                @endphp
                                <div class="queue-player-name">{!! $nickname !!}</div>
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

    
@endsection