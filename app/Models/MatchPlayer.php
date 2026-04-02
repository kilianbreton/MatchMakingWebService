<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    use HasFactory;

    protected $table = 'matchplayers';

    public $timestamps = false;

    protected $fillable = [
        'matchid',
        'playerid',
        'team',
        'playorder',
        'missing',
        'replaced'
    ];
}
