<?php

namespace Database\Seeders;

use App\Models\Boat;
use Illuminate\Database\Seeder;

class BoatSyncedSeeder extends Seeder
{
    public function run(): void
    {
        Boat::all()->each(function (Boat $boat){
            $boat->update(['synced' => true]);
        });

    }
}
