<?php

namespace App\Http\Controllers;

use App\Models\Map;
use App\Models\Matche;
use App\Models\Server;
use App\Models\Statistic;
use App\Models\Player;
use Illuminate\Http\Request;

class StatsController extends Controller
{
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

        return response()->json([
            'status' => 'ok',
            'inserted' => count($rows)
        ]);
    }
}
