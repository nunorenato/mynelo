<?php

namespace Database\Seeders;

use App\Enums\ProductTypeEnum;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        ProductType::create(['id' => ProductTypeEnum::Boat, 'name' => 'Boat']);
        ProductType::create(['id' => ProductTypeEnum::Seat, 'name' => 'Seat']);
        ProductType::create(['id' => ProductTypeEnum::Footrest, 'name' => 'Footrest']);
        ProductType::create(['id' => ProductTypeEnum::Rudder, 'name' => 'Rudder']);
        ProductType::create(['id' => ProductTypeEnum::Color, 'name' => 'Color']);
        ProductType::create(['id' => ProductTypeEnum::Cover, 'name' => 'Cover']);
        ProductType::create(['id' => ProductTypeEnum::NumberHolder, 'name' => 'Number holder']);
    }
}
