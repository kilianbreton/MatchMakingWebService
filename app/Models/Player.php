<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
    ];
    public function run(): void
    {
        $playerRole = Role::firstOrCreate(['name' => 'player']);
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
        $accessAdmin = Permission::firstOrCreate(['name' => 'access admin']);
        $manageServers = Permission::firstOrCreate(['name' => 'manage servers']);
        $manageMatches = Permission::firstOrCreate(['name' => 'manage matches']);
        $manageStatistics = Permission::firstOrCreate(['name' => 'manage statistics']);
    
        $moderatorRole->givePermissionTo([
            $manageMatches,
        ]);
    
        $adminRole->givePermissionTo([
            $accessAdmin,
            $manageServers,
            $manageMatches,
            $manageStatistics,
        ]);
    
        $playerRole->syncPermissions([]);
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
