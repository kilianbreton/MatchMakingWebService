<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $playerRole = Role::firstOrCreate(['name' => 'player']);
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
        $accessAdmin = Permission::firstOrCreate(['name' => 'access admin']);
        $manageServers = Permission::firstOrCreate(['name' => 'manage servers']);
        $manageMatches = Permission::firstOrCreate(['name' => 'manage matches']);
        $manageStatistics = Permission::firstOrCreate(['name' => 'manage statistics']);
        $manageMappacks = Permission::firstOrCreate(['name' => 'manage mappacks']);
        $manageQueues = Permission::firstOrCreate(['name' => 'manage queues']);
    
        $moderatorRole->givePermissionTo([
            $manageMappacks,
            $manageQueues,
        ]);
    
        $adminRole->givePermissionTo([
            $accessAdmin,
            $manageServers,
            $manageMatches,
            $manageStatistics,
            $manageMappacks,
            $manageQueues,
        ]);
    
        $playerRole->syncPermissions([]);
    }
}
