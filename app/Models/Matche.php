<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matche extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'serverid',
        'gamemodeid',
        'score',
        'mapuid',
        'winner',
        'finished',
    ];

    public function players()
    {
        return $this->belongsToMany(Player::class, 'matchplayers', 'matchid', 'playerid')
            ->withPivot('team', 'playorder', 'missing', 'replaced');
    }

    public function playersA()
    {
        return $this->belongsToMany(Player::class, 'matchplayers')
            ->withPivot('team', 'playorder', 'missing', 'replaced')
            ->withTimestamps()
            ->wherePivot('team', '=', 0);
    }

    public function playersB()
    {
        return $this->belongsToMany(Player::class, 'matchplayers')
            ->withPivot('team', 'playorder', 'missing', 'replaced')
            ->withTimestamps()
            ->wherePivot('team', '=', 1);
    }


    public function server()
    {
        return $this->belongsTo(Server::class, 'serverid');
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];
}
