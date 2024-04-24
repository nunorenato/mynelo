<?php

namespace Database\Seeders;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach([11, 62, 168,169, 14, 10, 326] as $tipo) {
            $response = Http::get(config('nelo.nelo_api_url') . "/products/type/v2/$tipo");
            if ($response->ok()) {
                $produtos = $response->json();
                foreach ($produtos as $produto) {
                    Product::where('external_id', '=', $produto['id'])->firstOr(function () use ($produto) {
                        ProductController::createFromJSON((object)$produto);
                        //dump((object) $produto);
                    });
                }
                //dump($produtos);
            }
        }
    }
}
