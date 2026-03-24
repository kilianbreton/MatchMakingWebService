<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $fillable = [
        'uid',
        'mxid',
        'name',
        'author',
    ];

    public function matche()
    {
        return $this->hasMany(Matche::class, 'mapuid');
    }
}
