<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Player extends Authenticatable
{
    use HasFactory;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'login',
        'name',
        'location',
        'token',
        'refresh',
        'is_banned',
        'banned_until',
        'ban_reason',
        'is_muted',
        'muted_until',
        'mute_reason',
    ];

    protected $casts = [
        'is_banned' => 'boolean',
        'is_muted' => 'boolean',
        'banned_until' => 'datetime',
        'muted_until' => 'datetime',
    ];
   
    public function getBanActiveAttribute(): bool
    {
        if (!$this->is_banned) {
            return false;
        }
    
        return $this->banned_until === null || $this->banned_until->isFuture();
    }
    
    public function getMuteActiveAttribute(): bool
    {
        if (!$this->is_muted) {
            return false;
        }
    
        return $this->muted_until === null || $this->muted_until->isFuture();
    }

    
    public function rankings()
    {
        return $this->hasMany(Ranking::class, 'playerid');
    }
    
    public function matches()
    {
        return $this->belongsToMany(Matche::class, 'matchplayers')
            ->withPivot('team', 'playorder', 'missing', 'replaced')
            ->withTimestamps();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'token',
        'refresh',
    ];
}
