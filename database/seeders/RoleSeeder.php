<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::create(['name' => 'Admin']);
        User::where('email', 'nuno.ramos@nelo.eu')->first()->assignRole($role);

    }
}
