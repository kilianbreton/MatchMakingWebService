<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $fillable = [
        'playerid',
        'gamemodeid',
        'ranking',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerid');
    }

    public function gamemode()
    {
        return $this->belongsTo(GameMode::class, 'gamemodeid');
    }
}