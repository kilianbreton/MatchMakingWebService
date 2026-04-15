<?php

namespace App\Services;

use App\Models\GameMode;
use App\Models\Player;
use App\Models\Ranking;

class PlayerService
{
    public function ensurePlayerExists(string $login, string $nickname = ''): Player
    {
        $player = Player::firstOrCreate(
            ['login' => $login],
            ['name' => $nickname !== '' ? $nickname : $login]
        );

        if ($nickname !== '' && $player->name !== $nickname) {
            $player->name = $nickname;
            $player->save();
        }

        $this->ensureDefaultRankings($player);

        return $player;
    }

    private function ensureDefaultRankings(Player $player): void
    {
        $gamemodeIds = GameMode::pluck('id');

        $existingGamemodeIds = Ranking::where('playerid', $player->id)
            ->pluck('gamemodeid');

        $missingGamemodeIds = $gamemodeIds->diff($existingGamemodeIds);

        if ($missingGamemodeIds->isEmpty()) {
            return;
        }

        $now = now();

        $rows = $missingGamemodeIds->map(function ($gamemodeId) use ($player, $now) {
            return [
                'playerid' => $player->id,
                'gamemodeid' => $gamemodeId,
                'ranking' => 1000,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->values()->all();

        Ranking::insert($rows);
    }
}