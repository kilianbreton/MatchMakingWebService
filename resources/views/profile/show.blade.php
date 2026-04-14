@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="page-header">
        <h1>Profile</h1>
    </div>

    <div class="profile-wrapper">
        <div class="profile-card">
            <h2>Player</h2>

            <div class="profile-row">
                <span class="profile-label">Login</span>
                <span class="profile-value">{{ $player->login }}</span>
            </div>

            @php
                $nickname = App\Services\TmNick::toHtml($player->name ?: $player->login);
            @endphp
            <div class="profile-row">
                <div class="profile-label">Nickname</div>
                <div class="profile-value">{!! $nickname !!}</div>
            </div>
        </div>

        <div class="profile-card">
            <h2>Servers</h2>

            @if($error)
                <div class="profile-error">{{ $error }}</div>
            @endif

            @if(empty($servers))
                <div class="empty-state">
                    No dedicated servers found.
                </div>
            @else
                <div class="table-wrapper">
                    <table class="dark-table">
                        <thead>
                            <tr>
                                <th>Server Login</th>
                                <th class="actions-col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servers as $server)
                                <tr>
                                    <td>{{ $server['login'] ?? 'Unknown' }}</td>
                                    <td class="actions-col">
                                        <a href="{{ route('servers.configure', ['login' => $server['login']]) }}" class="table-button">
                                            Configure
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection