<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMode extends Model
{
    protected $table = 'gamemodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'titlepack',
        'lobbyscript',
        'matchscript',
    ];

   
  
}
