<?php

namespace App\Http\Controllers;

use App\Events\LiveMatchUpdated;
use App\Models\GameMode;
use App\Models\Ranking;
use App\Models\Statistic;
use Illuminate\Support\Facades\DB;
use App\Models\Map;
use App\Models\Matche;
use App\Models\Server;
use App\Models\Player;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function show(GameMode $gamemode)
    {
        $bestRanks = Ranking::query()
            ->join('players', 'players.id', '=', 'rankings.playerid')
            ->where('rankings.gamemodeid', $gamemode->id)
            ->orderByDesc('rankings.ranking')
            ->limit(10)
            ->get([
                'players.id',
                'players.login',
                'players.name',
                'rankings.ranking',
            ]);

        $bestAttackers = $this->getRatioLeaderboard(
            gamemodeId: $gamemode->id,
            numeratorType: 17,   // Successful Attack
            denominatorType: 16, // Attacks
            alias: 'attack_ratio',
            minDenominator: 5      //au moins 5 attaques
        );

        $bestLaserAccuracy = $this->getRatioLeaderboard(
            gamemodeId: $gamemode->id,
            numeratorType: 7,    // Laser Hits
            denominatorType: 8,  // Laser Shots
            alias: 'laser_ratio',
            minDenominator: 10  //au moins 10 shots
        );

        $bestRocketAccuracy = $this->getRatioLeaderboard(
            gamemodeId: $gamemode->id,
            numeratorType: 11,   // Rocket Hits
            denominatorType: 12, // Rocket Shots
            alias: 'rocket_ratio',
            minDenominator: 50  //au moins 50 shots
        );

        $gamemodes = GameMode::orderBy('name')->get();

        return view('statistics.show', [
            'gamemode' => $gamemode,
            'gamemodes' => $gamemodes,
            'bestRanks' => $bestRanks,
            'bestAttackers' => $bestAttackers,
            'bestLaserAccuracy' => $bestLaserAccuracy,
            'bestRocketAccuracy' => $bestRocketAccuracy,
        ]);
    }

    private function getRatioLeaderboard(
        int $gamemodeId,
        int $numeratorType,
        int $denominatorType,
        string $alias,
        int $minDenominator = 1
    )
    {
        return Statistic::query()
            ->join('players', 'players.id', '=', 'statistics.playerid')
            ->select([
                'players.id',
                'players.login',
                'players.name',
                DB::raw("SUM(CASE WHEN statistics.type = {$numeratorType} THEN statistics.value ELSE 0 END) as numerator"),
                DB::raw("SUM(CASE WHEN statistics.type = {$denominatorType} THEN statistics.value ELSE 0 END) as denominator"),
                DB::raw("
                    CASE
                        WHEN SUM(CASE WHEN statistics.type = {$denominatorType} THEN statistics.value ELSE 0 END) = 0
                        THEN 0
                        ELSE
                            SUM(CASE WHEN statistics.type = {$numeratorType} THEN statistics.value ELSE 0 END)
                            /
                            SUM(CASE WHEN statistics.type = {$denominatorType} THEN statistics.value ELSE 0 END)
                    END as {$alias}
                "),
            ])
            ->where('statistics.gamemodeid', $gamemodeId)
            ->whereIn('statistics.type', [$numeratorType, $denominatorType])
            ->groupBy('players.id', 'players.login', 'players.name')
            ->havingRaw("denominator >= {$minDenominator}")
            ->orderByDesc($alias)
            ->orderByDesc('numerator')
            ->limit(10)
            ->get();
    }

    public function store(Request $request)
    {
        $matchId = $request->input('MatchId');
        $serverLogin = $request->input('Server');
        $stats = $request->input('Stats');
        $score = $request->input('Score');
        $finished = $request->boolean('Finished');


        $match = Matche::findOrFail($matchId);
        $server = Server::where('login', $serverLogin)->firstOrFail();

        $gamemodeId = $match->gamemodeid;
        $mapUid = $match->mapuid;

        if ($finished)
        {
            $tabScore = explode('-', $score);
            if (intval($tabScore[0]) > intval($tabScore[1]))
                $winner = 1;
            else
                $winner = 2;

            $match->update([
                'score' => $score,
                'finished' => 1,
                'winner' => $winner
            ]);
        }
        else
        {
            $match->update([
                'score' => $score,
            ]);
        }


        /*
     * -------------------------
     * MAP UPSERT (ultra rapide)
     * -------------------------
     */
        Map::upsert([
            [
                'uid' => $mapUid,
                'name' => $mapUid,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ], ['uid'], ['updated_at']);


        /*
     * -------------------------
     * COLLECT LOGINS
     * -------------------------
     */
        $logins = [];

        foreach ($stats as $players)
        {
            foreach ($players as $login => $value)
            {
                $logins[] = $login;
            }
        }

        $players = Player::whereIn('login', $logins)
            ->get()
            ->keyBy('login');


        /*
     * -------------------------
     * BUILD BULK INSERT
     * -------------------------
     */
        $now = now();
        $rows = [];

        foreach ($stats as $type => $playersStats)
        {
            foreach ($playersStats as $login => $value)
            {
                if (!isset($players[$login])) continue;

                $rows[] = [
                    'type' => (int)$type,
                    'playerid' => $players[$login]->id,
                    'matchid' => $match->id,
                    'serverid' => $server->id,
                    'gamemodeid' => $gamemodeId,
                    'mapuid' => $mapUid,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        /*
     * -------------------------
     * BULK UPSERT STATS
     * -------------------------
     */
        Statistic::upsert(
            $rows,
            ['matchid', 'playerid', 'type'], // unique key
            ['value', 'updated_at']
        );

        $gamemode = GameMode::find($gamemodeId);

        broadcast(new LiveMatchUpdated(
            $gamemode?->name ?? (string) $gamemodeId,
            [
                'matchId' => $match->id,
                'score' => $match->score,
                'finished' => (bool) $match->finished,
                'winner' => $match->winner,
                'server' => $server->login,
                'mapuid' => $match->mapuid,
            ]
        ));

        return response()->json([
            'status' => 'ok',
            'inserted' => count($rows)
        ]);
    }
}
