<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {

        Membership::create([
            'name' => 'Bronze',
            'rules' => '[{"boat_registrations":1,"paddle_lab_value":0}]',
            'sort' => 10,
        ]);
        Membership::create([
            'name' => 'Silver',
            'rules' => '[{"boat_registrations":1,"paddle_lab_value":500},{"boat_registrations":2,"paddle_lab_value":0}]',
            'sort' => 20,
        ]);
        Membership::create([
            'name' => 'Gold',
            'rules' => '[{"boat_registrations":1,"paddle_lab_value":1000},{"boat_registrations":2,"paddle_lab_value":500}]',
            'sort' => 30,
        ]);
        Membership::create([
            'name' => 'Platinum',
            'rules' => '[{"boat_registrations":4,"paddle_lab_value":0}]',
            'sort' => 40,
        ]);

        foreach (User::all() as $user) {
            $user->evaluateMembership();
        }
    }
}
