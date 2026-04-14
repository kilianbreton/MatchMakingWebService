<?php

namespace App\Http\Controllers;

use App\Models\GameMode;
use Illuminate\Support\Facades\Redis;

class QueuePageController extends Controller
{
    public function index()
    {
        $gamemodes = GameMode::orderBy('name')->get();

        $queues = $gamemodes->map(function ($gamemode) {
            $queueName = strtolower($gamemode->name);

            $orderKey = "queue:{$queueName}:order";
            $playersKey = "queue:{$queueName}:players";

            $logins = Redis::lrange($orderKey, 0, -1);

            $players = [];

            if (!empty($logins)) {
                $nicknames = Redis::hmget($playersKey, $logins);

                foreach ($logins as $index => $login) {
                    $players[] = [
                        'login' => $login,
                        'nickname' => $nicknames[$index] ?? $login,
                    ];
                }
            }

            return [
                'name' => $gamemode->name,
                'titlepack' => $gamemode->titlepack,
                'players' => $players,
                'count' => count($players),
            ];
        });

        return view('queues.index', [
            'queues' => $queues,
        ]);
    }
}