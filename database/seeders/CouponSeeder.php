<?php

namespace Database\Seeders;

use App\Jobs\MagentoCouponJob;
use App\Models\User;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereJsonContainsKey(column: 'extras->coupon', not: true)->get();
        //dump(count($users));
        foreach($users as $user){
            MagentoCouponJob::dispatch($user);
        }
    }
}
