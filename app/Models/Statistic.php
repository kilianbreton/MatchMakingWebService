<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'type',
        'playerid',
        'matchid',
        'serverid',
        'gamemodeid',
        'mapuid',
        'value',
    ];

    public function type()
    {
        return $this->belongsTo(StatInfo::class, 'type');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'playerid');
    }

    public function match()
    {
        return $this->belongsTo(Matche::class, 'matchid');
    }

    public function server()
    {
        return $this->belongsTo(Server::class, 'serverid');
    }

    public function gamemode()
    {
        return $this->belongsTo(GameMode::class, 'gamemodeid');
    }

    public function map()
    {
        return $this->belongsTo(Map::class, 'mapuid');
    }
}
