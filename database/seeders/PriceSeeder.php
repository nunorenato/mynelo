<?php

namespace Database\Seeders;

use App\Models\BoatModel;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $boats = BoatModel::all();

        foreach ($boats as $boat){
            dump($boat->external_id);
            $boat->updateFromAPI();
        }
    }
}
