<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class FitnessSeeder extends Seeder
{
    public function run(): void
    {
        $vanquishes = [36538,36539,36540,34435,45929,45925,36541,37881,37882,37883,37884,37885,36542,36543,36544,34317,45092,45928,36545,37887,37888,37889,37890,37891,27486,27488,28974,27544,27487,28651,28649,29581,28652,28653,23004,23005,34153,36505,23006,20362,21865,41552,34434,20363,20742,21866,47356,22862,22950,20741,20366,21867,34326,20365];
        foreach ($vanquishes as $vanquish) {
            dump("Searching for: $vanquish\n");
            if(!Product::firstWhere('external_id', $vanquish)) {
                dump("Syncing $vanquish");
                Product::getWithSync($vanquish);
            }
        }
    }
}
