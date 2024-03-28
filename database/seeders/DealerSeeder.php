<?php

namespace Database\Seeders;

use App\Models\Dealer;
use Illuminate\Database\Seeder;

class DealerSeeder extends Seeder
{
    public function run(): void
    {
        Dealer::create(['id' => 1, 'name' => 'Directly with Nelo', 'external_id' => -1]);
        Dealer::create(['id' => 2, 'name' => 'Private seller', 'external_id' => -2]);
    }
}
