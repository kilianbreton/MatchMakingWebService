@extends('layouts.admin')

@php
    use App\Services\TmNick;
@endphp

@section('title', 'Admin - Players')

@section('content')
    <div class="page-header">
        <h1>Players</h1>
    </div>

    <div class="profile-card">
        <form method="GET" action="{{ route('admin.players') }}" class="admin-search-form">
            <input
                type="text"
                name="q"
                value="{{ $search }}"
                class="dark-input"
                placeholder="Search by login or nickname"
            >
            <button type="submit" class="table-button">Search</button>
        </form>
    </div>

    <div class="profile-card">
        <div class="table-wrapper">
            <table class="dark-table">
                <thead>
                    <tr>
                        <th>Login</th>
                        <th>Nickname</th>
                        <th>Ban</th>
                        <th>Mute</th>
                        <th class="align-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                        <tr>
                            <td>{{ $player->login }}</td>
                            <td>{!! TmNick::toHtml($player->name ?: $player->login) !!}</td>
                            <td>
                                @if($player->ban_active)
                                    <span class="admin-badge admin-badge-danger">
                                        {{ $player->banned_until ? 'Until ' . $player->banned_until->format('Y-m-d H:i') : 'Permanent' }}
                                    </span>
                                @else
                                    <span class="admin-badge admin-badge-success">No</span>
                                @endif
                            </td>
                            <td>
                                @if($player->mute_active)
                                    <span class="admin-badge admin-badge-warning">
                                        {{ $player->muted_until ? 'Until ' . $player->muted_until->format('Y-m-d H:i') : 'Permanent' }}
                                    </span>
                                @else
                                    <span class="admin-badge admin-badge-success">No</span>
                                @endif
                            </td>
                            <td class="align-right">
                                <div class="admin-actions">
                                    @if(!$player->ban_active)
                                        <form method="POST" action="{{ route('admin.players.ban', $player) }}">
                                            @csrf
                                            <input type="hidden" name="duration" value="60">
                                            <button type="submit" class="danger-button">Ban 1h</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.players.ban', $player) }}">
                                            @csrf
                                            <input type="hidden" name="permanent" value="1">
                                            <button type="submit" class="danger-button">Permaban</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.players.unban', $player) }}">
                                            @csrf
                                            <button type="submit" class="secondary-button">Unban</button>
                                        </form>
                                    @endif

                                    @if(!$player->mute_active)
                                        <form method="POST" action="{{ route('admin.players.mute', $player) }}">
                                            @csrf
                                            <input type="hidden" name="duration" value="60">
                                            <button type="submit" class="warning-button">Mute 1h</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.players.mute', $player) }}">
                                            @csrf
                                            <input type="hidden" name="permanent" value="1">
                                            <button type="submit" class="warning-button">Permamute</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.players.unmute', $player) }}">
                                            @csrf
                                            <button type="submit" class="secondary-button">Unmute</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No players found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $players->links() }}
        </div>
    </div>
@endsection