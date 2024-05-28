<?php

namespace Database\Seeders;

use App\Enums\ProductTypeEnum;
use App\Models\Product;
use Illuminate\Database\Seeder;

class MagentoProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach(Product::where('product_type_id', '<>', [ProductTypeEnum::Boat, ProductTypeEnum::Color])->get() as $product){
            dump($product->name);
            $product->updateFromMagento();
        }


    }
}
