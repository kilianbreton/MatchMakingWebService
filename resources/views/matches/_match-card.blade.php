@php
    $scores = explode('-', $match->score);
    use App\Services\TmNick;
@endphp
<div class="match-card">
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
                    <div class="player-name">{!! TmNick::toHtml($player->name ?: $player->login) !!}</div>
                @endforeach
            </div>
        </div>


        <div class="match-center">
            <div class="score-line">
                <span class="score-blue">{{ $scores[0] ?? 0 }}</span>
                <span class="score-separator">-</span>
                <span class="score-red">{{ $scores[1] ?? 0 }}</span>
            </div>

            <div class="match-statuses">
                
                @if($match->finished)
                    <div class="status status-finished">FINISHED</div>
                @else
                  <div class="status status-live">IN PROGRESS</div>
                @endif
            </div>
        </div>

        <div class="team team-red">
            <div class="team-title">Red Team</div>
            <div class="team-players">
                @foreach($match->playersB as $player)
                    <div class="player-name">{!! TmNick::toHtml($player->name ?: $player->login) !!}</div>
                @endforeach
            </div>
        </div>
    </div>
</div>