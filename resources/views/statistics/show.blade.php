@extends('layouts.app')

@php
    use App\Services\TmNick;
@endphp

@section('title', 'Statistics - ' . $gamemode->name)

@section('content')
    <div class="page-header">
        <h1>Statistics - {{ $gamemode->name }}</h1>
    </div>

    <div class="stats-grid">
        <div class="profile-card">
            <h2>Top 10 Rank</h2>

            @if($bestRanks->isEmpty())
                <div class="queue-empty">No data available.</div>
            @else
                <div class="table-wrapper">
                    <table class="dark-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Player</th>
                                <th>Login</th>
                                <th class="align-right">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bestRanks as $index => $player)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{!! TmNick::toHtml($player->name ?: $player->login) !!}</td>
                                    <td>{{ $player->login }}</td>
                                    <td class="align-right">{{ $player->ranking }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="profile-card">
            <h2>Top 10 Attackers</h2>

            @if($bestAttackers->isEmpty())
                <div class="queue-empty">No data available.</div>
            @else
                <div class="table-wrapper">
                    <table class="dark-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Player</th>
                                <th>Login</th>
                                <th class="align-right">Successful</th>
                                <th class="align-right">Attacks</th>
                                <th class="align-right">Ratio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bestAttackers as $index => $player)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{!! TmNick::toHtml($player->name ?: $player->login) !!}</td>
                                    <td>{{ $player->login }}</td>
                                    <td class="">{{ (int) $player->numerator }}</td>
                                    <td class="">{{ (int) $player->denominator }}</td>
                                    <td class="">{{ number_format($player->attack_ratio * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="profile-card">
            <h2>Top 10 Laser Accuracy</h2>

            @if($bestLaserAccuracy->isEmpty())
                <div class="queue-empty">No data available.</div>
            @else
                <div class="table-wrapper">
                    <table class="dark-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Player</th>
                                <th>Login</th>
                                <th class="align-right">Hits</th>
                                <th class="align-right">Shots</th>
                                <th class="align-right">Accuracy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bestLaserAccuracy as $index => $player)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{!! TmNick::toHtml($player->name ?: $player->login) !!}</td>
                                    <td>{{ $player->login }}</td>
                                    <td class="">{{ (int) $player->numerator }}</td>
                                    <td class="">{{ (int) $player->denominator }}</td>
                                    <td class="">{{ number_format($player->laser_ratio * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="profile-card">
            <h2>Top 10 Rocket Accuracy</h2>

            @if($bestRocketAccuracy->isEmpty())
                <div class="queue-empty">No data available.</div>
            @else
                <div class="table-wrapper">
                    <table class="dark-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Player</th>
                                <th>Login</th>
                                <th class="align-right">Hits</th>
                                <th class="align-right">Shots</th>
                                <th class="align-right">Accuracy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bestRocketAccuracy as $index => $player)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{!! TmNick::toHtml($player->name ?: $player->login) !!}</td>
                                    <td>{{ $player->login }}</td>
                                    <td class="">{{ (int) $player->numerator }}</td>
                                    <td class="">{{ (int) $player->denominator }}</td>
                                    <td class="">{{ number_format($player->rocket_ratio * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection