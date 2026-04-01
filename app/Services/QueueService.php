<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Events\QueueUpdated;
use App\Events\MatchStarted;
use App\Models\GameMode;
use App\Models\Match;
use App\Models\Matche;
use App\Models\Player;
use App\Models\Server;

class QueueService
{



    private function key(string $queue): string
    {
        return "queue:$queue";
    }

    public function addPlayer(string $queue, $login)
    {
        // Vérifie si le gamemode/queue existe
        if (!GameMode::where('name', $queue)->exists())
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Unknown queue',
                'queue' => $queue,
                'player' => $login
            ]);
        }

        // Supprime le joueur de toutes les autres queues existantes
        $allQueues = Gamemode::pluck('name'); // récupère tous les noms de gamemodes
        foreach ($allQueues as $otherQueue)
        {
            $otherQueue = strtolower($otherQueue);
            if ($otherQueue === $queue) continue; // ignore la queue cible
            Redis::lrem("queue:$otherQueue", 0, $login);
        }
        // Récupère la liste actuelle depuis Redis
        $players = $this->getPlayers($queue);
        if (in_array($login, $players))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Already in queue',
                'queue' => $queue,
                'player' => $login
            ]);
        }

        // Ajoute le joueur dans Redis
        Redis::rpush("queue:$queue", $login);

 

        $this->broadcastQueue($queue);
        $this->tryMatch($queue);

        return response()->json([
            'status' => 'ok',
            'queue' => $queue,
            'player' => $login
        ]);
    }

    public function removePlayer(string $queue, string $login)
    {
        Redis::lrem($this->key($queue), 0, $login);

        $this->broadcastQueue($queue);
        return response()->json([
            'status' => 'ok',
            'queue' => $queue,
            'player' => $login
        ]);
    }

    public function getPlayers(string $queue): array
    {
        return Redis::lrange($this->key($queue), 0, -1);
    }

    private function broadcastQueue(string $queue)
    {
        broadcast(new QueueUpdated(
            $queue,
            $this->getPlayers($queue)
        ));
    }

    private function tryMatch(string $queue)
    {
        // Vérifie que la queue existe
        $gamemode = Gamemode::where('name', $queue)->first();
        if (!$gamemode) return;
    
        // Récupère les joueurs en queue
        $playersLogins = $this->getPlayers($queue);
        if (empty($playersLogins)) return;
    
        // Détermine le nombre de joueurs requis pour ce gamemode
        $requiredPlayers = 2; //TODO: get from gamemode
        if (count($playersLogins) < $requiredPlayers) return;
    
        // Sélectionne les joueurs nécessaires et les retire de Redis
        $selectedLogins = array_slice($playersLogins, 0, $requiredPlayers);
        foreach ($selectedLogins as $login) {
            Redis::lrem("queue:$queue", 0, $login);
        }
    
        // Upsert players dans MySQL
        $users = collect($selectedLogins)->map(fn($login) => [
            'login' => $login,
            'updated_at' => now(),
            'created_at' => now()
        ])->toArray();
        Player::upsert($users, ['login'], ['updated_at']);
    
        // Récupère les modèles Player
        $players = Player::whereIn('login', $selectedLogins)->get();
    
        // Récupère les serveurs disponibles
        $thresholdTime = now()->subDays(98498484984);
        $availableServers = Server::where('latestping', '>=', $thresholdTime)
            ->whereDoesntHave('matches', fn($q) => $q->where('finished', 0))
            ->get();
        if ($availableServers->isEmpty()) {
            // Remet les joueurs en queue si pas de serveur
            foreach ($selectedLogins as $login) {
                Redis::rpush("queue:$queue", $login);
            }
            return;
        }
    
        // Balance match par rank
        $teamA = [];
        $teamB = [];
        if (!$this->balanceMatch($players->getDictionary(), $requiredPlayers, $teamA, $teamB)) {
            // Remet les joueurs en queue si on ne peut pas équilibrer
            foreach ($selectedLogins as $login) {
                Redis::rpush("queue:$queue", $login);
            }
            return;
        }
    
        // Crée le match
        $server = $availableServers->first();
        $match = Matche::create([
            'serverid'   => $server->id,
            'gamemodeid' => $gamemode->id,
            'score'      => '0-0',
            'mapuid'     => 'Undefined',
            'finished'   => 0,
        ]);
    
        // Assigne les joueurs aux équipes
        foreach ($teamA as $idx => $player) {
            $match->players()->create([
                'playerid' => $player->id,
                'team' => 1,
                'playorder' => $idx,
            ]);
        }
        foreach ($teamB as $idx => $player) {
            $match->players()->create([
                'playerid' => $player->id,
                'team' => 2,
                'playorder' => $idx,
            ]);
        }
    
        // Broadcast MatchStarted
        broadcast(new MatchStarted([
            'queue' => $queue,
            'players' => $selectedLogins,
            'matchId' => $match->id
        ]));
    
        // Broadcast QueueUpdated
        $this->broadcastQueue($queue);
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
