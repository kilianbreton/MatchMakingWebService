<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Server extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'login',
        'name',
        'gamemode',
        'type',
        'latestping',
        'ownerid',
        'apikey',
        'nbplayers',
        'score',
        'lobbyid',
    ];
    public function getAuthPassword()
    {
        return $this->apikey;
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'type' => 'server'
        ];
    }

    public function lobby()
    {
        return $this->belongsTo(Server::class, 'lobbyid');
    }
    public function gamemode()
    {
        return $this->belongsTo(GameMode::class, 'gamemode');
    }

    public function owner()
    {
        return $this->belongsTo(Player::class, 'ownerid');
    }

    public function matches()
    {
        return $this->hasMany(Matche::class, 'serverid');
    }

    protected $hidden = [
        'apikey',
    ];
}
