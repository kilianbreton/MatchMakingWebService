<?php

namespace App\Services;

use App\Events\MatchStarted;
use App\Events\QueueUpdated;
use App\Models\GameMode;
use App\Models\Map;
use App\Models\Matche;
use App\Models\MatchPlayer;
use App\Models\Player;
use App\Models\Server;
use Illuminate\Support\Facades\Redis;

class QueueService
{

    public function __construct(private PlayerService $playerService)
    {

    }


    private function normalizeQueue(string $queue): string
    {
        return strtolower(trim($queue));
    }

    private function orderKey(string $queue): string
    {
        return 'queue:' . $this->normalizeQueue($queue) . ':order';
    }

    private function playersKey(string $queue): string
    {
        return 'queue:' . $this->normalizeQueue($queue) . ':players';
    }

    public function addPlayer(string $queue, string $login, string $nickname = '')
    {
        $queue = $this->normalizeQueue($queue);
        $login = trim($login);
        $nickname = trim($nickname);

        if ($login === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing login',
                'queue' => $queue,
                'player' => $login
            ], 400);
        }

        $gamemode = GameMode::whereRaw('LOWER(name) = ?', [$queue])->first();

        if (!$gamemode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unknown queue',
                'queue' => $queue,
                'player' => $login
            ], 404);
        }

        $this->playerService->ensurePlayerExists($login, $nickname);
        

        $allQueues = GameMode::pluck('name');

        foreach ($allQueues as $otherQueue) {
            $otherQueue = $this->normalizeQueue($otherQueue);

            if ($otherQueue === $queue) {
                continue;
            }

            $removed = Redis::lrem($this->orderKey($otherQueue), 0, $login);
            Redis::hdel($this->playersKey($otherQueue), $login);

            if ($removed > 0) {
                $this->broadcastQueue($otherQueue);
            }
        }

        $currentPlayers = $this->getPlayers($queue);
        foreach ($currentPlayers as $player) {
            if ($player['login'] === $login) {
                Redis::hset(
                    $this->playersKey($queue),
                    $login,
                    $nickname !== '' ? $nickname : ($player['nickname'] ?: $login)
                );

                $this->broadcastQueue($queue);

                return response()->json([
                    'status' => 'already_in_queue',
                    'queue' => $queue,
                    'player' => $login
                ]);
            }
        }

        Redis::rpush($this->orderKey($queue), $login);
        Redis::hset(
            $this->playersKey($queue),
            $login,
            $nickname !== '' ? $nickname : $login
        );

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
        $queue = $this->normalizeQueue($queue);
        $login = trim($login);

        $removed = Redis::lrem($this->orderKey($queue), 0, $login);
        Redis::hdel($this->playersKey($queue), $login);

        if ($removed > 0) {
            $this->broadcastQueue($queue);
        }

        return response()->json([
            'status' => 'ok',
            'queue' => $queue,
            'player' => $login
        ]);
    }

    public function getPlayers(string $queue): array
    {
        $queue = $this->normalizeQueue($queue);

        $logins = Redis::lrange($this->orderKey($queue), 0, -1);

        if (empty($logins)) {
            return [];
        }

        $nicknames = Redis::hmget($this->playersKey($queue), $logins);

        $result = [];

        foreach ($logins as $index => $login) {
            $result[] = [
                'login' => $login,
                'nickname' => $nicknames[$index] ?? $login,
            ];
        }

        return $result;
    }

    private function broadcastQueue(string $queue): void
    {
        $queue = $this->normalizeQueue($queue);

        broadcast(new QueueUpdated(
            $queue,
            $this->getPlayers($queue)
        ));
    }

    private function tryMatch(string $queue): void
    {
        $queue = $this->normalizeQueue($queue);

        $gamemode = GameMode::whereRaw('LOWER(name) = ?', [$queue])->first();
        if (!$gamemode) {
            return;
        }

        $players = $this->getPlayers($queue);
        $requiredPlayers = $this->countRequiredPlayers($gamemode->name);

        if (count($players) < $requiredPlayers) {
            return;
        }

        $selectedPlayers = array_slice($players, 0, $requiredPlayers);
        $selectedLogins = array_column($selectedPlayers, 'login');

        $thresholdTime = now()->subMinutes(10000);

        $availableServer = Server::where('gamemode', $gamemode->id)
            ->where('latestping', '>=', $thresholdTime)
            ->whereDoesntHave('matches', function ($query) {
                $query->where('finished', 0);
            })
            ->first();

        if (!$availableServer) {
            return;
        }

        $playerModels = Player::whereIn('login', $selectedLogins)->get()->keyBy('login');

        if ($playerModels->count() !== count($selectedLogins)) {
            return;
        }

        $playersForBalance = [];
        foreach ($selectedLogins as $login) {
            $playersForBalance[] = $playerModels[$login];
        }

        $teamA = [];
        $teamB = [];

        if (!$this->balanceMatch($playersForBalance, $requiredPlayers, $teamA, $teamB)) {
            return;
        }

        foreach ($selectedLogins as $login) {
            Redis::lrem($this->orderKey($queue), 0, $login);
            Redis::hdel($this->playersKey($queue), $login);
        }

        $match = Matche::create([
            'serverid' => $availableServer->id,
            'gamemodeid' => $gamemode->id,
            'score' => '0-0',
            'mapuid' => 'Undefined',
            'winner' => null,
            'finished' => 0,
        ]);

        foreach ($teamA as $idx => $player) {
            MatchPlayer::create([
                'matchid' => $match->id,
                'playerid' => $player->id,
                'team' => 1,
                'playorder' => $idx,
                'missing' => 0,
                'replaced' => 0,
            ]);
        }

        foreach ($teamB as $idx => $player) {
            MatchPlayer::create([
                'matchid' => $match->id,
                'playerid' => $player->id,
                'team' => 2,
                'playorder' => $idx,
                'missing' => 0,
                'replaced' => 0,
            ]);
        }

        broadcast(new MatchStarted([
            'queue' => $queue,
            'matchId' => $match->id,
            'players' => $selectedPlayers,
        ]));

        $this->broadcastQueue($queue);
    }

    private function countRequiredPlayers(string $gamemodeName): int
    {
        return match (strtolower($gamemodeName)) {
            'elite' => 6,
            'siege' => 4,
            default => 2,
        };
    }

    private function balanceMatch(array $players, int $requiredPlayers, array &$teamA, array &$teamB): bool
    {
        if ($requiredPlayers % 2 !== 0) {
            return false;
        }

        if (count($players) !== $requiredPlayers) {
            return false;
        }

        $bestDiff = PHP_INT_MAX;
        $bestTeamA = [];
        $bestTeamB = [];

        foreach ($this->combinations($players, (int) ($requiredPlayers / 2)) as $candidateTeamA) {
            $candidateTeamB = array_udiff(
                $players,
                $candidateTeamA,
                fn($a, $b) => $a->id <=> $b->id
            );

            $sumA = array_sum(array_map(fn($p) => (int) ($p->ranking ?? 0), $candidateTeamA));
            $sumB = array_sum(array_map(fn($p) => (int) ($p->ranking ?? 0), $candidateTeamB));

            $diff = abs($sumA - $sumB);

            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $bestTeamA = $candidateTeamA;
                $bestTeamB = $candidateTeamB;
            }
        }

        if (empty($bestTeamA) || empty($bestTeamB)) {
            return false;
        }

        $teamA = array_values($bestTeamA);
        $teamB = array_values($bestTeamB);

        return true;
    }

    private function combinations(array $items, int $k): array
    {
        if ($k === 0) {
            return [[]];
        }

        if (count($items) < $k) {
            return [];
        }

        $result = [];
        $items = array_values($items);

        $first = array_shift($items);

        foreach ($this->combinations($items, $k - 1) as $combo) {
            $result[] = array_merge([$first], $combo);
        }

        foreach ($this->combinations($items, $k) as $combo) {
            $result[] = $combo;
        }

        return $result;
    }
}