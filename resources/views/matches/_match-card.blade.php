@php
    [$blueScore, $redScore] = array_pad(explode('-', $match->score ?? '0-0'), 2, 0);
    $gamemodeName = strtolower($match->gamemode->name ?? '');
@endphp

<div
    class="match-card"
    data-match-id="{{ $match->id }}"
    data-gamemode="{{ $gamemodeName }}"
    data-finished="{{ $match->finished ? '1' : '0' }}"
    data-updated-at="{{ optional($match->updated_at)->toIso8601String() }}"
>
    <div class="match-card-header">
        <div class="match-title">
            Match #{{ $match->id }}
        </div>

        <div class="match-server">
            Server: {{ $match->server->name ?? $match->server->login ?? 'Unknown' }}
        </div>
    </div>

    <div class="match-card-body">
        <div class="team team-blue">
            <div class="team-title">Blue Team</div>
            <div class="team-players">
                @foreach($match->playersA as $player)
                    <div class="player-name">{!! \App\Services\TmNick::toHtml($player->name ?: $player->login) !!}</div>
                @endforeach
            </div>
        </div>

        <div class="match-center">
            <div class="score-line">
                <span class="score-blue js-score-blue">{{ $blueScore }}</span>
                <span class="score-separator">-</span>
                <span class="score-red js-score-red">{{ $redScore }}</span>
            </div>

            <div class="match-statuses">
                <div class="status {{ $match->finished ? 'status-finished' : 'status-live' }} js-status-live">
                    {{ $match->finished ? 'FINISHED' : 'IN PROGRESS' }}
                </div>
            </div>
        </div>

        <div class="team team-red">
            <div class="team-title">Red Team</div>
            <div class="team-players">
                @foreach($match->playersB as $player)
                    <div class="player-name">{!! \App\Services\TmNick::toHtml($player->name ?: $player->login) !!}</div>
                @endforeach
            </div>
        </div>
    </div>
</div>