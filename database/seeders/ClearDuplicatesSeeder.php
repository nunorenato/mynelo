<?php

namespace Database\Seeders;

use App\Models\Boat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearDuplicatesSeeder extends Seeder
{
    public function run(): void
    {

        $duplicates = DB::query()->selectRaw('boat_product.boat_id, product_id, count(product_id) as duplicate_count
FROM boat_product
	INNER JOIN products p ON p.id = product_id
	INNER JOIN product_types pt ON pt.id = p.product_type_id
WHERE  pt.fitting = 1
GROUP BY boat_id, product_id, attribute_id
HAVING COUNT(product_id) > 1')->get();

       //dump($duplicates);
        $duplicates->each(function ($item) {

            DB::table('boat_product')
                ->where('product_id', '=', $item->product_id)
                ->where('boat_id', '=', $item->boat_id)
                ->limit($item->duplicate_count - 1)
                ->orderByDesc('id')
                ->get()->each(function ($fitting) {
                    dump($fitting->id);
                    DB::table('boat_product')->delete($fitting->id);
                });
        });
    }
}
