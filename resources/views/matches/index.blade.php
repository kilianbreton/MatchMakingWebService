@extends('layouts.app')

@section('title', 'Matchs en cours')

@section('content')
    <div class="page-header">
        <h1>Running matches</h1>
    </div>

    <div class="matches-list">
        @forelse($matches as $match)
            @include('matches._match-card', ['match' => $match])
        @empty
            <div class="empty-state">
                No running matches
            </div>
        @endforelse
    </div>
@endsection