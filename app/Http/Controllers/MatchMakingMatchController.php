<?php

namespace App\Http\Controllers;

use App\Models\Matche;
use App\Models\Server;
use App\Models\Statistic;
use Illuminate\Http\Request;

class MatchMakingMatchController extends Controller
{
    public function searchMatch(Request $request)
    {
        $serverlogin = $request->input('serverlogin');
    }

    public function liveMatch(Request $request)
    {
        if (
            !isset($request->serverlogin)
            || !isset($request->status)
            || !isset($request->matchid)
            || !isset($request->penalty)
            || !isset($request->scores)
            || !isset($request->missingplayers)
        )
        {
            return response();
        }

        Server::where('login', $request->serverlogin)->update([
            'latestping' => now(),
        ]);

        if ($request->status == 0 || $request->matchid == -1)
            return response();



        if ($request->status == 4)
            $finished = 1;
        else
            $finished = 0;

        $score = $request->scores[0] . "-" . $request->scores[1];

        $match = Matche::find($request->matchid);
        if($match == null)
            return response(null, 404);

        $match->update([
            'score'       => $score,
            'finished'    => $finished,
        ]);


        if ($finished == 0)
            return response();

        if ($request->scores[0] > $request->scores[1])
            $players = $match->playersA;
        else
            $players = $match->playersB;

        $matchId = $request->matchid;
        $serverId = $request->serverid;

        $rows = collect($players)->map(function ($playerId) use ($matchId, $serverId)
        {
            return [
                'type'      => 1,
                'playerid'  => $playerId,
                'matchid'   => $matchId,
                'serverid'  => $serverId,
                'value'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        Statistic::insert($rows);

        return request();
    }
}
