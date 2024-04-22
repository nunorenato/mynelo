<?php

namespace Database\Seeders;

use App\Enums\ProductTypeEnum;
use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {

        foreach (ProductTypeEnum::cases() as $type) {
            ProductType::create(['id' => $type->value, 'name' => implode(' ', Str::ucsplit($type->name))]);
        }


    }
}
