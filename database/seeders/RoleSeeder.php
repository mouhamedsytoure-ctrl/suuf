<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Rôles du Groupe 1 (MVP). Les autres (acheteur, ONG, admin...) viendront en V1/V2.
        foreach (['administrateur', 'chef_exploitation', 'producteur', 'agent_terrain'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
