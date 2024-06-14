<?php

namespace Database\Seeders;

use App\Enums\ProductTypeEnum;
use App\Models\Boat;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ComponentSeeder extends Seeder
{
    public function run(): void
    {


        if(ProductType::count() > 7)
            dd('Already run. Abort');

        ProductType::whereNotIn('id', [
            ProductTypeEnum::Color,
            ProductTypeEnum::NumberHolder,
        ])->update(['fitting' => true]);

        $types = Arr::where(ProductTypeEnum::cases(), fn(ProductTypeEnum $type) => $type->value > 7);
        foreach ($types as $type){
            ProductType::create(['id' => $type->value, 'name' => implode(' ', Str::ucsplit($type->name)), 'fitting' => false]);
        }

        Boat::all()->each(function (Boat $boat) {
            dump('Getting components for boat', $boat->external_id);
            $boat->syncComponents();
        });

    }
}
