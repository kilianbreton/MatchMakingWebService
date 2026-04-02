@extends('layouts.app')

@section('title', 'Matchs en cours')

@section('content')
    <div class="page-header">
        <h1>Matchs en cours</h1>
    </div>

    <div class="matches-list">
        @forelse($matches as $match)
            @include('matches._match-card', ['match' => $match])
        @empty
            <div class="empty-state">
                Aucun match en cours.
            </div>
        @endforelse
    </div>
@endsection