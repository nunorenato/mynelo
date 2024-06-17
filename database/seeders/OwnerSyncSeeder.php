<?php

namespace Database\Seeders;

use App\Models\BoatRegistration;
use App\Models\User;
use App\Services\NeloApiClient;
use Illuminate\Database\Seeder;

class OwnerSyncSeeder extends Seeder
{
    public function run(): void
    {
        $nelo = new NeloApiClient();


        User::all()->each(function (User $user) use ($nelo){
            dump("Storing user {$user->name}");
            $nelo->storeOwner($user);

            $user->boats->each(function (BoatRegistration $boatRegistration) use ($nelo){
                dump("Storing boat {$boatRegistration->boat->external_id}");
                $nelo->storeRegistration($boatRegistration);
            });
        });


    }
}
