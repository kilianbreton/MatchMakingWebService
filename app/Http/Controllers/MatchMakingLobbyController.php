<?php

namespace App\Http\Controllers;

use App\Models\Matche;
use App\Models\Player;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class MatchMakingLobbyController extends Controller
{
    public function matchmaking_live(Request $request)
    {
        if (
            !isset($request->lobby)
            || !isset($request->matchservers)
            || !isset($request->gamemode)
            || !isset($request->titleid)
            || !isset($request->format)
            || !isset($request->players)
            || !isset($request->waitinglogins)
            || !isset($request->cancelers)
            || !isset($request->penalties)
        )
        {
            return response();
        }


        // Update lobby id for match servers
        Server::whereIn('login', $request->matchservers)
            ->update(['lobbyid' => Server::where('login', $request->lobby)->value('id')]);


        //Get available servers (onlines with no match)
        // Calculer le timestamp d'il y a 10 minutes
        $thresholdTime = now()->subMinutes(10);

        // Récupérer les serveurs avec les critères donnés
        $availableServers = Server::where('latestping', '>=', $thresholdTime)
            ->whereDoesntHave('matches', function ($query)
            {
                $query->where('finished', 0);
            })
            ->get();


        $lobbyLogin = $request->lobby;
        $currentMatches = Matche::where('finished', 0)
            ->whereHas('server', function ($query) use ($lobbyLogin)
            {
                $query->whereHas('lobby', function ($query) use ($lobbyLogin)
                {
                    $query->where('login', $lobbyLogin);
                });
            })
            ->get();

        $requiredPlayers = $this->countRequiredPlayers($request->format);
        if ($requiredPlayers <= count($request->players) && count($availableServers) > 0) //Création d'un match
        {
            $players = array_slice($request->players, 0, $requiredPlayers);
            $playersLogins = array_map(function ($player)
            {
                return $player["login"];
            }, $players);


            $users = collect($playersLogins)->map(function ($item)
            {
                return [
                    'login'      => $item,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            })->toArray();

            //Bulk upsert
            Player::upsert(
                $users,
                ['login'],        // clé unique
                ['updated_at'] // champs à mettre à jour
            );
            
            $players = Player::whereIn('login', $playersLogins)->get();
        
            //Balance match
            $teamA = [];
            $teamB = [];
            if (!$this->balanceMatch($players->getDictionary(), $requiredPlayers, $teamA, $teamB))
            {
                return response(null, 401);
            }

            $match = Matche::create([
                'serverid'   => $availableServers[0]->id,
                'gamemodeid' => 1,
                'score'      => '0-0',
                'mapuid'     => "Undefined",
                'finished'   => 0,
            ]);

            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><response></response>');
            $status = $xml->addChild('status');
            $status->addAttribute('availableservers', count($availableServers));
            $status->addAttribute('playersnb', count($playersLogins));
            $xml->addChild("matches");
            $xml->addChild("masters");
            $xml->addChild("substitutes");
            $xml->addChild("penalties");

            $match = $xml->addChild("match");
            $match->addAttribute("id", $match->id);

            $xmlServer = $xml->addChild("server");
            $xmlServer->addAttribute("lobby", $request->lobby);
            $xmlServer->addAttribute("match", $availableServers[0]->login);
            $xmlFormat = $xml->addChild("format");
            $xmlFormat->addChild("clan")->addAttribute("number", count($teamA));
            $xmlFormat->addChild("clan")->addAttribute("number", count($teamB));

            $xmlPlayers = $xml->addChild("players");

            $cpt = 0;
            foreach ($teamA as $player)
            {
                $xmlPlayer = $xmlPlayers->addChild("player");
                $xmlPlayer->addAttribute("login", $player->login);
                $xmlPlayer->addAttribute("clan", 0);
                $xmlPlayer->addAttribute("order", $cpt++);
            }
            $cpt = 0;
            foreach ($teamB as $player)
            {
                $xmlPlayer = $xmlPlayers->addChild("player");
                $xmlPlayer->addAttribute("login", $player->login);
                $xmlPlayer->addAttribute("clan", 1);
                $xmlPlayer->addAttribute("order", $cpt++);
            }

            return response($xml->asXML(), 200)
                    ->header('Content-Type', 'text/xml');


        }
    }







    private function balanceMatch($players, $requiredPlayers, &$teamA, &$teamB)
    {
      
        $bestDiff = PHP_INT_MAX;
        $bestTeamA = [];
        $bestTeamB = [];

        foreach ($this->combinations($players, ($requiredPlayers / 2)) as $teamA)
        {

            // Équipe B = joueurs restants
            $teamB = array_udiff(
                $players,
                $teamA,
                fn($a, $b) => $a->id <=> $b->id
            );

            // Sommes des ranks
            $sumA = array_sum(array_map(fn($p) => $p->rank, $teamA));
            $sumB = array_sum(array_map(fn($p) => $p->rank, $teamB));

            $diff = abs($sumA - $sumB);

            if ($diff < $bestDiff)
            {
                $bestDiff = $diff;
                $bestTeamA = $teamA;
                $bestTeamB = $teamB;
            }
        }

        $teamA = $bestTeamA;
        $teamB = $bestTeamB;

        return true;
    }

    private function combinations($items, int $k): array
    {
        if ($k === 0)
        {
            return [[]];
        }

        if (count($items) < $k)
        {
            return [];
        }

        $result = [];

        $first = array_shift($items);

        // Inclure le premier élément
        foreach ($this->combinations($items, $k - 1) as $combo)
        {
            $result[] = array_merge([$first], $combo);
        }

        // Exclure le premier élément
        foreach ($this->combinations($items, $k) as $combo)
        {
            $result[] = $combo;
        }

        return $result;
    }
    private function countRequiredPlayers($format)
    {
        $count = intval($format[0]) + intval($format[1]);
        return $count;
    }
}
