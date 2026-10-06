<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Admin panel roles: owner — everything, manager — orders, editor — content.
     */
    public function run(): void
    {
        foreach (['owner', 'manager', 'editor'] as $role) {
            Role::findOrCreate($role);
        }
    }
}
