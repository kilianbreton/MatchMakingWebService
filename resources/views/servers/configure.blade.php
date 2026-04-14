@extends('layouts.app')

@section('title', 'Configure Server')

@section('content')
    <div class="page-header">
        <h1>Configure Server</h1>
    </div>

    <div class="profile-wrapper">
        <div class="profile-card">
            <h2>{{ $serverLogin }}</h2>

            @if(session('generated_api_key'))
                <div class="api-key-box">
                    <div class="api-key-title">API Key</div>
                    <div class="api-key-help">
                        Copy this key now. It will not be shown again.
                    </div>
                    <div class="api-key-value">{{ session('generated_api_key') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('servers.configure.update', ['login' => $serverLogin]) }}" class="server-form">
                @csrf

                <div class="form-group">
                    <label for="name">Display name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $server->name ?? $serverLogin) }}"
                        class="dark-input"
                    >
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gamemode">Game mode</label>
                    <select id="gamemode" name="gamemode" class="dark-input" required>
                        <option value="">Select a game mode</option>
                        @foreach($gamemodes as $gamemode)
                            <option
                                value="{{ $gamemode->id }}"
                                @selected(old('gamemode', $server->gamemode ?? null) == $gamemode->id)
                            >
                                {{ $gamemode->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('gamemode')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">Server type</label>
                    <select id="type" name="type" class="dark-input" required>
                        <option value="1" @selected((string) old('type', $server->type ?? '1') === '1')>Lobby</option>
                        <option value="2" @selected((string) old('type', $server->type ?? '') === '2')>Match</option>
                    </select>
                    @error('type')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('profile') }}" class="secondary-button">Back</a>
                    <button type="submit" class="table-button">Save configuration</button>
                </div>
            </form>

            @if($server)
                <div class="server-extra-actions">
                    <form method="POST" action="{{ route('servers.regenerate-key', ['login' => $serverLogin]) }}">
                        @csrf
                        <button type="submit" class="danger-button">
                            Regenerate API Key
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection