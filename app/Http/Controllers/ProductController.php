<?php

namespace App\Http\Controllers;

use App\Enums\DisciplineEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'image' => ['nullable'],
            'external_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
        ]);

        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required'],
            'image' => ['nullable'],
            'external_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
        ]);

        $product->update($data);

        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json();
    }

    public static function getWithSync(int $externalID):Product|null
    {
        return Product::where('external_id', '=', $externalID)->firstOr(function() use ($externalID){
            $response = Http::get(config('nelo.nelo_api_url')."/product/v2/$externalID");
            if($response->ok()){
                $product = $response->object();

                Log::info("Creating product with external id {$product->id}");

                $type = null;
                foreach ($product->genealogy as $ancestor){
                    $type = ProductTypeEnum::fromAPI($ancestor);
                    if($type != null)
                        break;
                }
                if($type == null){
                    Log::error('Product Type not mapped: '.$product->type);
                    return null;
                }

                $p = Product::create([
                    'external_id' => $product->id,
                    'name' => $product->name,
                    'product_type_id' => $type,
                    'discipline_id' => $product->discipline==null?null:DisciplineEnum::fromAPI($product->discipline),
                ]);
                if(!empty($product->image)){
                    $ic = new ImageController();
                    $p->image()->associate(ImageController::fromURL($product->image, $product->name, 'products'))->save();
                }
                return $p;
            }
            else{
                Log::error('Get product API Error', ['P_ID' => $externalID]);
                return null;
            }
        });
    }
}
