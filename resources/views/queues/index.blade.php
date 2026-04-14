@extends('layouts.app')

@section('title', 'Queues')

@section('content')
    <div class="page-header">
        <h1>Queues</h1>
    </div>

    <div class="queues-grid">
        @forelse($queues as $queue)
            <div class="queue-card">
                <div class="queue-card-header">
                    <div>
                        <h2>{{ $queue['name'] }}</h2>
                        @if(!empty($queue['titlepack']))
                            <div class="queue-subtitle">{{ $queue['titlepack'] }}</div>
                        @endif
                    </div>

                    <div class="queue-count">
                        {{ $queue['count'] }}
                    </div>
                </div>

                <div class="queue-card-body">
                    @if($queue['count'] > 0)
                        <div class="queue-players">
                            @foreach($queue['players'] as $player)
                                <div class="queue-player">
                                    <div class="queue-player-name">
                                        @php
                                            $nickname = $player['nickname'] ?: $player['login'];
                                            $nickname = App\Services\TmNick::toHtml($nickname);
                                        @endphp
                                        {!! $nickname !!}
                                    </div>
                                    <div class="queue-player-login">
                                        {{ $player['login'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="queue-empty">
                            No players in queue.
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                No queues available.
            </div>
        @endforelse
    </div>
@endsection